<?php

namespace Database\Factories;

use App\Models\Requirement;
use App\Models\Vendor;
use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequirementFactory extends Factory
{
    protected $model = Requirement::class;

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

        // Generate requirement_id in the format REQ-YYYY-MM-XXX
        $year = date('Y');
        $month = date('m');
        $sequence = $this->faker->unique()->numberBetween(1, 999);
        $requirement_id = sprintf("REQ-%s-%s-%03d", $year, $month, $sequence);

        return [
            'vendor_id' => Vendor::factory(),
            'requirement_id' => $requirement_id,
            'job_description' => $this->faker->paragraph(3),
            //'status' => $this->faker->randomElement($statuses),
            //'hod_approved' => $this->faker->boolean(),
            //'founder_approved' => $this->faker->boolean(),
            'department_id' => $this->faker->randomElement(Department::pluck('id')->toArray()),
            //'approved_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            //'approved_by' => $this->faker->optional()->randomElement(User::pluck('id')->toArray()),
        ];
    }
} 