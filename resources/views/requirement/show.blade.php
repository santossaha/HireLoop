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

    <!-- Uploaded Resumes Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Uploaded Resumes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="candidatesTable">
                    <thead>
                        <tr>
                            <th>Candidate Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Budget</th>
                            <th>Uploaded By</th>
                            <th>Status</th>
                            <th>Uploaded At</th>
                            <th>Resume</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($candidates as $candidate)
                        <tr>
                            <td>{{ $candidate->candidate_name }}</td>
                            <td>{{ $candidate->email }}</td>
                            <td>{{ $candidate->phone }}</td>
                            <td>${{ number_format($candidate->budget, 2) }}</td>
                            <td>{{ $candidate->uploadedBy->name ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $candidate->status === 'pending' ? 'warning' : ($candidate->status === 'approved' ? 'success' : 'danger') }}">
                                    {{ ucfirst($candidate->status) }}
                                </span>
                            </td>
                            <td>{{ $candidate->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if($candidate->resume_path)
                                    <a href="{{ asset('storage/' . $candidate->resume_path) }}" class="btn btn-sm btn-primary" target="_blank">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewResumeModal{{ $candidate->id }}">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No resumes uploaded yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Resume View Modals -->
@foreach($candidates as $candidate)
<div class="modal fade" id="viewResumeModal{{ $candidate->id }}" tabindex="-1" aria-labelledby="viewResumeModalLabel{{ $candidate->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewResumeModalLabel{{ $candidate->id }}">Resume Details - {{ $candidate->candidate_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Personal Information</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Name</th>
                                <td>{{ $candidate->candidate_name }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $candidate->email }}</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $candidate->phone }}</td>
                            </tr>
                            <tr>
                                <th>Budget</th>
                                <td>${{ number_format($candidate->budget, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Upload Information</h6>
                        <table class="table table-bordered">
                            <tr>
                                <th>Uploaded By</th>
                                <td>{{ $candidate->uploadedBy->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Upload Date</th>
                                <td>{{ $candidate->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge bg-{{ $candidate->status === 'pending' ? 'warning' : ($candidate->status === 'approved' ? 'success' : 'danger') }}">
                                        {{ ucfirst($candidate->status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="font-weight-bold">Resume Preview</h6>
                        @if($candidate->resume_path)
                            <div class="embed-responsive embed-responsive-16by9">
                                <iframe class="embed-responsive-item" src="{{ asset('storage/' . $candidate->resume_path) }}" style="width: 100%; height: 500px;"></iframe>
                            </div>
                        @else
                            <p class="text-muted">No resume available for preview.</p>
                        @endif
                    </div>
                </div>

                @if($candidate->notes)
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="font-weight-bold">Additional Notes</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $candidate->notes }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                @if($candidate->resume_path)
                    <a href="{{ asset('storage/' . $candidate->resume_path) }}" class="btn btn-primary" target="_blank">
                        <i class="fas fa-download"></i> Download Resume
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
    $(document).ready(function() {
        $('#candidatesTable').DataTable({
            "order": [[6, "desc"]], // Sort by uploaded date by default
            "pageLength": 10,
            "language": {
                "search": "Search candidates:"
            }
        });
    });
</script>
@endpush
@endsection
