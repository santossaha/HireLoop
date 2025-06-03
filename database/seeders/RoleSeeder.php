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
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'hod', 'guard_name' => 'web'],
            ['name' => 'founder', 'guard_name' => 'web'],
            ['name' => 'poc', 'guard_name' => 'web'],
            ['name' => 'accounts', 'guard_name' => 'web'],
            ['name' => 'vendor', 'guard_name' => 'web'],
            ['name' => 'pm', 'guard_name' => 'web'],
            ['name' => 'dm', 'guard_name' => 'web'],
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
