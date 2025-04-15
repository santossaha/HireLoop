@extends('layouts.app')

@section('title', 'Vendor Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Vendor Management</h1>
        @if(auth()->user()->isAdmin() || auth()->user()->isHod())
        <div>
            <a href="{{ route('vendors.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle me-1"></i> Add New Vendor
            </a>
        </div>
        @endif
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Vendors</h6>
            <div class="btn-group">
                <a href="{{ route('vendors.index', ['status' => 'all']) }}" class="btn btn-sm btn-outline-primary {{ request('status', 'all') == 'all' ? 'active' : '' }}">All</a>
                <a href="{{ route('vendors.index', ['status' => 'approved']) }}" class="btn btn-sm btn-outline-success {{ request('status') == 'approved' ? 'active' : '' }}">Approved</a>
                <a href="{{ route('vendors.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-warning {{ request('status') == 'pending' ? 'active' : '' }}">Pending</a>
                <a href="{{ route('vendors.index', ['status' => 'rejected']) }}" class="btn btn-sm btn-outline-danger {{ request('status') == 'rejected' ? 'active' : '' }}">Rejected</a>
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
                            <th>Status</th>
                            <th>Client Ready</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
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
                            <td>
                                @if($vendor->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($vendor->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                @if($vendor->client_ready)
                                    <span class="badge bg-success">Ready</span>
                                @else
                                    <span class="badge bg-secondary">Not Ready</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('vendors.show', $vendor->id) }}" class="btn btn-info btn-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->isAdmin() || auth()->user()->isHod() || 
                                        (auth()->user()->isPoc() && $vendor->internal_poc_id == auth()->id()))
                                    <a href="{{ route('vendors.edit', $vendor->id) }}" class="btn btn-primary btn-sm" title="Edit Vendor">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                    @if(auth()->user()->isAdmin() || auth()->user()->isFounder())
                                    <form action="{{ route('vendors.destroy', $vendor->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                title="Delete Vendor"
                                                onclick="return confirm('Are you sure you want to delete this vendor?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No vendors found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $vendors->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Init other scripts for vendors page here if needed
    });
</script>
@endsection