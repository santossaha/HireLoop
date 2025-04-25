<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::with('permissions')->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role with web guard
        $role = Role::where('id', $request->role)
            ->where('guard_name', 'web')
            ->firstOrFail();
            
        $user->assignRole($role);

        // Sync permissions based on the role
        $permissions = $role->permissions()
            ->where('guard_name', 'web')
            ->pluck('name')
            ->toArray();
            
        $user->syncPermissions($permissions);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    public function edit(User $user)
    {
        $roles = Role::with('permissions')->get();
        $userRole = $user->roles->first();
        
        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }
       
        // Remove all existing roles
        $user->roles()->detach();
         
        // Assign new role with web guard
        $role = Role::where('id', $request->role)
            ->where('guard_name', 'web')
            ->firstOrFail();
           
            
        $user->assignRole($role);

        // Sync permissions based on the role
        $permissions = $role->permissions()
            ->where('guard_name', 'web')
            ->pluck('name')
            ->toArray();
            
        $user->syncPermissions($permissions);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }
} 