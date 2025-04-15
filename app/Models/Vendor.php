<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Vendor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vendor_type',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'skype_id',
        'slack_id',
        'internal_poc_id',
        'budget_3_years',
        'budget_5_years',
        'budget_7_years',
        'budget_10_years',
        'status',
        'communication_rating',
        'technical_rating',
        'client_ready',
        'availability',
        'mt_ead_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'budget_3_years' => 'decimal:2',
        'budget_5_years' => 'decimal:2',
        'budget_7_years' => 'decimal:2',
        'budget_10_years' => 'decimal:2',
    ];

    /**
     * Get the user associated with the vendor
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Get the internal point of contact
     */
    public function internalPoc()
    {
        return $this->belongsTo(User::class, 'internal_poc_id');
    }

    /**
     * Get the requirements submitted by this vendor
     */
    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }

    /**
     * Get the interviews for this vendor
     */
    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    /**
     * Get the attendances for this vendor
     */
    public function attendances()
    {
        return $this->hasMany(VendorAttendance::class);
    }

    /**
     * Get the invoices submitted by this vendor
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get the payments made to this vendor
     */
    public function payments()
    {
        return $this->hasMany(VendorPayment::class);
    }
    
    /**
     * Scope a query to filter by department ID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $departmentId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDepartment($query, $departmentId)
    {
        return $query->whereHas('internalPoc', function ($q) use ($departmentId) {
            $q->where('department_id', $departmentId);
        });
    }
    
    /**
     * Scope a query to filter by internal POC ID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $pocId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPoc($query, $pocId)
    {
        return $query->where('internal_poc_id', $pocId);
    }
    
    /**
     * Scope a query to filter by vendor status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope a query to filter by vendor type.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByType($query, $type)
    {
        return $query->where('vendor_type', $type);
    }
    
    /**
     * Create a new vendor.
     *
     * @param  array  $attributes
     * @return \App\Models\Vendor
     */
    public static function create(array $attributes = [])
    {
        $model = new static;
        $model->fill($attributes);
        $model->save();
        
        return $model;
    }
}