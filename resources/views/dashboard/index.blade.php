@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Dashboard</h1>
        <div>
            <span class="badge bg-secondary">{{ now()->format('F Y') }}</span>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Vendors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['totalVendors'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Client Ready</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['totalClientReady'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->isAdmin() || auth()->user()->isFounder())
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pending Approvals</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pendingFounderApproval'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pendingVendorPayments'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->isHod())
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pending HOD Approvals</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pendingHodApproval'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Department Vendors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['departmentVendors'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->isPoc())
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                My Vendors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['myVendors'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Attendance</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pendingAttendance'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="row">
        <!-- Pending Tasks Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Pending Tasks</h6>
                </div>
                <div class="card-body">
                    @if(count($pendingTasks) > 0)
                        <ul class="list-group">
                            @foreach($pendingTasks as $task)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $task['message'] }}
                                    <span class="badge bg-primary rounded-pill">{{ $task['count'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-center">No pending tasks at the moment.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activities Card -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activities</h6>
                </div>
                <div class="card-body">
                    @if(count($recentActivities) > 0)
                        <div class="timeline">
                            @foreach($recentActivities as $activity)
                                <div class="timeline-item">
                                    <div class="timeline-item-marker">
                                        <div class="timeline-item-marker-indicator bg-primary">
                                            <i class="fas fa-circle text-white"></i>
                                        </div>
                                    </div>
                                    <div class="timeline-item-content">
                                        <p class="fw-bold mb-2">{{ $activity['message'] }}</p>
                                        <p class="text-muted small">
                                            {{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center">No recent activities.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->isAdmin() || auth()->user()->isFounder())
    <!-- Revenue & Payment Charts -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Overview</h6>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Vendor Payments</h6>
                </div>
                <div class="card-body">
                    <canvas id="paymentStatusChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
@if(auth()->user()->isAdmin() || auth()->user()->isFounder())
<script>
    // Revenue Chart
    var ctx = document.getElementById('revenueChart').getContext('2d');
    var revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Client Revenue',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointRadius: 3,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointHoverRadius: 5,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, {{ $stats['monthlyRevenue'] ?? 0 }}],
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Payment Status Chart
    var ctx2 = document.getElementById('paymentStatusChart').getContext('2d');
    var paymentStatusChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Paid', 'Pending', 'Approved'],
            datasets: [{
                data: [
                    {{ $stats['totalPaidToVendors'] ?? 0 }}, 
                    {{ $stats['pendingVendorPayments'] ?? 0 }}, 
                    {{ $stats['approvedPayments'] ?? 0 }}
                ],
                backgroundColor: ['#4e73df', '#f6c23e', '#1cc88a'],
                hoverBackgroundColor: ['#3a5ccc', '#e6b42a', '#17a673'],
                hoverBorderColor: 'rgba(234, 236, 244, 1)',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        }
    });
</script>
@endif
@endsection
