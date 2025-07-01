<?php

use Illuminate\Support\Facades\Crypt;

if(!function_exists('encrypt_id')){
    function encrypt_id($id) {
        return Crypt::encryptString($id);
    }
}

if(!function_exists('decrypt_id')){
    function decrypt_id($encryptedId) {
        return Crypt::decryptString($encryptedId);
    }
}

if (!function_exists('get_status_badge_class')) {
    function get_status_badge_class($status) {
        $statusColors = [
            'pending' => 'bg-warning text-dark',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'paid' => 'bg-primary',
        ];
        return $statusColors[$status] ?? 'bg-secondary';
    }
}