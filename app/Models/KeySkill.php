<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KeySkill extends Model
{
    protected $fillable = ['name'];

    public function vendors(): BelongsToMany
    {
        return $this->belongsToMany(Vendor::class, 'vendor_key_skills');
    }
} 