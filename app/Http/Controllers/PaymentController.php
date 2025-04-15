<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Invoice;
use App\Models\ClientPayment;
use App\Models\VendorPayment;
use App\Models\VendorAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Notifications\PaymentStatusNotification;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of the vendor payments
     */
    public function index(Request $request)
    {
        $query = VendorPayment::query();
        
        // Filter by payment status if provided
        if ($request->has('payment_status') && !empty($request->payment_status)) {
            $query->where('payment_status', $request->payment_status);
        }
        
        // Filter by month/year if provided
        if ($request->has('month') && $request->has('year')) {
            $month = $request->month;
            $year = $request->year;
            
            if (!empty($month) && !empty($year)) {
                $query->whereYear('month_year', $year)
                      ->whereMonth('month_year', $month);
            }
        }
        
        // Filter by vendor if provided
        if ($request->has('vendor_id') && !empty($request->vendor_id)) {
            $query->where('vendor_id', $request->vendor_id);
        }
        
        $vendorPayments = $query->with(['vendor', 'invoice', 'clientPayment', 'approvedBy', 'paidBy'])
                               ->orderBy('created_at', 'desc')
                               ->paginate(10);
        
        // Get vendors for the dropdown
        $vendors = Vendor::all();
        
        // Get stats for the dashboard
        $pendingApproval = VendorPayment::where('payment_status', 'pending')->count();
        $approvedPayments = VendorPayment::where('payment_status', 'approved')->count();
        $paidPayments = VendorPayment::where('payment_status', 'paid')->count();
        
        // Get client payment status
        $pendingClientPayments = ClientPayment::where('payment_status', 'pending')->count();
        
        return view('payment.vendor-payment', compact('vendorPayments', 'vendors', 'pendingApproval', 'approvedPayments', 'paidPayments', 'pendingClientPayments'));
    }

    /**
     * Calculate payment for a vendor based on attendance
     */
    public function calculate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vendor_id' => 'required|exists:vendors,id',
            'month_year' => 'required|date',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $vendorId = $request->vendor_id;
        $monthYear = Carbon::parse($request->month_year);
        
        // Check if attendance has been approved
        $attendance = VendorAttendance::where('vendor_id', $vendorId)
                                    ->whereYear('month_year', $monthYear->year)
                                    ->whereMonth('month_year', $monthYear->month)
                                    ->where('status', 'approved')
                                    ->first();
                                    
        if (!$attendance) {
            return back()->with('error', 'No approved attendance record found for this month.');
        }
        
        // Check if invoice has been verified
        $invoice = Invoice::where('vendor_id', $vendorId)
                        ->whereYear('month_year', $monthYear->year)
                        ->whereMonth('month_year', $monthYear->month)
                        ->where('verification_status', 'verified')
                        ->first();
                        
        if (!$invoice) {
            return back()->with('error', 'No verified invoice found for this month.');
        }
        
        // Check if client payment has been received
        $clientPayment = ClientPayment::where('payment_status', 'received')
                                    ->whereDate('payment_date', '<=', now())
                                    ->latest()
                                    ->first();
                                    
        if (!$clientPayment) {
            return back()->with('error', 'No confirmed client payment found. Cannot process vendor payment without client payment confirmation.');
        }
        
        // Calculate payment
        $paymentData = VendorPayment::calculatePayment($vendorId, $monthYear);
        
        if (!$paymentData) {
            return back()->with('error', 'Unable to calculate payment amount. Please check vendor requirements and attendance.');
        }
        
        // Check if payment already exists
        $existingPayment = VendorPayment::where('vendor_id', $vendorId)
                                      ->whereYear('month_year', $monthYear->year)
                                      ->whereMonth('month_year', $monthYear->month)
                                      ->first();
                                      
        if ($existingPayment) {
            return redirect()->route('vendor.payments.index')
                ->with('info', 'Payment record already exists for this vendor and month.');
        }
        
        // Create payment record
        $payment = VendorPayment::create([
            'vendor_id' => $vendorId,
            'invoice_id' => $invoice->id,
            'client_payment_id' => $clientPayment->id,
            'month_year' => $monthYear,
            'working_days' => $paymentData['working_days'],
            'present_days' => $paymentData['present_days'],
            'approved_leave_days' => $paymentData['approved_leave_days'],
            'daily_rate' => $paymentData['daily_rate'],
            'calculated_amount' => $paymentData['calculated_amount'],
            'deductions' => $paymentData['deductions'],
            'final_amount' => $paymentData['final_amount'],
            'payment_status' => 'pending',
        ]);
        
        // Update the invoice payment status
        $invoice->payment_status = 'approved';
        $invoice->save();
        
        // Notify founders for payment approval
        $founders = User::where('role', 'founder')->get();
        
        foreach ($founders as $founder) {
            $founder->notify(new PaymentStatusNotification(
                'Vendor Payment Ready for Approval',
                "A payment for " . $payment->vendor->company_name . " has been calculated and is ready for your approval."
            ));
        }
        
        return redirect()->route('vendor.payments.index')
            ->with('success', 'Payment calculated successfully and is pending approval.');
    }

    /**
     * Approve a vendor payment
     */
    public function approve(Request $request, VendorPayment $payment)
    {
        // Check if user is a founder
        if (!Auth::user()->isFounder()) {
            return redirect()->route('vendor.payments.index')
                ->with('error', 'You are not authorized to approve payments.');
        }

        // Verify client payment is received
        if (!$payment->clientPayment || !$payment->clientPayment->isReceived()) {
            return redirect()->route('vendor.payments.index')
                ->with('error', 'Cannot approve payment. Client payment has not been received yet.');
        }

        // Approve the payment
        $payment->approve(Auth::id());
        
        // Notify accounts team to process the payment
        $accountsTeam = User::where('role', 'accounts')->get();
        
        foreach ($accountsTeam as $user) {
            $user->notify(new PaymentStatusNotification(
                'Payment Approved - Ready for Processing',
                "A payment for " . $payment->vendor->company_name . " has been approved and is ready to be processed."
            ));
        }
        
        return redirect()->route('vendor.payments.index')
            ->with('success', 'Payment approved successfully.');
    }

    /**
     * Mark a payment as paid
     */
    public function markAsPaid(Request $request, VendorPayment $payment)
    {
        // Check if user is from accounts team or admin
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('accounts')) {
            return redirect()->route('vendor.payments.index')
                ->with('error', 'You are not authorized to mark payments as paid.');
        }

        $validator = Validator::make($request->all(), [
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Mark as paid
        $payment->payment_date = $request->payment_date;
        $payment->notes = $request->notes;
        $payment->markAsPaid(Auth::id());
        
        // Notify the vendor's POC
        $poc = $payment->vendor->internalPoc;
        
        if ($poc) {
            $poc->notify(new PaymentStatusNotification(
                'Vendor Payment Processed',
                "The payment for " . $payment->vendor->company_name . " for " . $payment->month_year->format('F Y') . " has been processed."
            ));
        }
        
        return redirect()->route('vendor.payments.index')
            ->with('success', 'Payment marked as paid successfully.');
    }
}
