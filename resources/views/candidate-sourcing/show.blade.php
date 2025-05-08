@extends('layouts.app')

@section('title', 'Vendor Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Vendor Dashboard</h1>
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
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Requirement Information</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Requirement ID</th>
                                    <th>Department</th>
                                    <th>Company</th>
                                    <th>Created By</th>
                                </tr>
                                <tr>
                                    <td>{{ $requirement->requirement_id }}</td>
                                    <td>{{ $requirement->department->name ?? 'N/A' }}</td>
                                    <td>{{ $requirement->company->name ?? 'N/A' }}</td>
                                    <td>{{ $requirement->createBy->name ?? 'N/A' }}</td>
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
                </div>
            </div>

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
                                <textarea name="" id="" cols="100" rows="10" class="form-control" readonly>{!! $requirement->job_description !!}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                                    {{-- <th>Status</th> --}}
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
                                    <td>{{ number_format($candidate->budget, 2) }}</td>
                                    <td>{{ $candidate->uploadedBy->name ?? 'N/A' }}</td>
                                    {{-- <td>
                                        <span class="badge bg-{{ $candidate->status === 'pending' ? 'warning' : ($candidate->status === 'approved' ? 'success' : 'danger') }}">
                                            {{ ucfirst($candidate->status) }}
                                        </span>
                                    </td> --}}
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

             <!-- You have been selected for mock round. -->
             <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Mock Round Details</h6>
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
                                    <th>Uploaded At</th>
                                    <th>Status</th>
                                     <th>Interview DateTime</th>
                                   
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
                                        @if($candidate->interviews)
                                                <div class="mb-1">
                                                    <span class="badge bg-{{ $candidate->interviews->type === 'mock' ? 'info' : ($candidate->interviews->type === 'client' ? 'primary' : 'success') }}">
                                                        {{ ucfirst($candidate->interviews->type) }}
                                                    </span>
                                                    <span class="badge bg-{{ $candidate->interviews->status === 'scheduled' ? 'warning' : ($candidate->interviews->status === 'completed' ? 'success' : 'danger') }}">
                                                        {{ ucfirst($candidate->interviews->status) }}
                                                    </span>
                                                </div>
                                           
                                        @else
                                            <span class="text-muted">No interviews</span>
                                        @endif
                                    </td>
                                   
                                    <td> @if($candidate->interviews){{ $candidate->interviews->scheduled_at->format('Y-m-d H:i') }} @else
                                        <span class="text-muted">No Date</span>
                                    @endif</td>
                                    
                                  
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