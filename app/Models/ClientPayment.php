<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'client_name',
        'invoice_number',
        'payment_amount',
        'payment_date',
        'payment_status',
        'confirmed_by',
        'confirmed_at',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_amount' => 'decimal:2',
        'payment_date' => 'date',
        'confirmed_at' => 'datetime',
    ];

    /**
     * Get the user who created this client payment
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who marked this payment as received
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Get the vendor payments linked to this client payment
     */
    public function vendorPayments()
    {
        return $this->hasMany(VendorPayment::class);
    }

    /**
     * Check if the client payment is received
     */
    public function isReceived()
    {
        return $this->payment_status === 'received';
    }

    /**
     * Check if the client payment is overdue
     */
    public function isOverdue()
    {
        if ($this->payment_status !== 'pending') {
            return false;
        }
        
        return $this->payment_date && $this->payment_date->isPast();
    }

    /**
     * Get the total amount allocated to vendor payments
     *
     * @return float
     */
    public function getTotalAllocatedAmount()
    {
        return $this->vendorPayments()->sum('amount');
    }

    /**
     * Get the remaining amount that can be allocated
     *
     * @return float
     */
    public function getRemainingAmount()
    {
        return $this->payment_amount - $this->getTotalAllocatedAmount();
    }

    /**
     * Get payment status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $labels = [
            'pending' => 'Pending',
            'received' => 'Received',
            'cancelled' => 'Cancelled',
            'partial' => 'Partially Received',
        ];

        return $labels[$this->payment_status] ?? ucfirst($this->payment_status);
    }

    /**
     * Scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope for received payments
     */
    public function scopeReceived($query)
    {
        return $query->where('payment_status', 'received');
    }

    /**
     * Scope for cancelled payments
     */
    public function scopeCancelled($query)
    {
        return $query->where('payment_status', 'cancelled');
    }

    /**
     * Scope for partially received payments
     */
    public function scopePartial($query)
    {
        return $query->where('payment_status', 'partial');
    }

    /**
     * Scope for payments received this month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    /**
     * Scope for payments by month and year
     */
    public function scopeByMonthYear($query, $month, $year)
    {
        return $query->whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year);
    }

    /**
     * Scope for payments by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    /**
     * Scope for overdue payments
     */
    public function scopeOverdue($query)
    {
        return $query->where('payment_status', 'pending')
            ->where('payment_date', '<', now());
    }
    
    /**
     * Create a new client payment.
     *
     * @param  array  $attributes
     * @return \App\Models\ClientPayment
     */
    public static function create(array $attributes = [])
    {
        $model = new static;
        $model->fill($attributes);
        $model->save();
        
        return $model;
    }
    
    /**
     * Scope a query to filter by value.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $field
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhere($query, $field, $value)
    {
        return $query->where($field, $value);
    }
}