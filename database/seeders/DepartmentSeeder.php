<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\User;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define departments from the requirements
        $departments = [
            [
                'name' => 'Mobile Technology',
                'description' => 'iOS, Android, Flutter, React Native, Unity, Unreal, AI/ML, QA, AR/VR/MR',
                'hod_name' => 'Nikhil Solanki',
                'hod_email' => 'nikhil.solanki@example.com',
            ],
            [
                'name' => 'Open Source',
                'description' => 'PHP, Laravel, React.js, Node.js, Vue.js, Next.js, SalesForce, Magento, WordPress',
                'hod_name' => 'Ruchir Pandya',
                'hod_email' => 'ruchir.pandya@example.com',
            ],
            [
                'name' => '.Net',
                'description' => 'C#, .Net, Xamarin, SiteCore, NuGet, ASP.Net, Microsoft Azure',
                'hod_name' => 'Milan Shah',
                'hod_email' => 'milan.shah@example.com',
            ],
        ];

        foreach ($departments as $dept) {
            // First create/find the HOD user
            $hod = User::firstOrCreate(
                ['email' => $dept['hod_email']],
                [
                    'name' => $dept['hod_name'],
                    'password' => bcrypt('password'), // Default password, should be changed
                    'role' => 'hod',
                    'email_verified_at' => now(),
                ]
            );

            // Then create the department
            Department::create([
                'name' => $dept['name'],
                'description' => $dept['description'],
                'hod_id' => $hod->id,
            ]);
        }
    }
}
