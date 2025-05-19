<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('companies')->insert([
            [
                'name' => 'MT',
                'detail' => 'Manektech Solution Pvt. Ltd.',
                'created_at' => now(),
                'updated_at' => now(),
                
            ],
            [
                'name' => 'EAD',
                'detail' => 'International business services company',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'LAVORG',
                'detail' => 'LAVORG',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 