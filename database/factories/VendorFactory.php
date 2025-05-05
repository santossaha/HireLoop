<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class VendorFactory extends Factory
{
    protected $model = Vendor::class;

    public function definition()
    {
        // Create a user with vendor role first
        $user = User::create([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('password'),
            'role' => 'vendor',
        ]);

        // Assign vendor role and permissions
        $role = Role::where('name', 'vendor')->first();
        $user->assignRole($role);
        
        // Sync permissions based on the role
        $permissions = $role->permissions()->pluck('name')->toArray();
        $user->syncPermissions($permissions);

        $vendorTypes = ['company', 'freelancer'];
        $statuses = ['pending', 'approved', 'rejected'];
        $mtEadStatuses = ['pending', 'approved', 'rejected'];
        $technicalRatings = ['excellent', 'good', 'average', 'bad'];
        $communicationRatings = ['excellent', 'good', 'average', 'bad'];
        
        return [
            'vendor_type' => $this->faker->randomElement($vendorTypes),
            'user_id' => $user->id,
            'company_name' => $this->faker->company,
            'contact_person' => $user->name,
            'email' => $user->email,
            'phone' => $this->faker->phoneNumber,
            'skype_id' => $this->faker->userName,
            'slack_id' => $this->faker->userName,
            'internal_poc_id' => User::whereIn('role', ['admin', 'poc', 'hod'])->inRandomOrder()->first()->id,
            'budget_3_years' => $this->faker->randomFloat(2, 10000, 1000000),
            'budget_5_years' => $this->faker->randomFloat(2, 20000, 2000000),
            'budget_7_years' => $this->faker->randomFloat(2, 30000, 3000000),
            'budget_10_years' => $this->faker->randomFloat(2, 50000, 5000000),
            'status' => $this->faker->randomElement($statuses),
            'communication_rating' => $this->faker->randomElement($communicationRatings),
            'technical_rating' => $this->faker->randomElement($technicalRatings),
            'client_ready' => $this->faker->boolean,
            'availability' => $this->faker->randomElement(['full_time', 'part_time', 'contract']),
            'mt_ead_status' => $this->faker->randomElement($mtEadStatuses),
        ];
    }
} 