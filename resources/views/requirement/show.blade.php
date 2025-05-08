@extends('layouts.app')

@section('title', 'Requirement Details')


@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Requirement Details</h1>
        <div>
            
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
                            <th>Company</th>
                            <th>Department</th>
                            <th>Created By</th>
                        </tr>
                        <tr>
                            <td>{{ $requirement->requirement_id }}</td>
                            <td>{{ $requirement->company->name ?? 'N/A' }}</td>
                            <td>{{ $requirement->department->name ?? 'N/A' }}</td>
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
                            <th>Vendors</th>
                            <th>Status</th>
                            <th>Interview Date</th>
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
                                {{-- <span class="badge bg-{{ $candidate->status === 'pending' ? 'warning' : ($candidate->status === 'approved' ? 'success' : 'danger') }}">
                                    {{ ucfirst($candidate->status) }}
                                </span> --}}
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
                            @if($candidate->interviews)
                            <td>{{ $candidate->interviews->scheduled_at->format('M d, Y  h:i a')}}</td>
                            @else
                            <td>N/A</td>
                            @endif
                            <td>{{ $candidate->created_at->format('M d, Y  h:i a') }}</td>
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
                <h5 class="modal-title" id="viewResumeModalLabel{{ $candidate->id }}">
                     - {{ $candidate->candidate_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered">
                            <tr>
                                <th>Requirement ID</th>
                                <th>Company</th>
                                <th>Department</th>
                                <th>Created By</th>
                               
                            </tr>
                            <tr>
                                <td>{{ $requirement->requirement_id }}</td>
                                <td>{{ $requirement->company->name }}</td>
                                <td>{{ $requirement->department->name }}</td>
                                <td>{{ $requirement->createBy->name ?? 'N/A' }}</td>
                                
                            </tr>
                        </table>
                    </div>
                </div>
               

                @if($candidate->candidate_details)
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="font-weight-bold">Additional Notes</h6>
                        <div class="p-3 bg-light rounded">
                            <textarea name="" id="" cols="100" rows="2" class="form-control" readonly>{!! $candidate->candidate_details !!}</textarea>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row mt-4">
                    <div class="col-12">
                        {{-- @php
                            $hasScheduledInterview = $candidate->interviewSchedule && $candidate->interviewSchedule->proceed_for_mock;
                        @endphp --}}
                        {{-- @if($hasScheduledInterview)
                            <button type="button" class="btn btn-danger" disabled>
                                <i class="fas fa-times-circle"></i> Interview Scheduled
                            </button>
                        @else
                            <button type="button" class="btn btn-success" id="proceedForMockBtn{{ $candidate->id }}" onclick="showDateTimeField({{ $candidate->id }})">
                                <i class="fas fa-calendar-plus"></i> Proceed for Mock
                            </button>
                        @endif --}}
                    </div>
                </div>

                {{-- <div class="row mt-3" id="mockDateTime{{ $candidate->id }}" style="display: none;">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="mockDateTimeInput{{ $candidate->id }}">Select Date and Time</label>
                            <input type="datetime-local" class="form-control" id="mockDateTimeInput{{ $candidate->id }}" name="mock_datetime">
                        </div>
                    </div>
                </div> --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('interviews.create', ['candidate_id' => $candidate->id, 'requirement_id' => $requirement->id]) }}" class="btn btn-primary">Schedule Interview</a>
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
                    <textarea name="" id="" cols="100" rows="10" class="form-control" readonly>{!! $requirement->job_description !!}</textarea>
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
    // function toggleDescription() {
    //     const shortDesc = document.getElementById('shortDescription');
    //     const fullDesc = document.getElementById('fullDescription');
    //     const readMoreBtn = document.getElementById('readMoreBtn');

    //     if (shortDesc.style.display !== 'none') {
    //         shortDesc.style.display = 'none';
    //         fullDesc.style.display = 'block';
    //         readMoreBtn.textContent = 'Show Less';
    //     } else {
    //         shortDesc.style.display = 'block';
    //         fullDesc.style.display = 'none';
    //         readMoreBtn.textContent = 'Read More';
    //     }
    // }

    // $(document).ready(function() {
    //     $('#candidatesTable').DataTable({
    //         "order": [[6, "desc"]], // Sort by uploaded date by default
    //         "pageLength": 10,
    //         "language": {
    //             "search": "Search candidates:"
    //         }
    //     });
    // });

    // function showDateTimeField(candidateId) {
    //     const dateTimeDiv = document.getElementById('mockDateTime' + candidateId);
    //     dateTimeDiv.style.display = 'block';
    // }

    // function submitMockSchedule(candidateId) {
    //     const mockDateTime = document.getElementById('mockDateTimeInput' + candidateId).value;
       
    //     if (!mockDateTime) {
    //         alert('Please select date and time for mock interview');
    //         return;
    //     }

    //     // Format the date to ensure it's in the correct format for Laravel
    //     const formattedDateTime = new Date(mockDateTime).toISOString().slice(0, 19).replace('T', ' ');

    //     // Send AJAX request to save the schedule
    //     $.ajax({
           
    //         type: 'POST', 
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         data: {
    //             candidate_id: candidateId,
    //             requirement_id: {{ $requirement->id }},
    //             proceed_for_mock: 1,
    //             mock_datetime: formattedDateTime
    //         },
    //         success: function(response) {
    //             console.log(response);
    //             if (response.success) {
    //                 // Update button appearance
    //                 const button = document.getElementById('proceedForMockBtn' + candidateId);
    //                 button.className = 'btn btn-danger';
    //                 button.disabled = true;
    //                 button.innerHTML = '<i class="fas fa-times-circle"></i> Interview Scheduled';
                    
    //                 // Close the modal
    //                 const modal = document.getElementById('viewResumeModal' + candidateId);
    //                 const modalInstance = bootstrap.Modal.getInstance(modal);
    //                 modalInstance.hide();
                    
    //                 alert('Interview scheduled successfully');
    //             } else {
    //                 alert('Error scheduling interview');
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             console.error('Error:', error);
    //             if (xhr.status === 422) {
    //                 const errors = xhr.responseJSON.errors;
    //                 let errorMessage = 'Validation errors:\n';
    //                 for (let field in errors) {
    //                     errorMessage += errors[field].join('\n') + '\n';
    //                 }
    //                 alert(errorMessage);
    //             } else {
    //                 alert('Error scheduling interview');
    //             }
    //         }
    //     });
    // }
</script>
@endsection
