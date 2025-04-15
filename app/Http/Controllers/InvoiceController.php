<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Vendor;
use App\Models\User;
use App\Models\VendorAttendance;
use App\Models\VendorPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of invoices.
     */
    public function index()
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isPoc() && !Auth::user()->isAccounts() && !Auth::user()->isFounder() && !Auth::user()->isVendor()) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view invoices.');
        }

        $query = Invoice::with(['vendor', 'submitter', 'verifier']);
        
        // Filter by role
        if (Auth::user()->isVendor()) {
            $vendor = Auth::user()->vendor;
            if (!$vendor) {
                return redirect()->route('dashboard')->with('error', 'You do not have a vendor profile.');
            }
            $query->where('vendor_id', $vendor->id);
        } elseif (Auth::user()->isPoc()) {
            $managedVendors = Vendor::where('internal_poc_id', Auth::id())->pluck('id');
            $query->whereIn('vendor_id', $managedVendors);
        }
        
        // Filter by month if provided
        if (request()->has('month')) {
            $month = Carbon::parse(request('month'));
            $query->whereMonth('month_year', $month->month)
                  ->whereYear('month_year', $month->year);
        }
        
        // Filter by verification status if provided
        if (request()->has('status') && request('status') !== 'all') {
            $query->where('verification_status', request('status'));
        }
        
        $invoices = $query->orderBy('invoice_date', 'desc')->get();
        
        // Generate months list for filter
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();
            $months[$date->format('Y-m')] = $date->format('F Y');
        }
        
        return view('invoices.index', compact('invoices', 'months'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isPoc() && !Auth::user()->isVendor()) {
            return redirect()->route('invoices.index')->with('error', 'You do not have permission to create invoices.');
        }
        
        // Get vendor based on role
        if (Auth::user()->isVendor()) {
            $vendor = Auth::user()->vendor;
            if (!$vendor) {
                return redirect()->route('dashboard')->with('error', 'You do not have a vendor profile.');
            }
            $vendors = collect([$vendor]);
        } elseif (Auth::user()->isPoc()) {
            $vendors = Vendor::where('internal_poc_id', Auth::id())->get();
            if ($vendors->isEmpty()) {
                return redirect()->route('invoices.index')->with('error', 'You do not have any vendors assigned to you.');
            }
        } else {
            $vendors = Vendor::all();
        }
        
        // Get current month for default value
        $currentMonth = Carbon::now()->format('Y-m');
        
        return view('invoices.create', compact('vendors', 'currentMonth'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isPoc() && !Auth::user()->isVendor()) {
            return redirect()->route('invoices.index')->with('error', 'You do not have permission to create invoices.');
        }
        
        // Validate vendor access if POC or vendor
        if (Auth::user()->isVendor()) {
            $vendor = Auth::user()->vendor;
            if (!$vendor || $vendor->id != $request->vendor_id) {
                return redirect()->route('invoices.index')->with('error', 'You can only submit invoices for your own vendor profile.');
            }
        } elseif (Auth::user()->isPoc()) {
            $vendor = Vendor::findOrFail($request->vendor_id);
            if ($vendor->internal_poc_id !== Auth::id()) {
                return redirect()->route('invoices.index')->with('error', 'You can only submit invoices for vendors assigned to you.');
            }
        }
        
        // Validate request data
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'invoice_number' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'month_year' => 'required|date',
            'invoice_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        
        // Check for duplicate invoice number
        $exists = Invoice::where('vendor_id', $validated['vendor_id'])
            ->where('invoice_number', $validated['invoice_number'])
            ->exists();
            
        if ($exists) {
            return redirect()->route('invoices.index')->with('error', 'Invoice with this number already exists for this vendor.');
        }
        
        // Store the invoice file
        $invoicePath = $request->file('invoice_file')->store('invoices', 'public');
        
        // Parse month_year to ensure it's the first day of the month
        $monthYear = Carbon::parse($validated['month_year'])->startOfMonth()->toDateString();
        
        // Create invoice record
        $invoice = Invoice::create([
            'vendor_id' => $validated['vendor_id'],
            'invoice_number' => $validated['invoice_number'],
            'invoice_date' => $validated['invoice_date'],
            'amount' => $validated['amount'],
            'month_year' => $monthYear,
            'invoice_path' => $invoicePath,
            'submitted_by' => Auth::id(),
            'submitted_at' => now(),
            'verification_status' => 'pending',
            'payment_status' => 'pending',
        ]);
        
        return redirect()->route('invoices.index')->with('success', 'Invoice submitted successfully.');
    }

    /**
     * Display the specified invoice.
     */
    public function show(Invoice $invoice)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isPoc() && !Auth::user()->isAccounts() && !Auth::user()->isFounder() && !Auth::user()->isVendor()) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view invoices.');
        }
        
        // Check if vendor can access this invoice
        if (Auth::user()->isVendor()) {
            $vendor = Auth::user()->vendor;
            if (!$vendor || $vendor->id !== $invoice->vendor_id) {
                return redirect()->route('invoices.index')->with('error', 'You can only view your own invoices.');
            }
        }
        
        // Check if POC can access this invoice
        if (Auth::user()->isPoc()) {
            $vendor = $invoice->vendor;
            if ($vendor->internal_poc_id !== Auth::id()) {
                return redirect()->route('invoices.index')->with('error', 'You can only view invoices for vendors assigned to you.');
            }
        }
        
        $invoice->load(['vendor', 'submitter', 'verifier', 'payments']);
        
        // Get attendance record for the same month
        $attendance = VendorAttendance::where('vendor_id', $invoice->vendor_id)
            ->whereYear('month_year', Carbon::parse($invoice->month_year)->year)
            ->whereMonth('month_year', Carbon::parse($invoice->month_year)->month)
            ->first();
            
        // Calculate expected amount based on attendance, if available
        $expectedAmount = null;
        if ($attendance && $attendance->status === 'approved') {
            $vendor = $invoice->vendor;
            $dailyRate = $this->calculateDailyRate($vendor);
            $payableDays = $attendance->present_days + $attendance->approved_leave_days;
            $expectedAmount = $dailyRate * $payableDays;
        }
        
        return view('invoices.show', compact('invoice', 'attendance', 'expectedAmount'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit(Invoice $invoice)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isPoc() && !Auth::user()->isVendor()) {
            return redirect()->route('invoices.index')->with('error', 'You do not have permission to edit invoices.');
        }
        
        // Check if vendor can access this invoice
        if (Auth::user()->isVendor()) {
            $vendor = Auth::user()->vendor;
            if (!$vendor || $vendor->id !== $invoice->vendor_id) {
                return redirect()->route('invoices.index')->with('error', 'You can only edit your own invoices.');
            }
        }
        
        // Check if POC can access this invoice
        if (Auth::user()->isPoc()) {
            $vendor = $invoice->vendor;
            if ($vendor->internal_poc_id !== Auth::id()) {
                return redirect()->route('invoices.index')->with('error', 'You can only edit invoices for vendors assigned to you.');
            }
        }
        
        // Can't edit if already verified
        if ($invoice->verification_status !== 'pending') {
            return redirect()->route('invoices.show', $invoice)->with('error', 'Cannot edit an invoice that has been verified.');
        }
        
        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isPoc() && !Auth::user()->isVendor()) {
            return redirect()->route('invoices.index')->with('error', 'You do not have permission to update invoices.');
        }
        
        // Check if vendor can access this invoice
        if (Auth::user()->isVendor()) {
            $vendor = Auth::user()->vendor;
            if (!$vendor || $vendor->id !== $invoice->vendor_id) {
                return redirect()->route('invoices.index')->with('error', 'You can only update your own invoices.');
            }
        }
        
        // Check if POC can access this invoice
        if (Auth::user()->isPoc()) {
            $vendor = $invoice->vendor;
            if ($vendor->internal_poc_id !== Auth::id()) {
                return redirect()->route('invoices.index')->with('error', 'You can only update invoices for vendors assigned to you.');
            }
        }
        
        // Can't update if already verified
        if ($invoice->verification_status !== 'pending') {
            return redirect()->route('invoices.show', $invoice)->with('error', 'Cannot update an invoice that has been verified.');
        }
        
        // Validate request data
        $validated = $request->validate([
            'invoice_number' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'month_year' => 'required|date',
            'invoice_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);
        
        // Check for duplicate invoice number
        $exists = Invoice::where('vendor_id', $invoice->vendor_id)
            ->where('invoice_number', $validated['invoice_number'])
            ->where('id', '!=', $invoice->id)
            ->exists();
            
        if ($exists) {
            return redirect()->route('invoices.index')->with('error', 'Invoice with this number already exists for this vendor.');
        }
        
        // Handle file update if provided
        if ($request->hasFile('invoice_file')) {
            // Delete the old file
            Storage::disk('public')->delete($invoice->invoice_path);
            
            // Store the new file
            $validated['invoice_path'] = $request->file('invoice_file')->store('invoices', 'public');
        }
        
        // Parse month_year to ensure it's the first day of the month
        $validated['month_year'] = Carbon::parse($validated['month_year'])->startOfMonth()->toDateString();
        
        // Update submitted by info
        $validated['submitted_by'] = Auth::id();
        $validated['submitted_at'] = now();
        
        $invoice->update($validated);
        
        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
    }

    /**
     * Verify the specified invoice.
     */
    public function verify(Request $request, Invoice $invoice)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts()) {
            return redirect()->route('invoices.index')->with('error', 'You do not have permission to verify invoices.');
        }
        
        // Can't verify if already verified
        if ($invoice->verification_status !== 'pending') {
            return redirect()->route('invoices.show', $invoice)->with('error', 'Invoice has already been verified.');
        }
        
        // Validate request data
        $validated = $request->validate([
            'verification_status' => 'required|in:verified,discrepancy',
            'discrepancy_notes' => 'required_if:verification_status,discrepancy|nullable|string',
        ]);
        
        // Update verification details
        $invoice->update([
            'verification_status' => $validated['verification_status'],
            'discrepancy_notes' => $validated['discrepancy_notes'],
            'verified_by' => Auth::id(),
            'verified_at' => now(),
        ]);
        
        // Create vendor payment if verified
        if ($validated['verification_status'] === 'verified') {
            $this->createOrUpdateVendorPayment($invoice);
        }
        
        $status = $validated['verification_status'] === 'verified' ? 'verified' : 'marked with discrepancies';
        return redirect()->route('invoices.show', $invoice)->with('success', "Invoice {$status} successfully.");
    }

    /**
     * Download the invoice file.
     */
    public function download(Invoice $invoice)
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isPoc() && !Auth::user()->isAccounts() && !Auth::user()->isFounder() && !Auth::user()->isVendor()) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to download invoices.');
        }
        
        // Check if vendor can access this invoice
        if (Auth::user()->isVendor()) {
            $vendor = Auth::user()->vendor;
            if (!$vendor || $vendor->id !== $invoice->vendor_id) {
                return redirect()->route('invoices.index')->with('error', 'You can only download your own invoices.');
            }
        }
        
        // Check if POC can access this invoice
        if (Auth::user()->isPoc()) {
            $vendor = $invoice->vendor;
            if ($vendor->internal_poc_id !== Auth::id()) {
                return redirect()->route('invoices.index')->with('error', 'You can only download invoices for vendors assigned to you.');
            }
        }
        
        // Check if file exists
        if (!Storage::disk('public')->exists($invoice->invoice_path)) {
            return redirect()->route('invoices.show', $invoice)->with('error', 'Invoice file not found.');
        }
        
        return Storage::disk('public')->download($invoice->invoice_path, "Invoice_{$invoice->invoice_number}.pdf");
    }

    /**
     * Display pending invoices that need verification.
     */
    public function pendingVerification()
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts()) {
            return redirect()->route('invoices.index')->with('error', 'You do not have permission to view pending verifications.');
        }
        
        $invoices = Invoice::with(['vendor', 'submitter'])
            ->where('verification_status', 'pending')
            ->orderBy('submitted_at', 'asc')
            ->get();
            
        // Get attendance records to compare amounts
        $invoicesWithData = [];
        
        foreach ($invoices as $invoice) {
            $attendance = VendorAttendance::where('vendor_id', $invoice->vendor_id)
                ->whereYear('month_year', Carbon::parse($invoice->month_year)->year)
                ->whereMonth('month_year', Carbon::parse($invoice->month_year)->month)
                ->first();
                
            $expectedAmount = null;
            $discrepancy = false;
            
            if ($attendance && $attendance->status === 'approved') {
                $vendor = $invoice->vendor;
                $dailyRate = $this->calculateDailyRate($vendor);
                $payableDays = $attendance->present_days + $attendance->approved_leave_days;
                $expectedAmount = $dailyRate * $payableDays;
                
                // Check for discrepancy (more than 1% difference)
                $difference = abs($invoice->amount - $expectedAmount);
                $percentDifference = ($expectedAmount > 0) ? ($difference / $expectedAmount) * 100 : 0;
                $discrepancy = $percentDifference > 1;
            }
            
            $invoicesWithData[] = [
                'invoice' => $invoice,
                'attendance' => $attendance,
                'expectedAmount' => $expectedAmount,
                'discrepancy' => $discrepancy
            ];
        }
        
        return view('invoices.pending-verification', compact('invoicesWithData'));
    }

    /**
     * Display invoices with discrepancies.
     */
    public function discrepancies()
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts() && !Auth::user()->isFounder()) {
            return redirect()->route('invoices.index')->with('error', 'You do not have permission to view invoice discrepancies.');
        }
        
        $invoices = Invoice::with(['vendor', 'submitter', 'verifier'])
            ->where('verification_status', 'discrepancy')
            ->orderBy('verified_at', 'desc')
            ->get();
            
        return view('invoices.discrepancies', compact('invoices'));
    }

    /**
     * Display monthly summary report of invoices.
     */
    public function summary()
    {
        // Access control check
        if (!Auth::user()->isAdmin() && !Auth::user()->isAccounts() && !Auth::user()->isFounder()) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view invoice summary.');
        }
        
        // Get selected month or default to current month
        $selectedMonth = request('month') ? Carbon::parse(request('month')) : Carbon::now()->startOfMonth();
        
        // Get all invoices for the selected month
        $invoices = Invoice::with(['vendor', 'verifier'])
            ->whereYear('month_year', $selectedMonth->year)
            ->whereMonth('month_year', $selectedMonth->month)
            ->get();
            
        // Calculate statistics
        $totalAmount = $invoices->sum('amount');
        $verifiedAmount = $invoices->where('verification_status', 'verified')->sum('amount');
        $discrepancyAmount = $invoices->where('verification_status', 'discrepancy')->sum('amount');
        $pendingAmount = $invoices->where('verification_status', 'pending')->sum('amount');
        
        $verifiedCount = $invoices->where('verification_status', 'verified')->count();
        $discrepancyCount = $invoices->where('verification_status', 'discrepancy')->count();
        $pendingCount = $invoices->where('verification_status', 'pending')->count();
        $totalCount = $invoices->count();
        
        // Generate months list for filter
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i)->startOfMonth();
            $months[$date->format('Y-m')] = $date->format('F Y');
        }
        
        return view('invoices.summary', compact(
            'invoices',
            'months',
            'selectedMonth',
            'totalAmount',
            'verifiedAmount',
            'discrepancyAmount',
            'pendingAmount',
            'verifiedCount',
            'discrepancyCount',
            'pendingCount',
            'totalCount'
        ));
    }

    /**
     * Helper function to calculate daily rate based on vendor experience level.
     */
    private function calculateDailyRate($vendor)
    {
        // Determine monthly budget based on experience level
        $monthlyRate = $vendor->budget_3_years; // Default to lowest experience level
        
        // In a real application, we would determine the experience level based on vendor data
        // For now, we'll just use the lowest level
        
        // Calculate daily rate (assuming 22 working days in a month)
        return $monthlyRate / 22;
    }

    /**
     * Helper function to create or update vendor payment record.
     */
    private function createOrUpdateVendorPayment($invoice)
    {
        // Get attendance record for the same month
        $attendance = VendorAttendance::where('vendor_id', $invoice->vendor_id)
            ->whereYear('month_year', Carbon::parse($invoice->month_year)->year)
            ->whereMonth('month_year', Carbon::parse($invoice->month_year)->month)
            ->first();
            
        if (!$attendance || $attendance->status !== 'approved') {
            // Cannot create payment without approved attendance
            return;
        }
        
        // Calculate payment amount
        $vendor = $invoice->vendor;
        $dailyRate = $this->calculateDailyRate($vendor);
        $payableDays = $attendance->present_days + $attendance->approved_leave_days;
        $calculatedAmount = $dailyRate * $payableDays;
        
        // Check for existing payment record
        $payment = VendorPayment::where('vendor_id', $invoice->vendor_id)
            ->whereYear('month_year', Carbon::parse($invoice->month_year)->year)
            ->whereMonth('month_year', Carbon::parse($invoice->month_year)->month)
            ->first();
            
        if ($payment) {
            // Update existing payment
            $payment->update([
                'invoice_id' => $invoice->id,
                'working_days' => $attendance->working_days,
                'present_days' => $attendance->present_days,
                'approved_leave_days' => $attendance->approved_leave_days,
                'daily_rate' => $dailyRate,
                'calculated_amount' => $calculatedAmount,
                'final_amount' => $invoice->amount,
                'payment_status' => 'pending',
            ]);
        } else {
            // Create new payment
            $payment = VendorPayment::create([
                'vendor_id' => $invoice->vendor_id,
                'invoice_id' => $invoice->id,
                'month_year' => $invoice->month_year,
                'working_days' => $attendance->working_days,
                'present_days' => $attendance->present_days,
                'approved_leave_days' => $attendance->approved_leave_days,
                'daily_rate' => $dailyRate,
                'calculated_amount' => $calculatedAmount,
                'deductions' => 0,
                'final_amount' => $invoice->amount,
                'payment_status' => 'pending',
            ]);
        }
        
        return $payment;
    }
}