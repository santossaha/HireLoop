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
                                <tr>
                                    <th>Total Days Worked</th>
                                    <td>{{ $onboarding->getWorkingDays() }} days</td>
                                </tr>
                                <tr>
                                    <th>Total Leaves</th>
                                    <td>{{ $onboarding->getTotalLeaves() }} days</td>
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
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#leaveModal">
                        <i class="fas fa-calendar-alt"></i> Apply Leave
                    </button>
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

<!-- Leave Modal -->
<div class="modal fade" id="leaveModal" tabindex="-1" aria-labelledby="leaveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveModalLabel">Apply Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('onboardings.leaves.store', $onboarding->id) }}" method="POST" id="leaveForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="from_date" class="form-label">From Date</label>
                                <input type="date" class="form-control @error('from_date') is-invalid @enderror" 
                                    id="from_date" name="from_date" required>
                                @error('from_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="to_date" class="form-label">To Date</label>
                                <input type="date" class="form-control @error('to_date') is-invalid @enderror" 
                                    id="to_date" name="to_date" required>
                                @error('to_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div id="totalDaysDisplay" class="alert alert-info" style="display: none;">
                            Total Leave Days: <span id="totalDays">0</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason</label>
                        <input type="text" class="form-control @error('reason') is-invalid @enderror" 
                            id="reason" name="reason" required>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3" id="halfDayContainer" style="display: none;">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_half_day" name="is_half_day" value="1">
                            <label class="form-check-label" for="is_half_day">
                                Half Day Leave
                            </label>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h6>Leave History</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Reason</th>
                                        <th>Days</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($onboarding->leaves as $leave)
                                    <tr>
                                        <td>{{ $leave->from_date->format('d M Y') }}</td>
                                        <td>{{ $leave->to_date->format('d M Y') }}</td>
                                        <td>{{ $leave->reason }}</td>
                                        <td>{{ $leave->total_days }}</td>
                                        <td>
                                            <button type="button" class="btn btn-danger btn-sm delete-leave" 
                                                data-onboarding-id="{{ $onboarding->id }}" 
                                                data-leave-id="{{ $leave->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Apply Leave</button>
                </div>
            </form>
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

<!-- Leave Delete Modal -->
<div class="modal fade" id="deleteLeaveModal" tabindex="-1" aria-labelledby="deleteLeaveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLeaveModalLabel">Delete Leave</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this leave?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteLeave">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fromDateInput = document.getElementById('from_date');
    const toDateInput = document.getElementById('to_date');
    const halfDayContainer = document.getElementById('halfDayContainer');
    const isHalfDayCheckbox = document.getElementById('is_half_day');

    // Set minimum date for from_date to today
    fromDateInput.min = new Date().toISOString().split('T')[0];

    fromDateInput.addEventListener('change', function() {
        // Set minimum date for to_date to from_date
        toDateInput.min = this.value;
        
        // If to_date is before from_date, reset it
        if (toDateInput.value && toDateInput.value < this.value) {
            toDateInput.value = this.value;
        }
        
        checkDates();
    });

    toDateInput.addEventListener('change', function() {
        checkDates();
    });

    function checkDates() {
        if (fromDateInput.value && toDateInput.value) {
            if (fromDateInput.value === toDateInput.value) {
                halfDayContainer.style.display = 'block';
                document.getElementById('totalDaysDisplay').style.display = 'block';
                document.getElementById('totalDays').textContent = isHalfDayCheckbox.checked ? '0.5' : '1';
            } else {
                halfDayContainer.style.display = 'none';
                isHalfDayCheckbox.checked = false;
                
                // Calculate days between dates
                const start = new Date(fromDateInput.value);
                const end = new Date(toDateInput.value);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 to include both start and end dates
                
                document.getElementById('totalDaysDisplay').style.display = 'block';
                document.getElementById('totalDays').textContent = diffDays;
            }
        } else {
            document.getElementById('totalDaysDisplay').style.display = 'none';
        }
    }

    // Add event listener for half day checkbox
    isHalfDayCheckbox.addEventListener('change', function() {
        if (fromDateInput.value === toDateInput.value) {
            document.getElementById('totalDays').textContent = this.checked ? '0.5' : '1';
        }
    });

    // Delete Leave functionality
    const deleteButtons = document.querySelectorAll('.delete-leave');
    const deleteLeaveModal = new bootstrap.Modal(document.getElementById('deleteLeaveModal'));
    let currentLeaveId = null;
    let currentOnboardingId = null;

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentLeaveId = this.dataset.leaveId;
            currentOnboardingId = this.dataset.onboardingId;
            deleteLeaveModal.show();
        });
    });

    document.getElementById('confirmDeleteLeave').addEventListener('click', function() {
        if (currentLeaveId && currentOnboardingId) {
            // Send AJAX request
            fetch(`/onboardings/${currentOnboardingId}/leaves/${currentLeaveId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the row from the table
                    const row = document.querySelector(`[data-leave-id="${currentLeaveId}"]`).closest('tr');
                    row.remove();
                    // Show success message
                   // alert('Leave deleted successfully');
                } else {
                    alert(data.message || 'Error deleting leave');
                }
                // Hide the modal
                deleteLeaveModal.hide();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting leave');
                deleteLeaveModal.hide();
            });
        }
    });
});
</script>
@endsection 