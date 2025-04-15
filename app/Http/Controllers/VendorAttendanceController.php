<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorAttendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VendorAttendanceController extends Controller
{
    /**
     * Display a listing of vendor attendances.
     */
    public function index()
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isPoc() && !Auth::user()->isAccounts() && !Auth::user()->isFounder()) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view attendance records.');
        }

        $query = VendorAttendance::with(['vendor', 'submitter', 'approver']);
        
        // Filter by role
        if (Auth::user()->isPoc()) {
            $managedVendors = Vendor::where('internal_poc_id', Auth::id())->pluck('id');
            $query->whereIn('vendor_id', $managedVendors);
        }
        
        // Filter by month if provided
        $selectedMonth = request('month') ? Carbon::parse(request('month')) : Carbon::now()->startOfMonth();
        $query->whereMonth('month_year', $selectedMonth->month)
              ->whereYear('month_year', $selectedMonth->year);
        
        $attendances = $query->orderBy('created_at', 'desc')->get();
        
        // Generate months list for filter
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();
            $months[$date->format('Y-m')] = $date->format('F Y');
        }
        
        return view('vendor-attendances.index', compact('attendances', 'months', 'selectedMonth'));
    }

    /**
     * Show the form for creating a new vendor attendance.
     */
    public function create()
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isPoc()) {
            return redirect()->route('vendor-attendances.index')->with('error', 'You do not have permission to create attendance records.');
        }
        
        // Get vendors based on role
        if (Auth::user()->isAdmin()) {
            $vendors = Vendor::all();
        } else {
            $vendors = Vendor::where('internal_poc_id', Auth::id())->get();
        }
        
        // Check if no vendors are assigned
        if ($vendors->isEmpty() && Auth::user()->isPoc()) {
            return redirect()->route('vendor-attendances.index')->with('error', 'You do not have any vendors assigned to you.');
        }
        
        // Get current month for default value
        $currentMonth = Carbon::now()->format('Y-m');
        $monthDays = Carbon::now()->daysInMonth;
        
        return view('vendor-attendances.create', compact('vendors', 'currentMonth', 'monthDays'));
    }

    /**
     * Store a newly created vendor attendance in storage.
     */
    public function store(Request $request)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isPoc()) {
            return redirect()->route('vendor-attendances.index')->with('error', 'You do not have permission to create attendance records.');
        }
        
        // Validate vendor access if POC
        if (Auth::user()->isPoc()) {
            $vendor = Vendor::findOrFail($request->vendor_id);
            if ($vendor->internal_poc_id !== Auth::id()) {
                return redirect()->route('vendor-attendances.index')->with('error', 'You can only submit attendance for vendors assigned to you.');
            }
        }
        
        // Validate request data
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'month_year' => 'required|date',
            'working_days' => 'required|integer|min:0|max:31',
            'present_days' => 'required|integer|min:0|max:31',
            'leave_days' => 'required|integer|min:0|max:31',
            'notes' => 'nullable|string',
        ]);
        
        // Check if attendance for this vendor and month already exists
        $exists = VendorAttendance::where('vendor_id', $validated['vendor_id'])
            ->whereMonth('month_year', Carbon::parse($validated['month_year'])->month)
            ->whereYear('month_year', Carbon::parse($validated['month_year'])->year)
            ->exists();
            
        if ($exists) {
            return redirect()->route('vendor-attendances.index')->with('error', 'Attendance record for this vendor and month already exists.');
        }
        
        // Check if present_days + leave_days exceeds working_days
        if ($validated['present_days'] + $validated['leave_days'] > $validated['working_days']) {
            return redirect()->back()->withInput()->with('error', 'Present days plus leave days cannot exceed working days.');
        }
        
        // Add submitted_by and submitted_at
        $validated['submitted_by'] = Auth::id();
        $validated['submitted_at'] = now();
        $validated['status'] = 'pending';
        
        // Parse month_year to ensure it's the first day of the month
        $validated['month_year'] = Carbon::parse($validated['month_year'])->startOfMonth()->toDateString();
        
        VendorAttendance::create($validated);
        
        return redirect()->route('vendor-attendances.index')->with('success', 'Vendor attendance record created successfully.');
    }

    /**
     * Display the specified vendor attendance.
     */
    public function show(VendorAttendance $vendorAttendance)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isPoc() && !Auth::user()->isAccounts() && !Auth::user()->isFounder()) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view attendance records.');
        }
        
        // Check if POC can access this attendance
        if (Auth::user()->isPoc()) {
            $vendor = $vendorAttendance->vendor;
            if ($vendor->internal_poc_id !== Auth::id()) {
                return redirect()->route('vendor-attendances.index')->with('error', 'You can only view attendance for vendors assigned to you.');
            }
        }
        
        $vendorAttendance->load(['vendor', 'submitter', 'approver']);
        
        return view('vendor-attendances.show', compact('vendorAttendance'));
    }

    /**
     * Show the form for editing the specified vendor attendance.
     */
    public function edit(VendorAttendance $vendorAttendance)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isPoc()) {
            return redirect()->route('vendor-attendances.index')->with('error', 'You do not have permission to edit attendance records.');
        }
        
        // Check if POC can access this attendance
        if (Auth::user()->isPoc()) {
            $vendor = $vendorAttendance->vendor;
            if ($vendor->internal_poc_id !== Auth::id()) {
                return redirect()->route('vendor-attendances.index')->with('error', 'You can only edit attendance for vendors assigned to you.');
            }
        }
        
        // Can't edit if already approved
        if ($vendorAttendance->status === 'approved') {
            return redirect()->route('vendor-attendances.show', $vendorAttendance)->with('error', 'Cannot edit an approved attendance record.');
        }
        
        $vendors = Auth::user()->isAdmin() 
            ? Vendor::all() 
            : Vendor::where('internal_poc_id', Auth::id())->get();
            
        $monthDays = Carbon::parse($vendorAttendance->month_year)->daysInMonth;
        
        return view('vendor-attendances.edit', compact('vendorAttendance', 'vendors', 'monthDays'));
    }

    /**
     * Update the specified vendor attendance in storage.
     */
    public function update(Request $request, VendorAttendance $vendorAttendance)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isPoc()) {
            return redirect()->route('vendor-attendances.index')->with('error', 'You do not have permission to update attendance records.');
        }
        
        // Check if POC can access this attendance
        if (Auth::user()->isPoc()) {
            $vendor = $vendorAttendance->vendor;
            if ($vendor->internal_poc_id !== Auth::id()) {
                return redirect()->route('vendor-attendances.index')->with('error', 'You can only update attendance for vendors assigned to you.');
            }
        }
        
        // Can't update if already approved
        if ($vendorAttendance->status === 'approved') {
            return redirect()->route('vendor-attendances.show', $vendorAttendance)->with('error', 'Cannot update an approved attendance record.');
        }
        
        // Validate request data
        $validated = $request->validate([
            'working_days' => 'required|integer|min:0|max:31',
            'present_days' => 'required|integer|min:0|max:31',
            'leave_days' => 'required|integer|min:0|max:31',
            'notes' => 'nullable|string',
        ]);
        
        // Check if present_days + leave_days exceeds working_days
        if ($validated['present_days'] + $validated['leave_days'] > $validated['working_days']) {
            return redirect()->back()->withInput()->with('error', 'Present days plus leave days cannot exceed working days.');
        }
        
        // Update submission details
        $validated['submitted_by'] = Auth::id();
        $validated['submitted_at'] = now();
        $validated['status'] = 'pending';
        
        $vendorAttendance->update($validated);
        
        return redirect()->route('vendor-attendances.index')->with('success', 'Vendor attendance record updated successfully.');
    }

    /**
     * Approve the vendor attendance record.
     */
    public function approve(VendorAttendance $vendorAttendance, Request $request)
    {
        // Access control check - only admin and founder can approve
        if (!Auth::user()->isAdmin() && !Auth::user()->isFounder()) {
            return redirect()->route('vendor-attendances.index')->with('error', 'You do not have permission to approve attendance records.');
        }
        
        // Validate request
        $validated = $request->validate([
            'approved_leave_days' => 'required|integer|min:0|max:' . $vendorAttendance->leave_days,
            'notes' => 'nullable|string',
        ]);
        
        // Update approval details
        $vendorAttendance->update([
            'approved_leave_days' => $validated['approved_leave_days'],
            'notes' => $request->filled('notes') ? $vendorAttendance->notes . "\n\nApproval notes: " . $validated['notes'] : $vendorAttendance->notes,
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        
        return redirect()->route('vendor-attendances.show', $vendorAttendance)->with('success', 'Vendor attendance record approved successfully.');
    }

    /**
     * Reject the vendor attendance record.
     */
    public function reject(VendorAttendance $vendorAttendance, Request $request)
    {
        // Access control check - only admin and founder can reject
        if (!Auth::user()->isAdmin() && !Auth::user()->isFounder()) {
            return redirect()->route('vendor-attendances.index')->with('error', 'You do not have permission to reject attendance records.');
        }
        
        // Validate request
        $validated = $request->validate([
            'notes' => 'required|string|min:5',
        ]);
        
        // Update rejection details
        $vendorAttendance->update([
            'notes' => $vendorAttendance->notes . "\n\nRejection notes: " . $validated['notes'],
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);
        
        return redirect()->route('vendor-attendances.show', $vendorAttendance)->with('success', 'Vendor attendance record rejected successfully.');
    }

    /**
     * Send reminders to POCs who haven't submitted attendance yet.
     */
    public function sendReminders()
    {
        // Access control check - only admin and accounts can send reminders
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts()) {
            return redirect()->route('vendor-attendances.index')->with('error', 'You do not have permission to send attendance reminders.');
        }
        
        // Get current month
        $currentMonth = Carbon::now()->startOfMonth();
        
        // Get all vendors with POCs
        $vendors = Vendor::whereNotNull('internal_poc_id')->get();
        
        // Group vendors by POC
        $pocVendors = [];
        foreach ($vendors as $vendor) {
            $pocId = $vendor->internal_poc_id;
            if (!isset($pocVendors[$pocId])) {
                $pocVendors[$pocId] = [];
            }
            $pocVendors[$pocId][] = $vendor->id;
        }
        
        // Check which vendors don't have attendance records for the current month
        $remindersSent = 0;
        
        foreach ($pocVendors as $pocId => $vendorIds) {
            $missingAttendance = [];
            
            foreach ($vendorIds as $vendorId) {
                $exists = VendorAttendance::where('vendor_id', $vendorId)
                    ->whereYear('month_year', $currentMonth->year)
                    ->whereMonth('month_year', $currentMonth->month)
                    ->exists();
                    
                if (!$exists) {
                    $vendor = Vendor::find($vendorId);
                    $missingAttendance[] = $vendor->company_name;
                }
            }
            
            // If there are vendors missing attendance records, send a reminder to the POC
            if (!empty($missingAttendance)) {
                $poc = User::find($pocId);
                
                // In a real application, we would send an email here
                // For now, we'll just log it
                \Log::info("Reminder sent to POC {$poc->name} for missing attendance records: " . implode(', ', $missingAttendance));
                
                $remindersSent++;
            }
        }
        
        return redirect()->route('vendor-attendances.index')->with('success', "Reminders sent to {$remindersSent} POCs for missing attendance records.");
    }

    /**
     * Display a summary of attendance records.
     */
    public function summary()
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts() && !Auth::user()->isFounder()) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view attendance summary.');
        }
        
        // Get selected month or default to current month
        $selectedMonth = request('month') ? Carbon::parse(request('month')) : Carbon::now()->startOfMonth();
        
        // Get all attendance records for the selected month
        $attendances = VendorAttendance::with('vendor')
            ->whereYear('month_year', $selectedMonth->year)
            ->whereMonth('month_year', $selectedMonth->month)
            ->get();
            
        // Calculate statistics
        $totalVendors = Vendor::count();
        $submittedCount = $attendances->count();
        $approvedCount = $attendances->where('status', 'approved')->count();
        $pendingCount = $attendances->where('status', 'pending')->count();
        $rejectedCount = $attendances->where('status', 'rejected')->count();
        
        $totalWorkingDays = $attendances->sum('working_days');
        $totalPresentDays = $attendances->sum('present_days');
        $totalLeaveDays = $attendances->sum('leave_days');
        $totalApprovedLeaveDays = $attendances->sum('approved_leave_days');
        
        // Calculate attendance percentage
        $attendancePercentage = $totalWorkingDays > 0 
            ? round(($totalPresentDays + $totalApprovedLeaveDays) / $totalWorkingDays * 100, 2) 
            : 0;
            
        // Generate months list for filter
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();
            $months[$date->format('Y-m')] = $date->format('F Y');
        }
        
        return view('vendor-attendances.summary', compact(
            'attendances',
            'months',
            'selectedMonth',
            'totalVendors',
            'submittedCount',
            'approvedCount',
            'pendingCount',
            'rejectedCount',
            'totalWorkingDays',
            'totalPresentDays',
            'totalLeaveDays',
            'totalApprovedLeaveDays',
            'attendancePercentage'
        ));
    }
}