<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Billing extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'onboarding_id',
        'final_budget',
        'start_date',
        'end_date',
        'requirement_id',
        'requirement_title',
        'vendor_name',
        'vendor_email',
        'vendor_phone',
        'candidate_name',
        'candidate_email',
        'candidate_phone',
        'month',
        'year',
        'total_working_days',
        'total_leave_days',
        'net_working_days',
        'per_day_salary',
        'monthly_salary',
        'status',
        'approved_at',
        'approved_by_name',
        'paid_at',
        'paid_by_name',
        'remarks'
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'total_working_days' => 'integer',
        'total_leave_days' => 'decimal:1',
        'net_working_days' => 'decimal:1',
        'per_day_salary' => 'decimal:2',
        'monthly_salary' => 'decimal:2',
        'final_budget' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PAID = 'paid';
    const STATUS_REJECTED = 'rejected';

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_APPROVED => 'badge-success',
            self::STATUS_PAID => 'badge-info',
            self::STATUS_REJECTED => 'badge-danger'
        ];

        return $badges[$this->status] ?? 'badge-secondary';
    }

    public function getMonthNameAttribute()
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    public function getFormattedMonthlySalaryAttribute()
    {
        return '₹' . number_format($this->monthly_salary, 2);
    }

    public function getFormattedPerDaySalaryAttribute()
    {
        return '₹' . number_format($this->per_day_salary, 2);
    }

    public function getFormattedFinalBudgetAttribute()
    {
        return '₹' . number_format($this->final_budget, 2);
    }

    // Optional: If you want to still access related data when available
    public function onboarding()
    {
        return $this->belongsTo(Onboarding::class)->withTrashed();
    }

    public function requirement()
    {
        return $this->belongsTo(Requirement::class, 'requirement_id', 'requirement_id')->withTrashed();
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class)->withTrashed();
    }

    public function candidate()
    {
        return $this->belongsTo(CandidateSourcing::class)->withTrashed();
    }
} 