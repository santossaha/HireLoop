<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorAttendance extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'date',
        'status',
        'hours_worked',
        'submitted_by',
        'approved_by',
        'approved_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'hours_worked' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the vendor for this attendance record
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user who submitted this attendance record
     */
    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Get the user who approved this attendance record
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Check if the attendance is approved
     */
    public function isApproved()
    {
        return !is_null($this->approved_by) && !is_null($this->approved_at);
    }

    /**
     * Get status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $labels = [
            'present' => 'Present',
            'absent' => 'Absent',
            'leave' => 'Leave',
            'approved_leave' => 'Approved Leave',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Scope for attendance records from today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }

    /**
     * Scope for attendance records from this week
     */
    public function scopeThisWeek($query)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        return $query->whereBetween('date', [$startOfWeek, $endOfWeek]);
    }

    /**
     * Scope for attendance records from this month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
    }

    /**
     * Scope for attendance records by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope for present attendances
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope for absent attendances
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    /**
     * Scope for leave attendances
     */
    public function scopeOnLeave($query)
    {
        return $query->whereIn('status', ['leave', 'approved_leave']);
    }

    /**
     * Scope for pending approval attendances
     */
    public function scopePendingApproval($query)
    {
        return $query->whereNull('approved_by')
            ->orWhereNull('approved_at');
    }

    /**
     * Scope for approved attendances
     */
    public function scopeApproved($query)
    {
        return $query->whereNotNull('approved_by')
            ->whereNotNull('approved_at');
    }
    
    /**
     * Create a new vendor attendance record.
     *
     * @param  array  $attributes
     * @return \App\Models\VendorAttendance
     */
    public static function create(array $attributes = [])
    {
        $model = new static;
        $model->fill($attributes);
        $model->save();
        
        return $model;
    }
}