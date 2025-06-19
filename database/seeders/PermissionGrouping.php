<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionGrouping extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Truncate the permissions table
        Permission::truncate();
        
        // Truncate the role_has_permissions table
        DB::table('role_has_permissions')->truncate();
        
        // Truncate the model_has_permissions table
        DB::table('model_has_permissions')->truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        $timestamp = Carbon::create(2025, 6, 6, 17, 34, 7);

        $permissions = [
            'view-dashboard',
            ];
        $client_permissions = [
            'view-client-payments',
            'create-client-payment',
            'edit-client-payment',
            'delete-client-payment',
            'view-client-payment-details',
            'mark-client-payment-received',
            'view-client-payment-dashboard',
        ];
        $vendor_permissions = [
            'view-vendors',
            'create-vendor',
            'edit-vendor',
            'delete-vendor',
            'view-vendor-details',
            'update-vendor-status',
            'view-vendor-approvals',
            'approve-vendor',
            'view-vendor-attendances',
            'create-vendor-attendance',
            'edit-vendor-attendance',
            'view-vendor-attendance-details',
            'approve-vendor-attendance',
            'send-vendor-attendance-reminders',
            'view-vendor-attendance-summary',
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
            'view-vendor-nda-document',
            'view-vendor-top-candidates',
        ];
        $invoice_permissions = [
            'view-invoices',
            'create-invoice',
            'edit-invoice',
            'view-invoice-details',
            'verify-invoice',
            'download-invoice',
            'view-pending-invoices',
            'view-invoice-discrepancies',
            'view-invoice-summary',
        ];
        $requirement_permissions = [
            'view-requirements',
            'create-requirement',
            'edit-requirement',
            'delete-requirement',
            'view-requirement-details',
            'approve-requirement',
            'view-requirement-counts',
        ];
        $interview_permissions = [

            'view-interviews',
            'create-interview',
            'edit-interview',
            'delete-interview',
            'view-interview-details',
            'submit-interview-feedback',
            'view-interview-stats',
        ];
        $user_permissions = [

            'view-users',
            'create-user',
            'edit-user',
            'delete-user',
            'view-roles',
            'create-role',
            'edit-role',
            'delete-role',
        ];
        $company_permissions = [
            'company-list',
            'company-create',
            'company-edit',
            'company-delete',
        ];
        $candidate_permissions = [

            'view-attendance-reports',
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
        $onboarding_permissions = [
            
            'view-onboarding-list',
            'create-onboarding',
            'view-onboarding-details',
            'edit-onboarding',
            'delete-onboarding',
        ];

        $billing_permissions = [
            'view-billing',
            'view-billing-details',
            'approve-billing',
            'reject-billing',
            'mark-billing-paid',
            'export-billing',
        ];

        foreach ($permissions as $name) {
            DB::table('permissions')->insert([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'group_name' => 'general',
            ]);
        }
        foreach ($client_permissions as $group_name => $name) {
            DB::table('permissions')->insert([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'group_name' => 'client',
            ]);
        }
        foreach ($candidate_permissions as $group_name => $name) {
            DB::table('permissions')->insert([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'group_name' => 'candidate',
            ]);
        }
        foreach ($vendor_permissions as $group_name => $name) {
            DB::table('permissions')->insert([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'group_name' => 'vendor',
            ]);
        }
        foreach ($interview_permissions as $group_name => $name) {
            DB::table('permissions')->insert([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'group_name' => 'interview',
            ]);
        }
        foreach ($invoice_permissions as $group_name => $name) {
            DB::table('permissions')->insert([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'group_name' => 'invoice',
            ]);
        }
        foreach ($requirement_permissions as $group_name => $name) {
            DB::table('permissions')->insert([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'group_name' => 'requirement',
            ]);
        }
        foreach ($company_permissions as $group_name => $name) {
            DB::table('permissions')->insert([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'group_name' => 'company',
            ]);
        }
        foreach ($user_permissions as $group_name => $name) {
            DB::table('permissions')->insert([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'group_name' => 'user',
            ]);
        }
        foreach ($onboarding_permissions as $group_name => $name) {
            DB::table('permissions')->insert([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'group_name' => 'onboarding',
            ]);
        }

        foreach ($billing_permissions as $group_name => $name) {
            DB::table('permissions')->insert([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
                'group_name' => 'onboarding',
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

            //onboarding
            'view-onboarding-list',
            'create-onboarding',
            'view-onboarding-details',
            'edit-onboarding',
            'delete-onboarding', 

            //vendor-top-candidates 
            'view-vendor-nda-document',
            'view-vendor-top-candidates',
 
            //billing 
            'view-billing',
            'view-billing-details',
            'approve-billing',
            'reject-billing',
            'mark-billing-paid',
            'export-billing',
        ];


       
        // Insert permissions
        // foreach ($permissions as $permission) {
        //     DB::table('permissions')->insertOrIgnore([
        //         'name' => $permission,
        //         'guard_name' => 'web',
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ]);
        // }
        
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

                //onboarding
                'view-onboarding-list',
                'create-onboarding',
                'view-onboarding-details',
                'edit-onboarding',
                'delete-onboarding',

                //Billing
                'view-billing',
                'view-billing-details',
                'approve-billing',
                'reject-billing',
                'mark-billing-paid',
                'export-billing',
            ],
            'hod' => [
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

                //onboarding
                'view-onboarding-list',
                'create-onboarding',
                'view-onboarding-details',
                'edit-onboarding',
                'delete-onboarding',
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
                'view-dashboard',
                
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
            'pm' => [
                'view-dashboard',
                
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
            'dm' => [
                'view-dashboard',
                
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

                //onboarding
                'view-onboarding-list',
                'create-onboarding',
                'view-onboarding-details',
                'edit-onboarding',
                'delete-onboarding',
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
