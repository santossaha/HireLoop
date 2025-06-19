@extends('layouts.app')

@section('title', 'Billing Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Billing Management</h3>
                    {{-- <div class="card-tools">
                        <a href="{{ route('billing.export') }}?month={{ $selectedMonth }}" class="btn btn-sm btn-success">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div> --}}
                </div>
                <div class="card-body">
                    <!-- Month Filter -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            {{-- <form method="GET" action="{{ route('billing.index') }}" class="form-inline">
                                <div class="input-group">
                                    <select name="month" class="form-control" onchange="this.form.submit()">
                                        @foreach($months as $value => $label)
                                            <option value="{{ $value }}" {{ $selectedMonth == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                    </div>
                                </div>
                            </form> --}}
                        </div>
                        <div class="col-md-8 text-right">
                            <span class="text-muted">
                                Showing billing for: <strong>{{ \Carbon\Carbon::parse($selectedMonth)->format('F Y') }}</strong>
                            </span>
                        </div>
                    </div>

                    <!-- Billing Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="billingTable">
                            <thead>
                                <tr>
                                    <th>Requirement ID</th>
                                    <th>Vendor</th>
                                    <th>Candidate Name</th>
                                    <th>Month Year</th>
                                    {{-- <th>Working Days</th> --}}
                                    <th>Leave Days</th>
                                    <th>Net Days</th>
                                    <th>Total Salary</th>
                                    <th>Pay Salary</th>
                                    <th>GST Amount</th>
                                    {{-- <th>Status</th>
                                    <th>Actions</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($billings as $billing)
                                <tr>
                                    <td>
                                        {{-- <a href="{{ route('requirement.show', $billing->requirement_id) }}" target="_blank"> --}}
                                            {{ $billing->requirement_id }}
                                        {{-- </a> --}}
                                    </td>
                                    <td>
                                        <div>{{ $billing->vendor_name ?? 'N/A' }}</div>
                                        @if($billing->vendor_email)
                                            <small class="text-muted">{{ $billing->vendor_email }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $billing->candidate_name ?? 'N/A' }}</div>
                                        @if($billing->candidate_email)
                                            <small class="text-muted">{{ $billing->candidate_email }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $billing->month_name }} {{ $billing->year }}</td>
                                    
                                    {{-- <td>{{ $billing->total_working_days }}</td> --}}
                                    <td>{{ $billing->total_leave_days }}</td>
                                    <td>{{ $billing->net_working_days }}</td>
                                    <td>{{ $billing->final_budget }}</td>
                                    <td>{{ $billing->formatted_monthly_salary }}</td>
                                    <td>{{ $billing->gst_amount }}</td>
                                    {{-- <td>
                                        <span class="badge {{ $billing->status_badge }}">
                                            {{ ucfirst($billing->status) }}
                                        </span>
                                    </td> --}}
                                    {{-- <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('billing.show', $billing->id) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($billing->status === 'pending')
                                                <button type="button" 
                                                        class="btn btn-sm btn-success" 
                                                        onclick="approveBilling({{ $billing->id }})"
                                                        title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        onclick="rejectBilling({{ $billing->id }})"
                                                        title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            
                                            @if($billing->status === 'approved')
                                                <button type="button" 
                                                        class="btn btn-sm btn-primary" 
                                                        onclick="markAsPaid({{ $billing->id }})"
                                                        title="Mark as Paid">
                                                    <i class="fas fa-money-bill"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td> --}}
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="12" class="text-center">No billing records found for this month.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Billing</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="remarks">Rejection Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#billingTable').DataTable({
        "pageLength": 25,
        "order": [[0, "desc"]],
        "responsive": true,
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "infoFiltered": "(filtered from _MAX_ total entries)"
        }
    });
});

function approveBilling(billingId) {
    if (confirm('Are you sure you want to approve this billing?')) {
        window.location.href = `/billing/${billingId}/approve`;
    }
}

function rejectBilling(billingId) {
    $('#rejectForm').attr('action', `/billing/${billingId}/reject`);
    $('#rejectModal').modal('show');
}

function markAsPaid(billingId) {
    if (confirm('Are you sure you want to mark this billing as paid?')) {
        window.location.href = `/billing/${billingId}/mark-as-paid`;
    }
}
</script>
@endpush

@push('styles')
<style>
.badge {
    font-size: 0.8em;
}
.btn-group .btn {
    margin-right: 2px;
}
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
</style>
@endpush 