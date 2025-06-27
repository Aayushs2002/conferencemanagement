<?php

namespace App\Models\Workshop;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Workshop extends Model
{
    protected $fillable = [
        'conference_id',
        'workshop_title',
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
        'status'
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
}
