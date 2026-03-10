<?php

namespace App\Models\Payment;

use App\Models\User\Country;
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
        'encryption_key_id',
        'bank_detail',
        'qr_details',
        'status',
        'payment_type'
    ];

    /**
     * Countries associated with this international payment
     */
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'international_payment_countries', 'international_payment_id', 'country_id');
    }
}
