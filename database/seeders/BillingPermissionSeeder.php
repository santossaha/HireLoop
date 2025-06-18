<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BillingPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create billing permissions
        $permissions = [
            'view-billing',
            'view-billing-details',
            'approve-billing',
            'reject-billing',
            'mark-billing-paid',
            'export-billing',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to admin role (if exists)
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // Assign permissions to manager role (if exists)
        $managerRole = Role::where('name', 'manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'view-billing',
                'view-billing-details',
                'approve-billing',
                'reject-billing',
                'export-billing',
            ]);
        }

        // Assign view permissions to user role (if exists)
        $userRole = Role::where('name', 'user')->first();
        if ($userRole) {
            $userRole->givePermissionTo([
                'view-billing',
                'view-billing-details',
            ]);
        }
    }
} 