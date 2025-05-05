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

        // Define the roles
        $roles = [
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'hod', 'guard_name' => 'web'],
            ['name' => 'founder', 'guard_name' => 'web'],
            ['name' => 'poc', 'guard_name' => 'web'],
            ['name' => 'accounts', 'guard_name' => 'web'],
            ['name' => 'vendor', 'guard_name' => 'web'],
            ['name' => 'bde', 'guard_name' => 'web'],
        ];

        // Insert roles into database
        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'guard_name' => $role['guard_name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        
        // Create payment management permissions
        $permissions = [
            // Vendor Management:
            'view-dashboard',
            // Vendor Management:
            'view-vendors',
            'create-vendor',
            'edit-vendor',
            'delete-vendor',
            'view-vendor-details',
            'update-vendor-status',
            'view-vendor-approvals',
            'approve-vendor',
            
            // Client Payment Management:
            'view-client-payments',
            'create-client-payment',
            'edit-client-payment',
            'delete-client-payment',
            'view-client-payment-details',
            'mark-client-payment-received',
            'view-client-payment-dashboard',
            
            //Vendor Attendance Management:
            'view-vendor-attendances',
            'create-vendor-attendance',
            'edit-vendor-attendance',
            'view-vendor-attendance-details',
            'approve-vendor-attendance',
            'send-vendor-attendance-reminders',
            'view-vendor-attendance-summary',
           
            
            //Invoice Management:
            'view-invoices',
            'create-invoice',
            'edit-invoice',
            'view-invoice-details',
            'verify-invoice',
            'download-invoice',
            'view-pending-invoices',
            'view-invoice-discrepancies',
            'view-invoice-summary',

            //Requirement Management:
            'view-requirements',
            'create-requirement',
            'edit-requirement',
            'delete-requirement',
            'view-requirement-details',
            'approve-requirement',
            'view-requirement-counts',

            //Requirement Management:
            'view-interviews',
            'create-interview',
            'edit-interview',
            'delete-interview',
            'view-interview-details',
            'submit-interview-feedback',
            'view-interview-stats',

            //User Management:
            'view-users',
            'create-user',
            'edit-user',
            'delete-user',

            //User Management:
            'view-roles',
            'create-role',
            'edit-role',
            'delete-role',

            // Vender Payment 
            'view-vendor-payments',
            'create-vendor-payments',
            'edit-vendor-payments',
            'delete-vendor-payments',
            'approve-vendor-payments',
            'reject-vendor-payments',
            'mark-vendor-payments-paid',
            'generate-vendor-payments',
            'view-vendor-payment-approval',
            'view-vendor-payment-processing',
            'view-vendor-payment-reports',
            'export-vendor-payments',
            'view-vendor-payment-api',

            // Company Management:
            'company-list',
            'company-create',
            'company-edit',
            'company-delete',

            // Client Payment Management:
            'view-client-payments',
            'view-vendor-payments',
            'view-invoices',
            'create-invoice',
            'view-vendor-attendances',
            'create-vendor-attendance',
            'edit-vendor-attendance',
            'approve-vendor-attendance',
            'view-attendance-reports',

            // Candidate Sourcing:
            'view-candidate-sourcing',
            'create-candidate-sourcing',
            'edit-candidate-sourcing',
            'delete-candidate-sourcing',
            'view-candidate-sourcing-details',
            'approve-candidate-sourcing',
            'reject-candidate-sourcing',
            'schedule-candidate-interview',
            'upload-candidate',
            

           
 
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
                         // Vendor Management:
                'view-dashboard',
                // Vendor Management:
                'view-vendors',
                'create-vendor',
                'edit-vendor',
                'delete-vendor',
                'view-vendor-details',
                'update-vendor-status',
                'view-vendor-approvals',
                'approve-vendor',
                
                // Client Payment Management:
                'view-client-payments',
                'create-client-payment',
                'edit-client-payment',
                'delete-client-payment',
                'view-client-payment-details',
                'mark-client-payment-received',
                'view-client-payment-dashboard',
                
                //Vendor Attendance Management:
                'view-vendor-attendances',
                'create-vendor-attendance',
                'edit-vendor-attendance',
                'view-vendor-attendance-details',
                'approve-vendor-attendance',
                'send-vendor-attendance-reminders',
                'view-vendor-attendance-summary',
            
                
                //Invoice Management:
                'view-invoices',
                'create-invoice',
                'edit-invoice',
                'view-invoice-details',
                'verify-invoice',
                'download-invoice',
                'view-pending-invoices',
                'view-invoice-discrepancies',
                'view-invoice-summary',

                //Requirement Management:
                'view-requirements',
                'create-requirement',
                'edit-requirement',
                'delete-requirement',
                'view-requirement-details',
                'approve-requirement',
                'view-requirement-counts',

                //Requirement Management:
                'view-interviews',
                'create-interview',
                'edit-interview',
                'delete-interview',
                'view-interview-details',
                'submit-interview-feedback',
                'view-interview-stats',

                //User Management:
                'view-users',
                'create-user',
                'edit-user',
                'delete-user',

                //User Management:
                'view-roles',
                'create-role',
                'edit-role',
                'delete-role',

                // Vender Payment 
                'view-vendor-payments',
                'create-vendor-payments',
                'edit-vendor-payments',
                'delete-vendor-payments',
                'approve-vendor-payments',
                'reject-vendor-payments',
                'mark-vendor-payments-paid',
                'generate-vendor-payments',
                'view-vendor-payment-approval',
                'view-vendor-payment-processing',
                'view-vendor-payment-reports',
                'export-vendor-payments',
                'view-vendor-payment-api',

                // Company Management:
                'company-list',
                'company-create',
                'company-edit',
                'company-delete',

                // Client Payment Management:
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
                'accounts' => [
                        // Vendor Management:
                        'view-dashboard',
                        //Vendor Attendance Management:
                        'view-vendor-attendances',
                        'create-vendor-attendance',
                        'edit-vendor-attendance',
                        'view-vendor-attendance-details',
                        'approve-vendor-attendance',
                        'send-vendor-attendance-reminders',
                        'view-vendor-attendance-summary',
                    
                        
                        //Invoice Management:
                        'view-invoices',
                        'create-invoice',
                        'edit-invoice',
                        'view-invoice-details',
                        'verify-invoice',
                        'download-invoice',
                        'view-pending-invoices',
                        'view-invoice-discrepancies',
                        'view-invoice-summary',

                        // Vender Payment
                        'view-vendor-payments',
                        'view-vendor-payments',
                        'create-vendor-payments',
                        'edit-vendor-payments',
                        'delete-vendor-payments',
                        'approve-vendor-payments',
                        'reject-vendor-payments',
                        'mark-vendor-payments-paid',
                        'generate-vendor-payments',
                        'view-vendor-payment-approval',
                        'view-vendor-payment-processing',
                        'view-vendor-payment-reports',
                        'export-vendor-payments',
                        'view-vendor-payment-api'
                ],
                'poc' => [
                    // Vendor Management:
                    'view-dashboard',

                    // Client Payment Management:
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
                  // 'view-dashboard',
                    
                  // Candidate Sourcing:
                    'view-candidate-sourcing', 
                    'create-candidate-sourcing',
                    'edit-candidate-sourcing',
                    'delete-candidate-sourcing',
                    'view-candidate-sourcing-details',
                    'approve-candidate-sourcing',
                    'reject-candidate-sourcing',
                    'schedule-candidate-interview',
                    'upload-candidate',
                ],
                'bde' => [
                    // Vendor Management:

                    'view-dashboard',
                    //Requirement Management:
                    'view-requirements',
                    'create-requirement',
                    'edit-requirement',
                    'delete-requirement',
                    'view-requirement-details',
                    'approve-requirement',
                    'view-requirement-counts',

                    //Requirement Management:
                    'view-interviews',
                    'create-interview',
                    'edit-interview',
                    'delete-interview',
                    'view-interview-details',
                    'submit-interview-feedback',
                    'view-interview-stats',
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