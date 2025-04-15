@extends('layouts.app')

@section('title', 'Edit Interview')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Interview</h1>
        <a href="{{ route('interviews.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Interviews
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Interview Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('interviews.update', $interview->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="vendor_id" class="form-label">Vendor <span class="text-danger">*</span></label>
                        <select id="vendor_id" name="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror" required>
                            <option value="">Select Vendor</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('vendor_id', $interview->vendor_id) == $vendor->id ? 'selected' : '' }}>
                                    {{ $vendor->company_name }} ({{ ucfirst($vendor->vendor_type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('vendor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="requirement_id" class="form-label">Requirement <span class="text-danger">*</span></label>
                        <select id="requirement_id" name="requirement_id" class="form-select @error('requirement_id') is-invalid @enderror" required>
                            <option value="">Select Requirement</option>
                            @foreach($requirements as $requirement)
                                <option value="{{ $requirement->id }}" {{ old('requirement_id', $interview->requirement_id) == $requirement->id ? 'selected' : '' }}>
                                    {{ $requirement->requirement_id }} - {{ $requirement->department->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('requirement_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="type" class="form-label">Interview Type <span class="text-danger">*</span></label>
                        <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">Select Type</option>
                            <option value="mock" {{ old('type', $interview->type) == 'mock' ? 'selected' : '' }}>Mock Interview</option>
                            <option value="internal" {{ old('type', $interview->type) == 'internal' ? 'selected' : '' }}>Internal Interview</option>
                            <option value="client" {{ old('type', $interview->type) == 'client' ? 'selected' : '' }}>Client Interview</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="interviewer_id" class="form-label">Interviewer <span class="text-danger">*</span></label>
                        <select id="interviewer_id" name="interviewer_id" class="form-select @error('interviewer_id') is-invalid @enderror" required>
                            <option value="">Select Interviewer</option>
                            @foreach($interviewers as $interviewer)
                                <option value="{{ $interviewer->id }}" {{ old('interviewer_id', $interview->interviewer_id) == $interviewer->id ? 'selected' : '' }}>
                                    {{ $interviewer->name }} {{ $interviewer->department ? '(' . $interviewer->department->name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('interviewer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="scheduled_at" class="form-label">Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at', $interview->scheduled_at->format('Y-m-d\TH:i')) }}" required>
                        @error('scheduled_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="scheduled" {{ old('status', $interview->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="completed" {{ old('status', $interview->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status', $interview->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> To record interview results and feedback, please mark the interview as "Completed" and submit feedback from the interview details page.
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('interviews.show', $interview->id) }}" class="btn btn-secondary me-md-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Interview</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Filter requirements based on selected vendor
        $('#vendor_id').change(function() {
            const vendorId = $(this).val();
            
            // Clear and disable requirements dropdown if no vendor is selected
            if (!vendorId) {
                $('#requirement_id').html('<option value="">Select Requirement</option>').prop('disabled', true);
                return;
            }
            
            // Save current selection if possible
            const currentRequirement = $('#requirement_id').val();
            
            // Filter requirements to only show those belonging to selected vendor
            const requirementOptions = [
                '<option value="">Select Requirement</option>'
            ];
            
            @foreach($requirements as $requirement)
                if ('{{ $requirement->vendor_id }}' == vendorId) {
                    const selected = currentRequirement == '{{ $requirement->id }}' ? 'selected' : '';
                    requirementOptions.push(`<option value="{{ $requirement->id }}" ${selected}>{{ $requirement->requirement_id }} - {{ $requirement->department->name ?? 'N/A' }}</option>`);
                }
            @endforeach
            
            $('#requirement_id').html(requirementOptions.join('')).prop('disabled', false);
        });
        
        // Trigger vendor change event on page load
        $('#vendor_id').trigger('change');
    });
</script>
@endsection
