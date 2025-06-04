<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();

        $general_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'general');
        $user_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'user');
        $vendor_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'vendor');
        $requirement_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'requirement');
        $company_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'company');
        $candidate_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'candidate');
        $invoice_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'invoice');
        $interview_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'interview');
        $client_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'client');


        return view('roles.create', compact('general_managements','user_managements','vendor_managements',
        'requirement_managements','company_managements','candidate_managements','invoice_managements','interview_managements','client_managements'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);
        
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)
                ->where('guard_name', 'web')
                ->pluck('name')
                ->toArray();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        $general_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'general');
        $user_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'user');
        $vendor_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'vendor');
        $requirement_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'requirement');
        $company_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'company');
        $candidate_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'candidate');
        $invoice_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'invoice');
        $interview_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'interview');
        $client_managements = $permissions->filter(fn($permissions) => $permissions->group_name == 'client');

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions','general_managements','user_managements','vendor_managements',
            'requirement_managements','company_managements','candidate_managements','invoice_managements','interview_managements','client_managements'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);
        
        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)
                ->where('guard_name', 'web')
                ->pluck('name')
                ->toArray();
            $role->syncPermissions($permissions);
        }

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully');
    }

    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')
            ->with('success', 'Role deleted successfully');
    }
} 