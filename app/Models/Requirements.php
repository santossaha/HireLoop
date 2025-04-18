<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requirements extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'requirement_id',
        'job_description',
        'client_budget',
        'proposed_budget',
        'cv_path',
        'status',
        'hod_approved',
        'founder_approved',
        'department_id',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'hod_approved' => 'boolean',
        'founder_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
} 