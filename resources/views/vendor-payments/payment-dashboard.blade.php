@extends('layouts.app')

@section('title', 'Payment Processing Dashboard')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Payment Processing Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('vendor-payments.index') }}">Vendor Payments</a></li>
        <li class="breadcrumb-item active">Payment Processing</li>
    </ol>

    @include('partials.flash-messages')

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-money-bill-wave me-1"></i>
            Approved Payments Ready for Processing
        </div>
        <div class="card-body">
            @if($approvedPayments->isEmpty())
                <div class="alert alert-info">There are no approved payments ready for processing.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="approvedPaymentsTable">
                        <thead>
                            <tr>
                                <th>Vendor</th>
                                <th>Amount</th>
                                <th>Payment Date</th>
                                <th>Payment Method</th>
                                <th>Invoice</th>
                                <th>Client Payment</th>
                                <th>Approver</th>
                                <th>Approved At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($approvedPayments as $payment)
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
                                                {{ $payment->clientPayment->client_name }} -
                                                {{ $payment->clientPayment->currency }} {{ number_format($payment->clientPayment->amount, 2) }}
                                            </a>
                                        @else
                                            <span class="text-muted">None</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($payment->approver)->name }}</td>
                                    <td>{{ optional($payment->approved_at)->format('M d, Y H:i') }}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('vendor-payments.show', $payment) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#markAsPaidModal{{ $payment->id }}">
                                                <i class="fas fa-check-circle"></i> Mark as Paid
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Mark as Paid Modal -->
                                <div class="modal fade" id="markAsPaidModal{{ $payment->id }}" tabindex="-1" aria-labelledby="markAsPaidModalLabel{{ $payment->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('vendor-payments.mark-as-paid', $payment) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="markAsPaidModalLabel{{ $payment->id }}">Mark Payment as Paid</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <p>You are about to mark the following payment as paid:</p>
                                                        <ul class="list-group mb-3">
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <span>Vendor:</span>
                                                                <span class="fw-bold">{{ $payment->vendor->company_name }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <span>Amount:</span>
                                                                <span class="fw-bold">{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                <span>Payment Date:</span>
                                                                <span>{{ $payment->payment_date->format('M d, Y') }}</span>
                                                            </li>
                                                            @if($payment->invoice)
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    <span>Invoice:</span>
                                                                    <span>{{ $payment->invoice->invoice_number }}</span>
                                                                </li>
                                                            @endif
                                                        </ul>

                                                        <div class="form-group mb-3">
                                                            <label for="transaction_reference{{ $payment->id }}" class="form-label">Transaction Reference</label>
                                                            <input type="text" class="form-control" id="transaction_reference{{ $payment->id }}" name="transaction_reference" 
                                                                value="{{ $payment->transaction_reference }}" placeholder="Enter transaction reference or ID">
                                                        </div>

                                                        <div class="form-group">
                                                            <label for="payment_notes{{ $payment->id }}" class="form-label">Payment Notes</label>
                                                            <textarea class="form-control" id="payment_notes{{ $payment->id }}" name="payment_notes" rows="3" 
                                                                placeholder="Enter any notes about this payment"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-success">Mark as Paid</button>
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
            <i class="fas fa-chart-pie me-1"></i>
            Payment Statistics
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white mb-4">
                        <div class="card-body">
                            <h4 class="mb-0">{{ $approvedPayments->count() }}</h4>
                            <div>Ready for Payment</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <h4 class="mb-0">{{ $approvedPayments->sum('amount') }}</h4>
                            <div>Total Amount (USD)</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">
                            <h4 class="mb-0">{{ $approvedPayments->filter(function($p) { return $p->invoice_id !== null; })->count() }}</h4>
                            <div>With Invoices</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white mb-4">
                        <div class="card-body">
                            <h4 class="mb-0">{{ $approvedPayments->filter(function($p) { return $p->client_payment_id !== null; })->count() }}</h4>
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
        $('#approvedPaymentsTable').DataTable({
            order: [[7, 'asc']],
            responsive: true
        });
    });
</script>
@endsection