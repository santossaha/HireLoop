<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_id',
        'invoice_number',
        'amount',
        'invoice_date',
        'due_date',
        'status',
        'currency',
        'notes',
        'invoice_file_path',
        'uploaded_by',
        'verified_by',
        'verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the vendor that submitted this invoice
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user who uploaded this invoice
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the user who verified this invoice
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the vendor payments associated with this invoice
     */
    public function vendorPayments()
    {
        return $this->hasMany(VendorPayment::class);
    }

    /**
     * Check if the invoice is overdue
     *
     * @return bool
     */
    public function isOverdue()
    {
        if ($this->status !== 'pending' && $this->status !== 'partially_paid') {
            return false;
        }
        
        return $this->due_date->isPast();
    }

    /**
     * Get the total amount paid for this invoice
     *
     * @return float
     */
    public function getTotalPaidAmount()
    {
        return $this->vendorPayments()
            ->where('status', 'paid')
            ->sum('amount');
    }

    /**
     * Get the remaining amount to be paid for this invoice
     *
     * @return float
     */
    public function getRemainingAmount()
    {
        return $this->amount - $this->getTotalPaidAmount();
    }

    /**
     * Get the pending payments for this invoice
     */
    public function pendingPayments()
    {
        return $this->vendorPayments()
            ->whereIn('status', ['pending', 'approved'])
            ->get();
    }

    /**
     * Get invoice status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        $labels = [
            'pending' => 'Pending',
            'verified' => 'Verified',
            'partially_paid' => 'Partially Paid',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled',
            'rejected' => 'Rejected',
        ];

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Scope for pending invoices
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for verified invoices
     */
    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope for paid invoices
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for partially paid invoices
     */
    public function scopePartiallyPaid($query)
    {
        return $query->where('status', 'partially_paid');
    }

    /**
     * Scope for unpaid invoices (pending, verified, or partially paid)
     */
    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['pending', 'verified', 'partially_paid']);
    }

    /**
     * Scope for overdue invoices
     */
    public function scopeOverdue($query)
    {
        return $query->whereIn('status', ['pending', 'partially_paid'])
            ->where('due_date', '<', now());
    }
    
    /**
     * Create a new invoice.
     *
     * @param  array  $attributes
     * @return \App\Models\Invoice
     */
    public static function create(array $attributes = [])
    {
        $model = new static;
        $model->fill($attributes);
        $model->save();
        
        return $model;
    }
}