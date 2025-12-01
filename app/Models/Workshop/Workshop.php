<?php

namespace App\Models\Workshop;

use App\Models\User;
use App\Models\WorkshopRating;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Workshop extends Model
{
    protected $fillable = [
        'conference_id',
        'workshop_title',
        'image',
        'workshop_type',
        'start_date',
        'end_date',
        'registration_deadline',
        'start_time',
        'end_time',
        'contact_person_name',
        'contact_person_phone',
        'contact_person_email',
        'no_of_participants',
        'workshop_description',
        'status',
        'slug',
        'schedule_plan_attachment',
        'created_by',
        'approval_status',
        'admin_remarks',
        'reviewed_by',
        'reviewed_at',
        'proposed_budget',
        'registration_fee',
        'overview_of_organiztion',
        'training_method_expected_outcome',
        'resource_requirement'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function getRouteKey()
    {
        return Hashids::encode($this->attributes['id']);
    }

    public static function findByHashid($hashid)
    {
        $id = Hashids::decode($hashid)[0] ?? null;
        return static::findOrFail($id);
    }

    public function WorkshopVenueDetail()
    {
        return $this->hasOne(WorkshopVenueDetail::class, 'workshop_id', 'id');
    }

    public function WorkshopChairPersonDetail()
    {
        return $this->hasOne(WorkshopChairPersonDetail::class, 'workshop_id', 'id');
    }

    public function registrations()
    {
        return $this->hasMany(WorkshopRegistration::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    public function scopeCorrectionNeeded($query)
    {
        return $query->where('approval_status', 'correction_needed');
    }

    // Helper methods
    public function isPending()
    {
        return $this->approval_status === 'pending';
    }

    public function isApproved()
    {
        return $this->approval_status === 'approved';
    }

    public function isRejected()
    {
        return $this->approval_status === 'rejected';
    }

    public function needsCorrection()
    {
        return $this->approval_status === 'correction_needed';
    }

    public function getStatusBadgeClass()
    {
        return match ($this->approval_status) {
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'correction_needed' => 'bg-info',
            default => 'bg-secondary'
        };
    }

    public function getStatusLabel()
    {
        return match ($this->approval_status) {
            'pending' => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'correction_needed' => 'Correction Needed',
            default => 'Unknown'
        };
    }
    
    public function ratings()
    {
        return $this->hasMany(WorkshopRating::class);
    }
    // public function trainers()
    // {
    //     return $this->hasMany(WorkshopTrainer::class,'workshop_id', 'id');
    // }
}
