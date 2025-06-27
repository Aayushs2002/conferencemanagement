<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class NationalPayment extends Model 
{
    protected $fillable = [
        'society_id',
        'profile_id',
        'secret_key',
        'moco_merchant_id',
        'moco_outlet_id',
        'moco_terminal_id',
        'moco_shared_key',
        'esewa_secret_key',
        'esewa_product_code',
        'khalti_live_secret_key',
        'account_detail',
        'status'
    ];
}
