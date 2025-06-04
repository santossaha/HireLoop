@extends('layouts.app')

@section('title', 'Onboarding List')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Onboarding Filter</h6>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form id="filterForm" class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="reportrange" class="form-label">Date Range</label>
                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="statusFilter" class="form-label">Status</label>
                            <select class="form-select" id="statusFilter" name="status">
                                <option value="">All Status</option>
                                <option value="Yet to Start">Yet to Start</option>
                                <option value="Running">Running</option>
                                <option value="Hold">Hold</option>
                                <option value="Stopped">Stopped</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-primary me-2" id="applyFilter">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" class="btn btn-secondary" id="resetFilter">
                                <i class="fas fa-redo me-1"></i> Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Onboarding List</h6>
                    <a href="{{ route('onboardings.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Create New Onboarding
                    </a>
                </div>

                <div class="card-body">
                   

                    <div class="table-responsive">
                        <table class="table table-bordered" id="onboardingTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Requirement ID</th>
                                    <th>Vendor</th>
                                    <th>Candidate</th>
                                    <th>Delivery Manager</th>
                                    <th>Start Date</th>
                                    <th>Project Type</th>
                                    <th>Client Budget</th>
                                    <th>Final Budget</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this onboarding record?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
$(document).ready(function() {
    // State variables to hold filter values
    var selectedStartDate = null;
    var selectedEndDate = null;
    var selectedStatus = '';
    var filtersApplied = false;

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
        autoUpdateInput: false, // Don't update the input field automatically
        locale: { cancelLabel: 'Clear' }, // Add a clear button
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    // Update the date range display initially
    $('#reportrange span').html('Select Date Range'); // Initial display

    // Then Initialize DataTable
    var table = $('#onboardingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('onboardings.index') }}",
            data: function(d) {
                // Add filters only if they have been applied
                if (filtersApplied) {
                    if (selectedStartDate && selectedEndDate) {
                        d.start_date = selectedStartDate;
                        d.end_date = selectedEndDate;
                    }
                    if (selectedStatus) {
                        d.status = selectedStatus;
                    }
                } else {
                    // Ensure no filter parameters are sent on initial load or after reset
                    delete d.start_date;
                    delete d.end_date;
                    delete d.status;
                }
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'requirement_id', name: 'requirement_id'},
            {data: 'vendor_name', name: 'vendor_name'},
            {data: 'candidate_name', name: 'candidate_name'},
            {data: 'delivery_manager_name', name: 'delivery_manager_name'},
            {data: 'start_date', name: 'start_date'},
            {data: 'project_type', name: 'project_type'},
            {data: 'client_budget', name: 'client_budget'},
            {data: 'final_budget', name: 'final_budget'},
            {data: 'status', name: 'status'},
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="d-flex gap-1"><a href="${data.show_url}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a><a href="${data.edit_url}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a><button type="button" class="btn btn-danger btn-sm delete-btn" data-url="${data.delete_url}"><i class="fas fa-trash"></i></button></div>`;
                }
            },
        ],
        order: [[0, 'desc']],
        search: {
            smart: true,
            caseInsensitive: true
        }
    });

    // Daterange picker apply event
    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        $('#reportrange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
        selectedStartDate = picker.startDate.format('YYYY-MM-DD');
        selectedEndDate = picker.endDate.format('YYYY-MM-DD');
        selectedStatus = $('#statusFilter').val(); // Get status filter value
        filtersApplied = true;
        table.ajax.reload();
    });

    // Daterange picker cancel event (for the Clear button)
    $('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).find('span').html('Select Date Range');
        selectedStartDate = null;
        selectedEndDate = null;
        selectedStatus = $('#statusFilter').val(); // Keep status filter value
        filtersApplied = selectedStatus !== ''; // Only apply filters if status is selected
        table.ajax.reload();
    });

    // Apply Filter button or Status filter change handler
    $('#applyFilter').click(function() {
        var dateRange = $('#reportrange').data('daterangepicker');
        // Check if a date range is actually selected before updating variables
        if (dateRange.startDate && dateRange.endDate && $('#reportrange span').html() !== 'Select Date Range') {
            selectedStartDate = dateRange.startDate.format('YYYY-MM-DD');
            selectedEndDate = dateRange.endDate.format('YYYY-MM-DD');
        } else {
            selectedStartDate = null;
            selectedEndDate = null;
        }
        selectedStatus = $('#statusFilter').val();
        filtersApplied = true;
        table.ajax.reload();
    });

    // Status filter change handler
    $('#statusFilter').change(function() {
        selectedStatus = $(this).val();
        var dateRange = $('#reportrange').data('daterangepicker');
        selectedStartDate = dateRange.startDate.format('YYYY-MM-DD');
        selectedEndDate = dateRange.endDate.format('YYYY-MM-DD');
        table.ajax.reload();
    });

    // Reset Filter button click handler
    $('#resetFilter').click(function() {
       
        // Reset Date Range Picker to default (Last 30 Days) - This is just for picker's internal state
        $('#reportrange').val('');

        // Reset Date Range display
        $('#reportrange span').html('Select Date Range');

        // Reset Status Filter dropdown
        $('#statusFilter').val('');

        // Reload DataTable with no filters
        selectedStartDate = null;
        selectedEndDate = null;
        selectedStatus = '';
        filtersApplied = false;
        table.ajax.reload();
    });

    // Handle delete button click
    $(document).on('click', '.delete-btn', function() {
        var deleteUrl = $(this).data('url');
        $('#deleteForm').attr('action', deleteUrl);
        $('#deleteModal').modal('show');
    });
});
</script>
@endsection 