<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'requirement_id',
        'interviewer_id',
        'type', // 'mock', 'internal', 'client'
        'scheduled_at',
        'status', // 'scheduled', 'completed', 'cancelled'
        'result', // 'pass', 'fail'
        'feedback',
        'communication_rating',
        'technical_rating',
        'client_interview_ready',
        'previously_worked_with_client',
        'selected_in_internal',
        'selected_in_client',
        'last_approved_budget',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
        'client_interview_ready' => 'boolean',
        'previously_worked_with_client' => 'boolean',
        'selected_in_internal' => 'boolean',
        'selected_in_client' => 'boolean',
    ];

    /**
     * Get the vendor for this interview
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the requirement for this interview
     */
    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }

    /**
     * Get the interviewer who conducted the interview
     */
    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    /**
     * Scope a query to only include interviews of a specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include interviews with a specific status
     */
    public function scopeWithStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include interviews with a specific result
     */
    public function scopeWithResult($query, $result)
    {
        return $query->where('result', $result);
    }

    /**
     * Scope a query to only include upcoming interviews
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
                    ->where('status', 'scheduled');
    }

    /**
     * Check if this is a mock interview
     */
    public function isMockInterview()
    {
        return $this->type === 'mock';
    }

    /**
     * Check if this is an internal interview
     */
    public function isInternalInterview()
    {
        return $this->type === 'internal';
    }

    /**
     * Check if this is a client interview
     */
    public function isClientInterview()
    {
        return $this->type === 'client';
    }

    /**
     * Check if the interview is passed
     */
    public function isPassed()
    {
        return $this->result === 'pass';
    }
}
