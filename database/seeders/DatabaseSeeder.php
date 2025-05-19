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
            'description' => 'iOS, Android, Flutter, React Native, Unity, Unreal, AI/ML, QA, AR/VR/MR',
            'percentage' => 40
        ]);
        
        $openSource = Department::create([
            'name' => 'Open Source',
            'description' => 'PHP, Laravel, React.js, Node.js, Vue.js, Next.js, SalesForce, Magento, WordPress',
            'percentage' => 40
        ]);
        
        $dotNet = Department::create([
            'name' => 'DotNet',
            'description' => 'C#, .Net, Xamarin, SiteCore, NuGet, ASP.Net, Microsoft Azure',
            'percentage' => 40
        ]);


        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
        $admin->assignRole('admin');

      
        

        // Create HOD users
        $nikhil = User::firstOrCreate(
            ['email' => 'nikhil@gmail.com'],
            [
                'name' => 'Nikhil Solanki',
                'password' => Hash::make('password'),
                'role' => 'hod',
            ]
        );
        $nikhil->assignRole('hod');


        $ruchir = User::firstOrCreate(
            ['email' => 'ruchir@gmail.com'],
            [
                'name' => 'Ruchir Pandya',
                'password' => Hash::make('password'),
                'role' => 'hod',
                'department_id' => $openSource->id,
            ]
        );
        $ruchir->assignRole('hod');


        $milan = User::firstOrCreate(
            ['email' => 'milan@gmail.com'],
            [
                'name' => 'Milan Shah',
                'password' => Hash::make('password'),
                'role' => 'hod',
                'department_id' => $openSource->id,
            ]
        );
        $milan->assignRole('hod');


        // Update departments with HOD IDs
        $mobileTech->update(['hod_id' => $nikhil->id]);
        $openSource->update(['hod_id' => $ruchir->id]);
        $dotNet->update(['hod_id' => $milan->id]);

        // Create founder user

        $user1 = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Dilipbhai',
                'password' => Hash::make('password'),
                'role' => 'founder',
            ]
            );
        $user1->assignRole('founder');

        $user2 = User::firstOrCreate(
            ['email' => 'accounts@gmail.com'],
            [
                'name' => 'Accounts Manager',
                'password' => Hash::make('password'),
                'role' => 'accounts',
            ]
            );
        $user2->assignRole('accounts');

        $user3 = User::firstOrCreate(
            ['email' => 'poc@gmail.com'],
            [
                'name' => 'Project Coordinator',
                'password' => Hash::make('password'),
                'role' => 'poc',
            ]
            );
        $user3->assignRole('poc');

        $user4 = User::firstOrCreate(
            ['email' => 'vendor@gmail.com'],
            [
                'name' => 'Vendor User',
                'password' => Hash::make('password'),
                'role' => 'vendor',
            ]
            );
        $user4->assignRole('vendor');

        $user5 = User::firstOrCreate(
            ['email' => 'bde@gmail.com'],
            [
                'name' => 'BDE User',
                'password' => Hash::make('password'),
                'role' => 'bde',
            ]
            );
        $user5->assignRole('bde');

        // Seed Companies
        $this->call(CompanySeeder::class);

        // Seed vendors
        $this->call(VendorSeeder::class);

        // Call KeySkillSeeder
        $this->call(KeySkillSeeder::class);

        

        // Seed Requirements
        $this->call(RequirementSeeder::class);

      

        // Seed interviews
        $this->call([
            InterviewSeeder::class,
        ]);
    }
}