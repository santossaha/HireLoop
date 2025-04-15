@extends('layouts.app')

@section('title', 'Create Vendor Payment')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Vendor Payment</h5>
                    <a href="{{ route('vendor-payments.index') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Back to Payments
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('vendor-payments.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Basic Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="vendor_id" class="form-label">Vendor <span class="text-danger">*</span></label>
                                            <select class="form-select @error('vendor_id') is-invalid @enderror" id="vendor_id" name="vendor_id" required>
                                                <option value="">Select Vendor</option>
                                                @foreach($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                                        {{ $vendor->company_name }} - {{ $vendor->poc_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('vendor_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="month_year" class="form-label">Month & Year <span class="text-danger">*</span></label>
                                            <input type="month" class="form-control @error('month_year') is-invalid @enderror" id="month_year" name="month_year" value="{{ old('month_year') ?? now()->format('Y-m') }}" required>
                                            @error('month_year')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="client_payment_id" class="form-label">Link to Client Payment</label>
                                            <select class="form-select @error('client_payment_id') is-invalid @enderror" id="client_payment_id" name="client_payment_id">
                                                <option value="">Select Client Payment (Optional)</option>
                                                @foreach($clientPayments as $payment)
                                                    <option value="{{ $payment->id }}" {{ old('client_payment_id') == $payment->id ? 'selected' : '' }}>
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
                                                    <input type="number" class="form-control @error('working_days') is-invalid @enderror" id="working_days" name="working_days" value="{{ old('working_days', 22) }}" min="0" max="31" required>
                                                    @error('working_days')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="present_days" class="form-label">Present Days <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control @error('present_days') is-invalid @enderror" id="present_days" name="present_days" value="{{ old('present_days', 22) }}" min="0" max="31" required>
                                                    @error('present_days')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="approved_leave_days" class="form-label">Approved Leave Days</label>
                                                    <input type="number" class="form-control @error('approved_leave_days') is-invalid @enderror" id="approved_leave_days" name="approved_leave_days" value="{{ old('approved_leave_days', 0) }}" min="0" max="31">
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
                                                <input type="number" step="0.01" class="form-control @error('daily_rate') is-invalid @enderror" id="daily_rate" name="daily_rate" value="{{ old('daily_rate') }}" required>
                                            </div>
                                            @error('daily_rate')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="deductions" class="form-label">Deductions</label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" step="0.01" class="form-control @error('deductions') is-invalid @enderror" id="deductions" name="deductions" value="{{ old('deductions', 0) }}">
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
                                                        <td width="40%" id="preview_working_days">0</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Present Days</th>
                                                        <td id="preview_present_days">0</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Approved Leave Days</th>
                                                        <td id="preview_approved_leave_days">0</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Payable Days</th>
                                                        <td id="preview_payable_days">0</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th width="60%">Daily Rate</th>
                                                        <td width="40%" id="preview_daily_rate">$0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Calculated Amount</th>
                                                        <td id="preview_calculated_amount">$0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Deductions</th>
                                                        <td id="preview_deductions" class="text-danger">$0.00</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Final Amount</th>
                                                        <td id="preview_final_amount" class="fw-bold text-success">$0.00</td>
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
                                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Enter any additional information about this payment">{{ old('notes') }}</textarea>
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
                                <a href="{{ route('vendor-payments.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Create Payment
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
        $('#vendor_id, #client_payment_id').select2({
            theme: 'bootstrap4',
        });
        
        // Populate vendor rate when vendor is selected
        $('#vendor_id').on('change', function() {
            const vendorId = $(this).val();
            if (!vendorId) return;
            
            $.ajax({
                url: `/api/vendors/${vendorId}/rate`,
                method: 'GET',
                success: function(data) {
                    if (data.daily_rate) {
                        $('#daily_rate').val(data.daily_rate);
                        updateCalculation();
                    }
                }
            });
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