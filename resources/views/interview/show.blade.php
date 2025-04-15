@extends('layouts.app')

@section('title', 'Interview Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Interview Details</h1>
        <div>
            @if($interview->status != 'completed')
                <a href="{{ route('interviews.edit', $interview->id) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-1"></i> Edit Interview
                </a>
            @endif
            <a href="{{ route('vendors.show', $interview->vendor_id) }}" class="btn btn-info me-2">
                <i class="fas fa-user me-1"></i> View Vendor
            </a>
            <a href="{{ route('interviews.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Interviews
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Interview Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Interview Information</h6>
                    <div>
                        @if($interview->status == 'scheduled')
                            <span class="badge bg-primary">Scheduled</span>
                        @elseif($interview->status == 'completed')
                            <span class="badge bg-success">Completed</span>
                        @else
                            <span class="badge bg-danger">Cancelled</span>
                        @endif
                        
                        @if($interview->result)
                            <span class="badge bg-{{ $interview->result == 'pass' ? 'success' : 'danger' }}">
                                {{ ucfirst($interview->result) }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Vendor</h5>
                            <p>
                                <a href="{{ route('vendors.show', $interview->vendor_id) }}">
                                    {{ $interview->vendor->company_name }}
                                </a> 
                                ({{ ucfirst($interview->vendor->vendor_type) }})
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Requirement</h5>
                            <p>
                                @if($interview->requirement)
                                    <a href="{{ route('requirements.show', $interview->requirement_id) }}">
                                        {{ $interview->requirement->requirement_id }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Interview Type</h5>
                            <p>{{ ucfirst($interview->type) }} Interview</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Date & Time</h5>
                            <p>{{ $interview->scheduled_at->format('F d, Y H:i') }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Interviewer</h5>
                            <p>{{ $interview->interviewer->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="font-weight-bold">Status</h5>
                            <p>
                                @if($interview->status == 'scheduled')
                                    <span class="text-primary">Scheduled</span>
                                @elseif($interview->status == 'completed')
                                    <span class="text-success">Completed</span>
                                @else
                                    <span class="text-danger">Cancelled</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($interview->status == 'completed')
                        <hr>
                        
                        <h5 class="font-weight-bold mb-3">Interview Results</h5>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Result</h6>
                                <p>
                                    @if($interview->result == 'pass')
                                        <span class="text-success">Pass</span>
                                    @elseif($interview->result == 'fail')
                                        <span class="text-danger">Fail</span>
                                    @else
                                        <span class="text-secondary">Pending</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Last Approved Budget</h6>
                                <p>${{ number_format($interview->last_approved_budget ?? 0, 2) }}</p>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Communication Rating</h6>
                                <p>
                                    @if($interview->communication_rating == 'excellent')
                                        <span class="text-success">Excellent</span>
                                    @elseif($interview->communication_rating == 'good')
                                        <span class="text-primary">Good</span>
                                    @elseif($interview->communication_rating == 'average')
                                        <span class="text-warning">Average</span>
                                    @elseif($interview->communication_rating == 'bad')
                                        <span class="text-danger">Bad</span>
                                    @else
                                        <span class="text-secondary">Not rated</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Technical Rating</h6>
                                <p>
                                    @if($interview->technical_rating == 'excellent')
                                        <span class="text-success">Excellent</span>
                                    @elseif($interview->technical_rating == 'good')
                                        <span class="text-primary">Good</span>
                                    @elseif($interview->technical_rating == 'average')
                                        <span class="text-warning">Average</span>
                                    @elseif($interview->technical_rating == 'bad')
                                        <span class="text-danger">Bad</span>
                                    @else
                                        <span class="text-secondary">Not rated</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Client Interview Ready</h6>
                                <p>
                                    @if($interview->client_interview_ready === true)
                                        <span class="text-success">Yes</span>
                                    @elseif($interview->client_interview_ready === false)
                                        <span class="text-danger">No</span>
                                    @else
                                        <span class="text-secondary">Not specified</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Previously Worked with Client</h6>
                                <p>
                                    @if($interview->previously_worked_with_client === true)
                                        <span class="text-success">Yes</span>
                                    @elseif($interview->previously_worked_with_client === false)
                                        <span class="text-danger">No</span>
                                    @else
                                        <span class="text-secondary">Not specified</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Selected in Internal Interview</h6>
                                <p>
                                    @if($interview->selected_in_internal === true)
                                        <span class="text-success">Yes</span>
                                    @elseif($interview->selected_in_internal === false)
                                        <span class="text-danger">No</span>
                                    @else
                                        <span class="text-secondary">Not applicable</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold">Selected in Client Interview</h6>
                                <p>
                                    @if($interview->selected_in_client === true)
                                        <span class="text-success">Yes</span>
                                    @elseif($interview->selected_in_client === false)
                                        <span class="text-danger">No</span>
                                    @else
                                        <span class="text-secondary">Not applicable</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="font-weight-bold">Feedback</h6>
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($interview->feedback)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Submit Feedback Form (if interview is completed and user is the interviewer or admin) -->
            @if($interview->status == 'completed' && !$interview->result && (auth()->user()->id == $interview->interviewer_id || auth()->user()->isAdmin()))
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-success text-white">
                        <h6 class="m-0 font-weight-bold">Submit Interview Feedback</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('interviews.feedback', $interview->id) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="result" class="form-label">Interview Result <span class="text-danger">*</span></label>
                                <select class="form-select @error('result') is-invalid @enderror" id="result" name="result" required>
                                    <option value="">Select Result</option>
                                    <option value="pass" {{ old('result') == 'pass' ? 'selected' : '' }}>Pass</option>
                                    <option value="fail" {{ old('result') == 'fail' ? 'selected' : '' }}>Fail</option>
                                </select>
                                @error('result')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="communication_rating" class="form-label">Communication Rating <span class="text-danger">*</span></label>
                                <select class="form-select @error('communication_rating') is-invalid @enderror" id="communication_rating" name="communication_rating" required>
                                    <option value="">Select Rating</option>
                                    <option value="excellent" {{ old('communication_rating') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="good" {{ old('communication_rating') == 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="average" {{ old('communication_rating') == 'average' ? 'selected' : '' }}>Average</option>
                                    <option value="bad" {{ old('communication_rating') == 'bad' ? 'selected' : '' }}>Bad</option>
                                </select>
                                @error('communication_rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="technical_rating" class="form-label">Technical Rating <span class="text-danger">*</span></label>
                                <select class="form-select @error('technical_rating') is-invalid @enderror" id="technical_rating" name="technical_rating" required>
                                    <option value="">Select Rating</option>
                                    <option value="excellent" {{ old('technical_rating') == 'excellent' ? 'selected' : '' }}>Excellent</option>
                                    <option value="good" {{ old('technical_rating') == 'good' ? 'selected' : '' }}>Good</option>
                                    <option value="average" {{ old('technical_rating') == 'average' ? 'selected' : '' }}>Average</option>
                                    <option value="bad" {{ old('technical_rating') == 'bad' ? 'selected' : '' }}>Bad</option>
                                </select>
                                @error('technical_rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="client_interview_ready" class="form-label">Client Interview Ready <span class="text-danger">*</span></label>
                                <select class="form-select @error('client_interview_ready') is-invalid @enderror" id="client_interview_ready" name="client_interview_ready" required>
                                    <option value="1" {{ old('client_interview_ready') == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ old('client_interview_ready') == '0' ? 'selected' : '' }}>No</option>
                                </select>
                                @error('client_interview_ready')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="feedback" class="form-label">Detailed Feedback <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('feedback') is-invalid @enderror" id="feedback" name="feedback" rows="6" required>{{ old('feedback') }}</textarea>
                                @error('feedback')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            @if($interview->type != 'mock')
                                <div class="mb-3">
                                    <label for="previously_worked_with_client" class="form-label">Previously Worked with Client</label>
                                    <select class="form-select @error('previously_worked_with_client') is-invalid @enderror" id="previously_worked_with_client" name="previously_worked_with_client">
                                        <option value="">Select Option</option>
                                        <option value="1" {{ old('previously_worked_with_client') == '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('previously_worked_with_client') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                    @error('previously_worked_with_client')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            
                            @if($interview->type == 'internal')
                                <div class="mb-3">
                                    <label for="selected_in_internal" class="form-label">Selected in Internal Interview</label>
                                    <select class="form-select @error('selected_in_internal') is-invalid @enderror" id="selected_in_internal" name="selected_in_internal">
                                        <option value="">Select Option</option>
                                        <option value="1" {{ old('selected_in_internal') == '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('selected_in_internal') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                    @error('selected_in_internal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            
                            @if($interview->type == 'client')
                                <div class="mb-3">
                                    <label for="selected_in_client" class="form-label">Selected in Client Interview</label>
                                    <select class="form-select @error('selected_in_client') is-invalid @enderror" id="selected_in_client" name="selected_in_client">
                                        <option value="">Select Option</option>
                                        <option value="1" {{ old('selected_in_client') == '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ old('selected_in_client') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                    @error('selected_in_client')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="last_approved_budget" class="form-label">Last Approved Budget</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" min="0" class="form-control @error('last_approved_budget') is-invalid @enderror" id="last_approved_budget" name="last_approved_budget" value="{{ old('last_approved_budget') }}">
                                    </div>
                                    @error('last_approved_budget')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i> Submit Feedback
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif($interview->status == 'scheduled')
                <!-- Quick Actions Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <a href="{{ route('interviews.edit', $interview->id) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-edit me-1"></i> Edit Interview
                            </a>
                        </div>
                        
                        <form action="{{ route('interviews.update', $interview->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="vendor_id" value="{{ $interview->vendor_id }}">
                            <input type="hidden" name="requirement_id" value="{{ $interview->requirement_id }}">
                            <input type="hidden" name="interviewer_id" value="{{ $interview->interviewer_id }}">
                            <input type="hidden" name="type" value="{{ $interview->type }}">
                            <input type="hidden" name="scheduled_at" value="{{ $interview->scheduled_at->format('Y-m-d\TH:i') }}">
                            <input type="hidden" name="status" value="completed">
                            
                            <div class="mb-3">
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-check-circle me-1"></i> Mark as Completed
                                </button>
                            </div>
                        </form>
                        
                        <form action="{{ route('interviews.update', $interview->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="vendor_id" value="{{ $interview->vendor_id }}">
                            <input type="hidden" name="requirement_id" value="{{ $interview->requirement_id }}">
                            <input type="hidden" name="interviewer_id" value="{{ $interview->interviewer_id }}">
                            <input type="hidden" name="type" value="{{ $interview->type }}">
                            <input type="hidden" name="scheduled_at" value="{{ $interview->scheduled_at->format('Y-m-d\TH:i') }}">
                            <input type="hidden" name="status" value="cancelled">
                            
                            <div class="mb-3">
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-times-circle me-1"></i> Cancel Interview
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
            
            <!-- Vendor Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Vendor Info</h6>
                </div>
                <div class="card-body">
                    <h5>{{ $interview->vendor->company_name }}</h5>
                    <p class="badge bg-{{ $interview->vendor->vendor_type == 'company' ? 'primary' : 'secondary' }}">
                        {{ ucfirst($interview->vendor->vendor_type) }}
                    </p>
                    
                    <hr>
                    
                    <p><strong>Contact Person:</strong> {{ $interview->vendor->contact_person }}</p>
                    <p><strong>Email:</strong> {{ $interview->vendor->email }}</p>
                    <p><strong>Phone:</strong> {{ $interview->vendor->phone }}</p>
                    
                    <div class="mb-3">
                        <strong>Ratings:</strong><br>
                        <span class="badge bg-{{ $interview->vendor->communication_rating == 'excellent' ? 'success' : ($interview->vendor->communication_rating == 'good' ? 'primary' : ($interview->vendor->communication_rating == 'average' ? 'warning' : 'danger')) }}">
                            Communication: {{ ucfirst($interview->vendor->communication_rating ?? 'Not rated') }}
                        </span>
                        <br>
                        <span class="badge bg-{{ $interview->vendor->technical_rating == 'excellent' ? 'success' : ($interview->vendor->technical_rating == 'good' ? 'primary' : ($interview->vendor->technical_rating == 'average' ? 'warning' : 'danger')) }}">
                            Technical: {{ ucfirst($interview->vendor->technical_rating ?? 'Not rated') }}
                        </span>
                    </div>
                    
                    <a href="{{ route('vendors.show', $interview->vendor_id) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-user me-1"></i> View Full Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
