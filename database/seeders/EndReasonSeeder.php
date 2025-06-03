<?php

namespace Database\Seeders;

use App\Models\EndReason;
use Illuminate\Database\Seeder;

class EndReasonSeeder extends Seeder
{
    public function run()
    {
        $reasons = [
            'Project Completed',
            'Budget Constraints',
            'Performance Issues',
            'Client Requirements Changed',
            'Resource Not Available',
            'Contract Expired',
            'Mutual Agreement',
            'Quality Issues',
            'Communication Problems',
            'Technical Mismatch',
            'Client Project Cancelled',
            'Vendor Requested Termination',
            'Company Policy Violation',
            'Better Opportunity Found',
            'Health Issues'
        ];

        foreach ($reasons as $reason) {
            EndReason::create([
                'name' => $reason
            ]);
        }
    }
} 