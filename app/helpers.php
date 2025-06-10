<?php

use Illuminate\Support\Facades\Crypt;

function encrypt_id($id) {
    return Crypt::encryptString($id);
}

function decrypt_id($encryptedId) {
    return Crypt::decryptString($encryptedId);
}