@extends('layouts.app')

@section('title', 'Requirements')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Requirements</h1>
        <a href="{{ route('requirements.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-1"></i> Add New Requirement
        </a>
    </div>

    <div class="row">
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Current Open</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $open_requirement_count }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Closed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $closed_requirement_count }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Requirements Filter</h6>
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                <div class="col-md-2">
                    <label for="vendor_id" class="form-label">Vendor</label>
                    <select class="form-select" id="company_id" name="company_id">
                        <option value="">All Comapnay</option>
                        @foreach(App\Models\Company::orderBy('name')->get() as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">All Departments</option>
                        @foreach(App\Models\Department::orderBy('name')->get() as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="is_closed" class="form-label">Status</label>
                    <select class="form-select" id="is_closed" name="is_closed">
                        <option value="">Select Status</option>
                        <option value="99">Open</option>
                        <option value="1">Closed</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary me-2" id="filterBtn">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <button type="button" class="btn btn-secondary" id="resetBtn">
                        <i class="fas fa-redo me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Requirements</h6>
            
            @if(auth()->user()->isHod())
                <span class="badge bg-warning" id="pendingHodCount"></span>
            @elseif(auth()->user()->isFounder())
                <span class="badge bg-warning" id="pendingFounderCount"></span>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="requirementsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Requirement ID</th>
                            <th>Company</th>
                            <th>Department</th>
                            <th>Title</th>
                            <th>Skills</th>
                            <th>BDE Name</th>
                            <th>Client Budget</th>
                            <th>Final Budget</th>
                            <th>Created On</th>
                            <th>Created By</th>
                            <th>Candidate Count</th>
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
        var table = $('#requirementsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('requirements.index') }}",
                data: function(d) {
                    d.company_id = $('#company_id').val();
                    d.department_id = $('#department_id').val();
                    d.is_closed = $('#is_closed').val();
                }
            },
            columns: [
                { data: 'requirement_id', name: 'requirement_id' },
                { data: 'company', name: 'company' },
                { data: 'department', name: 'department' },
                { data: 'title', name: 'title' },
                { data: 'skills', name: 'skills' },
                { data: 'bde_name', name: 'bde_name' },
                { data: 'client_budget', name: 'client_budget' },
                { data: 'final_budget', name: 'final_budget' },
                { data: 'created_at', name: 'created_at' },
                { data: 'created_by', name: 'created_by' },
                { data: 'candidate_count', name: 'candidate_count' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            pageLength: 10,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search..."
            }
        });

        // Filter button click handler
        $('#filterBtn').click(function() {
            table.ajax.reload();
        });

        // Reset button click handler
        $('#resetBtn').click(function() {
            $('#filterForm select').val('');
            table.ajax.reload();
        });

        // Update pending counts
        // function updatePendingCounts() {
        //     $.ajax({
        //         url: "{{ route('requirements.pending-counts') }}",
        //         type: 'GET',
        //         success: function(response) {
        //             if (response.pending_hod) {
        //                 $('#pendingHodCount').text(response.pending_hod + ' pending your approval');
        //             }
        //             if (response.pending_founder) {
        //                 $('#pendingFounderCount').text(response.pending_founder + ' pending your approval');
        //             }
        //         }
        //     });
        // }

        // Initial update of pending counts
       // updatePendingCounts();
    });
</script>
@endsection
