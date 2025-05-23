@extends('layouts.app')

@section('title', 'Vendor Management')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">Vendor Top Candidates</h1>
        </div>

        <div class="card shadow mb-4">

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="vendorsTable" width="100%" cellspacing="0">
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
                        @if(!empty($top_candidates))
                            @foreach($top_candidates as $top_candidate)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{ !empty($top_candidate->candidate) ? $top_candidate->candidate->candidate_name : '-' }}</td>
                                    <td>{!! !empty($top_candidate->candidate) ? '<span class="d-block"><i class="fas fa-envelope me-1"></i> '.$top_candidate->candidate->email.'</span>
                                <span class="d-block"><i class="fas fa-phone me-1"></i> '.$top_candidate->candidate->phone.'</span>' : '-' !!}</td>
                                    <td>{{ !empty($top_candidate->requirement) ? $top_candidate->requirement->requirement_id : '-' }}</td>
                                    <td>{{ !empty($top_candidate->interviewer) ? $top_candidate->interviewer->name : '-' }}</td>
                                    <td><a href="{{ asset('storage/' . $top_candidate->candidate->resume_path) }}"
                                           class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-download"></i> Download
                                        </a></td>
                                    <td>{{ $top_candidate->mock_feedback }}</td>
                                    @can('delete-vendor-top-candidates')
                                    <td><a href="#" onclick="deleteCandidate({{$top_candidate->id}})" class="btn btn-danger">Delete</a> </td>
                                    @endcan
                                </tr>
                            @endforeach

                        @endif
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
        </script>
{{--    <script>--}}
{{--        $(document).ready(function() {--}}
{{--            var table = $('#vendorsTable').DataTable({--}}
{{--                processing: true,--}}
{{--                serverSide: true,--}}
{{--                ajax: {--}}
{{--                    url: "{{ route('vendors.data') }}",--}}
{{--                    data: function(d) {--}}
{{--                        d.status = $('.filter-btn.active').data('status');--}}
{{--                    }--}}
{{--                },--}}
{{--                columns: [--}}
{{--                    { data: 'id' },--}}
{{--                    { data: 'name' },--}}
{{--                    { data: 'vendor_type' },--}}
{{--                    { data: 'contact_person' },--}}
{{--                    {--}}
{{--                        data: 'contact_info',--}}
{{--                        render: function(data) {--}}
{{--                            return `<span class="d-block"><i class="fas fa-envelope me-1"></i> ${data.email}</span>--}}
{{--                                <span class="d-block"><i class="fas fa-phone me-1"></i> ${data.phone}</span>`;--}}
{{--                        }--}}
{{--                    },--}}
{{--                    { data: 'internal_poc' },--}}
{{--                    // {--}}
{{--                    //     data: 'status',--}}
{{--                    //     render: function(data) {--}}
{{--                    //         let badgeClass = 'bg-secondary';--}}
{{--                    //         if (data === 'approved') badgeClass = 'bg-success';--}}
{{--                    //         else if (data === 'pending') badgeClass = 'bg-warning';--}}
{{--                    //         else if (data === 'rejected') badgeClass = 'bg-danger';--}}

{{--                    //         return `<span class="badge ${badgeClass}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;--}}
{{--                    //     }--}}
{{--                    // },--}}
{{--                    // {--}}
{{--                    //     data: 'client_ready',--}}
{{--                    //     render: function(data) {--}}
{{--                    //         return data ?--}}
{{--                    //             '<span class="badge bg-success">Ready</span>' :--}}
{{--                    //             '<span class="badge bg-secondary">Not Ready</span>';--}}
{{--                    //     }--}}
{{--                    // },--}}
{{--                    {--}}
{{--                        data: 'actions',--}}
{{--                        orderable: false,--}}
{{--                        searchable: false--}}
{{--                    }--}}
{{--                ],--}}
{{--                order: [[0, 'desc']],--}}
{{--                pageLength: 10,--}}
{{--                dom: 'rtip',--}}
{{--                language: {--}}
{{--                    search: "",--}}
{{--                    searchPlaceholder: "Search by Name,Technology,POC",--}}
{{--                    lengthMenu: "",--}}
{{--                    info: "Showing _START_ to _END_ of _TOTAL_ entries",--}}
{{--                    infoEmpty: "Showing 0 to 0 of 0 entries",--}}
{{--                    infoFiltered: "(filtered from _MAX_ total entries)"--}}
{{--                },--}}
{{--                initComplete: function() {--}}
{{--                    // Hide the default search box--}}
{{--                    // $('.dataTables_filter').hide();--}}
{{--                    // Hide length menu--}}
{{--                    $('.dataTables_length').hide();--}}
{{--                }--}}
{{--            });--}}

{{--            // Custom search input handler--}}
{{--            $('.dataTables_filter input').on('keyup', function() {--}}
{{--                console.log(this.value);--}}
{{--                table.search(this.value).draw();--}}
{{--            });--}}

{{--            // Filter buttons click handler--}}
{{--            $('.filter-btn').click(function() {--}}
{{--                $('.filter-btn').removeClass('active');--}}
{{--                $(this).addClass('active');--}}
{{--                table.ajax.reload();--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
@endsection