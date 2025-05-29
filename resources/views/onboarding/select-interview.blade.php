@extends('layouts.app')

@section('title', 'Select Interview for Onboarding')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Select Interview for Onboarding</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="interviewTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Requirement ID</th>
                                    <th>Vendor</th>
                                    <th>Candidate</th>
                                    <th>Interview Type</th>
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
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#interviewTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('onboardings.index') }}",
        columns: [
            {data: 'id', name: 'id'},
            {data: 'requirement.requirement_id', name: 'requirement.requirement_id'},
            {data: 'vendor.company_name', name: 'vendor.company_name'},
            {data: 'candidate.candidate_name', name: 'candidate.candidate_name', defaultContent: 'N/A'},
            {
                data: 'type',
                name: 'type',
                render: function(data) {
                    return data.charAt(0).toUpperCase() + data.slice(1);
                }
            },
            {
                data: 'status',
                name: 'status',
                render: function(data) {
                    return data.charAt(0).toUpperCase() + data.slice(1);
                }
            },
            {
                data: 'result',
                name: 'result',
                render: function(data) {
                    return data ? data.charAt(0).toUpperCase() + data.slice(1) : 'N/A';
                }
            },
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return `
                        <a href="/onboardings/create/${data}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Create Onboarding
                        </a>
                    `;
                }
            }
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endsection 