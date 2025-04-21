<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vendor;
use App\Models\Requirement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Interview>
 */
class InterviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vendor_id' => Vendor::factory(),
            'requirement_id' => Requirement::inRandomOrder()->first()->id,
            'interviewer_id' => User::inRandomOrder()->first()->id,
            'type' => fake()->randomElement(['mock', 'internal', 'client']),
            'scheduled_at' => fake()->dateTimeBetween('now', '+3 months'),
            'status' => fake()->randomElement(['scheduled', 'completed', 'cancelled']),
            'result' => fake()->optional(0.7)->randomElement(['pass', 'fail']),
            'feedback' => fake()->optional(0.8)->text(200),
            'communication_rating' => fake()->optional(0.7)->randomElement(['excellent', 'good', 'average', 'bad']),
            'technical_rating' => fake()->optional(0.7)->randomElement(['excellent', 'good', 'average', 'bad']),
            'client_interview_ready' => fake()->optional(0.6)->boolean(),
            'previously_worked_with_client' => fake()->optional(0.4)->boolean(),
            'selected_in_internal' => fake()->optional(0.5)->boolean(),
            'selected_in_client' => fake()->optional(0.5)->boolean(),
            'last_approved_budget' => fake()->optional(0.6)->randomFloat(2, 1000, 10000),
        ];
    }
} 