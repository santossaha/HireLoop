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
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('vendors.store') }}" method="POST">
                @csrf

                <div class="row">
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
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to generate random password">
                            <small class="text-muted">If left blank, a random password will be generated</small>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-3">Vendor Profile</h5>
                        <div class="mb-3">
                            <label for="type" class="form-label">Vendor Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="company" {{ old('type') == 'company' ? 'selected' : '' }}>Company</option>
                                <option value="freelancer" {{ old('type') == 'freelancer' ? 'selected' : '' }}>Freelancer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="skype" class="form-label">Skype ID</label>
                            <input type="text" class="form-control" id="skype" name="skype" value="{{ old('skype') }}">
                        </div>
                        <div class="mb-3">
                            <label for="slack" class="form-label">Slack ID</label>
                            <input type="text" class="form-control" id="slack" name="slack" value="{{ old('slack') }}">
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="poc_name" class="form-label">POC Name</label>
                            <input type="text" class="form-control" id="poc_name" name="poc_name" value="{{ old('poc_name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="internal_poc_id" class="form-label">Internal POC</label>
                            <select class="form-select" id="internal_poc_id" name="internal_poc_id" required>
                                <option value="">Select Internal POC</option>
                                @foreach($internalPocs as $poc)
                                <option value="{{ $poc->id }}" {{ old('internal_poc_id') == $poc->id ? 'selected' : '' }}>
                                    {{ $poc->name }} ({{ ucfirst($poc->role) }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="status" class="form-label">Initial Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>

                <h5 class="mb-3">Budget Information</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="budget_3_years" class="form-label">3 Years Experience</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="budget_3_years" name="budget_3_years" value="{{ old('budget_3_years') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="budget_5_years" class="form-label">5 Years Experience</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="budget_5_years" name="budget_5_years" value="{{ old('budget_5_years') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="budget_7_years" class="form-label">7+ Years Experience</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="budget_7_years" name="budget_7_years" value="{{ old('budget_7_years') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="budget_10_years" class="form-label">10+ Years Experience</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="budget_10_years" name="budget_10_years" value="{{ old('budget_10_years') }}" required>
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