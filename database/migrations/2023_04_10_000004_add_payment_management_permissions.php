<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create payment management permissions
        $permissions = [
            // Client Payment permissions
            'view-client-payments',
            'create-client-payment',
            'edit-client-payment',
            'delete-client-payment',
            'mark-client-payment-received',
            
            // Vendor Payment permissions
            'view-vendor-payments',
            'create-vendor-payment',
            'edit-vendor-payment',
            'delete-vendor-payment',
            'approve-payment',
            'mark-payment-paid',
            'view-payment-reports',
            
            // Invoice permissions
            'view-invoices',
            'create-invoice',
            'edit-invoice',
            'delete-invoice',
            'verify-invoice',
            
            // Vendor Attendance permissions
            'view-vendor-attendances',
            'create-vendor-attendance',
            'edit-vendor-attendance',
            'approve-vendor-attendance',
            'view-attendance-reports',
        ];

        // First, check if permissions table exists
        $tableExists = Schema::hasTable('permissions');
        
        if ($tableExists) {
            // Insert permissions
            foreach ($permissions as $permission) {
                DB::table('permissions')->insertOrIgnore([
                    'name' => $permission,
                    'guard_name' => 'web',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Get permission IDs
            $permissionIds = DB::table('permissions')
                ->whereIn('name', $permissions)
                ->pluck('id')
                ->toArray();
            
            // Define role permissions
            $rolePermissions = [
                'admin' => $permissions,
                'founder' => [
                    'view-client-payments',
                    'view-vendor-payments',
                    'approve-payment',
                    'view-payment-reports',
                    'view-invoices',
                    'verify-invoice',
                    'view-vendor-attendances',
                    'view-attendance-reports',
                ],
                'accounts' => [
                    'view-client-payments',
                    'create-client-payment',
                    'edit-client-payment',
                    'mark-client-payment-received',
                    'view-vendor-payments',
                    'create-vendor-payment',
                    'edit-vendor-payment',
                    'mark-payment-paid',
                    'view-payment-reports',
                    'view-invoices',
                    'verify-invoice',
                    'view-vendor-attendances',
                    'view-attendance-reports',
                ],
                'poc' => [
                    'view-client-payments',
                    'view-vendor-payments',
                    'view-invoices',
                    'create-invoice',
                    'view-vendor-attendances',
                    'create-vendor-attendance',
                    'edit-vendor-attendance',
                    'approve-vendor-attendance',
                    'view-attendance-reports',
                ],
                'vendor' => [
                    'view-vendor-payments',
                    'view-invoices',
                    'create-invoice',
                    'view-vendor-attendances',
                    'create-vendor-attendance',
                ],
            ];
            
            // Get role IDs
            $roles = DB::table('roles')
                ->whereIn('name', array_keys($rolePermissions))
                ->get(['id', 'name'])
                ->keyBy('name')
                ->toArray();
            
            // Assign permissions to roles through role_has_permissions table
            foreach ($roles as $roleName => $role) {
                $assignedPermissions = $rolePermissions[$roleName];
                
                // Get IDs of permissions to assign
                $permissionIdsToAssign = DB::table('permissions')
                    ->whereIn('name', $assignedPermissions)
                    ->pluck('id')
                    ->toArray();
                
                // Insert into role_has_permissions table
                foreach ($permissionIdsToAssign as $permissionId) {
                    DB::table('role_has_permissions')->insertOrIgnore([
                        'permission_id' => $permissionId,
                        'role_id' => $role->id,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete payment management permissions
        $permissions = [
            // Client Payment permissions
            'view-client-payments',
            'create-client-payment',
            'edit-client-payment',
            'delete-client-payment',
            'mark-client-payment-received',
            
            // Vendor Payment permissions
            'view-vendor-payments',
            'create-vendor-payment',
            'edit-vendor-payment',
            'delete-vendor-payment',
            'approve-payment',
            'mark-payment-paid',
            'view-payment-reports',
            
            // Invoice permissions
            'view-invoices',
            'create-invoice',
            'edit-invoice',
            'delete-invoice',
            'verify-invoice',
            
            // Vendor Attendance permissions
            'view-vendor-attendances',
            'create-vendor-attendance',
            'edit-vendor-attendance',
            'approve-vendor-attendance',
            'view-attendance-reports',
        ];

        // Check if permissions table exists
        $tableExists = Schema::hasTable('permissions');
        
        if ($tableExists) {
            // First, find and delete any role-permission associations
            $permissionIds = DB::table('permissions')
                ->whereIn('name', $permissions)
                ->pluck('id')
                ->toArray();
            
            if (!empty($permissionIds)) {
                DB::table('role_has_permissions')
                    ->whereIn('permission_id', $permissionIds)
                    ->delete();
                
                // Then delete the permissions themselves
                DB::table('permissions')
                    ->whereIn('name', $permissions)
                    ->delete();
            }
        }
    }
};