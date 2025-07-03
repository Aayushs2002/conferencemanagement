<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class InternationalPayment extends Model
{
    protected $fillable = [
        'society_id',
        'merchant_key',
        'api_key',
        'access_token',
        'merchant_signing_private_key',
        'paco_encryption_public_key',
        'merchant_decryption_private_key',
        'paco_signing_public_key',
        'bank_detail',
        'status'
    ];
}
