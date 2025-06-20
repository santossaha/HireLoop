@extends('layouts.app')

@section('title', 'Vendor Management')

@section('content')
    <style>
        .btn-outline-warning:hover{
            color: #fff;
        }
    </style>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Vendor Management</h1>
        {{-- @if(auth()->user()->isAdmin() || auth()->user()->isHod()) --}}
        <div>
            <a href="{{ route('vendors.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle me-1"></i> Add New Vendor
            </a>
            <a href="#" data-toggle="modal" data-target="#vendorInvite" class="btn btn-info" onclick="$('#vendorInvite').modal('toggle')">
                <i class="fas fa-user me-1"></i> Invite Vendor
            </a>
        </div>
        {{-- @endif --}}
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    {{-- <h6 class="m-0 font-weight-bold text-primary me-3">Vendors</h6> --}}
                    <div class="dataTables_filter">
                        <input type="search" class="form-control" placeholder="Search by Name, POC, Email" aria-controls="vendorsTable">
                    </div>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary filter-btn active" data-status="all">All</button>
                    <button type="button" class="btn btn-sm btn-outline-success filter-btn" data-status="approved">Approved</button>
                    <button type="button" class="btn btn-sm btn-outline-warning filter-btn" data-status="pending">Pending</button>
                    <button type="button" class="btn btn-sm btn-outline-danger filter-btn" data-status="rejected">Rejected</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="vendorsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Contact Person</th>
                            <th>Contact Info</th>
                            <th>Internal POC</th>
                            {{-- <th>Status</th> --}}
                            {{-- <th>Client Ready</th> --}}
                            <th>Actions</th>
                        </tr>
                    </thead>
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

    @include('vendor_invite.modals')


@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        var table = $('#vendorsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('vendors.data') }}",
                data: function(d) {
                    d.status = $('.filter-btn.active').data('status');
                }
            },
            columns: [
                { data: 'id' },
                { data: 'name' },
                { data: 'vendor_type' },
                { data: 'contact_person' },
                {
                    data: 'contact_info',
                    render: function(data) {
                        return `<span class="d-block"><i class="fas fa-envelope me-1"></i> ${data.email}</span>
                                <span class="d-block"><i class="fas fa-phone me-1"></i> ${data.phone}</span>`;
                    }
                },
                { data: 'internal_poc' },
                
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[0, 'desc']],
            ordering: false,
            pageLength: 10,
            dom: 'rtip',
            language: {
                search: "",
                searchPlaceholder: "Search by Name,Technology,POC",
                lengthMenu: "",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)"
            },
            initComplete: function() {
               
                $('.dataTables_length').hide();
            }
        });

        // Custom search input handler
        $('.dataTables_filter input').on('keyup', function() {
            console.log(this.value);
            table.search(this.value).draw();
        });
        $('.dataTables_filter input').on('change', function() {
            console.log('click',this.value);
            table.search(this.value).draw();
        });
        $('.dataTables_filter input').on('search', function() {

            console.log('click',this.value);
            table.search(this.value).draw();
        });

        // Filter buttons click handler
        $('.filter-btn').click(function() {
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            table.ajax.reload();
        });

        $('.close-modal').click(function (){
            $('#vendorInvite').modal('hide');
        });
    });
</script>
@endsection