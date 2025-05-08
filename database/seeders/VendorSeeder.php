<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorSeeder extends Seeder
{
    public function run()
    {
        // Clear existing vendors and their associated users
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Vendor::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Create 100 vendors with associated users
        Vendor::factory()->count(12)->create();
    }
} 