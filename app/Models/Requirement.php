<?php

namespace App\Models;

use App\Traits\AutoGeneratesRequirementId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\RequirementNotification;
use App\Models\User;

class Requirement extends Model
{
    use HasFactory, SoftDeletes, AutoGeneratesRequirementId;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'requirement_id',
        'job_description',
        'department_id',
        'create_by',
        'status',
       // 'hod_approved',
       // 'founder_approved',
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

    protected static function booted()
    {
        static::created(function ($requirement) {
            // Notify all vendor users when a new requirement is created
            $vendorUsers =  User::role('vendor')->get();
            foreach ($vendorUsers as $user) {
                $user->notify(new RequirementNotification($requirement, 'created'));
            }
        });
    }

    public function notifyResumeUploaded()
    {
        // Notify BDE user when a resume is uploaded
        $bdeUser = User::role('bde')->first();
        if ($bdeUser) {
            $bdeUser->notify(new RequirementNotification($this, 'resume_uploaded'));
        }
    }

    public function candidateSourcing()
    {
        return $this->hasMany(CandidateSourcing::class, 'requirement_id', 'id');
    }

    public function createBy()
    {
        return $this->belongsTo(User::class, 'create_by');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'create_by', 'user_id');
    }

    /**
     * Get the vendor that submitted the requirement
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
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
     * Get the candidate sourcing records for this requirement
     */
    public function candidateSourcings()
    {
        return $this->hasMany(CandidateSourcing::class);
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
