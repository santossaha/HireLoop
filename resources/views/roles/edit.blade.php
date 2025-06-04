@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-5">
                <div class="card-header">Edit Role</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('roles.update', $role->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Role Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

{{--                        <div class="mb-3">--}}
{{--                            <label class="form-label">Permissions</label>--}}
{{--                            <div class="row">--}}
{{--                                @foreach($permissions as $permission)--}}
{{--                                    <div class="col-md-4 mb-2">--}}
{{--                                        <div class="form-check">--}}
{{--                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}" --}}
{{--                                                id="permission_{{ $permission->id }}"--}}
{{--                                                {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}>--}}
{{--                                            <label class="form-check-label" for="permission_{{ $permission->id }}">--}}
{{--                                                {{ $permission->name }}--}}
{{--                                            </label>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                @endforeach--}}
{{--                            </div>--}}
{{--                            @error('permissions')--}}
{{--                                <div class="text-danger">{{ $message }}</div>--}}
{{--                            @enderror--}}
{{--                        </div>--}}


                        <div class="mb-3">
                            <h5 for="name">Permissions</h5>
                            <label class="mt-3 mb-3"><strong>General Management</strong></label>
                            <div class="row">
                                @foreach($general_managements as $permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                   id="permission_{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <label class="mt-3 mb-3"><strong>User Management</strong></label>
                            <div class="row">
                                @foreach($user_managements as $permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                   id="permission_{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <label class="mt-3 mb-3"><strong>Vendor Management</strong></label>
                            <div class="row">
                                @foreach($vendor_managements as $permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                   id="permission_{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <label class="mt-3 mb-3"><strong>Requirement Management</strong></label>
                            <div class="row">
                                @foreach($requirement_managements as $permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                   id="permission_{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <label class="mt-3 mb-3"><strong>Company Management</strong></label>
                            <div class="row">
                                @foreach($company_managements as $permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                   id="permission_{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <label class="mt-3 mb-3"><strong>Candidate Management</strong></label>
                            <div class="row">
                                @foreach($candidate_managements as $permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                   id="permission_{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <label class="mt-3 mb-3"><strong>Invoice Management</strong></label>
                            <div class="row">
                                @foreach($invoice_managements as $permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                   id="permission_{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <label class="mt-3 mb-3"><strong>Interview Management</strong></label>
                            <div class="row">
                                @foreach($interview_managements as $permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                   id="permission_{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <label class="mt-3 mb-3"><strong>Client Management</strong></label>
                            <div class="row">
                                @foreach($client_managements as $permission)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                   id="permission_{{ $permission->id }}" {{ in_array($permission->id, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                                    {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="d-flex justify-content-between">
                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Update Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 