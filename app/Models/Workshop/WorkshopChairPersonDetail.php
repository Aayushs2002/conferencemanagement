<?php

namespace App\Models\Workshop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class WorkshopChairPersonDetail extends Model
{
    protected $fillable = [
        'workshop_id',
        'chairperson_id',
        'photo',
        'short_cv'
    ];

    public function chairPerson()
    {
        return $this->belongsTo(User::class, 'chairperson_id');
    }
}
