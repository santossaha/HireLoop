<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CandidateSourcing extends Model
{
    use HasFactory;

    protected $fillable = [
        'requirement_id',
        'uploaded_by',
        'candidate_name',
        'email',
        'phone',
        'resume_path',
        'budget',
        'candidate_details',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
        'interview_scheduled_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'interview_scheduled_at' => 'datetime'
    ];

    public function interviews()
    {
        return $this->hasOne(Interview::class, 'candidate_id');
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function interviewSchedule()
    {
        return $this->hasOne(InterviewSchedule::class, 'candidate_id');
    }
}
