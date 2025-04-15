@extends('layouts.app')

@section('title', 'Vendor Payments')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Vendor Payments</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Vendor Payments</li>
    </ol>

    @include('partials.flash-messages')

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-money-bill-wave me-1"></i>
                Vendor Payments
            </div>
            <div>
                @can('create-vendor-payments')
                <a href="{{ route('vendor-payments.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Create New Payment
                </a>
                @endcan
                @can('approve-vendor-payments')
                <a href="{{ route('vendor-payments.approval-dashboard') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-check-circle"></i> Approval Dashboard
                </a>
                @endcan
                @can('mark-vendor-payments-paid')
                <a href="{{ route('vendor-payments.processing-dashboard') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-money-check"></i> Payment Processing
                </a>
                @endcan
                @can('view-vendor-payment-reports')
                <a href="{{ route('vendor-payments.monthly-report') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-chart-bar"></i> Monthly Reports
                </a>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="vendorPaymentsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vendor</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->id }}</td>
                                <td>
                                    <a href="{{ route('vendors.show', $payment->vendor) }}">
                                        {{ $payment->vendor->company_name }}
                                    </a>
                                </td>
                                <td>{{ $payment->currency }} {{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td>{{ $payment->getPaymentMethodLabel() }}</td>
                                <td>
                                    @if($payment->status == 'draft')
                                        <span class="badge bg-secondary">Draft</span>
                                    @elseif($payment->status == 'pending_approval')
                                        <span class="badge bg-warning text-dark">Pending Approval</span>
                                    @elseif($payment->status == 'approved')
                                        <span class="badge bg-primary">Approved</span>
                                    @elseif($payment->status == 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($payment->status == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ optional($payment->creator)->name }}</td>
                                <td>{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('vendor-payments.show', $payment) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        
                                        @if($payment->status == 'draft' || $payment->status == 'pending_approval')
                                            @can('edit-vendor-payments')
                                            <a href="{{ route('vendor-payments.edit', $payment) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            @endcan
                                        @endif
                                        
                                        @if($payment->status == 'pending_approval')
                                            @can('approve-vendor-payments')
                                            <form action="{{ route('vendor-payments.approve', $payment) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to approve this payment?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            @endcan
                                        @endif
                                        
                                        @if($payment->status == 'approved')
                                            @can('mark-vendor-payments-paid')
                                            <form action="{{ route('vendor-payments.mark-as-paid', $payment) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#markAsPaidModal{{ $payment->id }}">
                                                    <i class="fas fa-money-bill"></i> Mark Paid
                                                </button>
                                            </form>
                                            @endcan
                                        @endif
                                    </div>
                                    
                                    @if($payment->status == 'approved')
                                        <!-- Mark as Paid Modal -->
                                        <div class="modal fade" id="markAsPaidModal{{ $payment->id }}" tabindex="-1" aria-labelledby="markAsPaidModalLabel{{ $payment->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('vendor-payments.mark-as-paid', $payment) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="markAsPaidModalLabel{{ $payment->id }}">Mark Payment as Paid</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label for="transaction_reference{{ $payment->id }}" class="form-label">Transaction Reference</label>
                                                                <input type="text" class="form-control" id="transaction_reference{{ $payment->id }}" name="transaction_reference" placeholder="Enter transaction reference">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="payment_notes{{ $payment->id }}" class="form-label">Payment Notes</label>
                                                                <textarea class="form-control" id="payment_notes{{ $payment->id }}" name="payment_notes" rows="3" placeholder="Enter any notes about this payment"></textarea>
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
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#vendorPaymentsTable').DataTable({
            order: [[7, 'desc']],
            responsive: true,
            pageLength: 25,
            "columnDefs": [
                { "orderable": false, "targets": 8 }
            ]
        });
    });
</script>
@endsection