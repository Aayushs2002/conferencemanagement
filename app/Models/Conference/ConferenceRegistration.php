<?php

namespace App\Models\Conference;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        'status',
        'invitation_accepted_at',
        'invitation_response_token'
    ];

    protected $casts = [
        'invitation_accepted_at' => 'datetime',
        'is_invited' => 'boolean',
        'is_featured' => 'boolean',
        'certificate_required' => 'boolean',
    ];

    // Constants for better code readability
    public const REGISTRANT_ATTENDEE = 1;
    public const REGISTRANT_SPEAKER = 2;
    public const REGISTRANT_SESSION_CHAIR = 3;
    public const REGISTRANT_SPECIAL_GUEST = 4;

    public const ATTEND_PHYSICAL = 1;
    public const ATTEND_ONLINE = 2;

    public const STATUS_PENDING = 0;
    public const STATUS_ACCEPTED = 1;
    public const STATUS_REJECTED = 2;

    /**
     * Relationships
     */
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

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'conference_registration_id', 'id');
    }

    public function meals()
    {
        return $this->hasMany(Meal::class, 'conference_registration_id', 'id');
    }

    public function addons()
    {
        return $this->hasMany(ConferenceRegistration_addon::class, 'conference_registration_id', 'id');
    }

    public function internationalAccommodation()
    {
        return $this->hasOne(\App\Models\Accomodation\InternationalAccommodation::class);
    }

    /**
     * Scopes
     */
    public function scopeInvited(Builder $query): Builder
    {
        return $query->where('is_invited', true);
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->where('verified_status', self::STATUS_ACCEPTED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('verified_status', self::STATUS_PENDING);
    }

    public function scopeInvitationAccepted(Builder $query): Builder
    {
        return $query->whereNotNull('invitation_accepted_at');
    }

    public function scopeInternationalParticipants(Builder $query): Builder
    {
        return $query->whereHas('user.userDetail', function ($q) {
            $q->where('country_id', '!=', 125); // Assuming 125 is domestic country ID
        });
    }

    /**
     * Accessors & Mutators
     */
    protected function registrantTypeText(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->registrant_type) {
                self::REGISTRANT_ATTENDEE => 'Attendee',
                self::REGISTRANT_SPEAKER => 'Speaker/Presenter',
                self::REGISTRANT_SESSION_CHAIR => 'Session Chair',
                self::REGISTRANT_SPECIAL_GUEST => 'Special Guest',
                default => 'Unknown'
            }
        );
    }

    protected function verifiedStatusText(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->verified_status) {
                self::STATUS_PENDING => 'Pending',
                self::STATUS_ACCEPTED => 'Accepted',
                self::STATUS_REJECTED => 'Rejected',
                default => 'Unknown'
            }
        );
    }

    /**
     * Helper Methods
     */
    public function isInvited(): bool
    {
        return $this->is_invited;
    }

    public function isSelfRegistered(): bool
    {
        return !$this->is_invited;
    }

    public function hasAcceptedInvitation(): bool
    {
        return !is_null($this->invitation_accepted_at);
    }

    public function isInternationalParticipant(): bool
    {
        return $this->user?->userDetail?->country_id !== 125; // Assuming 125 is domestic country
    }

    public function canFillOwnAccommodation(): bool
    {
        return $this->isInternationalParticipant() &&
            $this->isSelfRegistered() &&
            $this->verified_status === self::STATUS_ACCEPTED;
    }

    public function canReceiveAdminAccommodation(): bool
    {
        return $this->isInternationalParticipant() &&
            $this->isInvited() &&
            $this->hasAcceptedInvitation() &&
            $this->verified_status === self::STATUS_ACCEPTED;
    }

    public function canReceiveAccommodation(): bool
    {
        return $this->canFillOwnAccommodation() || $this->canReceiveAdminAccommodation();
    }

    public function acceptInvitation(): bool
    {
        if (!$this->isInvited() || $this->hasAcceptedInvitation()) {
            return false;
        }

        return $this->update([
            'invitation_accepted_at' => now(),
            'verified_status' => self::STATUS_ACCEPTED
        ]);
    }

    public function generateInvitationToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->update(['invitation_response_token' => $token]);
        return $token;
    }

    /**
     * Optimized totalRegistrants method using Eloquent
     */
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
    /**
     * Get participants eligible for accommodation (both self-registered and invited)
     */
    public static function getAccommodationEligibleParticipants($conference)
    {
        return self::with(['user.userDetail.country', 'internationalAccommodation'])
            ->where('conference_id', $conference->id)
            ->where('verified_status', self::STATUS_ACCEPTED)
            ->where(function ($query) {
                // Self-registered international participants
                $query->where('is_invited', false)
                    ->whereHas('user.userDetail', function ($q) {
                        $q->where('country_id', '!=', 125);
                    });
            })
            ->orWhere(function ($query) use ($conference) {
                // Invited international participants who accepted
                $query->where('conference_id', $conference->id)
                    ->where('is_invited', true)
                    ->whereNotNull('invitation_accepted_at')
                    ->where('verified_status', self::STATUS_ACCEPTED)
                    ->whereHas('user.userDetail', function ($q) {
                        $q->where('country_id', '!=', 125);
                    });
            })
            ->get();
    }

    /**
     * Get invited participants awaiting admin accommodation setup
     */
    public static function getInvitedAwaitingAccommodation($conference)
    {
        return self::with(['user.userDetail.country'])
            ->where('conference_id', $conference->id)
            ->where('is_invited', true)
            ->whereNotNull('invitation_accepted_at')
            ->where('verified_status', self::STATUS_ACCEPTED)
            ->whereHas('user.userDetail', function ($q) {
                $q->where('country_id', '!=', 125);
            })
            ->whereDoesntHave('internationalAccommodation')
            ->get();
    }

    /**
     * Get self-registered participants who need to fill accommodation
     */
    public static function getSelfRegisteredNeedingAccommodation($conference)
    {
        return self::with(['user.userDetail.country'])
            ->where('conference_id', $conference->id)
            ->where('is_invited', false)
            ->where('verified_status', self::STATUS_ACCEPTED)
            ->whereHas('user.userDetail', function ($q) {
                $q->where('country_id', '!=', 125);
            })
            ->whereDoesntHave('internationalAccommodation')
            ->get();
    }

    /**
     * Check if participant needs accommodation reminder
     */
    public function needsAccommodationReminder(): bool
    {
        return $this->canReceiveAccommodation() &&
            !$this->internationalAccommodation;
    }
}
