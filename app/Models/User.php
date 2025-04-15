<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the department that the user belongs to
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is HOD
     */
    public function isHod()
    {
        return $this->role === 'hod';
    }

    /**
     * Check if user is founder
     */
    public function isFounder()
    {
        return $this->role === 'founder';
    }

    /**
     * Check if user is POC
     */
    public function isPoc()
    {
        return $this->role === 'poc';
    }

    /**
     * Check if user is accounts
     */
    public function isAccounts()
    {
        return $this->role === 'accounts';
    }

    /**
     * Check if user is vendor
     */
    public function isVendor()
    {
        return $this->role === 'vendor';
    }

    /**
     * Get vendor associated with this user
     */
    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'email', 'email');
    }

    /**
     * Get the departments where user is HOD
     */
    public function managedDepartments()
    {
        return $this->hasMany(Department::class, 'hod_id');
    }
    
    /**
     * Get the client payments created by this user
     */
    public function createdClientPayments()
    {
        return $this->hasMany(ClientPayment::class, 'created_by');
    }
    
    /**
     * Get the client payments marked as received by this user
     */
    public function receivedClientPayments()
    {
        return $this->hasMany(ClientPayment::class, 'received_by');
    }
    
    /**
     * Get the vendor payments created by this user
     */
    public function createdVendorPayments()
    {
        return $this->hasMany(VendorPayment::class, 'created_by');
    }
    
    /**
     * Get the vendor payments approved by this user
     */
    public function approvedVendorPayments()
    {
        return $this->hasMany(VendorPayment::class, 'approved_by');
    }
    
    /**
     * Get the vendor payments marked as paid by this user
     */
    public function paidVendorPayments()
    {
        return $this->hasMany(VendorPayment::class, 'paid_by');
    }
    
    /**
     * Get the invoices uploaded by this user
     */
    public function uploadedInvoices()
    {
        return $this->hasMany(Invoice::class, 'uploaded_by');
    }
    
    /**
     * Get the invoices verified by this user
     */
    public function verifiedInvoices()
    {
        return $this->hasMany(Invoice::class, 'verified_by');
    }
    
    /**
     * Get the vendor attendance records submitted by this user
     */
    public function submittedAttendances()
    {
        return $this->hasMany(VendorAttendance::class, 'submitted_by');
    }
    
    /**
     * Get the vendor attendance records approved by this user
     */
    public function approvedAttendances()
    {
        return $this->hasMany(VendorAttendance::class, 'approved_by');
    }
    
    /**
     * Get the vendors for which this user is the point of contact
     */
    public function managedVendors()
    {
        return $this->hasMany(Vendor::class, 'internal_poc_id');
    }
    
    /**
     * Scope a query to only include users with specific roles.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  array|string  $roles
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereRole($query, $roles)
    {
        if (is_array($roles)) {
            return $query->whereIn('role', $roles);
        }
        
        return $query->where('role', $roles);
    }
    
    /**
     * Scope a query to find a user by ID.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFindById($query, $id)
    {
        return $query->where('id', $id)->first();
    }
    
    /**
     * Find a user by the given ID or fail.
     *
     * @param  int  $id
     * @return \App\Models\User
     */
    public static function findOrFail($id)
    {
        return static::where('id', $id)->firstOrFail();
    }
    
    /**
     * Create a new user.
     *
     * @param  array  $attributes
     * @return \App\Models\User
     */
    public static function create(array $attributes = [])
    {
        $model = new static;
        $model->fill($attributes);
        $model->save();
        
        return $model;
    }
}