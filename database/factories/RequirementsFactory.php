<?php

namespace Database\Factories;

use App\Models\Requirements;
use App\Models\Vendor;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequirementsFactory extends Factory
{
    protected $model = Requirements::class;

    public function definition(): array
    {
        $statuses = ['pending', 'approved', 'rejected'];
        $jobTitles = [
            'Senior PHP Developer',
            'Full Stack Developer',
            'Frontend Developer',
            'Backend Developer',
            'DevOps Engineer',
            'QA Engineer',
            'Project Manager',
            'UI/UX Designer'
        ];

        return [
            'vendor_id' => Vendor::factory(),
            'requirement_id' => 'REQ-' . $this->faker->unique()->numberBetween(1000, 9999),
            'job_description' => $this->faker->paragraph(3),
            'client_budget' => $this->faker->numberBetween(50000, 200000),
            'proposed_budget' => $this->faker->numberBetween(40000, 180000),
            'cv_path' => 'cvs/' . $this->faker->uuid() . '.pdf',
            'status' => $this->faker->randomElement($statuses),
            'hod_approved' => $this->faker->boolean(),
            'founder_approved' => $this->faker->boolean(),
            'department_id' => $this->faker->randomElement(Department::pluck('id')->toArray()),
            'approved_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'approved_by' => $this->faker->optional()->randomElement(User::pluck('id')->toArray()),
        ];
    }
} 