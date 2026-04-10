<?php

namespace App\Models\Conference;

use App\Models\Committee\CommitteeMember;
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
        'payment_currency',
        'transaction_id',
        'verified_status',
        'token',
        'total_attendee',
        'registration_id',
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
    public const REGISTRANT_ORGANIZER = 5;
    public const REGISTRANT_FACULTY = 6; 
    public const REGISTRANT_VOLUNTEER = 7; 
    

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

    public function conferenceRegistrationKit()
    {
        return $this->hasOne(ConferenceRegistrationKit::class, 'conference_registration_id', 'id');
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
                self::REGISTRANT_ORGANIZER => 'Organizer',
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
WHERE MT.status = 1 AND MT.society_id = $society->id $cond
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

    /**
     * Get conference statistics with single optimized query
     * 
     * @param int $conferenceId
     * @return array
     */
    public static function getConferenceStats($conferenceId): array
    {
        $stats = DB::table('conference_registrations as cr')
            ->join('users as u', 'cr.user_id', '=', 'u.id')
            ->join('user_details as ud', 'u.id', '=', 'ud.user_id')
            ->where('cr.conference_id', $conferenceId)
            ->where('cr.status', 1)
            ->where('cr.verified_status', self::STATUS_ACCEPTED)
            ->selectRaw('
                COUNT(*) as total_participants,
                SUM(CASE WHEN cr.registrant_type = ? THEN 1 ELSE 0 END) as speakers,
                SUM(CASE WHEN ud.country_id = 125 THEN 1 ELSE 0 END) as national_participants,
                SUM(CASE WHEN ud.country_id != 125 THEN 1 ELSE 0 END) as international_participants
            ', [self::REGISTRANT_SPEAKER])
            ->first();

        return [
            'speakers' => (int) ($stats->speakers ?? 0),
            'national_participants' => (int) ($stats->national_participants ?? 0),
            'international_participants' => (int) ($stats->international_participants ?? 0),
            'total_participants' => (int) ($stats->total_participants ?? 0),
        ];
    }

    /**
     * Update registration IDs for all registrants in a conference efficiently
     * Handles large datasets by processing in groups
     * Real users sorted alphabetically, then dummy users by creation date
     * 
     * @param int $conferenceId
     * @return array Statistics about the update
     */
    public static function updateRegistrationIds($conferenceId): array
    {
        DB::beginTransaction();
        
        try {
            $stats = [
                'invited' => 0,
                'participant' => 0, 
                'speaker' => 0,
                'session_chair' => 0,
                'special_guest' => 0,
                'organizer' => 0,
                'committee_member' => 0,
                'total' => 0
            ];

            // Get all committee member user IDs for this conference
            $committeeMemberUserIds = CommitteeMember::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->pluck('user_id')
                ->toArray();

            // 1. Collect all ORG_ participants (committee members + registrant_type = 5)
            // Committee Members (all users who are committee members)
            $committeeMembersReal = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->whereIn('user_id', $committeeMemberUserIds)
                ->with(['user' => function($q) {
                    $q->select('id', 'f_name', 'm_name', 'l_name');
                }])
                ->get();

            // Organizers (registrant_type = 5, not invited, not committee members)
            $organizersReal = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->where('registrant_type', self::REGISTRANT_ORGANIZER)
                ->where('is_invited', 0)
                ->whereNotNull('user_id')
                ->whereNotIn('user_id', $committeeMemberUserIds)
                ->with(['user' => function($q) {
                    $q->select('id', 'f_name', 'm_name', 'l_name');
                }])
                ->get();

            $organizersDummy = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->where('registrant_type', self::REGISTRANT_ORGANIZER)
                ->where('is_invited', 0)
                ->whereNull('user_id')
                ->orderBy('created_at')
                ->get();

            // Combine real committee members and real organizers, sort alphabetically together
            $allOrgReal = $committeeMembersReal->merge($organizersReal)
                ->sortBy(function($registration) {
                    return $registration->user ? 
                        strtolower($registration->user->f_name . ' ' . $registration->user->m_name . ' ' . $registration->user->l_name) : 
                        '';
                });

            // Assign ORG_ IDs to real users (alphabetically), then dummy users
            $orgCounter = 1;
            foreach ($allOrgReal as $registration) {
                $registration->registration_id = 'ORG_' . str_pad($orgCounter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $orgCounter++;
                
                // Track if it's a committee member
                if (in_array($registration->user_id, $committeeMemberUserIds)) {
                    $stats['committee_member']++;
                }
                $stats['organizer']++;
            }
            
            // Assign dummy organizers
            foreach ($organizersDummy as $registration) {
                $registration->registration_id = 'ORG_' . str_pad($orgCounter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $orgCounter++;
                $stats['organizer']++;
            }

            // 2. Invited Participants (is_invited = 1, not committee members) - both real and dummy
            $invitedReal = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->where('is_invited', 1)
                ->whereNotNull('user_id')
                ->whereNotIn('user_id', $committeeMemberUserIds)
                ->with(['user' => function($q) {
                    $q->select('id', 'f_name', 'm_name', 'l_name');
                }])
                ->get()
                ->sortBy(function($registration) {
                    return $registration->user ? 
                        strtolower($registration->user->f_name . ' ' . $registration->user->m_name . ' ' . $registration->user->l_name) : 
                        '';
                });

            $invitedDummy = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->where('is_invited', 1)
                ->whereNull('user_id')
                ->orderBy('created_at')
                ->get();

            $counter = 1;
            foreach ($invitedReal as $registration) {
                $registration->registration_id = 'INV_' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $counter++;
                $stats['invited']++;
            }
            foreach ($invitedDummy as $registration) {
                $registration->registration_id = 'INV_' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $counter++;
                $stats['invited']++;
            }

            // 3. Participants (registrant_type = 1, not invited, not committee members) - real then dummy
            $participantsReal = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->where('registrant_type', self::REGISTRANT_ATTENDEE)
                ->where('is_invited', 0)
                ->whereNotNull('user_id')
                ->whereNotIn('user_id', $committeeMemberUserIds)
                ->with(['user' => function($q) {
                    $q->select('id', 'f_name', 'm_name', 'l_name');
                }])
                ->get()
                ->sortBy(function($registration) {
                    return $registration->user ? 
                        strtolower($registration->user->f_name . ' ' . $registration->user->m_name . ' ' . $registration->user->l_name) : 
                        '';
                });

            $participantsDummy = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->where('registrant_type', self::REGISTRANT_ATTENDEE)
                ->where('is_invited', 0)
                ->whereNull('user_id')
                ->orderBy('created_at')
                ->get();

            $counter = 1;
            foreach ($participantsReal as $registration) {
                $registration->registration_id = 'PAR_' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $counter++;
                $stats['participant']++;
            }
            foreach ($participantsDummy as $registration) {
                $registration->registration_id = 'PAR_' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $counter++;
                $stats['participant']++;
            }

            // 4. Speakers (registrant_type = 2, not invited, not committee members) - real then dummy
            $speakersReal = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->where('registrant_type', self::REGISTRANT_SPEAKER)
                ->where('is_invited', 0)
                ->whereNotNull('user_id')
                ->whereNotIn('user_id', $committeeMemberUserIds)
                ->with(['user' => function($q) {
                    $q->select('id', 'f_name', 'm_name', 'l_name');
                }])
                ->get()
                ->sortBy(function($registration) {
                    return $registration->user ? 
                        strtolower($registration->user->f_name . ' ' . $registration->user->m_name . ' ' . $registration->user->l_name) : 
                        '';
                });

            $speakersDummy = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->where('registrant_type', self::REGISTRANT_SPEAKER)
                ->where('is_invited', 0)
                ->whereNull('user_id')
                ->orderBy('created_at')
                ->get();

            $counter = 1;
            foreach ($speakersReal as $registration) {
                $registration->registration_id = 'SPE_' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $counter++;
                $stats['speaker']++;
            }
            foreach ($speakersDummy as $registration) {
                $registration->registration_id = 'SPE_' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $counter++;
                $stats['speaker']++;
            }

            // 5. Session Chairs (registrant_type = 3, not invited, not committee members) - real then dummy
            $sessionChairsReal = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->where('registrant_type', self::REGISTRANT_SESSION_CHAIR)
                ->where('is_invited', 0)
                ->whereNotNull('user_id')
                ->whereNotIn('user_id', $committeeMemberUserIds)
                ->with(['user' => function($q) {
                    $q->select('id', 'f_name', 'm_name', 'l_name');
                }])
                ->get()
                ->sortBy(function($registration) {
                    return $registration->user ? 
                        strtolower($registration->user->f_name . ' ' . $registration->user->m_name . ' ' . $registration->user->l_name) : 
                        '';
                });

            $sessionChairsDummy = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->where('registrant_type', self::REGISTRANT_SESSION_CHAIR)
                ->where('is_invited', 0)
                ->whereNull('user_id')
                ->orderBy('created_at')
                ->get();

            $counter = 1;
            foreach ($sessionChairsReal as $registration) {
                $registration->registration_id = 'SCH_' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $counter++;
                $stats['session_chair']++;
            }
            foreach ($sessionChairsDummy as $registration) {
                $registration->registration_id = 'SCH_' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $counter++;
                $stats['session_chair']++;
            }

            // 6. Special Guests (registrant_type = 4, not invited, not committee members) - real then dummy
            $specialGuestsReal = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->where('registrant_type', self::REGISTRANT_SPECIAL_GUEST)
                ->where('is_invited', 0)
                ->whereNotNull('user_id')
                ->whereNotIn('user_id', $committeeMemberUserIds)
                ->with(['user' => function($q) {
                    $q->select('id', 'f_name', 'm_name', 'l_name');
                }])
                ->get()
                ->sortBy(function($registration) {
                    return $registration->user ? 
                        strtolower($registration->user->f_name . ' ' . $registration->user->m_name . ' ' . $registration->user->l_name) : 
                        '';
                });

            $specialGuestsDummy = self::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->where('registrant_type', self::REGISTRANT_SPECIAL_GUEST)
                ->where('is_invited', 0)
                ->whereNull('user_id')
                ->orderBy('created_at')
                ->get();

            $counter = 1;
            foreach ($specialGuestsReal as $registration) {
                $registration->registration_id = 'SGU_' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $counter++;
                $stats['special_guest']++;
            }
            foreach ($specialGuestsDummy as $registration) {
                $registration->registration_id = 'SGU_' . str_pad($counter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $counter++;
                $stats['special_guest']++;
            }

            $stats['total'] = $stats['invited'] + $stats['participant'] + $stats['speaker'] + 
                             $stats['session_chair'] + $stats['special_guest'] + $stats['organizer'];

            DB::commit();
            
            return $stats;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update registration IDs for one registrant type only.
     * Uses the same sorting and committee-member handling pattern as bulk update.
     */
    public static function updateRegistrationIdsByRegistrantType(int $conferenceId, int $registrantType): array
    {
        DB::beginTransaction();

        try {
            $committeeMemberUserIds = CommitteeMember::where('conference_id', $conferenceId)
                ->where('status', 1)
                ->pluck('user_id')
                ->toArray();

            $typeMeta = [
                self::REGISTRANT_ATTENDEE => ['prefix' => 'PAR', 'label' => 'Attendee'],
                self::REGISTRANT_SPEAKER => ['prefix' => 'SPE', 'label' => 'Speaker'],
                self::REGISTRANT_SESSION_CHAIR => ['prefix' => 'SCH', 'label' => 'Session Chair'],
                self::REGISTRANT_SPECIAL_GUEST => ['prefix' => 'SGU', 'label' => 'Special Guest'],
                self::REGISTRANT_ORGANIZER => ['prefix' => 'ORG', 'label' => 'Organizer'],
                self::REGISTRANT_FACULTY => ['prefix' => 'FAC', 'label' => 'Faculty'],
                self::REGISTRANT_VOLUNTEER => ['prefix' => 'VOL', 'label' => 'Volunteer'],
            ];

            if (! isset($typeMeta[$registrantType])) {
                throw new \InvalidArgumentException('Invalid registrant type selected.');
            }

            $prefix = $typeMeta[$registrantType]['prefix'];
            $label = $typeMeta[$registrantType]['label'];

            if ($registrantType === self::REGISTRANT_ORGANIZER) {
                $realRegistrants = self::where('conference_id', $conferenceId)
                    ->where('status', 1)
                    ->where('is_invited', 0)
                    ->whereNotNull('user_id')
                    ->where(function ($query) use ($committeeMemberUserIds) {
                        $query->where('registrant_type', self::REGISTRANT_ORGANIZER)
                            ->orWhereIn('user_id', $committeeMemberUserIds);
                    })
                    ->with(['user' => function ($q) {
                        $q->select('id', 'f_name', 'm_name', 'l_name');
                    }])
                    ->get()
                    ->sortBy(function ($registration) {
                        return $registration->user
                            ? strtolower($registration->user->f_name.' '.$registration->user->m_name.' '.$registration->user->l_name)
                            : '';
                    })
                    ->values();

                $dummyRegistrants = self::where('conference_id', $conferenceId)
                    ->where('status', 1)
                    ->where('registrant_type', self::REGISTRANT_ORGANIZER)
                    ->where('is_invited', 0)
                    ->whereNull('user_id')
                    ->orderBy('created_at')
                    ->get();
            } else {
                $realRegistrants = self::where('conference_id', $conferenceId)
                    ->where('status', 1)
                    ->where('registrant_type', $registrantType)
                    ->where('is_invited', 0)
                    ->whereNotNull('user_id')
                    ->whereNotIn('user_id', $committeeMemberUserIds)
                    ->with(['user' => function ($q) {
                        $q->select('id', 'f_name', 'm_name', 'l_name');
                    }])
                    ->get()
                    ->sortBy(function ($registration) {
                        return $registration->user
                            ? strtolower($registration->user->f_name.' '.$registration->user->m_name.' '.$registration->user->l_name)
                            : '';
                    })
                    ->values();

                $dummyRegistrants = self::where('conference_id', $conferenceId)
                    ->where('status', 1)
                    ->where('registrant_type', $registrantType)
                    ->where('is_invited', 0)
                    ->whereNull('user_id')
                    ->orderBy('created_at')
                    ->get();
            }

            $counter = 1;
            foreach ($realRegistrants as $registration) {
                $registration->registration_id = $prefix.'_'.str_pad($counter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $counter++;
            }

            foreach ($dummyRegistrants as $registration) {
                $registration->registration_id = $prefix.'_'.str_pad($counter, 3, '0', STR_PAD_LEFT);
                $registration->save();
                $counter++;
            }

            DB::commit();

            return [
                'label' => $label,
                'total' => max(0, $counter - 1),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Get registration ID prefix based on type
     */
    public function getRegistrationIdPrefix(): string
    {
        if ($this->is_invited) {
            return 'INV';
        }

        return match ($this->registrant_type) {
            self::REGISTRANT_ATTENDEE => 'PAR',
            self::REGISTRANT_SPEAKER => 'SPE',
            self::REGISTRANT_SESSION_CHAIR => 'SCH',
            self::REGISTRANT_SPECIAL_GUEST => 'SGU',
            self::REGISTRANT_ORGANIZER => 'ORG',
            self::REGISTRANT_FACULTY => 'FAC',
            self::REGISTRANT_VOLUNTEER => 'VOL',
            default => 'REG'
        };
    }
}
