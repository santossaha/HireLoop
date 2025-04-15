<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Notifications\AttendanceReminderNotification;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the attendances
     */
    public function index(Request $request)
    {
        $query = VendorAttendance::query();
        
        // Filter by month/year if provided
        if ($request->has('month') && $request->has('year')) {
            $month = $request->month;
            $year = $request->year;
            
            if (!empty($month) && !empty($year)) {
                $query->whereYear('month_year', $year)
                      ->whereMonth('month_year', $month);
            }
        } else {
            // Default to current month
            $query->whereYear('month_year', now()->year)
                  ->whereMonth('month_year', now()->month);
        }
        
        // Filter by status if provided
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Filter by vendor if provided
        if ($request->has('vendor_id') && !empty($request->vendor_id)) {
            $query->where('vendor_id', $request->vendor_id);
        }
        
        // If user is a POC, only show their vendors
        if (Auth::user()->isPoc()) {
            $vendorIds = Vendor::where('internal_poc_id', Auth::id())->pluck('id');
            $query->whereIn('vendor_id', $vendorIds);
        }
        
        $attendances = $query->with(['vendor', 'submittedBy', 'approvedBy'])
                             ->orderBy('vendor_id')
                             ->paginate(15);
        
        // Get vendors for the dropdown
        $vendors = Vendor::all();
        
        // Get stats for the dashboard
        $pendingCount = VendorAttendance::whereYear('month_year', now()->year)
                                      ->whereMonth('month_year', now()->month)
                                      ->where('status', 'pending')
                                      ->count();
                                      
        $approvedCount = VendorAttendance::whereYear('month_year', now()->year)
                                       ->whereMonth('month_year', now()->month)
                                       ->where('status', 'approved')
                                       ->count();
                                       
        $rejectedCount = VendorAttendance::whereYear('month_year', now()->year)
                                       ->whereMonth('month_year', now()->month)
                                       ->where('status', 'rejected')
                                       ->count();
        
        return view('attendance.index', compact('attendances', 'vendors', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    /**
     * Show the form for submitting attendance
     */
    public function create()
    {
        // If user is a POC, get their vendors
        if (Auth::user()->isPoc()) {
            $vendors = Vendor::where('internal_poc_id', Auth::id())->get();
        } else {
            $vendors = Vendor::all();
        }
        
        return view('attendance.create', compact('vendors'));
    }

    /**
     * Submit attendance for a vendor
     */
    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'month_year' => 'required|date',
            'working_days' => 'required|integer|min:0|max:31',
            'present_days' => 'required|integer|min:0',
            'leave_days' => 'required|integer|min:0',
            'approved_leave_days' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if attendance already exists for this vendor and month
        $existingAttendance = VendorAttendance::where('vendor_id', $request->vendor_id)
                                            ->whereYear('month_year', Carbon::parse($request->month_year)->year)
                                            ->whereMonth('month_year', Carbon::parse($request->month_year)->month)
                                            ->first();
        
        if ($existingAttendance) {
            // Update existing attendance
            $existingAttendance->working_days = $request->working_days;
            $existingAttendance->present_days = $request->present_days;
            $existingAttendance->leave_days = $request->leave_days;
            $existingAttendance->approved_leave_days = $request->approved_leave_days;
            $existingAttendance->notes = $request->notes;
            $existingAttendance->submitted_by = Auth::id();
            $existingAttendance->submitted_at = now();
            $existingAttendance->status = 'pending';
            $existingAttendance->save();
            
            $message = 'Attendance record updated successfully.';
        } else {
            // Create new attendance record
            $attendance = new VendorAttendance();
            $attendance->vendor_id = $request->vendor_id;
            $attendance->month_year = $request->month_year;
            $attendance->working_days = $request->working_days;
            $attendance->present_days = $request->present_days;
            $attendance->leave_days = $request->leave_days;
            $attendance->approved_leave_days = $request->approved_leave_days;
            $attendance->notes = $request->notes;
            $attendance->submitted_by = Auth::id();
            $attendance->submitted_at = now();
            $attendance->status = 'pending';
            $attendance->save();
            
            $message = 'Attendance record submitted successfully.';
        }
        
        return redirect()->route('attendance.index')
            ->with('success', $message);
    }

    /**
     * Approve or reject an attendance record
     */
    public function approve(Request $request, VendorAttendance $attendance)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($request->action === 'approve') {
            $attendance->approve(Auth::id());
            $message = 'Attendance record approved successfully.';
        } else {
            $attendance->reject(Auth::id(), $request->notes);
            $message = 'Attendance record rejected.';
        }
        
        return redirect()->route('attendance.index')
            ->with('success', $message);
    }

    /**
     * Send reminders to POCs to submit attendance
     */
    public function sendReminders()
    {
        // Check if it's between the 20th and 25th of the month
        $today = now();
        $dayOfMonth = $today->day;
        
        if ($dayOfMonth >= 20 && $dayOfMonth <= 25) {
            // Get all POCs
            $pocs = User::where('role', 'poc')->get();
            
            foreach ($pocs as $poc) {
                // Get vendors managed by this POC
                $vendors = Vendor::where('internal_poc_id', $poc->id)->pluck('id');
                
                // Check which vendors don't have attendance for the current month
                $pendingVendors = [];
                
                foreach ($vendors as $vendorId) {
                    $attendance = VendorAttendance::where('vendor_id', $vendorId)
                                                ->whereYear('month_year', $today->year)
                                                ->whereMonth('month_year', $today->month)
                                                ->first();
                    
                    if (!$attendance) {
                        $vendor = Vendor::find($vendorId);
                        $pendingVendors[] = $vendor->company_name;
                    }
                }
                
                // If there are pending vendors, send a reminder
                if (count($pendingVendors) > 0) {
                    $poc->notify(new AttendanceReminderNotification($pendingVendors));
                }
            }
            
            return redirect()->back()->with('success', 'Reminders sent to POCs successfully.');
        }
        
        return redirect()->back()->with('error', 'Reminders are only sent between the 20th and 25th of the month.');
    }
}
