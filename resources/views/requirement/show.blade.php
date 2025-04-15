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

    <div class="row">
        <div class="col-lg-8">
            <!-- Requirement Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Requirement Information</h6>
                    <div>
                        @if($requirement->status == 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @elseif($requirement->founder_approved && $requirement->hod_approved)
                            <span class="badge bg-success">Fully Approved</span>
                        @elseif($requirement->hod_approved)
                            <span class="badge bg-warning">HOD Approved</span>
                        @else
                            <span class="badge bg-secondary">Pending HOD Approval</span>
                        @endif
                    </div>
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
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Client Budget</h5>
                            <p>${{ number_format($requirement->client_budget, 2) }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Proposed Budget</h5>
                            <p>${{ number_format($requirement->proposed_budget, 2) }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">Job Description</h5>
                        <div class="p-3 bg-light rounded">
                            {!! nl2br(e($requirement->job_description)) !!}
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="font-weight-bold">CV File</h5>
                        <a href="#" class="btn btn-sm btn-primary">
                            <i class="fas fa-download me-1"></i> Download CV
                        </a>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Submitted On</h5>
                            <p>{{ $requirement->created_at->format('F d, Y H:i') }}</p>
                        </div>
                        @if($requirement->approved_at)
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Approved On</h5>
                            <p>{{ $requirement->approved_at->format('F d, Y H:i') }} by {{ $requirement->approvedBy->name ?? 'N/A' }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Interviews -->
            @if($requirement->interviews->count() > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Related Interviews</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Interviewer</th>
                                    <th>Status</th>
                                    <th>Result</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requirement->interviews as $interview)
                                <tr>
                                    <td>{{ $interview->scheduled_at->format('M d, Y H:i') }}</td>
                                    <td>{{ ucfirst($interview->type) }}</td>
                                    <td>{{ $interview->interviewer->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($interview->status == 'scheduled')
                                            <span class="badge bg-primary">Scheduled</span>
                                        @elseif($interview->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @else
                                            <span class="badge bg-danger">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($interview->result == 'pass')
                                            <span class="badge bg-success">Pass</span>
                                        @elseif($interview->result == 'fail')
                                            <span class="badge bg-danger">Fail</span>
                                        @else
                                            <span class="badge bg-secondary">Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('interviews.show', $interview->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Approval Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Approval Status</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            HOD Approval
                            @if($requirement->hod_approved)
                                <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i></span>
                            @else
                                <span class="badge bg-secondary rounded-pill"><i class="fas fa-hourglass"></i></span>
                            @endif
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Founder Approval
                            @if($requirement->founder_approved)
                                <span class="badge bg-success rounded-pill"><i class="fas fa-check"></i></span>
                            @elseif($requirement->hod_approved)
                                <span class="badge bg-warning rounded-pill"><i class="fas fa-hourglass"></i></span>
                            @else
                                <span class="badge bg-secondary rounded-pill"><i class="fas fa-lock"></i></span>
                            @endif
                        </li>
                    </ul>

                    <!-- HOD Approval Form -->
                    @if(auth()->user()->isHod() && auth()->user()->department_id == $requirement->department_id && !$requirement->hod_approved && !($requirement->status == 'rejected'))
                    <div class="card mb-4">
                        <div class="card-header py-3 bg-warning">
                            <h6 class="m-0 font-weight-bold text-dark">HOD Approval Required</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('requirements.hodApprove', $requirement->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="comments" class="form-label">Comments (Optional)</label>
                                    <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" name="approve" value="1" class="btn btn-success mb-2">
                                        <i class="fas fa-check me-1"></i> Approve
                                    </button>
                                    <button type="submit" name="approve" value="0" class="btn btn-danger">
                                        <i class="fas fa-times me-1"></i> Reject
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- Founder Approval Form -->
                    @if(auth()->user()->isFounder() && $requirement->hod_approved && !$requirement->founder_approved && !($requirement->status == 'rejected'))
                    <div class="card mb-4">
                        <div class="card-header py-3 bg-warning">
                            <h6 class="m-0 font-weight-bold text-dark">Founder Approval Required</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('requirements.founderApprove', $requirement->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="comments" class="form-label">Comments (Optional)</label>
                                    <textarea class="form-control" id="comments" name="comments" rows="3"></textarea>
                                </div>
                                <div class="d-grid gap-2">
                                    <button type="submit" name="approve" value="1" class="btn btn-success mb-2">
                                        <i class="fas fa-check me-1"></i> Approve
                                    </button>
                                    <button type="submit" name="approve" value="0" class="btn btn-danger">
                                        <i class="fas fa-times me-1"></i> Reject
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                    @if($requirement->isApproved())
                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle me-2"></i> This requirement has been fully approved and can be shared with the client.
                    </div>
                    @elseif($requirement->status == 'rejected')
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-times-circle me-2"></i> This requirement has been rejected.
                    </div>
                    @endif
                </div>
            </div>

            <!-- Schedule Interview Card -->
            @if($requirement->isApproved())
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Schedule Interview</h6>
                </div>
                <div class="card-body">
                    <p>Ready to proceed with the interview process?</p>
                    <a href="{{ route('interviews.create', ['requirement_id' => $requirement->id, 'vendor_id' => $requirement->vendor_id]) }}" class="btn btn-success btn-block">
                        <i class="fas fa-calendar-plus me-1"></i> Schedule Interview
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
