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
        Permission::truncate();
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

    }
}
