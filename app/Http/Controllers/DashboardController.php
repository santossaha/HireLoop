<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        
        // Prepare dashboard data based on user role
        $stats = $this->getDashboardStats($user);
        $pendingTasks = $this->getPendingTasks($user);
        $recentActivities = $this->getRecentActivities($user);
        
        return view('dashboard.index', [
            'stats' => $stats,
            'pendingTasks' => $pendingTasks,
            'recentActivities' => $recentActivities
        ]);
    }
    
    /**
     * Get dashboard statistics based on user role
     */
    private function getDashboardStats($user)
    {
        $stats = [
            'totalVendors' => \App\Models\Vendor::count(),
            'totalClientReady' => \App\Models\Vendor::where('client_ready', true)->count(),
        ];
        
        if ($user->isAdmin() || $user->isFounder()) {
            $stats['pendingFounderApproval'] = \App\Models\VendorPayment::pendingApproval()->count();
            $stats['pendingVendorPayments'] = \App\Models\VendorPayment::pendingApproval()->count();
            $stats['approvedPayments'] = \App\Models\VendorPayment::approved()->count();
            $stats['totalPaidToVendors'] = \App\Models\VendorPayment::paid()->count();
            $stats['monthlyRevenue'] = \App\Models\ClientPayment::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('payment_amount');
        }
        
        if ($user->isHod()) {
            $departmentId = $user->department_id;
            $stats['pendingHodApproval'] = 0; // Add your query here
            $stats['departmentVendors'] = $user->department ? \App\Models\Vendor::where('department_id', $user->department_id)->count() : 0;
        }
        
        if ($user->isPoc()) {
            $stats['myVendors'] = \App\Models\Vendor::where('internal_poc_id', $user->id)->count();
            $stats['pendingAttendance'] = \App\Models\VendorAttendance::whereHas('vendor', function ($query) use ($user) {
                $query->where('internal_poc_id', $user->id);
            })->where('status', 'pending')->count();
        }
        
        return $stats;
    }
    
    /**
     * Get pending tasks based on user role
     */
    private function getPendingTasks($user)
    {
        $tasks = [];
        
        if ($user->isAdmin() || $user->isFounder()) {
            $pendingPayments = \App\Models\VendorPayment::pendingApproval()->count();
            if ($pendingPayments > 0) {
                $tasks[] = [
                    'message' => 'Vendor payments pending approval',
                    'count' => $pendingPayments,
                    'url' => route('vendor-payments.approval-dashboard')
                ];
            }
        }
        
        if ($user->isAccounts()) {
            $approvedPayments = \App\Models\VendorPayment::approved()->count();
            if ($approvedPayments > 0) {
                $tasks[] = [
                    'message' => 'Approved payments ready for processing',
                    'count' => $approvedPayments,
                    'url' => route('vendor-payments.processing-dashboard')
                ];
            }
        }
        
        if ($user->isPoc()) {
            $pendingAttendance = \App\Models\VendorAttendance::whereHas('vendor', function ($query) use ($user) {
                $query->where('internal_poc_id', $user->id);
            })->where('status', 'pending')->count();
            
            if ($pendingAttendance > 0) {
                $tasks[] = [
                    'message' => 'Vendor attendance records to verify',
                    'count' => $pendingAttendance,
                    'url' => route('vendor-attendances.index')
                ];
            }
        }
        
        return $tasks;
    }
    
    /**
     * Get recent activities
     */
    private function getRecentActivities($user)
    {
        $activities = [];
        
        // This would normally be populated from an activity log
        // For demonstration, we'll add some placeholder activities based on role
        
        if ($user->isAdmin() || $user->isFounder() || $user->isAccounts()) {
            $recentPayments = \App\Models\VendorPayment::with('vendor')
                ->latest()
                ->take(5)
                ->get();
                
            foreach ($recentPayments as $payment) {
                $message = '';
                switch ($payment->payment_status) {
                    case 'paid':
                        $message = "Payment of {$payment->amount} made to {$payment->vendor->company_name}";
                        break;
                    case 'approved':
                        $message = "Payment of {$payment->amount} approved for {$payment->vendor->company_name}";
                        break;
                    case 'pending_approval':
                        $message = "Payment of {$payment->amount} pending approval for {$payment->vendor->company_name}";
                        break;
                }
                
                if ($message) {
                    $activities[] = [
                        'message' => $message,
                        'date' => $payment->updated_at
                    ];
                }
            }
        }
        
        return $activities;
    }
}