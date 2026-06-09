<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class MemberType extends Model
{
    protected $fillable = [
        'society_id',
        'delegate',
        'type',
        'display_order',
        'is_society_member',
        'requires_student_verification',
        'status'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('display_order', function ($query) {
            $query->orderByRaw('CASE WHEN display_order IS NULL THEN 1 ELSE 0 END, display_order ASC, id ASC');
        });
    }
}
