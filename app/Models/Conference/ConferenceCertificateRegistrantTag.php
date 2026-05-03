<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ConferenceCertificateRegistrantTag extends Model
{
    protected $table = 'conference_certificate_registrant_tags';

    protected $fillable = [
        'conference_id',
        'registrant_type',
        'name_tag',
        'participating_text',
    ];
}
