<?php

namespace App\Http\Controllers;

use App\Models\ClientPayment;
use App\Models\Invoice;
use App\Models\Vendor;
use App\Models\VendorAttendance;
use App\Models\VendorPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VendorPaymentsExport;

class VendorPaymentController extends Controller
{
    /**
     * Display a listing of vendor payments.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()->can('view-vendor-payments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view vendor payments.');
        }

        $query = VendorPayment::with(['vendor', 'invoice', 'clientPayment', 'creator', 'approver', 'payer']);

        // Filter payments based on user role
        if (Auth::user()->isFounder()) {
            $query->where('payment_status', '!=', 'draft');
        } elseif (Auth::user()->isAccounts()) {
            $query->where('payment_status', 'approved');
        } elseif (Auth::user()->isPoc()) {
            $query->whereHas('vendor', function ($q) {
                $q->where('internal_poc_id', Auth::id());
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('vendor-payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new vendor payment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!Auth::user()->can('create-vendor-payments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to create vendor payments.');
        }

        $vendors = Vendor::orderBy('company_name')->get();
        $invoices = Invoice::where('status', 'verified')->get();
        $clientPayments = ClientPayment::where('status', 'received')->get();

        return view('vendor-payments.create', compact('vendors', 'invoices', 'clientPayments'));
    }

    /**
     * Store a newly created vendor payment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!Auth::user()->can('create-vendor-payments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to create vendor payments.');
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:bank_transfer,paypal,wise,payoneer,other',
            'invoice_id' => 'nullable|exists:invoices,id',
            'client_payment_id' => 'nullable|exists:client_payments,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Check if invoice exists and is not already used
            if ($request->invoice_id) {
                $invoice = Invoice::find($request->invoice_id);
                if ($invoice && $invoice->vendor_payment_id) {
                    throw new \Exception('Invoice is already associated with another payment.');
                }
            }

            // Create the payment
            $payment = new VendorPayment();
            $payment->vendor_id = $request->vendor_id;
            $payment->amount = $request->amount;
            $payment->currency = $request->currency;
            $payment->payment_date = $request->payment_date;
            $payment->payment_method = $request->payment_method;
            $payment->invoice_id = $request->invoice_id;
            $payment->client_payment_id = $request->client_payment_id;
            $payment->notes = $request->notes;
            $payment->created_by = Auth::id();
            $payment->payment_status = Auth::user()->isAdmin() || Auth::user()->isAccounts() ? 'pending_approval' : 'draft';
            $payment->save();

            // Update invoice if provided
            if ($request->invoice_id && $invoice) {
                $invoice->vendor_payment_id = $payment->id;
                $invoice->save();
            }

            DB::commit();

            return redirect()->route('vendor-payments.index')->with('success', 'Vendor payment created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error creating payment: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified vendor payment.
     *
     * @param  \App\Models\VendorPayment  $vendorPayment
     * @return \Illuminate\Http\Response
     */
    public function show(VendorPayment $vendorPayment)
    {
        if (!Auth::user()->can('view-vendor-payments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view vendor payments.');
        }

        $vendorPayment->load(['vendor', 'invoice', 'clientPayment', 'creator', 'approver', 'payer']);

        return view('vendor-payments.show', compact('vendorPayment'));
    }

    /**
     * Show the form for editing the specified vendor payment.
     *
     * @param  \App\Models\VendorPayment  $vendorPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(VendorPayment $vendorPayment)
    {
        if (!Auth::user()->can('edit-vendor-payments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to edit vendor payments.');
        }

        // Prevent editing of payments that are already approved or paid
        if ($vendorPayment->payment_status === 'approved' || $vendorPayment->payment_status === 'paid') {
            return redirect()->route('vendor-payments.show', $vendorPayment)->with('error', 'Cannot edit a payment that has been approved or paid.');
        }

        $vendors = Vendor::orderBy('company_name')->get();
        $invoices = Invoice::where('status', 'verified')
            ->where(function ($query) use ($vendorPayment) {
                $query->whereNull('vendor_payment_id')
                    ->orWhere('vendor_payment_id', $vendorPayment->id);
            })->get();
        $clientPayments = ClientPayment::where('status', 'received')->get();

        return view('vendor-payments.edit', compact('vendorPayment', 'vendors', 'invoices', 'clientPayments'));
    }

    /**
     * Update the specified vendor payment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VendorPayment  $vendorPayment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, VendorPayment $vendorPayment)
    {
        if (!Auth::user()->can('edit-vendor-payments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to edit vendor payments.');
        }

        // Prevent editing of payments that are already approved or paid
        if ($vendorPayment->payment_status === 'approved' || $vendorPayment->payment_status === 'paid') {
            return redirect()->route('vendor-payments.show', $vendorPayment)->with('error', 'Cannot edit a payment that has been approved or paid.');
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:bank_transfer,paypal,wise,payoneer,other',
            'invoice_id' => 'nullable|exists:invoices,id',
            'client_payment_id' => 'nullable|exists:client_payments,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // If invoice has changed, update the old one and check the new one
            if ($vendorPayment->invoice_id != $request->invoice_id) {
                // Clear vendor_payment_id from the old invoice
                if ($vendorPayment->invoice_id) {
                    $oldInvoice = Invoice::find($vendorPayment->invoice_id);
                    if ($oldInvoice) {
                        $oldInvoice->vendor_payment_id = null;
                        $oldInvoice->save();
                    }
                }

                // Check if new invoice is available
                if ($request->invoice_id) {
                    $newInvoice = Invoice::find($request->invoice_id);
                    if ($newInvoice && $newInvoice->vendor_payment_id) {
                        throw new \Exception('New invoice is already associated with another payment.');
                    }
                }
            }

            // Update the payment
            $vendorPayment->vendor_id = $request->vendor_id;
            $vendorPayment->amount = $request->amount;
            $vendorPayment->currency = $request->currency;
            $vendorPayment->payment_date = $request->payment_date;
            $vendorPayment->payment_method = $request->payment_method;
            $vendorPayment->invoice_id = $request->invoice_id;
            $vendorPayment->client_payment_id = $request->client_payment_id;
            $vendorPayment->notes = $request->notes;
            $vendorPayment->updated_at = now();
            $vendorPayment->save();

            // Update new invoice if provided
            if ($request->invoice_id) {
                $newInvoice = Invoice::find($request->invoice_id);
                if ($newInvoice) {
                    $newInvoice->vendor_payment_id = $vendorPayment->id;
                    $newInvoice->save();
                }
            }

            DB::commit();

            return redirect()->route('vendor-payments.show', $vendorPayment)->with('success', 'Vendor payment updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error updating payment: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Approve a vendor payment.
     *
     * @param  \App\Models\VendorPayment  $vendorPayment
     * @return \Illuminate\Http\Response
     */
    public function approve(Request $request, VendorPayment $vendorPayment)
    {
        if (!Auth::user()->can('approve-vendor-payments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to approve vendor payments.');
        }

        // Only pending payments can be approved
        if ($vendorPayment->payment_status !== 'pending_approval') {
            return redirect()->route('vendor-payments.show', $vendorPayment)
                ->with('error', 'Only pending payments can be approved.');
        }

        try {
            DB::beginTransaction();

            $vendorPayment->payment_status = 'approved';
            $vendorPayment->approved_by = Auth::id();
            $vendorPayment->approved_at = now();
            $vendorPayment->approval_notes = $request->approval_notes;
            $vendorPayment->save();

            DB::commit();

            return redirect()->route('vendor-payments.approval-dashboard')
                ->with('success', 'Vendor payment approved successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error approving payment: ' . $e->getMessage());
        }
    }

    /**
     * Reject a vendor payment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VendorPayment  $vendorPayment
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, VendorPayment $vendorPayment)
    {
        if (!Auth::user()->can('approve-vendor-payments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to reject vendor payments.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        // Only pending payments can be rejected
        if ($vendorPayment->payment_status !== 'pending_approval') {
            return redirect()->route('vendor-payments.show', $vendorPayment)
                ->with('error', 'Only pending payments can be rejected.');
        }

        try {
            DB::beginTransaction();

            $vendorPayment->payment_status = 'rejected';
            $vendorPayment->rejected_by = Auth::id();
            $vendorPayment->rejected_at = now();
            $vendorPayment->rejection_reason = $request->rejection_reason;
            $vendorPayment->save();

            // If there was an invoice, release it
            if ($vendorPayment->invoice_id) {
                $invoice = Invoice::find($vendorPayment->invoice_id);
                if ($invoice) {
                    $invoice->vendor_payment_id = null;
                    $invoice->save();
                }
            }

            DB::commit();

            return redirect()->route('vendor-payments.approval-dashboard')
                ->with('success', 'Vendor payment rejected successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error rejecting payment: ' . $e->getMessage());
        }
    }

    /**
     * Mark a vendor payment as paid.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VendorPayment  $vendorPayment
     * @return \Illuminate\Http\Response
     */
    public function markAsPaid(Request $request, VendorPayment $vendorPayment)
    {
        if (!Auth::user()->can('mark-vendor-payments-paid')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to mark payments as paid.');
        }

        // Only approved payments can be marked as paid
        if ($vendorPayment->payment_status !== 'approved') {
            return redirect()->route('vendor-payments.show', $vendorPayment)
                ->with('error', 'Only approved payments can be marked as paid.');
        }

        try {
            DB::beginTransaction();

            $vendorPayment->payment_status = 'paid';
            $vendorPayment->paid_by = Auth::id();
            $vendorPayment->paid_at = now();
            $vendorPayment->transaction_reference = $request->transaction_reference;
            $vendorPayment->payment_notes = $request->payment_notes;
            $vendorPayment->save();

            // Update the invoice status if present
            if ($vendorPayment->invoice_id) {
                $invoice = Invoice::find($vendorPayment->invoice_id);
                if ($invoice) {
                    $invoice->status = 'paid';
                    $invoice->save();
                }
            }

            DB::commit();

            return redirect()->route('vendor-payments.processing-dashboard')
                ->with('success', 'Vendor payment marked as paid successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error marking payment as paid: ' . $e->getMessage());
        }
    }

    /**
     * Generate vendor payment records based on attendance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateFromAttendance(Request $request)
    {
        if (!Auth::user()->can('create-vendor-payments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to generate vendor payments.');
        }

        $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2030',
            'rate_per_day' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'payment_method' => 'required|string|in:bank_transfer,paypal,wise,payoneer,other',
            'invoice_id' => 'nullable|exists:invoices,id',
            'client_payment_id' => 'nullable|exists:client_payments,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Get vendor
            $vendor = Vendor::findOrFail($request->vendor_id);

            // Get attendance records for the month
            $startDate = Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
            $endDate = $startDate->copy()->endOfMonth();

            $attendanceRecords = VendorAttendance::where('vendor_id', $vendor->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            // Calculate days worked
            $daysWorked = $attendanceRecords->where('payment_status', 'present')->count();
            $daysHalfDay = $attendanceRecords->where('payment_status', 'half_day')->count();
            $totalDays = $daysWorked + ($daysHalfDay / 2);

            // Calculate payment amount
            $amount = $totalDays * $request->rate_per_day;

            // Check if invoice exists and is available
            if ($request->invoice_id) {
                $invoice = Invoice::find($request->invoice_id);
                if ($invoice && $invoice->vendor_payment_id) {
                    throw new \Exception('Invoice is already associated with another payment.');
                }
            }

            // Create payment record
            $payment = new VendorPayment();
            $payment->vendor_id = $vendor->id;
            $payment->amount = $amount;
            $payment->currency = $request->currency;
            $payment->payment_date = now()->format('Y-m-d');
            $payment->payment_method = $request->payment_method;
            $payment->invoice_id = $request->invoice_id;
            $payment->client_payment_id = $request->client_payment_id;
            $payment->notes = $request->notes . "\n\nGenerated from attendance records: " .
                "$daysWorked full days, $daysHalfDay half days. Total: $totalDays days * {$request->rate_per_day} {$request->currency} = $amount {$request->currency}";
            $payment->created_by = Auth::id();
            $payment->payment_status = 'pending_approval';
            $payment->save();

            // Update invoice if provided
            if ($request->invoice_id && $invoice) {
                $invoice->vendor_payment_id = $payment->id;
                $invoice->save();
            }

            DB::commit();

            return redirect()->route('vendor-payments.index')->with('success', 
                "Payment generated successfully based on attendance. Days worked: $totalDays, Amount: $amount {$request->currency}");
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error generating payment: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the payment approval dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function approvalDashboard()
    {
        if (!Auth::user()->can('approve-vendor-payments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access the approval dashboard.');
        }

        $pendingPayments = VendorPayment::with(['vendor', 'invoice', 'clientPayment', 'creator'])
            ->where('payment_status', 'pending_approval')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('vendor-payments.approval-dashboard', compact('pendingPayments'));
    }

    /**
     * Display the payment processing dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function processingDashboard()
    {
        if (!Auth::user()->can('mark-vendor-payments-paid')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access the payment processing dashboard.');
        }

        $approvedPayments = VendorPayment::with(['vendor', 'invoice', 'clientPayment', 'approver'])
            ->where('payment_status', 'approved')
            ->orderBy('approved_at', 'asc')
            ->get();

        return view('vendor-payments.payment-dashboard', compact('approvedPayments'));
    }

    /**
     * Display the monthly payment report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function monthlyReport(Request $request)
    {
        if (!Auth::user()->can('view-vendor-payment-reports')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view payment reports.');
        }

        // Get month and year from request or use current month
        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        // Define month names and years for dropdown
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        
        // Get last 5 years for dropdown
        $currentYear = date('Y');
        $years = range($currentYear - 4, $currentYear);

        // Get start and end dates for the selected month
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Get payments for the month (only paid ones)
        $payments = VendorPayment::with(['vendor', 'invoice', 'clientPayment', 'payer'])
            ->where('payment_status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->orderBy('paid_at', 'desc')
            ->get();

        // Calculate vendor summary
        $vendorSummary = [];
        $totalAmount = 0;

        foreach ($payments as $payment) {
            $vendorId = $payment->vendor_id;
            
            if (!isset($vendorSummary[$vendorId])) {
                $vendorSummary[$vendorId] = [
                    'vendor' => $payment->vendor,
                    'count' => 0,
                    'total' => 0
                ];
            }
            
            $vendorSummary[$vendorId]['count']++;
            $vendorSummary[$vendorId]['total'] += $payment->amount;
            $totalAmount += $payment->amount;
        }

        return view('vendor-payments.monthly-report', compact(
            'payments',
            'vendorSummary',
            'totalAmount',
            'month',
            'year',
            'months',
            'years'
        ));
    }

    /**
     * Export monthly payment data to Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        if (!Auth::user()->can('export-vendor-payments')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to export payment data.');
        }

        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));
        $monthName = date('F', mktime(0, 0, 0, $month, 1));

        $fileName = "vendor_payments_{$monthName}_{$year}.xlsx";

        return Excel::download(new VendorPaymentsExport($month, $year), $fileName);
    }

    /**
     * Get payment details for a specific vendor (AJAX endpoint).
     *
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function getByVendor(Vendor $vendor)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $payments = VendorPayment::with(['invoice', 'clientPayment'])
            ->where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return response()->json([
            'payments' => $payments,
            'vendor' => $vendor->only(['id', 'company_name', 'email', 'payment_method', 'payment_details'])
        ]);
    }

    /**
     * Get details for a specific client payment (AJAX endpoint).
     *
     * @param  \App\Models\ClientPayment  $clientPayment
     * @return \Illuminate\Http\Response
     */
    public function getClientPaymentDetails(ClientPayment $clientPayment)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json($clientPayment);
    }

    /**
     * Get attendance records for a vendor in a specific month (AJAX endpoint).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendor  $vendor
     * @return \Illuminate\Http\Response
     */
    public function getVendorAttendance(Request $request, Vendor $vendor)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $month = $request->input('month', date('n'));
        $year = $request->input('year', date('Y'));

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $attendance = VendorAttendance::where('vendor_id', $vendor->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        // Calculate attendance stats
        $daysPresent = $attendance->where('status', 'present')->count();
        $daysHalfDay = $attendance->where('status', 'half_day')->count();
        $daysAbsent = $attendance->where('status', 'absent')->count();
        $daysLeave = $attendance->where('status', 'leave')->count();
        $totalDays = $daysPresent + ($daysHalfDay / 2);

        return response()->json([
            'attendance' => $attendance,
            'stats' => [
                'days_present' => $daysPresent,
                'days_half_day' => $daysHalfDay,
                'days_absent' => $daysAbsent,
                'days_leave' => $daysLeave,
                'total_days' => $totalDays
            ]
        ]);
    }
}