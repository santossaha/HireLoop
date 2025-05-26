@extends('layouts.app')

@section('title', 'Vendor Management')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Vendor Top Candidates</h1>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Vendor Top Candidates Filter</h6>
            </div>
            <div class="card-body">
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select class="form-select" id="department_id" name="department_id">
                            <option value="">All Departments</option>
                            @foreach(App\Models\Department::orderBy('name')->get() as $department)
                                <option value="{{ $department->id }}" {{ $department->id == 1 ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
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

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="vendorsTopCandidateTable" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Candidate Name</th>
                            <th>Contact Info</th>
                            <th>Requirement ID</th>
                            <th>Interviewer</th>
                            <th>Resume</th>
                            <th>Mock Feedback</th>
                            @can('delete-vendor-top-candidates')
                            <th>Actions</th>
                            @endcan
                        </tr>
                        </thead>
                        <tbody>
{{--                        @if(!empty($top_candidates))--}}
{{--                            @foreach($top_candidates as $top_candidate)--}}
{{--                                <tr>--}}
{{--                                    <td>{{$loop->iteration}}</td>--}}
{{--                                    <td>{{ !empty($top_candidate->candidate) ? $top_candidate->candidate->candidate_name : '-' }}</td>--}}
{{--                                    <td>{!! !empty($top_candidate->candidate) ? '<span class="d-block"><i class="fas fa-envelope me-1"></i> '.$top_candidate->candidate->email.'</span>--}}
{{--                                <span class="d-block"><i class="fas fa-phone me-1"></i> '.$top_candidate->candidate->phone.'</span>' : '-' !!}</td>--}}
{{--                                    <td>{{ !empty($top_candidate->requirement) ? $top_candidate->requirement->requirement_id : '-' }}</td>--}}
{{--                                    <td>{{ !empty($top_candidate->interviewer) ? $top_candidate->interviewer->name : '-' }}</td>--}}
{{--                                    <td><a href="{{ asset('storage/' . $top_candidate->candidate->resume_path) }}"--}}
{{--                                           class="btn btn-sm btn-primary" target="_blank">--}}
{{--                                            <i class="fas fa-download"></i> Download--}}
{{--                                        </a></td>--}}
{{--                                    <td>{{ $top_candidate->mock_feedback }}</td>--}}
{{--                                    @can('delete-vendor-top-candidates')--}}
{{--                                    <td><a href="#" onclick="deleteCandidate({{$top_candidate->id}})" class="btn btn-danger">Delete</a> </td>--}}
{{--                                    @endcan--}}
{{--                                </tr>--}}
{{--                            @endforeach--}}

{{--                        @endif--}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dataTables_filter {
            margin-right: 1rem;
        }
        .dataTables_filter input {
            width: 341px;
            padding: 0.375rem 0.75rem;
        }
    </style>


@endsection

@section('scripts')
        <script>
            $(document).ready(function() {
                 $('#vendorsTable').DataTable();
            });

            function deleteCandidate(cid){
                if(confirm('Are you sure wat to delete record ?')){
                    window.location = '{{url('vendor/top-candidates/delete')}}/'+cid;
                }
            }

            $(document).ready(function() {
                var table = $('#vendorsTopCandidateTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('vendor.top-candidates.index') }}",
                        data: function(d) {
                            d.department_id = $('#department_id').val();
                        }
                    },
                    columns: [
                        { data: 'id', name: 'id' },
                        { data: 'candidate_name', name: 'candidate_name' },
                        {
                            data: 'contact_info',
                            render: function(data) {
                                return `<span class="d-block"><i class="fas fa-envelope me-1"></i> ${data.email}</span>
                                <span class="d-block"><i class="fas fa-phone me-1"></i> ${data.phone}</span>`;
                            }
                        },
                        { data: 'requirement_id', name: 'requirement_id' },
                        { data: 'interviewer', name: 'interviewer' },
                        { data: 'resume', name: 'resume' },
                        { data: 'mock_feedback', name: 'mock_feedback' },
                        { data: 'actions', name: 'actions', orderable: false, searchable: false }
                    ],
                    order: [[0, 'asc']],
                    pageLength: 10,
                    // searchable:false,
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
            });
        </script>
@endsection