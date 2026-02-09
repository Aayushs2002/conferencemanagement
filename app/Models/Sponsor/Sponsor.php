<?php

namespace App\Models\Sponsor;

use App\Models\Conference\Conference;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Sponsor extends Model
{
    protected $fillable = [
        'conference_id',
        'sponsor_category_id',
        'name',
        'amount',
        'logo',
        'flyers_ads',
        'address',
        'contact_person',
        'email',
        'phone',
        'description',
        'total_attendee',
        'visible_status',
        'lunch_access',
        'dinner_access',
        'status',
        'token',
        'registration_id',
    ];

    public function category()
    {
        return $this->belongsTo(SponsorCategory::class, 'sponsor_category_id', 'id');
    }

    public function attendances()
    {
        return $this->hasMany(SponsorAttendance::class, 'sponsor_id', 'id');
    }

    public function meals()
    {
        return $this->hasMany(SponsorMeal::class, 'sponsor_id', 'id');
    }

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }

    /**
     * Update registration IDs for all sponsors in a conference
     * Sorted alphabetically by sponsor name
     * 
     * @param int $conferenceId
     * @return array Statistics about the update
     */
    public static function updateRegistrationIds($conferenceId): array
    {
        DB::beginTransaction();
        
        try {
            $stats = [
                'total' => 0
            ];

            // Get all sponsors for this conference sorted alphabetically by name
            $sponsors = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->orderBy('name')
                ->get();

            $counter = 1;
            foreach ($sponsors as $sponsor) {
                $sponsor->registration_id = 'SPO_' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                $sponsor->save();
                $counter++;
                $stats['total']++;
            }

            DB::commit();
            
            return $stats;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
