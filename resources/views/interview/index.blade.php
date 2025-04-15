@extends('layouts.app')

@section('title', 'Interviews')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Interviews</h1>
        <a href="{{ route('interviews.create') }}" class="btn btn-primary">
            <i class="fas fa-calendar-plus me-1"></i> Schedule New Interview
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Interviews Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('interviews.index') }}" class="row g-3">
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
                <div class="col-md-2">
                    <label for="type" class="form-label">Interview Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <option value="mock" {{ request('type') == 'mock' ? 'selected' : '' }}>Mock</option>
                        <option value="internal" {{ request('type') == 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="client" {{ request('type') == 'client' ? 'selected' : '' }}>Client</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="result" class="form-label">Result</label>
                    <select class="form-select" id="result" name="result">
                        <option value="">All Results</option>
                        <option value="pass" {{ request('result') == 'pass' ? 'selected' : '' }}>Pass</option>
                        <option value="fail" {{ request('result') == 'fail' ? 'selected' : '' }}>Fail</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <a href="{{ route('interviews.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Upcoming Interviews</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ App\Models\Interview::upcoming()->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pass Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $totalCompleted = App\Models\Interview::withStatus('completed')->count();
                                    $totalPassed = App\Models\Interview::withResult('pass')->count();
                                    $passRate = $totalCompleted > 0 ? round(($totalPassed / $totalCompleted) * 100) : 0;
                                @endphp
                                {{ $passRate }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Interviews This Week</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Interview::whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Client Interviews</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ App\Models\Interview::ofType('client')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">All Interviews</h6>
            
            <div class="btn-group">
                <a href="{{ route('interviews.index', array_merge(request()->all(), ['status' => 'scheduled'])) }}" class="btn btn-sm {{ request('status') == 'scheduled' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Upcoming
                </a>
                <a href="{{ route('interviews.index', array_merge(request()->all(), ['status' => 'completed'])) }}" class="btn btn-sm {{ request('status') == 'completed' ? 'btn-success' : 'btn-outline-success' }}">
                    Completed
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="interviewsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vendor</th>
                            <th>Requirement</th>
                            <th>Type</th>
                            <th>Date & Time</th>
                            <th>Interviewer</th>
                            <th>Status</th>
                            <th>Result</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interviews as $interview)
                        <tr>
                            <td>{{ $interview->id }}</td>
                            <td>
                                <a href="{{ route('vendors.show', $interview->vendor_id) }}">
                                    {{ $interview->vendor->company_name }}
                                </a>
                            </td>
                            <td>
                                @if($interview->requirement)
                                    <a href="{{ route('requirements.show', $interview->requirement_id) }}">
                                        {{ $interview->requirement->requirement_id }}
                                    </a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                @if($interview->type == 'mock')
                                    <span class="badge bg-secondary">Mock</span>
                                @elseif($interview->type == 'internal')
                                    <span class="badge bg-info">Internal</span>
                                @else
                                    <span class="badge bg-warning">Client</span>
                                @endif
                            </td>
                            <td>{{ $interview->scheduled_at->format('M d, Y H:i') }}</td>
                            <td>{{ $interview->interviewer->name ?? 'N/A' }}</td>
                            <td>
                                @if($interview->status == 'scheduled')
                                    <span class="badge bg-primary">Scheduled</span>
                                @elseif($interview->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                @if($interview->result == 'pass')
                                    <span class="badge bg-success">Pass</span>
                                @elseif($interview->result == 'fail')
                                    <span class="badge bg-danger">Fail</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('interviews.show', $interview->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($interview->status != 'completed')
                                        <a href="{{ route('interviews.edit', $interview->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $interview->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $interview->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $interview->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $interview->id }}">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this interview scheduled on {{ $interview->scheduled_at->format('M d, Y H:i') }}?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('interviews.destroy', $interview->id) }}" method="POST">
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
                            <td colspan="9" class="text-center">No interviews found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $interviews->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#interviewsTable').DataTable({
            paging: false,
            searching: true,
            ordering: true,
            info: false,
        });
    });
</script>
@endsection
