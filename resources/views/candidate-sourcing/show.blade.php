@extends('layouts.app')

@section('title', 'Requirement & Uploaded Resumes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Requirement Details</h1>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#uploadCandidateModal">
                <i class="fas fa-plus me-1"></i> Upload Candidate
            </button>
            <a href="{{ route('candidate-sourcing.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <!-- Requirement Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
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
                            <p>{{ $requirement->vendor->company_name ?? 'N/A' }}</p>
                        </div>
                        {{-- <div class="col-md-6">
                            <h5 class="font-weight-bold">Client Budget</h5>
                            <p>${{ number_format($requirement->client_budget, 2) }}</p>
                        </div> --}}
                    </div>
                    <div class="mb-4">
                        <h5 class="font-weight-bold">Job Description</h5>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($requirement->job_description)) !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upload Candidate Modal -->
            <div class="modal fade" id="uploadCandidateModal" tabindex="-1" aria-labelledby="uploadCandidateModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="uploadCandidateModalLabel">Upload Candidate Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('candidate-sourcing.upload-candidate', ['requirement' => $requirement->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="candidate_name" class="form-label">Candidate Name*</label>
                                        <input type="text" class="form-control" id="candidate_name" name="candidate_name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" >
                                    </div>
                                    <div class="col-md-6">
                                        <label for="resume" class="form-label">Resume*</label>
                                        <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="budget" class="form-label">Budget*</label>
                                        <input type="number" class="form-control" id="budget" name="budget" step="0.01" min="0" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="notes" class="form-label">Additional Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Upload Candidate</button>
                            </div>
                        </form>
                    </div>
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
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No resumes uploaded yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- You can add a right column for status/approval if needed -->
    </div>
</div>

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