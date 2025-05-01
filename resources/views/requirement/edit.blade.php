@extends('layouts.app')

@section('title', 'Edit Requirement')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Requirement</h1>
        <a href="{{ route('requirements.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Requirements
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Requirement Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('requirements.update', $requirement->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="company_id" class="form-label">Company <span class="text-danger">*</span></label>
                        <select id="company_id" name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ old('company_id', $requirement->company_id) == $company->id ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                        <select id="department_id" name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $requirement->department_id) == $department->id ? 'selected' : '' }}>
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
                        <input type="text" class="form-control @error('requirement_id') is-invalid @enderror" id="requirement_id" name="requirement_id" value="{{ old('requirement_id', $requirement->requirement_id) }}" required>
                        <div class="form-text">Unique identifier for this requirement (e.g., REQ-2023-001)</div>
                        @error('requirement_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="job_description" class="form-label">Job Description <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('job_description') is-invalid @enderror" id="job_description" name="job_description" rows="6" required>{{ old('job_description', $requirement->job_description) }}</textarea>
                    @error('job_description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Update Requirement</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
