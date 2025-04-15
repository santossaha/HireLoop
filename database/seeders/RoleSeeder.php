<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the roles
        $roles = [
            ['name' => 'admin', 'description' => 'Administrator with full access'],
            ['name' => 'hod', 'description' => 'Head of Department'],
            ['name' => 'founder', 'description' => 'Company Founder'],
            ['name' => 'poc', 'description' => 'Point of Contact'],
            ['name' => 'accounts', 'description' => 'Accounts Team Member'],
            ['name' => 'vendor', 'description' => 'External Vendor'],
        ];

        // Insert roles into database
        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role['name'],
                'description' => $role['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
