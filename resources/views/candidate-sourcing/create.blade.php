@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Upload Candidate Details</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('candidate-sourcing.store', $requirement->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="candidate_name" class="form-label">Candidate Name</label>
                            <input type="text" class="form-control @error('candidate_name') is-invalid @enderror" 
                                id="candidate_name" name="candidate_name" value="{{ old('candidate_name') }}" required>
                            @error('candidate_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                id="phone" name="phone" value="{{ old('phone') }}" required>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="resume" class="form-label">Resume</label>
                            <input type="file" class="form-control @error('resume') is-invalid @enderror" 
                                id="resume" name="resume" required>
                            @error('resume')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX (Max size: 2MB)</small>
                        </div>

                        <div class="mb-3">
                            <label for="candidate_details" class="form-label">Additional Details</label>
                            <textarea class="form-control @error('candidate_details') is-invalid @enderror" 
                                id="candidate_details" name="candidate_details" rows="4">{{ old('candidate_details') }}</textarea>
                            @error('candidate_details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <h5>Requirement Details</h5>
                            <div class="card">
                                <div class="card-body">
                                    <p><strong>Job Description:</strong> {{ $requirement->job_description }}</p>
                                    <p><strong>Client Budget:</strong> ${{ number_format($requirement->client_budget, 2) }}</p>
                                    <p><strong>Department:</strong> {{ $requirement->department->name }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Upload Candidate</button>
                            <a href="{{ route('candidate-sourcing.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 