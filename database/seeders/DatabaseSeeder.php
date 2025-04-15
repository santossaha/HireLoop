<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Departments
        $mobileTech = Department::create([
            'name' => 'Mobile Technology',
            'description' => 'iOS, Android, Flutter, React Native, Unity, Unreal, AI/ML, QA, AR/VR/MR'
        ]);
        
        $openSource = Department::create([
            'name' => 'Open Source',
            'description' => 'PHP, Laravel, React.js, Node.js, Vue.js, Next.js, SalesForce, Magento, WordPress'
        ]);
        
        $dotNet = Department::create([
            'name' => 'DotNet',
            'description' => 'C#, .Net, Xamarin, SiteCore, NuGet, ASP.Net, Microsoft Azure'
        ]);

        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@vendormanagement.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // Create HOD users
        $nikhil = User::create([
            'name' => 'Nikhil Solanki',
            'email' => 'nikhil@vendormanagement.com',
            'password' => Hash::make('password123'),
            'role' => 'hod',
            'department_id' => $mobileTech->id,
        ]);

        $ruchir = User::create([
            'name' => 'Ruchir Pandya',
            'email' => 'ruchir@vendormanagement.com',
            'password' => Hash::make('password123'),
            'role' => 'hod',
            'department_id' => $openSource->id,
        ]);

        $milan = User::create([
            'name' => 'Milan Shah',
            'email' => 'milan@vendormanagement.com',
            'password' => Hash::make('password123'),
            'role' => 'hod',
            'department_id' => $dotNet->id,
        ]);

        // Update departments with HOD IDs
        $mobileTech->update(['hod_id' => $nikhil->id]);
        $openSource->update(['hod_id' => $ruchir->id]);
        $dotNet->update(['hod_id' => $milan->id]);

        // Create founder user
        User::create([
            'name' => 'Dilipbhai',
            'email' => 'founder@vendormanagement.com',
            'password' => Hash::make('password123'),
            'role' => 'founder',
        ]);

        // Create accounts user
        User::create([
            'name' => 'Accounts Manager',
            'email' => 'accounts@vendormanagement.com',
            'password' => Hash::make('password123'),
            'role' => 'accounts',
        ]);

        // Create POC user
        User::create([
            'name' => 'Project Coordinator',
            'email' => 'poc@vendormanagement.com',
            'password' => Hash::make('password123'),
            'role' => 'poc',
        ]);

        // Create vendor user
        User::create([
            'name' => 'Vendor User',
            'email' => 'vendor@vendormanagement.com',
            'password' => Hash::make('password123'),
            'role' => 'vendor',
        ]);
    }
}