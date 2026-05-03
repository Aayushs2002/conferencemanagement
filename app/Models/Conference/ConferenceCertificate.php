<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class ConferenceCertificate extends Model
{
    protected $fillable = [
        'conference_id',
        'background_image',
        'signature',
        'custom_css',
        'include_signature',
        'include_title',
        'show_presentation_type',
    ];

    protected $casts = [
        'signature' => 'array',
        'include_signature' => 'boolean',
        'include_title' => 'boolean',
        'show_presentation_type' => 'boolean',
    ];

    public function getSignatureAttribute($value)
    {
        $signatures = is_array($value) ? $value : (json_decode($value ?? '[]', true) ?: []);

        usort($signatures, function ($a, $b) {
            $orderA = (int) ($a['order'] ?? 9999);
            $orderB = (int) ($b['order'] ?? 9999);

            return $orderA <=> $orderB;
        });

        return array_values($signatures);
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
