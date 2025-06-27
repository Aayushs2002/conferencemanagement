<?php

namespace App\Models\Workshop;

use Illuminate\Database\Eloquent\Model;

class WorkshopRegistrationPrice extends Model
{
    protected $fillable = [
        'workshop_id',
        'member_type_id',
        'price',
        'discount_price',
        'status'
    ];
}
