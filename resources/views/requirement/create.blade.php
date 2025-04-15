@extends('layouts.app')

@section('title', 'Add New Requirement')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Add New Requirement</h1>
        <a href="{{ route('requirements.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Requirements
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Requirement Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('requirements.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="vendor_id" class="form-label">Vendor <span class="text-danger">*</span></label>
                        <select id="vendor_id" name="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror" required>
                            <option value="">Select Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('vendor_id', request()->get('vendor_id')) == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->company_name }} ({{ ucfirst($vendor->vendor_type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                        <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }} (HOD: {{ $department->hod->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="requirement_id" class="form-label">Requirement ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('requirement_id') is-invalid @enderror" id="requirement_id" name="requirement_id" value="{{ old('requirement_id') }}" required>
                        <div class="form-text">Unique identifier for this requirement (e.g., REQ-2023-001)</div>
                        @error('requirement_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="cv_file" class="form-label">CV File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('cv_file') is-invalid @enderror" id="cv_file" name="cv_file" required>
                        <div class="form-text">Upload the vendor's CV (PDF, DOC, DOCX, max 5MB)</div>
                        @error('cv_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="client_budget" class="form-label">Client Budget <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('client_budget') is-invalid @enderror" id="client_budget" name="client_budget" value="{{ old('client_budget') }}" required>
                        </div>
                        <div class="form-text">Approximate budget from client</div>
                        @error('client_budget')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="proposed_budget" class="form-label">Proposed Budget <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0" class="form-control @error('proposed_budget') is-invalid @enderror" id="proposed_budget" name="proposed_budget" value="{{ old('proposed_budget') }}" required>
                        </div>
                        <div class="form-text">Budget proposed for this vendor</div>
                        @error('proposed_budget')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="job_description" class="form-label">Job Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('job_description') is-invalid @enderror" id="job_description" name="job_description" rows="6" required>{{ old('job_description') }}</textarea>
                    @error('job_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> This requirement will be submitted for approval to the respective department HOD, followed by founder approval.
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Submit Requirement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
