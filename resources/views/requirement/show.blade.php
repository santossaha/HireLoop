@extends('layouts.app')

@section('title', 'Requirement Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Requirement Details</h1>
        <div>
            @if(!$requirement->isApproved() && !($requirement->status == 'rejected'))
                <a href="{{ route('requirements.edit', $requirement->id) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-1"></i> Edit Requirement
                </a>
            @endif
            <a href="{{ route('vendors.show', $requirement->vendor_id) }}" class="btn btn-info me-2">
                <i class="fas fa-user me-1"></i> View Vendor
            </a>
            <a href="{{ route('requirements.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Requirements
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Requirement Information</h6>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Requirement ID</h5>
                    <p>{{ $requirement->requirement_id }}</p>
                </div>
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Department</h5>
                    <p>{{ $requirement->department->name ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Vendor</h5>
                    <p>
                        <a href="{{ route('vendors.show', $requirement->vendor_id) }}">
                            {{ $requirement->vendor->company_name }}
                        </a> 
                        ({{ ucfirst($requirement->vendor->vendor_type) }})
                    </p>
                </div>
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Internal POC</h5>
                    <p>{{ $requirement->vendor->internalPoc->name ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <h5 class="font-weight-bold">Job Description</h5>
                    <p>{{ $requirement->job_description }}</p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Status</h5>
                    <p>
                        @if($requirement->isApproved())
                            <span class="badge bg-success">Approved</span>
                        @elseif($requirement->status == 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @elseif($requirement->hod_approved && !$requirement->founder_approved)
                            <span class="badge bg-warning">Pending Founder Approval</span>
                        @else
                            <span class="badge bg-info">Pending HOD Approval</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Approved By</h5>
                    <p>{{ $requirement->approvedBy->name ?? 'N/A' }}</p>
                </div>
            </div>

            @if($requirement->isApproved())
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="font-weight-bold">Approved At</h5>
                        <p>{{ $requirement->approved_at->format('M d, Y H:i A') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
