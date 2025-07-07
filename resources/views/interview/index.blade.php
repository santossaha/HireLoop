@extends('layouts.app')

@section('title', 'Interviews')

@section('content')
<div class="container-fluid">
    {{-- <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Interviews</h1>
        <a href="{{ route('interviews.create') }}" class="btn btn-primary">
            <i class="fas fa-calendar-plus me-1"></i> Schedule New Interview
        </a>
    </div> --}}



    <div class="row mb-3">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Upcoming Interviews</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="upcomingCount">{{ $stats['upcoming'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pass Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="passRate">{{ $stats['pass_rate'] }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Interviews This Week</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="thisWeekCount">{{ $stats['this_week'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Client Interviews</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="clientInterviewsCount">{{ $stats['client_interviews'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Interviews Filter</h6>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label for="vendor_id" class="form-label">Vendor</label>
                    <select class="form-select" id="vendor_id" name="vendor_id">
                        <option value="">All Vendors</option>
                        @foreach(App\Models\Vendor::orderBy('company_name')->get() as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">Interview Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="mock">Mock</option>
                        <option value="internal">Internal</option>
                        <option value="client">Client</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="result" class="form-label">Result</label>
                    <select class="form-select" id="result" name="result">
                        <option value="">All Results</option>
                        <option value="pass">Pass</option>
                        <option value="fail">Fail</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" id="applyFilter" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <button type="button" id="resetFilter" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Interviews</h6>
            
            <div class="filtr btn-group">
                <button type="button" class="btn btn-sm btn-outline-primary up_btn" data-status="scheduled">Upcoming</button>
                <button type="button" class="btn btn-sm btn-outline-success ct_btn" data-status="completed">Completed</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="interviewsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vendor</th>
                            <th>Requirement</th>
                            <th>Date & Time</th>
                            <th>Candidate</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Result</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#interviewsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('interviews.index') }}",
            data: function(d) {
                d.vendor_id = $('#vendor_id').val();
                d.type = $('#type').val();
                d.status = $('#status').val();
                d.result = $('#result').val();
            }
        },
        columns: [
            { data: 'id' },
            { data: 'vendor' },
            { data: 'requirement' },
            { data: 'scheduled_at' },
            { data: 'candidate' },
            { data: 'type' },
            { data: 'status' },
            { data: 'result' },
            { data: 'actions', orderable: false, searchable: false }
        ],
        // order: [[3, 'asc']] // Sort by scheduled_at by default
        ordering:true,
    });

    // Apply filter button click handler
    $('#applyFilter').click(function() {
        table.ajax.reload();
        updateStatistics();
    });

    // Reset filter button click handler
    $('#resetFilter').click(function() {
        $('#filterForm select').val('');
        table.ajax.reload();
        updateStatistics();
        $('.up_btn').removeClass('btn-primary');
        $('.up_btn').addClass('btn-outline-primary');
        $('.ct_btn').removeClass('btn-success');
        $('.ct_btn').addClass('btn-outline-success');
    });

    // Status filter buttons click handler
    $('.filtr.btn-group button').click(function() {
        $('.filtr button').removeClass('btn-primary btn-success');
        // .addClass('btn-outline-primary btn-outline-success');
        // $(this).removeClass('btn-outline-primary btn-outline-success');
        if ($(this).data('status') === 'scheduled') {
            $('.up_btn').addClass('btn-primary');
            $('.up_btn').removeClass('btn-outline-primary');
            $('.ct_btn').addClass('btn-outline-success');
        } else {
            $('.ct_btn').addClass('btn-success');
            $('.ct_btn').removeClass('btn-outline-success');
            $('.up_btn').addClass('btn-outline-primary');
        }
        $('#status').val($(this).data('status'));
        table.ajax.reload();
        updateStatistics();
    });

    // Function to update statistics
    function updateStatistics() {
        $.ajax({
            url: "{{ route('interviews.stats') }}",
            data: {
                vendor_id: $('#vendor_id').val(),
                type: $('#type').val(),
                status: $('#status').val(),
                result: $('#result').val()
            },
            success: function(response) {
                $('#upcomingCount').text(response.upcoming);
                $('#passRate').text(response.pass_rate + '%');
                $('#thisWeekCount').text(response.this_week);
                $('#clientInterviewsCount').text(response.client_interviews);
            }
        });
    }
});
</script>
@endsection
