@extends('layouts.app')

@section('title', 'Candidate Sourcing')

@section('content')
<div class="container-fluid">
    

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Candidate Sourcing</h6>
            
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
                            <th>ID</th>
                            <th>Vendor</th>
                            <th>Requirement ID</th>
                            <th>Department</th>
                            {{-- <th>Client Budget</th>
                            <th>Proposed Budget</th>
                            <th>Status</th> --}}
                            <th>Created</th>
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
                    d.vendor_id = $('#vendor_id').val();
                    d.department_id = $('#department_id').val();
                    d.status = $('#status').val();
                }
            },
            columns: [
                { data: 'id', name: 'id' },
                { data: 'vendor', name: 'vendor' },
                { data: 'requirement_id', name: 'requirement_id' },
                { data: 'department', name: 'department' },
                // { data: 'client_budget', name: 'client_budget' },
                // { data: 'proposed_budget', name: 'proposed_budget' },
                // { data: 'status', name: 'status' },
                { data: 'created_at', name: 'created_at' },
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
