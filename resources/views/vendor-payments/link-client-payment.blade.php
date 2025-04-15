@extends('layouts.app')

@section('title', 'Link Client Payment to Vendor Payment')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Link Client Payment to Vendor Payment</h5>
                    <a href="{{ route('vendor-payments.show', $vendorPayment) }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Back to Payment
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Vendor Payment Details</h6>
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
                                            <th>Amount:</th>
                                            <td class="fw-bold text-primary">{{ number_format($vendorPayment->final_amount, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                <span class="badge bg-{{ $vendorPayment->payment_status == 'paid' ? 'success' : ($vendorPayment->payment_status == 'approved' ? 'info' : 'warning') }}">
                                                    {{ ucfirst($vendorPayment->payment_status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('vendor-payments.update-client-payment', $vendorPayment) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Select Client Payment</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Link this vendor payment to a client payment to ensure proper payment tracking. The vendor payment will only be approved after client payment confirmation.
                                </div>

                                <div class="mb-3">
                                    <label for="client_payment_id" class="form-label">Client Payment <span class="text-danger">*</span></label>
                                    <select class="form-select @error('client_payment_id') is-invalid @enderror" id="client_payment_id" name="client_payment_id" required>
                                        <option value="">Select Client Payment</option>
                                        @foreach($clientPayments as $payment)
                                            <option value="{{ $payment->id }}" data-status="{{ $payment->payment_status }}" data-amount="{{ $payment->amount }}" data-client="{{ $payment->client_name }}" data-project="{{ $payment->project_name }}">
                                                {{ $payment->client_name }} - {{ $payment->project_name }} - ${{ number_format($payment->amount, 2) }} ({{ ucfirst($payment->payment_status) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('client_payment_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div id="paymentDetails" class="card mb-4 d-none">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Selected Payment Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th width="40%">Client:</th>
                                                <td width="60%" id="client_name"></td>
                                            </tr>
                                            <tr>
                                                <th>Project:</th>
                                                <td id="project_name"></td>
                                            </tr>
                                            <tr>
                                                <th>Amount:</th>
                                                <td class="fw-bold" id="payment_amount"></td>
                                            </tr>
                                            <tr>
                                                <th>Status:</th>
                                                <td id="payment_status"></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="statusWarning" class="alert alert-warning d-none">
                                            <i class="fas fa-exclamation-triangle me-2"></i> <strong>Warning:</strong> The selected client payment is not marked as received. This vendor payment will be pending until the client payment is received.
                                        </div>
                                        <div id="statusSuccess" class="alert alert-success d-none">
                                            <i class="fas fa-check-circle me-2"></i> <strong>Good to go!</strong> The selected client payment is marked as received. This vendor payment can be approved.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="{{ route('vendor-payments.show', $vendorPayment) }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-link me-1"></i> Link Payment
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('#client_payment_id').select2({
            theme: 'bootstrap4',
            placeholder: 'Select a client payment to link'
        });
        
        // Show client payment details when selected
        $('#client_payment_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                const status = selectedOption.data('status');
                const amount = selectedOption.data('amount');
                const client = selectedOption.data('client');
                const project = selectedOption.data('project');
                
                // Populate details
                $('#client_name').text(client);
                $('#project_name').text(project);
                $('#payment_amount').text('$' + parseFloat(amount).toFixed(2));
                
                const statusText = status.charAt(0).toUpperCase() + status.slice(1);
                $('#payment_status').html(`<span class="badge bg-${status === 'received' ? 'success' : (status === 'delayed' ? 'danger' : 'warning')}">${statusText}</span>`);
                
                // Show appropriate alert
                if (status === 'received') {
                    $('#statusSuccess').removeClass('d-none');
                    $('#statusWarning').addClass('d-none');
                } else {
                    $('#statusWarning').removeClass('d-none');
                    $('#statusSuccess').addClass('d-none');
                }
                
                // Show the details section
                $('#paymentDetails').removeClass('d-none');
            } else {
                $('#paymentDetails').addClass('d-none');
            }
        });
    });
</script>
@endsection