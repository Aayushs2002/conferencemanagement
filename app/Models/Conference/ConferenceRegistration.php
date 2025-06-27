<?php

namespace App\Models\Conference;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use DB;

class ConferenceRegistration extends Model
{
    protected $fillable = [
        'user_id',
        'conference_id',
        'registrant_type',
        'certificate_required',
        'attend_type',
        'payment_type',
        'payment_voucher',
        'amount',
        'transaction_id',
        'verified_status',
        'token',
        'total_attendee',
        'is_invited',
        'is_featured',
        'meal_type',
        'remarks',
        'short_cv',
        'status'
    ];

    // 1 for attendee, 2 for speaker/presenter, 3 for session chair, 4 for special guest
    // attend_type => // 1 for physical, 2 for online
    // verified_status => // 0 for pending, 1 for accepted, 2 for rejected

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accompanyPersons()
    {
        return $this->hasMany(AccompanyPerson::class, 'conference_registration_id', 'id');
    }

    public static function totalRegistrants($delegate, $society, $conference)
    {
        if ($conference == null) {
            $conferenceId = 0;
        } else {
            $conferenceId = $conference->id;
        }

        $cond = "AND MT.delegate = $delegate";

        $sql = "SELECT
    MT.id,
    MT.delegate,
    MT.type,
    COUNT(DISTINCT UD.user_id) AS user_count,
    STRING_AGG(DISTINCT UD.user_id::text, ',') AS user_ids
FROM member_types AS MT
LEFT JOIN
(
    SELECT
        US.member_type_id,
        UD.status AS ud_status,
        CR.status AS cr_status,
        CR.verified_status,
        CR.user_id
    FROM user_details AS UD
    JOIN user_societies AS US ON UD.user_id = US.user_id
    JOIN conference_registrations AS CR ON UD.user_id = CR.user_id
    WHERE UD.status = 1 AND CR.status = 1 AND CR.verified_status = 1 AND CR.conference_id = $conferenceId AND US.society_id = $society->id
) AS UD ON MT.id = UD.member_type_id
WHERE MT.status = 1 $cond
GROUP BY MT.id, MT.delegate, MT.type";



        $totalRegistrants = DB::select($sql);

        return $totalRegistrants;
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'conference_registration_id', 'id');
    }

    public function meals()
    {
        return $this->hasMany(Meal::class, 'conference_registration_id', 'id');
    }
}
