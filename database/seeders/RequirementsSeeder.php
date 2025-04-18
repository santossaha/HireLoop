<?php

namespace Database\Seeders;

use App\Models\Requirements;
use Illuminate\Database\Seeder;

class RequirementsSeeder extends Seeder
{
    public function run(): void
    {
        Requirements::factory()->count(100)->create();
    }
} 