<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'hod_id',
    ];

    /**
     * Get the HOD (Head of Department) for this department.
     */
    public function hod()
    {
        return $this->belongsTo(User::class, 'hod_id');
    }

    /**
     * Get the users belonging to this department.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Create a new department.
     *
     * @param  array  $attributes
     * @return \App\Models\Department
     */
    public static function create(array $attributes = [])
    {
        $model = new static;
        $model->fill($attributes);
        $model->save();
        
        return $model;
    }
}