<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'project_type'
    ];

    protected $casts = [
        'start_date' => 'date',
        'cycle_date' => 'date',
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
} 