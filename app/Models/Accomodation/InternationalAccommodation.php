<?php

namespace App\Models\Accomodation;

use App\Models\Conference\ConferenceRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternationalAccommodation extends Model
{
    protected $fillable = [
        'user_id',
        'conference_registration_id',
        'hotel_id',
        'flight_number',
        'arrival_date',
        'arrival_time',
        'departure_date',
        'departure_time',
        // 'arrival_flight_number',
        // 'departure_flight_number',
        'airport_pickup_required',
        'special_requirements',
        'check_in_date',
        'check_out_date',
        'room_type',
        'status',
        'created_by_admin'
    ];

    protected $casts = [
        'arrival_date' => 'date',
        'departure_date' => 'date',
        'arrival_time' => 'datetime',
        'departure_time' => 'datetime',
        'check_in_date' => 'date',
        'check_out_date' => 'date',
        'airport_pickup_required' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function conferenceRegistration(): BelongsTo
    {
        return $this->belongsTo(ConferenceRegistration::class);
    }
}