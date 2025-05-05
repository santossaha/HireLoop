<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InterviewSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id',
        'requirement_id',
        'proceed_for_mock',
        'mock_datetime'
    ];

    protected $casts = [
        'proceed_for_mock' => 'boolean',
        'mock_datetime' => 'datetime'
    ];

    public function candidate()
    {
        return $this->belongsTo(CandidateSourcing::class, 'candidate_id');
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }
}
