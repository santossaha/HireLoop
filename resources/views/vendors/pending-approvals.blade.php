@extends('layouts.app')

@section('title', 'Pending Vendor Approvals')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Pending Vendor Approvals</h1>
        <div>
            <a href="{{ route('vendors.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Back to Vendors
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Vendors Awaiting Approval</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="pendingVendorsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Contact Person</th>
                            <th>Contact Info</th>
                            <th>Internal POC</th>
                            <th>Submitted Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingVendors as $vendor)
                        <tr>
                            <td>{{ $vendor->id }}</td>
                            <td>{{ $vendor->company_name }}</td>
                            <td>{{ ucfirst($vendor->vendor_type) }}</td>
                            <td>{{ $vendor->contact_person }}</td>
                            <td>
                                <span class="d-block"><i class="fas fa-envelope me-1"></i> {{ $vendor->email }}</span>
                                <span class="d-block"><i class="fas fa-phone me-1"></i> {{ $vendor->phone }}</span>
                            </td>
                            <td>{{ $vendor->internalPoc ? $vendor->internalPoc->name : 'N/A' }}</td>
                            <td>{{ $vendor->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('vendors.show', $vendor->id) }}" class="btn btn-info btn-sm me-2" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <form action="{{ route('vendors.updateStatus', $vendor->id) }}" method="POST" class="d-inline me-2">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-success btn-sm" title="Approve Vendor">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('vendors.updateStatus', $vendor->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Reject Vendor">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No pending vendors found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $pendingVendors->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#pendingVendorsTable').DataTable({
            "paging": false,
            "ordering": true,
            "info": false,
            "responsive": true
        });
    });
</script>
@endsection