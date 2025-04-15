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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Requirements Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('requirements.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="vendor_id" class="form-label">Vendor</label>
                    <select class="form-select" id="vendor_id" name="vendor_id">
                        <option value="">All Vendors</option>
                        @foreach(App\Models\Vendor::orderBy('company_name')->get() as $vendor)
                            <option value="{{ $vendor->id }}" {{ request('vendor_id') == $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id">
                        <option value="">All Departments</option>
                        @foreach(App\Models\Department::orderBy('name')->get() as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="pending_hod" {{ request('status') == 'pending_hod' ? 'selected' : '' }}>Pending HOD Approval</option>
                        <option value="pending_founder" {{ request('status') == 'pending_founder' ? 'selected' : '' }}>Pending Founder Approval</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Fully Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('requirements.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Requirements</h6>
            
            @if(auth()->user()->isHod())
                <span class="badge bg-warning">{{ App\Models\Requirement::pendingHodApproval()->forDepartment(auth()->user()->department_id)->count() }} pending your approval</span>
            @elseif(auth()->user()->isFounder())
                <span class="badge bg-warning">{{ App\Models\Requirement::pendingFounderApproval()->count() }} pending your approval</span>
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
                            <th>Client Budget</th>
                            <th>Proposed Budget</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requirements as $requirement)
                        <tr>
                            <td>{{ $requirement->id }}</td>
                            <td>
                                <a href="{{ route('vendors.show', $requirement->vendor_id) }}">
                                    {{ $requirement->vendor->company_name }}
                                </a>
                            </td>
                            <td>{{ $requirement->requirement_id }}</td>
                            <td>{{ $requirement->department->name ?? 'N/A' }}</td>
                            <td>${{ number_format($requirement->client_budget, 2) }}</td>
                            <td>${{ number_format($requirement->proposed_budget, 2) }}</td>
                            <td>
                                @if($requirement->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @elseif($requirement->founder_approved && $requirement->hod_approved)
                                    <span class="badge bg-success">Approved</span>
                                @elseif($requirement->hod_approved)
                                    <span class="badge bg-warning">HOD Approved</span>
                                @else
                                    <span class="badge bg-secondary">Pending HOD</span>
                                @endif
                            </td>
                            <td>{{ $requirement->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('requirements.show', $requirement->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(!$requirement->isApproved() && !($requirement->status == 'rejected'))
                                        <a href="{{ route('requirements.edit', $requirement->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $requirement->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $requirement->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $requirement->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $requirement->id }}">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this requirement ({{ $requirement->requirement_id }})?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('requirements.destroy', $requirement->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No requirements found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $requirements->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#requirementsTable').DataTable({
            paging: false,
            searching: true,
            ordering: true,
            info: false,
        });
    });
</script>
@endsection
