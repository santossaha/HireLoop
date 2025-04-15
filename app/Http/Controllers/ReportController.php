<?php

namespace App\Http\Controllers;

use App\Models\ClientPayment;
use App\Models\VendorPayment;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the payment history report
     */
    public function paymentHistory(Request $request)
    {
        $query = VendorPayment::query();
        
        // Filter by vendor if provided
        if ($request->has('vendor_id') && !empty($request->vendor_id)) {
            $query->where('vendor_id', $request->vendor_id);
        }
        
        // Filter by date range if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            if (!empty($startDate) && !empty($endDate)) {
                $query->whereBetween('month_year', [$startDate, $endDate]);
            }
        }
        
        // Filter by payment status if provided
        if ($request->has('payment_status') && !empty($request->payment_status)) {
            $query->where('payment_status', $request->payment_status);
        }
        
        $payments = $query->with(['vendor', 'invoice', 'approvedBy', 'paidBy'])
                        ->orderBy('month_year', 'desc')
                        ->paginate(15);
        
        // Get vendors for the dropdown
        $vendors = Vendor::all();
        
        // Calculate totals
        $totalPaid = VendorPayment::where('payment_status', 'paid')->sum('final_amount');
        $totalPending = VendorPayment::where('payment_status', 'pending')->sum('final_amount');
        
        return view('report.payment-history', compact('payments', 'vendors', 'totalPaid', 'totalPending'));
    }

    /**
     * Display the monthly report
     */
    public function monthlyReport(Request $request)
    {
        // Get the month/year from request or use current month
        $month = $request->has('month') ? $request->month : now()->month;
        $year = $request->has('year') ? $request->year : now()->year;
        
        $date = Carbon::createFromDate($year, $month, 1);
        
        // Get client payments for the month
        $clientPayments = ClientPayment::whereYear('payment_date', $year)
                                     ->whereMonth('payment_date', $month)
                                     ->get();
        
        // Get vendor payments for the month
        $vendorPayments = VendorPayment::whereYear('month_year', $year)
                                     ->whereMonth('month_year', $month)
                                     ->with('vendor')
                                     ->get();
        
        // Calculate totals
        $totalClientPayments = $clientPayments->where('payment_status', 'received')->sum('payment_amount');
        $totalVendorPayments = $vendorPayments->where('payment_status', 'paid')->sum('final_amount');
        $pendingClientPayments = $clientPayments->where('payment_status', 'pending')->sum('payment_amount');
        $pendingVendorPayments = $vendorPayments->where('payment_status', 'pending')->sum('final_amount');
        
        // Group vendor payments by department
        $departmentPayments = [];
        foreach ($vendorPayments as $payment) {
            $vendor = $payment->vendor;
            $internalPoc = $vendor->internalPoc;
            
            if ($internalPoc && $internalPoc->department) {
                $departmentName = $internalPoc->department->name;
                
                if (!isset($departmentPayments[$departmentName])) {
                    $departmentPayments[$departmentName] = 0;
                }
                
                if ($payment->payment_status === 'paid') {
                    $departmentPayments[$departmentName] += $payment->final_amount;
                }
            }
        }
        
        return view('report.monthly-report', compact(
            'date', 
            'clientPayments', 
            'vendorPayments', 
            'totalClientPayments', 
            'totalVendorPayments', 
            'pendingClientPayments', 
            'pendingVendorPayments', 
            'departmentPayments'
        ));
    }

    /**
     * Generate and download a report
     */
    public function generateReport(Request $request, $type)
    {
        // Get the month/year from request or use current month
        $month = $request->has('month') ? $request->month : now()->month;
        $year = $request->has('year') ? $request->year : now()->year;
        
        $date = Carbon::createFromDate($year, $month, 1);
        
        if ($type === 'payment-summary') {
            // Generate payment summary report
            $vendors = Vendor::with(['attendances' => function($query) use ($month, $year) {
                                $query->whereYear('month_year', $year)
                                      ->whereMonth('month_year', $month);
                             }, 'payments' => function($query) use ($month, $year) {
                                $query->whereYear('month_year', $year)
                                      ->whereMonth('month_year', $month);
                             }])
                           ->get();
            
            $data = [];
            
            foreach ($vendors as $vendor) {
                $attendance = $vendor->attendances->first();
                $payment = $vendor->payments->first();
                
                if ($attendance || $payment) {
                    $data[] = [
                        'vendor_name' => $vendor->company_name,
                        'internal_poc' => $vendor->internalPoc ? $vendor->internalPoc->name : 'N/A',
                        'working_days' => $attendance ? $attendance->working_days : 'N/A',
                        'present_days' => $attendance ? $attendance->present_days : 'N/A',
                        'approved_leave' => $attendance ? $attendance->approved_leave_days : 'N/A',
                        'amount' => $payment ? $payment->final_amount : 'N/A',
                        'status' => $payment ? $payment->payment_status : 'No payment record',
                    ];
                }
            }
            
            // For simplicity, we'll return a view with this data
            // In a real application, you might want to generate a PDF or Excel file
            return view('report.payment-summary', [
                'data' => $data,
                'month' => $date->format('F'),
                'year' => $year
            ]);
        } elseif ($type === 'client-payment') {
            // Generate client payment report
            $clientPayments = ClientPayment::whereYear('payment_date', $year)
                                         ->whereMonth('payment_date', $month)
                                         ->orderBy('client_name')
                                         ->get();
            
            // For simplicity, we'll return a view with this data
            return view('report.client-payment', [
                'clientPayments' => $clientPayments,
                'month' => $date->format('F'),
                'year' => $year
            ]);
        } elseif ($type === 'vendor-payment') {
            // Generate vendor payment report
            $vendorPayments = VendorPayment::whereYear('month_year', $year)
                                         ->whereMonth('month_year', $month)
                                         ->with('vendor')
                                         ->orderBy('vendor_id')
                                         ->get();
            
            // For simplicity, we'll return a view with this data
            return view('report.vendor-payment', [
                'vendorPayments' => $vendorPayments,
                'month' => $date->format('F'),
                'year' => $year
            ]);
        } else {
            return redirect()->back()->with('error', 'Invalid report type specified.');
        }
    }
}
