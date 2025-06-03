@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Onboarding Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('onboardings.index') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Requirement ID</th>
                                    <td>{{ $onboarding->requirement->requirement_id ?? $onboarding->requirement_id ??  'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Vendor Name</th>
                                    <td>{{ $onboarding->vendor->company_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Candidate Name</th>
                                    <td>{{ $onboarding->candidate->candidate_name ?? $onboarding->candidate_id ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Client Budget</th>
                                    <td>{{ number_format($onboarding->client_budget, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Final Budget</th>
                                    <td>{{ number_format($onboarding->final_budget, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Timesheet Link</th>
                                    <td>
                                        @if($onboarding->timesheet_link)
                                            <a href="{{ $onboarding->timesheet_link }}" target="_blank">View Timesheet</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Delivery Manager</th>
                                    <td>{{ $onboarding->delivery_manager_name }}</td>
                                </tr>
                                <tr>
                                    <th>Start Date</th>
                                    <td>{{ $onboarding->start_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Billing Term</th>
                                    <td>{{ $onboarding->billing_term }}</td>
                                </tr>
                                <tr>
                                    <th>Cycle Date</th>
                                    <td>{{ $onboarding->cycle_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Project Type</th>
                                    <td>{{ ucfirst($onboarding->project_type) }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>{{ $onboarding->status }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('onboardings.edit', $onboarding->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#endHiringModal">
                        <i class="fas fa-stop-circle"></i> End Hiring
                    </button>
                    <form action="{{ route('onboardings.destroy', $onboarding->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this onboarding?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- End Hiring Modal -->
<div class="modal fade" id="endHiringModal" tabindex="-1" aria-labelledby="endHiringModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="endHiringModalLabel">End Hiring</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('onboardings.end-hiring', $onboarding->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                            id="end_date" name="end_date" value="{{ old('end_date', $onboarding->end_date?->format('Y-m-d')) }}" required>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="end_reason_id" class="form-label">Reason</label>
                        <select class="form-select @error('end_reason_id') is-invalid @enderror" 
                            id="end_reason_id" name="end_reason_id" required>
                            <option value="">Select Reason</option>
                            @foreach($endReasons as $reason)
                                <option value="{{ $reason->id }}" {{ old('end_reason_id', $onboarding->end_reason_id) == $reason->id ? 'selected' : '' }}>
                                    {{ $reason->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('end_reason_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="end_comments" class="form-label">Comments</label>
                        <textarea class="form-control @error('end_comments') is-invalid @enderror" 
                            id="end_comments" name="end_comments" rows="3">{{ old('end_comments', $onboarding->end_comments) }}</textarea>
                        @error('end_comments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 