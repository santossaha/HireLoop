<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Onboarding extends Model
{
    protected $fillable = [
        'requirement_id',
        'vendor_id',
        'candidate_id',
        'client_budget',
        'final_budget',
        'timesheet_link',
        'delivery_manager_name',
        'start_date',
        'billing_term',
        'cycle_date',
        'project_type',
        'status',
        'end_date',
        'end_comments',
        'end_reason_id'
    ];

    protected $casts = [
        'start_date' => 'date',
        'cycle_date' => 'date',
        'end_date' => 'date',
        'client_budget' => 'decimal:2',
        'final_budget' => 'decimal:2'
    ];

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(Requirement::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateSourcing::class, 'candidate_id');
    }

    public function endReason()
    {
        return $this->belongsTo(EndReason::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function getWorkingDays()
    {
        $startDate = $this->start_date;
        $endDate = $this->end_date ?? now();    
       
        
        $workingDays = 0;
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            // Skip weekends (Saturday = 6, Sunday = 0)
            if ($currentDate->dayOfWeek !== 0 && $currentDate->dayOfWeek !== 6) {
                $workingDays++;
            }
            $currentDate->addDay();
        }
        
        // Subtract leaves (for now hardcoded to 0 as requested)
        $leaves = 0;
        $workingDays -= $leaves;
        
        return max(0, $workingDays);
    }
} 