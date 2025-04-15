@extends('layouts.app')

@section('title', 'Payment Approval Dashboard')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Payment Approval Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('vendor-payments.index') }}">Vendor Payments</a></li>
        <li class="breadcrumb-item active">Approval Dashboard</li>
    </ol>

    @include('partials.flash-messages')

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-money-check-alt me-1"></i>
            Pending Payments for Approval
        </div>
        <div class="card-body">
            @if($pendingPayments->isEmpty())
                <div class="alert alert-info">There are no pending payments requiring approval.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="pendingPaymentsTable">
                        <thead>
                            <tr>
                                <th>Vendor</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                                <th>Invoice</th>
                                <th>Client Payment</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingPayments as $payment)
                                <tr>
                                    <td>
                                        <a href="{{ route('vendors.show', $payment->vendor) }}">
                                            {{ $payment->vendor->company_name }}
                                        </a>
                                    </td>
                                    <td>{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->payment_date->format('M d, Y') }}</td>
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
                                                {{ $payment->clientPayment->client_name }} - 
                                                {{ $payment->clientPayment->currency }} {{ number_format($payment->clientPayment->amount, 2) }}
                                            </a>
                                        @else
                                            <span class="text-muted">None</span>
                                        @endif
                                    </td>
                                    <td>{{ $payment->creator->name }}</td>
                                    <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ Str::limit($payment->notes, 50) }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('vendor-payments.show', $payment) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <form action="{{ route('vendor-payments.approve', $payment) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to approve this payment?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectPaymentModal{{ $payment->id }}">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Reject Payment Modal -->
                                <div class="modal fade" id="rejectPaymentModal{{ $payment->id }}" tabindex="-1" aria-labelledby="rejectPaymentModalLabel{{ $payment->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('vendor-payments.reject', $payment) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="rejectPaymentModalLabel{{ $payment->id }}">Reject Payment</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="rejection_reason" class="form-label">Rejection Reason</label>
                                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Reject Payment</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-chart-line me-1"></i>
            Payment Approval Statistics
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h4 class="mb-0">{{ $pendingPayments->count() }}</h4>
                            <div>Pending Payments</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <h4 class="mb-0">{{ $pendingPayments->sum('amount') }}</h4>
                            <div>Total Amount (USD)</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">
                            <h4 class="mb-0">{{ $pendingPayments->filter(function($p) { return $p->invoice_id !== null; })->count() }}</h4>
                            <div>With Invoices</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white mb-4">
                        <div class="card-body">
                            <h4 class="mb-0">{{ $pendingPayments->filter(function($p) { return $p->client_payment_id !== null; })->count() }}</h4>
                            <div>With Client Payments</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#pendingPaymentsTable').DataTable({
            order: [[6, 'asc']],
            responsive: true
        });
    });
</script>
@endsection