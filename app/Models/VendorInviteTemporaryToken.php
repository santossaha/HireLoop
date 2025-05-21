<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorInviteTemporaryToken extends Model
{
    public function user_detail(){
        return $this->belongsTo(User::class,'user_id');
    }
}
