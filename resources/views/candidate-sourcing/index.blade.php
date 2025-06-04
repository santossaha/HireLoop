@extends('layouts.app')

@section('title', 'Candidate Sourcing')

@section('content')
<div class="container-fluid">
    

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Requirements</h6>
            
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="requirementsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Requirement ID</th>
{{--                            <th>Company</th>--}}
                            <th>Title</th>
                            <th>Skills</th>
                            <th>Department</th>
{{--                            <th>Department</th>--}}
                            <th>Created Date</th>
                            <th>Created By</th>
                            <th>Status</th>
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
                url: "{{ route('candidate-sourcing.index') }}",
                data: function(d) {
                    d.company_id = $('#company_id').val();
                    d.department_id = $('#department_id').val();
                    d.status = $('#status').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'requirement_id', name: 'requirement_id' },
                // { data: 'company', name: 'company' },
                { data: 'title', name: 'title' },
                { data: 'skills', name: 'skills' },
                { data: 'department', name: 'department' },
                { data: 'created_at', name: 'created_at' },
                { data: 'created_by', name: 'created_by' },
                { data: 'status', name: 'status' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[0, 'desc']],
            pageLength: 10,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search..."
            }
        });
    });
</script>
@endsection
