<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
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
        'job_description',
        'client_budget',
        'proposed_budget',
        'cv_path',
        'status',
        'hod_approved',
        'founder_approved',
        'department_id',
        'approved_at',
        'approved_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hod_approved' => 'boolean',
        'founder_approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the vendor that submitted the requirement
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the department associated with the requirement
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who approved the requirement
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the interviews associated with this requirement
     */
    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * Check if the requirement is fully approved
     */
    public function isApproved()
    {
        return $this->hod_approved && $this->founder_approved;
    }

    /**
     * Scope a query to only include requirements pending HOD approval
     */
    public function scopePendingHodApproval($query)
    {
        return $query->where('hod_approved', false);
    }

    /**
     * Scope a query to only include requirements pending founder approval
     */
    public function scopePendingFounderApproval($query)
    {
        return $query->where('hod_approved', true)
                    ->where('founder_approved', false);
    }

    /**
     * Scope a query to only include approved requirements
     */
    public function scopeApproved($query)
    {
        return $query->where('hod_approved', true)
                    ->where('founder_approved', true);
    }

    /**
     * Scope a query to only include requirements for a specific department
     */
    public function scopeForDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }
}
