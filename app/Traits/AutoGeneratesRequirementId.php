<?php

namespace App\Traits;

use App\Models\Requirement;

trait AutoGeneratesRequirementId
{
    protected static function bootAutoGeneratesRequirementId()
    {
        static::creating(function ($model) {
            if (empty($model->requirement_id)) {
                $model->requirement_id = static::generateRequirementId();
            }
        });
    }

    protected static function generateRequirementId()
    {
        $year = date('Y');
        $month = date('m');
        
        // Get the last requirement ID for this year and month
        $lastRequirement = Requirement::where('requirement_id', 'like', "REQ-{$year}-{$month}-%")
            ->orderBy('requirement_id', 'desc')
            ->first();

        if ($lastRequirement) {
            // Extract the sequence number and increment it
            $parts = explode('-', $lastRequirement->requirement_id);
            $sequence = (int) $parts[3] + 1;
        } else {
            // Start with sequence 1 if no requirements exist for this month
            $sequence = 1;
        }

        // Format: REQ-YYYY-MM-XXX (where XXX is a 3-digit sequence number)
        return sprintf("REQ-%s-%s-%03d", $year, $month, $sequence);
    }
} 