@extends('layouts.app')

@section('title', 'Monthly Vendor Payment Report')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Monthly Vendor Payment Report</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('vendor-payments.index') }}">Vendor Payments</a></li>
        <li class="breadcrumb-item active">Monthly Report</li>
    </ol>

    @include('partials.flash-messages')

    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-calendar-alt me-1"></i>
                    Report for {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}
                </div>
                <div>
                    <form action="{{ route('vendor-payments.monthly-report') }}" method="GET" class="d-flex">
                        <div class="me-2">
                            <select name="month" class="form-select">
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="me-2">
                            <select name="year" class="form-select">
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}" {{ $year == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">View Report</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h4>{{ $payments->count() }}</h4>
                            <div>Total Vendor Payments</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h4>{{ count($vendorSummary) }}</h4>
                            <div>Unique Vendors Paid</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h4>{{ number_format($totalAmount, 2) }} USD</h4>
                            <div>Total Amount Paid</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Vendor Payment Summary
                        </div>
                        <div class="card-body">
                            @if(count($vendorSummary) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="vendorSummaryTable">
                                        <thead>
                                            <tr>
                                                <th>Vendor</th>
                                                <th>Payment Count</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vendorSummary as $summary)
                                                <tr>
                                                    <td>
                                                        <a href="{{ route('vendors.show', $summary['vendor']) }}">
                                                            {{ $summary['vendor']->company_name }}
                                                        </a>
                                                    </td>
                                                    <td>{{ $summary['count'] }}</td>
                                                    <td>{{ number_format($summary['total'], 2) }} USD</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">No data available for this period.</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-1"></i>
                            Payment Distribution
                        </div>
                        <div class="card-body">
                            @if(count($vendorSummary) > 0)
                                <canvas id="paymentDistributionChart" width="100%" height="400"></canvas>
                            @else
                                <div class="alert alert-info">No data available for this period.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-list me-1"></i>
                    Detailed Payment Listing
                </div>
                <div class="card-body">
                    @if($payments->isEmpty())
                        <div class="alert alert-info">No payments were made during this period.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="detailedPaymentsTable">
                                <thead>
                                    <tr>
                                        <th>Vendor</th>
                                        <th>Amount</th>
                                        <th>Payment Date</th>
                                        <th>Method</th>
                                        <th>Invoice</th>
                                        <th>Client Payment</th>
                                        <th>Transaction Ref</th>
                                        <th>Paid By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <tr>
                                            <td>
                                                <a href="{{ route('vendors.show', $payment->vendor) }}">
                                                    {{ $payment->vendor->company_name }}
                                                </a>
                                            </td>
                                            <td>{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                            <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                            <td>{{ $payment->getPaymentMethodLabel() }}</td>
                                            <td>
                                                @if($payment->invoice)
                                                    <a href="{{ route('invoices.show', $payment->invoice) }}">
                                                        {{ $payment->invoice->invoice_number }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">None</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->clientPayment)
                                                    <a href="{{ route('client-payments.show', $payment->clientPayment) }}">
                                                        {{ $payment->clientPayment->client_name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">None</span>
                                                @endif
                                            </td>
                                            <td>{{ $payment->transaction_reference ?? '-' }}</td>
                                            <td>{{ optional($payment->payer)->name }}</td>
                                            <td>
                                                <a href="{{ route('vendor-payments.show', $payment) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <form action="{{ route('vendor-payments.export') }}" method="GET" class="d-inline">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-excel me-1"></i> Export to Excel
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        $('#vendorSummaryTable').DataTable({
            order: [[2, 'desc']],
            responsive: true
        });

        $('#detailedPaymentsTable').DataTable({
            order: [[2, 'desc']],
            responsive: true
        });

        @if(count($vendorSummary) > 0)
        // Payment Distribution Chart
        const ctx = document.getElementById('paymentDistributionChart').getContext('2d');
        
        // Prepare chart data
        const labels = [@foreach($vendorSummary as $summary) '{{ $summary['vendor']->company_name }}', @endforeach];
        const data = [@foreach($vendorSummary as $summary) {{ $summary['total'] }}, @endforeach];
        
        // Generate random colors
        const backgroundColors = generateColors({{ count($vendorSummary) }});
        
        const chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const percentage = ((value / {{ $totalAmount }}) * 100).toFixed(1);
                                return `${label}: $${value.toFixed(2)} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        function generateColors(count) {
            const colors = [];
            for (let i = 0; i < count; i++) {
                const hue = Math.round(i * (360 / count));
                colors.push(`hsl(${hue}, 70%, 60%)`);
            }
            return colors;
        }
        @endif
    });
</script>
@endsection