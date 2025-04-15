@extends('layouts.app')

@section('title', 'Edit Vendor Payment')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Vendor Payment</h5>
                    <div>
                        <a href="{{ route('vendor-payments.show', $vendorPayment) }}" class="btn btn-sm btn-light me-2">
                            <i class="fas fa-eye me-1"></i> View Details
                        </a>
                        <a href="{{ route('vendor-payments.index') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Back to Payments
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendor-payments.update', $vendorPayment) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Basic Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Vendor</label>
                                            <input type="text" class="form-control" value="{{ $vendorPayment->vendor->company_name }}" readonly>
                                            <div class="form-text">Vendor cannot be changed after creation.</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="month_year" class="form-label">Month & Year <span class="text-danger">*</span></label>
                                            <input type="month" class="form-control @error('month_year') is-invalid @enderror" id="month_year" name="month_year" value="{{ old('month_year') ?? \Carbon\Carbon::parse($vendorPayment->month_year)->format('Y-m') }}" required>
                                            @error('month_year')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="client_payment_id" class="form-label">Link to Client Payment</label>
                                            <select class="form-select @error('client_payment_id') is-invalid @enderror" id="client_payment_id" name="client_payment_id">
                                                <option value="">Select Client Payment (Optional)</option>
                                                @foreach($clientPayments as $payment)
                                                    <option value="{{ $payment->id }}" {{ (old('client_payment_id') ?? $vendorPayment->client_payment_id) == $payment->id ? 'selected' : '' }}>
                                                        {{ $payment->client_name }} - {{ $payment->project_name }} - {{ number_format($payment->amount, 2) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('client_payment_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">If this payment is linked to a specific client payment, select it here.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Attendance & Rate Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="working_days" class="form-label">Working Days <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('working_days') is-invalid @enderror" id="working_days" name="working_days" value="{{ old('working_days', $vendorPayment->working_days) }}" min="0" max="31" required>
                                                    @error('working_days')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="present_days" class="form-label">Present Days <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('present_days') is-invalid @enderror" id="present_days" name="present_days" value="{{ old('present_days', $vendorPayment->present_days) }}" min="0" max="31" required>
                                                    @error('present_days')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="approved_leave_days" class="form-label">Approved Leave Days</label>
                                                    <input type="number" class="form-control @error('approved_leave_days') is-invalid @enderror" id="approved_leave_days" name="approved_leave_days" value="{{ old('approved_leave_days', $vendorPayment->approved_leave_days) }}" min="0" max="31">
                                                    @error('approved_leave_days')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="daily_rate" class="form-label">Daily Rate <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" class="form-control @error('daily_rate') is-invalid @enderror" id="daily_rate" name="daily_rate" value="{{ old('daily_rate', $vendorPayment->daily_rate) }}" required>
                                            </div>
                                            @error('daily_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="deductions" class="form-label">Deductions</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" class="form-control @error('deductions') is-invalid @enderror" id="deductions" name="deductions" value="{{ old('deductions', $vendorPayment->deductions) }}">
                                            </div>
                                            @error('deductions')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Payment Calculation Preview</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th width="60%">Total Working Days</th>
                                                        <td width="40%" id="preview_working_days">{{ $vendorPayment->working_days }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Present Days</th>
                                                        <td id="preview_present_days">{{ $vendorPayment->present_days }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Approved Leave Days</th>
                                                        <td id="preview_approved_leave_days">{{ $vendorPayment->approved_leave_days }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Payable Days</th>
                                                        <td id="preview_payable_days">{{ min($vendorPayment->working_days, $vendorPayment->present_days + $vendorPayment->approved_leave_days) }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th width="60%">Daily Rate</th>
                                                        <td width="40%" id="preview_daily_rate">${{ number_format($vendorPayment->daily_rate, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Calculated Amount</th>
                                                        <td id="preview_calculated_amount">${{ number_format($vendorPayment->calculated_amount, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Deductions</th>
                                                        <td id="preview_deductions" class="text-danger">${{ number_format($vendorPayment->deductions, 2) }}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Final Amount</th>
                                                        <td id="preview_final_amount" class="fw-bold text-success">${{ number_format($vendorPayment->final_amount, 2) }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Additional Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Payment Notes</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Enter any additional information about this payment">{{ old('notes', $vendorPayment->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12 text-end">
                                <a href="{{ route('vendor-payments.show', $vendorPayment) }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Update Payment
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
        });
        
        // Update calculation previews
        function updateCalculation() {
            const workingDays = parseInt($('#working_days').val()) || 0;
            const presentDays = parseInt($('#present_days').val()) || 0;
            const approvedLeaveDays = parseInt($('#approved_leave_days').val()) || 0;
            const dailyRate = parseFloat($('#daily_rate').val()) || 0;
            const deductions = parseFloat($('#deductions').val()) || 0;
            
            const payableDays = Math.min(workingDays, presentDays + approvedLeaveDays);
            const calculatedAmount = payableDays * dailyRate;
            const finalAmount = calculatedAmount - deductions;
            
            // Update preview fields
            $('#preview_working_days').text(workingDays);
            $('#preview_present_days').text(presentDays);
            $('#preview_approved_leave_days').text(approvedLeaveDays);
            $('#preview_payable_days').text(payableDays);
            $('#preview_daily_rate').text('$' + dailyRate.toFixed(2));
            $('#preview_calculated_amount').text('$' + calculatedAmount.toFixed(2));
            $('#preview_deductions').text('$' + deductions.toFixed(2));
            $('#preview_final_amount').text('$' + finalAmount.toFixed(2));
            
            // Store calculated values
            $('<input>').attr({
                type: 'hidden',
                name: 'calculated_amount',
                value: calculatedAmount.toFixed(2)
            }).appendTo('form');
            
            $('<input>').attr({
                type: 'hidden',
                name: 'final_amount',
                value: finalAmount.toFixed(2)
            }).appendTo('form');
        }
        
        // Trigger calculation update when any input changes
        $('#working_days, #present_days, #approved_leave_days, #daily_rate, #deductions').on('input', updateCalculation);
        
        // Initial calculation
        updateCalculation();
    });
</script>
@endsection