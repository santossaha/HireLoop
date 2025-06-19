<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Onboarding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BillingController extends Controller
{
    /**
     * GST percentage to be deducted from monthly salary
     */
    const GST_PERCENTAGE = 18; // 18%

    public function index(Request $request)
    {
        $selectedMonth = $request->get('month', now()->format('Y-m'));
        $month = Carbon::parse($selectedMonth)->month;
        $year = Carbon::parse($selectedMonth)->year;

        $billings = Billing::where('month', $month)
            ->where('year', $year)
            ->orderBy('created_at', 'desc')
            ->get();

        $months = $this->getAvailableMonths();

        return view('billing.index', compact('billings', 'selectedMonth', 'months'));
    }

    public function show(Billing $billing)
    {
       
        return view('billing.show', compact('billing'));
    }

    public function approve(Billing $billing)
    {
        $billing->update([
            'status' => Billing::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by_name' => Auth::user()->name
        ]);

        return redirect()->back()->with('success', 'Billing approved successfully!');
    }

    public function reject(Request $request, Billing $billing)
    {
        $request->validate([
            'remarks' => 'required|string|max:500'
        ]);

        $billing->update([
            'status' => Billing::STATUS_REJECTED,
            'remarks' => $request->remarks
        ]);

        return redirect()->back()->with('success', 'Billing rejected successfully!');
    }

    public function markAsPaid(Billing $billing)
    {
        if ($billing->status !== Billing::STATUS_APPROVED) {
            return redirect()->back()->with('error', 'Only approved billings can be marked as paid!');
        }

        $billing->update([
            'status' => Billing::STATUS_PAID,
            'paid_at' => now(),
            'paid_by_name' => Auth::user()->name
        ]);

        return redirect()->back()->with('success', 'Billing marked as paid successfully!');
    }

    public function generateMonthlyBilling($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        // Get all active onboardings
        $onboardings = Onboarding::where('status','Running')
            ->whereNotNull('final_budget')
            ->with(['requirement', 'vendor', 'candidate'])
            ->get();

        $generatedCount = 0;

        foreach ($onboardings as $onboarding) {
            // Check if billing already exists for this month
            $existingBilling = Billing::where('onboarding_id', $onboarding->id)
                ->where('month', $month)
                ->where('year', $year)
                ->first();

            if ($existingBilling) {
                continue;
            }

            // Debug information
            Log::info("Processing onboarding ID: {$onboarding->id}");
            Log::info("Requirement ID: " . ($onboarding->requirement->requirement_id ?? 'NULL'));
            Log::info("Vendor Name: " . ($onboarding->vendor->name ?? 'NULL'));
            Log::info("Candidate Name: " . ($onboarding->candidate->candidate_name ?? 'NULL'));

            // Calculate billing details
            $billingData = $this->calculateBillingData($onboarding, $month, $year);

            if ($billingData) {
                Billing::create($billingData);
                $generatedCount++;
                
                Log::info("Created billing record for onboarding ID: {$onboarding->id}");
            }
        }

        return $generatedCount;
    }

    private function calculateBillingData($onboarding, $month, $year)
    {
        // Get month start and end dates
        $monthStart = Carbon::create($year, $month, 1)->startOfMonth();
        $monthEnd = $monthStart->copy()->endOfMonth();

        // Calculate working days (Monday to Friday)
        $totalWorkingDays = 0;
        $currentDate = $monthStart->copy();
        while ($currentDate <= $monthEnd) {
            if ($currentDate->dayOfWeek !== 0 && $currentDate->dayOfWeek !== 6) {
                $totalWorkingDays++;
            }
            $currentDate->addDay();
        }

        // Calculate effective working days for this onboarding
        $effectiveStartDate = $onboarding->start_date->isAfter($monthStart) 
            ? $onboarding->start_date 
            : $monthStart;
        $effectiveEndDate = $onboarding->end_date 
            ? min($onboarding->end_date, $monthEnd)
            : $monthEnd;

        // Calculate working days for this onboarding in the month
        $onboardingWorkingDays = 0;
        $currentDate = $effectiveStartDate->copy();
        while ($currentDate <= $effectiveEndDate) {
            if ($currentDate->dayOfWeek !== 0 && $currentDate->dayOfWeek !== 6) {
                $onboardingWorkingDays++;
            }
            $currentDate->addDay();
        }

        // Calculate leaves for this month
        $leaves = $onboarding->leaves()->sum('total_days');

        $netWorkingDays = max(0, $onboardingWorkingDays - $leaves);

        // Calculate per day salary based on actual working days in the month
        $perDaySalary = $onboarding->final_budget / ($totalWorkingDays > 0 ? $totalWorkingDays : 1);

        // Calculate monthly salary before GST
        $monthlySalaryWithoutGst = $netWorkingDays * $perDaySalary;

        // Calculate GST amount
        $gstAmount = ($monthlySalaryWithoutGst * self::GST_PERCENTAGE) / 100;

        // Calculate monthly salary after GST deduction
        $monthlySalary = $monthlySalaryWithoutGst - $gstAmount;

        return [
            'onboarding_id' => $onboarding->id,
            'final_budget' => $onboarding->final_budget,
            'start_date' => $onboarding->start_date,
            'end_date' => $onboarding->end_date,
            'requirement_id' => $onboarding->requirement_id ?? '',
            'vendor_name' => $onboarding->vendor->company_name ?? '',
            'vendor_email' => $onboarding->vendor->email ?? '',
            'vendor_phone' => $onboarding->vendor->phone ?? '',
            'candidate_name' => $onboarding->candidate_id ?? '',
            'candidate_email' => $onboarding->candidate->email ?? '',
            'candidate_phone' => $onboarding->candidate->phone ?? '',
            'month' => $month,
            'year' => $year,
            'total_working_days' => $totalWorkingDays,
            'total_leave_days' => $leaves,
            'net_working_days' => $netWorkingDays,
            'per_day_salary' => $perDaySalary,
            'monthly_salary_without_gst' => $monthlySalaryWithoutGst,
            'gst_percentage' => self::GST_PERCENTAGE,
            'gst_amount' => $gstAmount,
            'monthly_salary' => $monthlySalary,
            'status' => Billing::STATUS_PENDING
        ];
    }

    private function getAvailableMonths()
    {
        $months = [];
        $currentDate = Carbon::now();
        
        // Get last 12 months
        for ($i = 0; $i < 12; $i++) {
            $date = $currentDate->copy()->subMonths($i);
            $months[$date->format('Y-m')] = $date->format('F Y');
        }

        return $months;
    }

    public function export(Request $request)
    {
        $selectedMonth = $request->get('month', now()->format('Y-m'));
        $month = Carbon::parse($selectedMonth)->month;
        $year = Carbon::parse($selectedMonth)->year;

        $billings = Billing::where('month', $month)
            ->where('year', $year)
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = "billing_report_{$year}_{$month}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($billings) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Requirement ID',
                'Vendor Name',
                'Vendor Email',
                'Candidate Name',
                'Candidate Email',
                'Month',
                'Year',
                'Total Working Days',
                'Total Leave Days',
                'Net Working Days',
                'Per Day Salary',
                'Monthly Salary',
                'Status'
            ]);

            foreach ($billings as $billing) {
                fputcsv($file, [
                    $billing->requirement_id,
                    $billing->vendor_name,
                    $billing->vendor_email,
                    $billing->candidate_name,
                    $billing->candidate_email,
                    $billing->month_name,
                    $billing->year,
                    $billing->total_working_days,
                    $billing->total_leave_days,
                    $billing->net_working_days,
                    $billing->per_day_salary,
                    $billing->monthly_salary,
                    ucfirst($billing->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
} 