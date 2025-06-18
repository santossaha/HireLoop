@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Companies</h1>
        @can('company-create')
        <a href="{{ route('companies.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Company
        </a>
        @endcan
    </div>

    {{-- @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
    @endif --}}

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="companiesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Detail</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($companies as $company)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $company->name }}</td>
                            <td>{{ Str::limit($company->detail, 50) }}</td>
                            <td>
                            
                                @can('company-edit')
                                <a class="btn btn-primary btn-sm" href="{{ route('companies.edit',encrypt_id($company->id)) }}">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                @endcan
                                @can('company-delete')
                                <form action="{{ route('companies.destroy',$company->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this company?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#companiesTable').DataTable({
            "pageLength": 10,
            "ordering": true,
            "responsive": true,
            "language": {
                "search": "Search companies:"
            }
        });
    });
</script>
@endpush 