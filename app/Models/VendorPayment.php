<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vendor_id',
        'amount',
        'currency',
        'payment_date',
        'payment_method',
        'invoice_id',
        'client_payment_id',
        'notes',
        'payment_status',
        'created_by',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'paid_by',
        'paid_at',
        'transaction_reference',
        'payment_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'payment_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'paid_at' => 'datetime',
        'amount' => 'float',
    ];

    /**
     * Get the payment method label.
     *
     * @return string
     */
    public function getPaymentMethodLabel()
    {
        $methods = [
            'bank_transfer' => 'Bank Transfer',
            'paypal' => 'PayPal',
            'wise' => 'Wise (TransferWise)',
            'payoneer' => 'Payoneer',
            'other' => 'Other',
        ];

        return $methods[$this->payment_method] ?? ucfirst($this->payment_method);
    }

    /**
     * Get the vendor associated with the payment.
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the invoice associated with the payment.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the client payment associated with the payment.
     */
    public function clientPayment()
    {
        return $this->belongsTo(ClientPayment::class);
    }

    /**
     * Get the user who created the payment.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the payment.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who rejected the payment.
     */
    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Get the user who marked the payment as paid.
     */
    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Check if the payment is in draft status.
     */
    public function isDraft()
    {
        return $this->payment_status === 'draft';
    }

    /**
     * Check if the payment is pending approval.
     */
    public function isPendingApproval()
    {
        return $this->payment_status === 'pending_approval';
    }

    /**
     * Check if the payment is approved.
     */
    public function isApproved()
    {
        return $this->payment_status === 'approved';
    }

    /**
     * Check if the payment is paid.
     */
    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if the payment is rejected.
     */
    public function isRejected()
    {
        return $this->payment_status === 'rejected';
    }

    /**
     * Scope a query to only include draft payments.
     */
    public function scopeDraft($query)
    {
        return $query->where('payment_status', 'draft');
    }

    /**
     * Scope a query to only include pending approval payments.
     */
    public function scopePendingApproval($query)
    {
        return $query->where('payment_status', 'pending_approval');
    }

    /**
     * Scope a query to only include approved payments.
     */
    public function scopeApproved($query)
    {
        return $query->where('payment_status', 'approved');
    }

    /**
     * Scope a query to only include paid payments.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope a query to only include rejected payments.
     */
    public function scopeRejected($query)
    {
        return $query->where('payment_status', 'rejected');
    }

    /**
     * Scope a query to only include payments for a specific vendor.
     */
    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /**
     * Scope a query to only include payments created by a specific user.
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope a query to only include payments approved by a specific user.
     */
    public function scopeApprovedBy($query, $userId)
    {
        return $query->where('approved_by', $userId);
    }

    /**
     * Scope a query to only include payments paid by a specific user.
     */
    public function scopePaidBy($query, $userId)
    {
        return $query->where('paid_by', $userId);
    }

    /**
     * Scope a query to only include payments for a specific month and year.
     */
    public function scopeForMonthYear($query, $month, $year)
    {
        return $query->whereMonth('payment_date', $month)
                    ->whereYear('payment_date', $year);
    }

    /**
     * Scope a query to only include payments paid in a specific month and year.
     */
    public function scopePaidInMonthYear($query, $month, $year)
    {
        return $query->whereMonth('paid_at', $month)
                    ->whereYear('paid_at', $year);
    }
    
    /**
     * Create a new vendor payment.
     *
     * @param  array  $attributes
     * @return \App\Models\VendorPayment
     */
    public static function create(array $attributes = [])
    {
        $model = new static;
        $model->fill($attributes);
        $model->save();
        
        return $model;
    }
}