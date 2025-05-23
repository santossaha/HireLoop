@extends('layouts.app')

@section('title', 'Edit Vendor')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Vendor</h1>
        <div>
            <a href="{{ route('vendors.show', $vendor->id) }}" class="btn btn-info me-2">
                <i class="fas fa-eye me-1"></i> View Vendor
            </a>
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

            <form action="{{ route('vendors.update', $vendor->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">User Account Details</h5>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $vendor->user->name ?? '') }}">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $vendor->user->email ?? $vendor->email) }}">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                            <small class="text-muted">Only fill this in if you want to change the password</small>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
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
                                <option value="company" {{ old('vendor_type', $vendor->vendor_type) == 'company' ? 'selected' : '' }}>Company</option>
                                <option value="individual" {{ old('vendor_type', $vendor->vendor_type) == 'individual' ? 'selected' : '' }}>Individual</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="company_name" class="form-label company_name">Person/Founder Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $vendor->company_name) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label founder_number_label"> Contact Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $vendor->phone) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="company_email" class="form-label founder_email_label"> Email</label>
                            <input type="email" class="form-control" id="founder_email" name="company_email" value="{{ old('company_email', $vendor->email) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $vendor->address) }}" required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website" value="{{ old('website', $vendor->website) }}" maxlength="50">
                        </div>

                        <div class="individual_vendor" style="{{ old('vendor_type', $vendor->vendor_type) == 'individual' ? 'display: block' : 'display: none' }}">
                            <div class="mb-3">
                                <label for="year_of_experience" class="form-label">Year of Experience</label>
                                <input type="number" min="0" class="form-control" id="year_of_experience" name="year_of_experience" value="{{ old('year_of_experience', $vendor->year_of_experience) }}" maxlength="10">
                            </div>
                            <div class="mb-3">
                                <label for="budget" class="form-label">Budget</label>
                                <input type="number" class="form-control" id="budget" name="budget" value="{{ old('budget', $vendor->budget) }}" maxlength="70">
                            </div>
                        </div>


                        {{--  <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ old('contact_number', $vendor->phone) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="skype" class="form-label">Skype ID</label>
                            <input type="text" class="form-control" id="skype" name="skype" value="{{ old('skype', $vendor->skype_id) }}">
                        </div>  --}}
                        {{--  <div class="mb-3">
                            <label for="key_skills" class="form-label">Key Skills <span class="text-danger">*</span></label>
                            <select class="form-select select2 @error('key_skills') is-invalid @enderror" id="key_skills" name="key_skills[]" multiple required>
                                @foreach(App\Models\KeySkill::all() as $skill)
                                    <option value="{{ $skill->id }}" {{ in_array($skill->id, old('key_skills', $vendor->keySkills->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $skill->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('key_skills')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>  --}}
                    </div>
                </div>

                <hr>

                <div class="row">
                    {{--  <div class="col-md-6">
                        <div class="mb-3">
                            <label for="poc_name" class="form-label">POC Name</label>
                            <input type="text" class="form-control" id="poc_name" name="poc_name" value="{{ old('poc_name', $vendor->contact_person) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="internal_poc_id" class="form-label">Internal POC</label>
                            <select class="form-select" id="internal_poc_id" name="internal_poc_id" required>
                                <option value="">Select Internal POC</option>
                                @foreach($internalPocs as $poc)
                                <option value="{{ $poc->id }}" {{ old('internal_poc_id', $vendor->internal_poc_id) == $poc->id ? 'selected' : '' }}>
                                    {{ $poc->name }} ({{ ucfirst($poc->role) }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>  --}}

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" {{ old('status', $vendor->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status', $vendor->status) == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ old('status', $vendor->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>

<div class="company_budget" style="{{ old('vendor_type', $vendor->vendor_type) == 'company' ? 'display: block' : 'display: none' }}">
                <h5 class="mb-3">Budget Information</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="budget_3_years" class="form-label">3 Years Experience</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="budget_3_years" name="budget_3_years" value="{{ old('budget_3_years', $vendor->budget_3_years) }}" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="budget_5_years" class="form-label">5 Years Experience</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="budget_5_years" name="budget_5_years" value="{{ old('budget_5_years', $vendor->budget_5_years) }}" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="budget_7_years" class="form-label">7+ Years Experience</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="budget_7_years" name="budget_7_years" value="{{ old('budget_7_years', $vendor->budget_7_years) }}" >
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="budget_10_years" class="form-label">10+ Years Experience</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="budget_10_years" name="budget_10_years" value="{{ old('budget_10_years', $vendor->budget_10_years) }}" >
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Update Vendor</button>
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
