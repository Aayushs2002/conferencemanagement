<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class InvitationCategory extends Model
{
    protected $fillable = ['conference_id', 'name', 'description', 'display_order', 'status'];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the conference that owns this invitation category.
     */
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    /**
     * Get all conference registrations assigned to this category.
     */
    public function registrations()
    {
        return $this->hasMany(ConferenceRegistration::class, 'invitation_category_id');
    }

    /**
     * Scope to get active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to get categories ordered by display order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('id');
    }
}

