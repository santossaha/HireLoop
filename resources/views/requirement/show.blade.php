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
                <div class="col-12">
                    <table class="table table-bordered">
                        <tr>
                            <th>Requirement ID</th>
                            <th>Vendor</th>
                            <th>Department</th>
                            <th>Internal POC</th>
                        </tr>
                        <tr>
                            <td>{{ $requirement->requirement_id }}</td>
                            <td>
                                <a href="{{ route('vendors.show', $requirement->vendor_id) }}">
                                    {{ $requirement->vendor->company_name }}
                                </a> 
                                ({{ ucfirst($requirement->vendor->vendor_type) }})
                            </td>
                            <td>{{ $requirement->department->name ?? 'N/A' }}</td>
                            <td>{{ $requirement->vendor->internalPoc->name ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#jobDescriptionModal">
                        <i class="fas fa-file-alt"></i> Job Description
                    </button>
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
                            <td>{{ number_format($candidate->budget, 2) }}</td>
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
                    <div class="col-12">
                        <table class="table table-bordered">
                            <tr>
                                <th>Requirement ID</th>
                               
                                <th>Vendor</th>
                                
                                <th>Department</th>
                               
                                <th>Internal POC</th>
                               
                            </tr>
                            <tr>
                                <td>{{ $requirement->requirement_id }}</td>
                                <td>{{ $requirement->vendor->company_name }}</td>
                                <td>{{ $requirement->department->name }}</td>
                                <td>{{ $requirement->vendor->internalPoc->name ?? 'N/A'  }}</td>
                                
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="font-weight-bold">Job Description</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $requirement->job_description }}
                        </div>
                    </div>
                </div>
                
               

                @if($candidate->candidate_details)
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="font-weight-bold">Additional Notes</h6>
                        <div class="p-3 bg-light rounded">
                            {{ $candidate->candidate_details }}
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

<!-- Job Description Modal -->
<div class="modal fade" id="jobDescriptionModal" tabindex="-1" aria-labelledby="jobDescriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobDescriptionModalLabel">Job Description</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="p-3 bg-light rounded">
                    {{ $requirement->job_description }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function toggleDescription() {
        const shortDesc = document.getElementById('shortDescription');
        const fullDesc = document.getElementById('fullDescription');
        const readMoreBtn = document.getElementById('readMoreBtn');

        if (shortDesc.style.display !== 'none') {
            shortDesc.style.display = 'none';
            fullDesc.style.display = 'block';
            readMoreBtn.textContent = 'Show Less';
        } else {
            shortDesc.style.display = 'block';
            fullDesc.style.display = 'none';
            readMoreBtn.textContent = 'Read More';
        }
    }

    // $(document).ready(function() {
    //     $('#candidatesTable').DataTable({
    //         "order": [[6, "desc"]], // Sort by uploaded date by default
    //         "pageLength": 10,
    //         "language": {
    //             "search": "Search candidates:"
    //         }
    //     });
    // });
</script>
@endsection
