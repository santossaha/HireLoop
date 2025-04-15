<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run()
    {
        // Create 100 vendors
        Vendor::factory()->count(100)->create();
    }
} 