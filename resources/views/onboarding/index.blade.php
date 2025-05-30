@extends('layouts.app')

@section('title', 'Onboarding List')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this onboarding record?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
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
<script>
$(document).ready(function() {
    $('#onboardingTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('onboardings.index') }}",
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
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="d-flex gap-1"><a href="${data.show_url}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a><a href="${data.edit_url}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a><button type="button" class="btn btn-danger btn-sm delete-btn" data-url="${data.delete_url}"><i class="fas fa-trash"></i></button></div>`;
                }
            }
        ],
        order: [[0, 'desc']]
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