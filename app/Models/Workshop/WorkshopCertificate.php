<?php

namespace App\Models\Workshop;

use Illuminate\Database\Eloquent\Model;

class WorkshopCertificate extends Model
{
    protected $fillable = [
        'workshop_id',
        'background_image',
        'signature_image',
        'signature_name',
        'signature_designation',
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }
}
