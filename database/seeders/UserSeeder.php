<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create founder user
        User::firstOrCreate(
            ['email' => 'dilip@example.com'],
            [
                'name' => 'Dilipbhai',
                'password' => bcrypt('founder123'),
                'role' => 'founder',
                'email_verified_at' => now(),
            ]
        );

        // Create accounts user
        User::firstOrCreate(
            ['email' => 'accounts@example.com'],
            [
                'name' => 'Accounts Team',
                'password' => bcrypt('accounts123'),
                'role' => 'accounts',
                'email_verified_at' => now(),
            ]
        );

        // Create POC users for each department
        $departments = Department::all();
        
        foreach ($departments as $department) {
            $deptSlug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $department->name));
            
            User::firstOrCreate(
                ['email' => "poc_{$deptSlug}@example.com"],
                [
                    'name' => "POC {$department->name}",
                    'password' => bcrypt('poc123'),
                    'role' => 'poc',
                    'department_id' => $department->id,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
