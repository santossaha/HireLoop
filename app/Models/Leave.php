<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    protected $fillable = [
        'onboarding_id',
        'from_date',
        'to_date',
        'reason',
        'is_half_day',
        'total_days'
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'is_half_day' => 'boolean',
        'total_days' => 'decimal:1'
    ];

    public function onboarding(): BelongsTo
    {
        return $this->belongsTo(Onboarding::class);
    }
} 