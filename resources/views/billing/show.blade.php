@extends('layouts.app')

@section('title', 'Billing Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Billing Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('billing.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Basic Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Requirement ID:</strong></td>
                                            <td>
                                                {{-- <a href="{{ route('requirement.show', $billing->requirement_id) }}" target="_blank"> --}}
                                                    {{ $billing->requirement_id }}
                                                {{-- </a> --}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Vendor:</strong></td>
                                            <td>
                                                <div>{{ $billing->vendor_name ?? 'N/A' }}</div>
                                                @if($billing->vendor_email)
                                                    <small class="text-muted">{{ $billing->vendor_email }}</small>
                                                @endif
                                                @if($billing->vendor_phone)
                                                    <small class="text-muted d-block">{{ $billing->vendor_phone }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Candidate:</strong></td>
                                            <td>
                                                <div>{{ $billing->candidate_name ?? 'N/A' }}</div>
                                                @if($billing->candidate_email)
                                                    <small class="text-muted">{{ $billing->candidate_email }}</small>
                                                @endif
                                                @if($billing->candidate_phone)
                                                    <small class="text-muted d-block">{{ $billing->candidate_phone }}</small>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Month:</strong></td>
                                            <td>{{ $billing->month_name }} {{ $billing->year }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td>
                                                <span class="badge {{ $billing->status_badge }}">
                                                    {{ ucfirst($billing->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $billing->created_at->format('d M Y, h:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Billing Calculations -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Billing Calculations</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Final Budget:</strong></td>
                                            <td class="text-right">{{ $billing->formatted_final_budget }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Start Date:</strong></td>
                                            <td class="text-right">{{ $billing->start_date->format('d M Y') }}</td>
                                        </tr>
                                        @if($billing->end_date)
                                        <tr>
                                            <td><strong>End Date:</strong></td>
                                            <td class="text-right">{{ $billing->end_date->format('d M Y') }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td><strong>Total Working Days:</strong></td>
                                            <td class="text-right">{{ $billing->total_working_days }} days</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Leave Days:</strong></td>
                                            <td class="text-right">{{ $billing->total_leave_days }} days</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Net Working Days:</strong></td>
                                            <td class="text-right">
                                                <strong>{{ $billing->net_working_days }} days</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Per Day Salary:</strong></td>
                                            <td class="text-right">{{ $billing->formatted_per_day_salary }}</td>
                                        </tr>
                                        <tr class="table-success">
                                            <td><strong>Monthly Salary:</strong></td>
                                            <td class="text-right">
                                                <strong>{{ $billing->formatted_monthly_salary }}</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approval Information -->
                    @if($billing->status !== 'pending')
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Approval Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @if($billing->approved_at)
                                        <div class="col-md-6">
                                            <h6>Approval Details</h6>
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Approved By:</strong></td>
                                                    <td>{{ $billing->approved_by_name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Approved At:</strong></td>
                                                    <td>{{ $billing->approved_at->format('d M Y, h:i A') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        @endif

                                        @if($billing->paid_at)
                                        <div class="col-md-6">
                                            <h6>Payment Details</h6>
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td><strong>Paid By:</strong></td>
                                                    <td>{{ $billing->paid_by_name ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Paid At:</strong></td>
                                                    <td>{{ $billing->paid_at->format('d M Y, h:i A') }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Remarks -->
                    @if($billing->remarks)
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Remarks</h5>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">{{ $billing->remarks }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Actions</h5>
                                    <div class="btn-group" role="group">
                                        @if($billing->status === 'pending')
                                            <button type="button" 
                                                    class="btn btn-success" 
                                                    onclick="approveBilling({{ $billing->id }})">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-danger" 
                                                    onclick="rejectBilling({{ $billing->id }})">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        @endif
                                        
                                        @if($billing->status === 'approved')
                                            <button type="button" 
                                                    class="btn btn-primary" 
                                                    onclick="markAsPaid({{ $billing->id }})">
                                                <i class="fas fa-money-bill"></i> Mark as Paid
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
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
    font-size: 0.9em;
    padding: 0.5em 0.8em;
}
.table-borderless td {
    padding: 0.5rem 0;
}
.card-header {
    background-color: #f8f9fa;
}
.table-success {
    background-color: #d4edda !important;
}
</style>
@endpush 