@extends('layouts.app')

@section('title', 'Billing Management')

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
                                    <th>Leave Days</th>
                                    <th>Net Days</th>
                                    <th>Total Salary</th>
                                    <th>GST Amount</th>
                                    <th>Pay Salary</th>
                                    <th>Status</th>
                                    <th>Actions</th>
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
                                    <td>{{ $billing->monthly_salary_without_gst }}</td>
                                    <td>{{ $billing->gst_amount }}</td>
                                    <td>{{ $billing->formatted_monthly_salary }}</td>
                                    <td>
                                        <span class="badge {{ get_status_badge_class($billing->status) }}">{{ ucfirst($billing->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('billing.show', $billing->id) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                           @if($billing->status === 'pending')
                                                <a href="javascript:void(0);" 
                                                   class="btn btn-sm btn-success action-btn" 
                                                   data-action="approve" 
                                                   data-id="{{ $billing->id }}"
                                                   title="Approve">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a href="javascript:void(0);" 
                                                   class="btn btn-sm btn-danger action-btn" 
                                                   data-action="reject" 
                                                   data-id="{{ $billing->id }}"
                                                   title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            @endif
                                            
                                            @if($billing->status === 'approved')
                                                <a href="javascript:void(0);" 
                                                   class="btn btn-sm btn-primary action-btn" 
                                                   data-action="mark-as-paid" 
                                                   data-id="{{ $billing->id }}"
                                                   title="Mark as Paid">
                                                    <i class="fas fa-money-bill"></i>
                                                </a>
                                            @endif
                                        </div>


                                    </td>
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


<!-- Confirmation Modal -->
<div class="modal fade" id="actionModal" tabindex="-1" role="dialog" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form id="actionForm" method="POST">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="actionModalLabel">Confirm Action</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p id="actionModalMessage"></p>
            <div id="remarksGroup" class="form-group" style="display:none;">
              <label for="remarks">Rejection Remarks</label>
              <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="actionModalConfirmBtn">Yes</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            
          </div>
        </div>
      </form>
    </div>
  </div>


@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#billingTable').DataTable({
        "pageLength": 25,
        "order": [[0, "desc"]],
        "responsive": true,
        "language": {
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "infoEmpty": "Showing 0 to 0 of 0 entries",
            "infoFiltered": "(filtered from _MAX_ total entries)"
        },
        "dom": 'ltip' // Remove the search box and entries per page ("f" = filter input, "l" = length menu)
    });
});

$(document).on('click', '.action-btn', function() {
    
    var action = $(this).data('action');
    var billingId = $(this).data('id');
    var modal = $('#actionModal');
    var remarksGroup = $('#remarksGroup');
    var remarks = $('#remarks');
    var form = $('#actionForm');
    var message = '';
    remarksGroup.hide();
    remarks.val('');

    if (action === 'approve') {
        message = 'Are you sure you want to approve this billing?';
        form.data('action', 'approve');
    } else if (action === 'reject') {
        message = 'Are you sure you want to reject this billing? Please provide remarks.';
        remarksGroup.show();
        form.data('action', 'reject');
    } else if (action === 'mark-as-paid') {
        message = 'Are you sure you want to mark this billing as paid?';
        form.data('action', 'mark-as-paid');
    }
    form.data('id', billingId);
    $('#actionModalMessage').text(message);
    modal.modal('show');
});

// Handle form submit
$('#actionForm').submit(function(e) {
    console.log('clicked');
    e.preventDefault();
    var action = $(this).data('action');
    var billingId = $(this).data('id');
    var url = '';
    var data = {_token: $('meta[name="csrf-token"]').attr('content')};


    if (action === 'approve') {
        url = '/billing/' + billingId + '/approve';
        data._method = 'PATCH';
    } else if (action === 'reject') {
        url = '/billing/' + billingId + '/reject';
        data._method = 'PATCH';
        data.remarks = $('#remarks').val();
    } else if (action === 'mark-as-paid') {
        url = '/billing/' + billingId + '/mark-as-paid';
        data._method = 'PATCH';
    }

    console.log(url);   

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function(response) {
            $('#actionModal').modal('hide');
            // Option 1: Reload the page
            location.reload();
            // Option 2: Update the row/status in the table (advanced)
        },
        error: function(xhr) {
            alert('An error occurred. Please try again.');
        }
    });
});
</script>
@endsection 

