@extends('layouts.app')

@section('title', 'Mark Payment as Paid')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Mark Vendor Payment as Paid</h5>
                    <a href="{{ route('vendor-payments.show', $vendorPayment) }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Back to Payment
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Payment Details</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Vendor:</th>
                                            <td width="60%">{{ $vendorPayment->vendor->company_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Period:</th>
                                            <td>{{ \Carbon\Carbon::parse($vendorPayment->month_year)->format('F Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Invoice Number:</th>
                                            <td>
                                                @if($vendorPayment->invoice)
                                                    {{ $vendorPayment->invoice->invoice_number }}
                                                @else
                                                    <span class="badge bg-secondary">Not Available</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Amount:</th>
                                            <td class="fw-bold text-success">{{ number_format($vendorPayment->final_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Payment Status:</th>
                                            <td>
                                                <span class="badge bg-{{ $vendorPayment->payment_status == 'approved' ? 'success' : ($vendorPayment->payment_status == 'pending' ? 'warning' : 'primary') }}">
                                                    {{ ucfirst($vendorPayment->payment_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('vendor-payments.mark-as-paid', $vendorPayment) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Payment Confirmation</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('payment_date') is-invalid @enderror" id="payment_date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required>
                                            @error('payment_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Payment Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="4" placeholder="Enter additional notes about this payment (e.g., transaction ID, bank reference)">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="confirm_payment" required>
                                            <label class="form-check-label" for="confirm_payment">
                                                I confirm that payment of {{ number_format($vendorPayment->final_amount, 2) }} has been processed to vendor {{ $vendorPayment->vendor->company_name }}.
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-light">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check me-1"></i> Mark as Paid
                                        </button>
                                        <a href="{{ route('vendor-payments.show', $vendorPayment) }}" class="btn btn-secondary">
                                            Cancel
                                        </a>
                                    </div>
                                </div>
                            </form>
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
        // Confirm payment submission
        $('form').on('submit', function(e) {
            if (!$('#confirm_payment').is(':checked')) {
                e.preventDefault();
                Swal.fire({
                    title: 'Confirmation Required',
                    text: "Please confirm that the payment has been processed",
                    icon: 'warning',
                    confirmButtonColor: '#3085d6',
                });
                return;
            }
            
            e.preventDefault();
            
            Swal.fire({
                title: 'Mark as Paid?',
                text: "Are you sure you want to mark this payment as paid? This action cannot be undone.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, mark as paid!'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });
</script>
@endsection