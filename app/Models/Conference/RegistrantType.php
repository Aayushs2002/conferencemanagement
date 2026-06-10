<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;

class RegistrantType extends Model
{
    protected $fillable = [
        'name',
        'conference_id',
        'status',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    /**
     * Get all active types for a given conference:
     * global types (conference_id = null) that are NOT hidden for this conference,
     * plus conference-specific types.
     */
    public static function forConference($conferenceId)
    {
        $hiddenIds = \DB::table('conference_registrant_type_hidden')
            ->where('conference_id', $conferenceId)
            ->pluck('registrant_type_id')
            ->toArray();

        return static::where('status', 1)
            ->where(function ($query) use ($conferenceId, $hiddenIds) {
                $query->where(function ($q) use ($hiddenIds) {
                    $q->whereNull('conference_id');
                    if (!empty($hiddenIds)) {
                        $q->whereNotIn('id', $hiddenIds);
                    }
                })->orWhere('conference_id', $conferenceId);
            })
            ->orderBy('id')
            ->get();
    }

    /**
     * Get IDs of global types hidden for a given conference.
     */
    public static function hiddenIdsForConference($conferenceId): array
    {
        return \DB::table('conference_registrant_type_hidden')
            ->where('conference_id', $conferenceId)
            ->pluck('registrant_type_id')
            ->toArray();
    }

    /**
     * Cached name-by-id map for all active types (avoids N+1 in loops).
     */
    public static function getLabelMap(): array
    {
        static $cache = null;
        if ($cache === null) {
            $cache = static::where('status', 1)->pluck('name', 'id')->toArray();
        }
        return $cache;
    }

    public static function getLabel(int $id): string
    {
        return static::getLabelMap()[$id] ?? 'Unknown';
    }
}
