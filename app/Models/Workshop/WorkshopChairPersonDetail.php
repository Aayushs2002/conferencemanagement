<?php

namespace App\Models\Workshop;

use Illuminate\Database\Eloquent\Model;

class WorkshopChairPersonDetail extends Model
{
    protected $fillable = [
        'workshop_id',
        'chairperson_id',
        'photo',
        'short_cv'
    ];
}
