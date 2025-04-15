@extends('layouts.app')

@section('title', 'View Vendor Payment')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Vendor Payment Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('vendor-payments.index') }}">Vendor Payments</a></li>
        <li class="breadcrumb-item active">Payment #{{ $vendorPayment->id }}</li>
    </ol>

    @include('partials.flash-messages')

    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-money-bill-wave me-1"></i>
                        Payment Information
                    </div>
                    <div>
                        <span class="badge 
                            @if($vendorPayment->status == 'draft') bg-secondary
                            @elseif($vendorPayment->status == 'pending_approval') bg-warning text-dark
                            @elseif($vendorPayment->status == 'approved') bg-primary
                            @elseif($vendorPayment->status == 'paid') bg-success
                            @elseif($vendorPayment->status == 'rejected') bg-danger
                            @endif
                            fs-6 me-2">
                            {{ ucfirst(str_replace('_', ' ', $vendorPayment->status)) }}
                        </span>
                        
                        <div class="btn-group">
                            @if($vendorPayment->status == 'draft' || $vendorPayment->status == 'pending_approval')
                                @can('edit-vendor-payments')
                                <a href="{{ route('vendor-payments.edit', $vendorPayment) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                                @endcan
                            @endif
                            
                            @if($vendorPayment->status == 'pending_approval')
                                @can('approve-vendor-payments')
                                <form action="{{ route('vendor-payments.approve', $vendorPayment) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to approve this payment?')">
                                        <i class="fas fa-check me-1"></i> Approve
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectPaymentModal">
                                    <i class="fas fa-times me-1"></i> Reject
                                </button>
                                @endcan
                            @endif
                            
                            @if($vendorPayment->status == 'approved')
                                @can('mark-vendor-payments-paid')
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#markAsPaidModal">
                                    <i class="fas fa-money-bill me-1"></i> Mark as Paid
                                </button>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h5 class="mb-3">Payment Details</h5>
                            <p class="mb-2"><strong>Payment ID:</strong> #{{ $vendorPayment->id }}</p>
                            <p class="mb-2"><strong>Amount:</strong> {{ $vendorPayment->currency }} {{ number_format($vendorPayment->amount, 2) }}</p>
                            <p class="mb-2"><strong>Payment Date:</strong> {{ $vendorPayment->payment_date->format('F d, Y') }}</p>
                            <p class="mb-2"><strong>Payment Method:</strong> {{ $vendorPayment->getPaymentMethodLabel() }}</p>
                            <p class="mb-2"><strong>Created By:</strong> {{ optional($vendorPayment->creator)->name }}</p>
                            <p class="mb-2"><strong>Created At:</strong> {{ $vendorPayment->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-sm-6">
                            <h5 class="mb-3">Vendor Information</h5>
                            <p class="mb-2"><strong>Vendor:</strong> 
                                <a href="{{ route('vendors.show', $vendorPayment->vendor) }}">
                                    {{ $vendorPayment->vendor->company_name }}
                                </a>
                            </p>
                            <p class="mb-2"><strong>Contact:</strong> {{ $vendorPayment->vendor->contact_number }}</p>
                            <p class="mb-2"><strong>Email:</strong> {{ $vendorPayment->vendor->email }}</p>
                            <p class="mb-2"><strong>Type:</strong> {{ ucfirst($vendorPayment->vendor->vendor_type) }}</p>
                            <p class="mb-2"><strong>POC:</strong> {{ $vendorPayment->vendor->vendor_poc_name }}</p>
                            <p class="mb-2"><strong>Internal POC:</strong> {{ optional($vendorPayment->vendor->internalPoc)->name }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <h5>Notes</h5>
                            <div class="border rounded p-3 bg-light">
                                {!! nl2br(e($vendorPayment->notes)) !!}
                            </div>
                        </div>
                    </div>

                    @if($vendorPayment->invoice)
                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <h5>Invoice Information</h5>
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th>Invoice Number</th>
                                    <td>
                                        <a href="{{ route('invoices.show', $vendorPayment->invoice) }}">
                                            {{ $vendorPayment->invoice->invoice_number }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Invoice Date</th>
                                    <td>{{ $vendorPayment->invoice->invoice_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td>{{ $vendorPayment->invoice->currency }} {{ number_format($vendorPayment->invoice->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge 
                                            @if($vendorPayment->invoice->status == 'draft') bg-secondary
                                            @elseif($vendorPayment->invoice->status == 'submitted') bg-info
                                            @elseif($vendorPayment->invoice->status == 'verified') bg-primary
                                            @elseif($vendorPayment->invoice->status == 'paid') bg-success
                                            @elseif($vendorPayment->invoice->status == 'rejected') bg-danger
                                            @endif">
                                            {{ ucfirst($vendorPayment->invoice->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if($vendorPayment->clientPayment)
                    <div class="row mb-4">
                        <div class="col-sm-12">
                            <h5>Client Payment Information</h5>
                            <table class="table table-bordered table-sm">
                                <tr>
                                    <th>Client</th>
                                    <td>
                                        <a href="{{ route('client-payments.show', $vendorPayment->clientPayment) }}">
                                            {{ $vendorPayment->clientPayment->client_name }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Payment Date</th>
                                    <td>{{ $vendorPayment->clientPayment->payment_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Amount</th>
                                    <td>{{ $vendorPayment->clientPayment->currency }} {{ number_format($vendorPayment->clientPayment->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge 
                                            @if($vendorPayment->clientPayment->status == 'pending') bg-warning text-dark
                                            @elseif($vendorPayment->clientPayment->status == 'received') bg-success
                                            @elseif($vendorPayment->clientPayment->status == 'delayed') bg-danger
                                            @endif">
                                            {{ ucfirst($vendorPayment->clientPayment->status) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-history me-1"></i>
                            Payment Status History
                        </div>
                        <div class="card-body p-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-secondary">Created</span>
                                            <span class="ms-2">By {{ optional($vendorPayment->creator)->name }}</span>
                                        </div>
                                        <small>{{ $vendorPayment->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </li>
                                
                                @if($vendorPayment->status != 'draft' && $vendorPayment->creator)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-warning text-dark">Submitted</span>
                                            <span class="ms-2">By {{ optional($vendorPayment->creator)->name }}</span>
                                        </div>
                                        <small>{{ $vendorPayment->created_at->format('M d, Y H:i') }}</small>
                                    </div>
                                </li>
                                @endif
                                
                                @if($vendorPayment->status == 'approved' || $vendorPayment->status == 'paid')
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-primary">Approved</span>
                                            <span class="ms-2">By {{ optional($vendorPayment->approver)->name }}</span>
                                        </div>
                                        <small>{{ $vendorPayment->approved_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    @if($vendorPayment->approval_notes)
                                    <div class="mt-2 small">
                                        <strong>Notes:</strong> {{ $vendorPayment->approval_notes }}
                                    </div>
                                    @endif
                                </li>
                                @endif
                                
                                @if($vendorPayment->status == 'paid')
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-success">Paid</span>
                                            <span class="ms-2">By {{ optional($vendorPayment->payer)->name }}</span>
                                        </div>
                                        <small>{{ $vendorPayment->paid_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    @if($vendorPayment->transaction_reference)
                                    <div class="mt-2 small">
                                        <strong>Transaction Ref:</strong> {{ $vendorPayment->transaction_reference }}
                                    </div>
                                    @endif
                                    @if($vendorPayment->payment_notes)
                                    <div class="mt-2 small">
                                        <strong>Payment Notes:</strong> {{ $vendorPayment->payment_notes }}
                                    </div>
                                    @endif
                                </li>
                                @endif
                                
                                @if($vendorPayment->status == 'rejected')
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-danger">Rejected</span>
                                            <span class="ms-2">By {{ optional($vendorPayment->rejector)->name }}</span>
                                        </div>
                                        <small>{{ $vendorPayment->rejected_at->format('M d, Y H:i') }}</small>
                                    </div>
                                    @if($vendorPayment->rejection_reason)
                                    <div class="mt-2 small">
                                        <strong>Reason:</strong> {{ $vendorPayment->rejection_reason }}
                                    </div>
                                    @endif
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-calculator me-1"></i>
                            Payment Summary
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Payment Amount:</span>
                                    <span class="fw-bold">{{ $vendorPayment->currency }} {{ number_format($vendorPayment->amount, 2) }}</span>
                                </div>
                                @if($vendorPayment->invoice)
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Invoice Amount:</span>
                                    <span>{{ $vendorPayment->invoice->currency }} {{ number_format($vendorPayment->invoice->amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Invoice Difference:</span>
                                    <span class="{{ $vendorPayment->amount == $vendorPayment->invoice->amount ? 'text-success' : 'text-danger' }}">
                                        {{ $vendorPayment->currency }} {{ number_format($vendorPayment->amount - $vendorPayment->invoice->amount, 2) }}
                                    </span>
                                </div>
                                @endif
                                <hr>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Status:</span>
                                    <span class="fw-bold">{{ ucfirst(str_replace('_', ' ', $vendorPayment->status)) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Payment Date:</span>
                                    <span>{{ $vendorPayment->payment_date->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Payment Modal -->
    <div class="modal fade" id="rejectPaymentModal" tabindex="-1" aria-labelledby="rejectPaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('vendor-payments.reject', $vendorPayment) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectPaymentModalLabel">Reject Payment</h5>
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

    <!-- Mark as Paid Modal -->
    <div class="modal fade" id="markAsPaidModal" tabindex="-1" aria-labelledby="markAsPaidModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('vendor-payments.mark-as-paid', $vendorPayment) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title" id="markAsPaidModalLabel">Mark Payment as Paid</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <p>You are about to mark the following payment as paid:</p>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Vendor:</span>
                                    <span class="fw-bold">{{ $vendorPayment->vendor->company_name }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Amount:</span>
                                    <span class="fw-bold">{{ $vendorPayment->currency }} {{ number_format($vendorPayment->amount, 2) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Payment Date:</span>
                                    <span>{{ $vendorPayment->payment_date->format('M d, Y') }}</span>
                                </li>
                                @if($vendorPayment->invoice)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>Invoice:</span>
                                        <span>{{ $vendorPayment->invoice->invoice_number }}</span>
                                    </li>
                                @endif
                            </ul>

                            <div class="form-group mb-3">
                                <label for="transaction_reference" class="form-label">Transaction Reference</label>
                                <input type="text" class="form-control" id="transaction_reference" name="transaction_reference" placeholder="Enter transaction reference or ID">
                            </div>

                            <div class="form-group">
                                <label for="payment_notes" class="form-label">Payment Notes</label>
                                <textarea class="form-control" id="payment_notes" name="payment_notes" rows="3" placeholder="Enter any notes about this payment"></textarea>
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
</div>
@endsection