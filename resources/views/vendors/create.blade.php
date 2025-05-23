@extends('layouts.app')

@section('title', 'Add New Vendor')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Vendor</h1>
        <div>
            <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Vendors
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Vendor Information</h6>
        </div>
        <div class="card-body">
            {{-- @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif --}}

            <form action="{{ route('vendors.store') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="mb-3">User Account Details</h5>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" value="{{ old('password') }}" name="password" placeholder="Leave blank to generate random password">
                            <small class="text-muted">If left blank, a random password will be generated</small>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" value="{{ old('password_confirmation') }}" name="password_confirmation">
                        </div>
                        <div class="mb-3">
                            <label for="poc_name" class="form-label">POC Name</label>
                            <input type="text" class="form-control" id="poc_name" name="poc_name" value="{{ Auth::user()->name }}" required disabled>
                            <input type="hidden" id="internal_poc_id" name="internal_poc_id" value="{{Auth::id()}}" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3">Vendor Profile</h5>
                        <div class="mb-3">
                            <label for="type" class="form-label">Vendor Type</label>
                            <select class="form-select" id="type" name="vendor_type" required onchange="vendorType(this.value);">
                                <option value="">Select Vendor Type</option>
                                <option value="company" {{ old('type') == 'company' ? 'selected' : '' }}>Company</option>
                                <option value="individual" {{ old('type') == 'individual' ? 'selected' : '' }}>Individual</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="company_name" class="form-label company_name">Person/Founder Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label founder_number_label"> Contact Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="company_email" class="form-label founder_email_label"> Email</label>
                            <input type="email" class="form-control" id="founder_email" name="company_email" value="{{ old('company_email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website" value="{{ old('website') }}" maxlength="50">
                        </div>

                        <div class="individual_vendor" style="display: none">
                            <div class="mb-3">
                                <label for="year_of_experience" class="form-label">Year of Experience</label>
                                <input type="number" min="0" class="form-control" id="year_of_experience" name="year_of_experience" value="{{ old('year_of_experience') }}" maxlength="10">
                            </div>
                            <div class="mb-3">
                                <label for="website" class="form-label">Budget</label>
                                <input type="url" class="form-control" id="website" name="website" value="{{ old('website') }}" maxlength="50">
                            </div>
                        </div>

                    </div>
                </div>

{{--                <hr>--}}

{{--                <div class="row mb-4">--}}
{{--                    <div class="col-12">--}}
{{--                        <h5>Company Details</h5>--}}
{{--                        <hr>--}}
{{--                    </div>--}}
{{--                    <div class="col-md-12 mb-3">--}}
{{--                        <label for="address" class="form-label">Address</label>--}}
{{--                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" required maxlength="150">--}}
{{--                    </div>--}}

{{--                    <div class="col-md-12 mb-3">--}}
{{--                        <label for="website" class="form-label">Website</label>--}}
{{--                        <input type="url" class="form-control" id="website" name="website" value="{{ old('website') }}" maxlength="50">--}}
{{--                    </div>--}}

{{--                </div>--}}

                <div class="row mb-4">
                    <div class="col-12">
                        <h5>Bank Details</h5>
                        <hr>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="account_owner_name" class="form-label">Account Owner Name</label>
                        <input type="text" class="form-control" id="account_owner_name" name="account_owner_name" value="{{ old('account_owner_name') }}"  maxlength="70">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="account_owner_name" class="form-label">Account Number</label>
                        <input type="text" class="form-control" id="account_number" name="account_number" value="{{ old('account_number') }}" maxlength="70">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="website" class="form-label">Bank Name</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name') }}" maxlength="70">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="ifsc_code" class="form-label">IFSC Code</label>
                        <input type="text" class="form-control" id="ifsc_code" name="ifsc_code" value="{{ old('ifsc_code') }}" maxlength="70">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="gst_number" class="form-label">GST Number</label>
                        <input type="text" class="form-control" id="gst_number" name="gst_number" value="{{ old('gst_number') }}" maxlength="70">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pan" class="form-label">PAN</label>
                        <input type="text" class="form-control" id="pan" name="pan" value="{{ old('pan') }}" maxlength="70">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="teams_id" class="form-label">Teams ID</label>
                        <input type="text" class="form-control" id="teams_id" name="teams_id" value="{{ old('teams_id') }}" maxlength="30">
                    </div>
                </div>


                <div class="company_budget" style="display: none">
                    <h5 class="mb-3">Budget Information</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="budget_3_years" class="form-label">3 Years Experience</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" id="budget_3_years" name="budget_3_years" value="{{ old('budget_3_years') }}" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="budget_5_years" class="form-label">5 Years Experience</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" id="budget_5_years" name="budget_5_years" value="{{ old('budget_5_years') }}" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="budget_7_years" class="form-label">7+ Years Experience</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" id="budget_7_years" name="budget_7_years" value="{{ old('budget_7_years') }}" >
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label for="budget_10_years" class="form-label">10+ Years Experience</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" id="budget_10_years" name="budget_10_years" value="{{ old('budget_10_years') }}" >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Create Vendor</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select key skills',
            allowClear: true,
            width: '100%'
        });
    });

    function vendorType(vtype){
        if(vtype == 'individual'){
            $('.company_budget').hide();
            $('.individual_vendor').show();
        }
        else if(vtype == 'company'){
            $('.company_budget').show();
            $('.individual_vendor').hide();
        }
    }
</script>
@endsection