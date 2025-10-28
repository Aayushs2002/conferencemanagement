<?php

namespace App\Imports;

use App\Models\Conference\ConferenceRegistration;
use App\Models\User;
use App\Models\User\Country;
use App\Models\User\Department;
use App\Models\User\Designation;
use App\Models\User\Institution;
use App\Models\User\MemberType;
use App\Models\User\NamePrefix;
use App\Models\User\UserDetail;
use App\Models\User\UserSociety;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ConferenceRegistationImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected $society;
    protected $conference;
    public $log = [];
    protected $lookupCache = [];
    protected $defaultPasswordHash = null;

    public function __construct($society, $conference)
    {
        $this->society = $society;
        $this->conference = $conference;
        // Hash the default password once instead of for every user
        $this->defaultPasswordHash = hash_password('password');
    }

    /**
     * Process data in chunks of 100 rows at a time
     */
    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Pre-load all lookup data to avoid N+1 queries
     */
    protected function preloadLookupData()
    {
        // Cache all prefixes
        $this->lookupCache['prefixes'] = NamePrefix::all()->keyBy(function ($item) {
            return strtolower($item->prefix);
        });

        // Cache all countries
        $this->lookupCache['countries'] = Country::all()->keyBy(function ($item) {
            return strtolower($item->country_name);
        });

        // Cache all member types for this society
        $this->lookupCache['member_types'] = MemberType::where('society_id', $this->society->id)
            ->get()
            ->keyBy(function ($item) {
                return strtolower($item->type);
            });

        // Cache all institutions
        $this->lookupCache['institutions'] = Institution::all()->keyBy(function ($item) {
            return strtolower($item->name);
        });

        // Cache all departments
        $this->lookupCache['departments'] = Department::all()->keyBy(function ($item) {
            return strtolower($item->name);
        });

        // Cache all designations
        $this->lookupCache['designations'] = Designation::all()->keyBy(function ($item) {
            return strtolower($item->designation);
        });
    }

    protected $genderMap = [
        'male' => 1,
        'female' => 2,
        'other' => 3,
    ];

    protected $mealTypeMap = [
        'veg' => 1,
        'non-veg' => 2,
    ];

    protected $registrantTypeMap = [
        'attendee' => 1,
        'speaker' => 2,
        'session chair' => 3,
        'special guest' => 4,
    ];

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        // Increase execution time and memory limit for large imports
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');

        // Pre-load all lookup data to avoid repeated queries
        if (empty($this->lookupCache)) {
            $this->preloadLookupData();
        }

        // Get all emails from this chunk to check existing users
        $emails = $rows->pluck('email')->filter()->map(fn($email) => trim($email))->unique();
        $existingUsers = User::whereIn('email', $emails)->get()->keyBy('email');

        // Get existing registrations for this conference
        $existingRegistrations = ConferenceRegistration::where('conference_id', $this->conference->id)
            ->whereIn('user_id', $existingUsers->pluck('id'))
            ->pluck('user_id')
            ->flip();

        // Get existing society memberships
        $existingSocietyMemberships = DB::table('user_societies')
            ->where('society_id', $this->society->id)
            ->whereIn('user_id', $existingUsers->pluck('id'))
            ->pluck('user_id')
            ->flip();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $email = trim($row['email']);

            if (!$email) {
                $this->log[] = ['row' => $rowNumber, 'name' => $row['first_name'] . '' . $row['last_name'], 'reason' => 'Email is empty, skipped.'];
                continue;
            }

            $prefixId = $this->resolveId(NamePrefix::class, $row['prefix']);
            $countryId = $this->resolveId(Country::class, $row['country']);
            $genderId = $this->resolveValue($row['gender'], $this->genderMap);
            $memberTypeId = $this->resolveId(MemberType::class, $row['member_type']);
            $designaionId = $this->resolveId(Designation::class, $row['designation']);
            $institutionId = $this->resolveId(Institution::class, $row['institution']);
            $departmentId = $this->resolveId(Department::class, $row['department']);

            $invalidFields = [];
            if (!$prefixId) $invalidFields[] = 'prefix';
            if (!$countryId) $invalidFields[] = 'country';
            if (!$genderId) $invalidFields[] = 'gender';
            if (!$memberTypeId) $invalidFields[] = 'member_type';
            // if (!$designaionId) $invalidFields[] = 'designaion';
            // if (!$institutionId) $invalidFields[] = 'institution';
            // if (!$departmentId) $invalidFields[] = 'department';

            if (!empty($invalidFields)) {
                $this->log[] = [
                    'row' => $rowNumber,
                    'name' => $row['first_name'] . '' . $row['last_name'],
                    'reason' => 'Invalid value for: ' . implode(', ', $invalidFields) . '. User skipped.'
                ];
                continue;
            }

            $user = $existingUsers->get($email);

            if (!$user) {
                $user = User::create([
                    'f_name' => $row['first_name'],
                    'm_name' => $row['middle_name'],
                    'l_name' => $row['last_name'],
                    'email' => $email,
                    'password' => $this->defaultPasswordHash // Use pre-hashed password
                ]);

                UserDetail::create([
                    'user_id' => $user->id,
                    'name_prefix_id' => $prefixId,
                    'country_id' => $countryId,
                    'gender_id' => $genderId,
                    'designation_id' => $designaionId,
                    'department_id' => $departmentId,
                    'institution_id' => $institutionId,
                    'phone' => $row['phone'],
                    'council_number' => $row['council_number'],
                ]);

                // Add to cache for next iterations
                $existingUsers->put($email, $user);
            }

            if (!isset($existingSocietyMemberships[$user->id])) {
                $user->societies()->attach($this->society->id, ['member_type_id' => $memberTypeId]);
                $existingSocietyMemberships[$user->id] = true;
            } else {
                $this->log[] = ['row' => $rowNumber, 'name' => $row['first_name'] . '' . $row['last_name'], 'reason' => 'User already exists in this society, skipped UserSociety.'];
            }

            if (isset($existingRegistrations[$user->id])) {
                $this->log[] = ['row' => $rowNumber, 'name' => $row['first_name'] . '' . $row['last_name'], 'reason' => 'User already registered for this conference, skipped ConferenceRegistration.'];
                continue;
            }

            $registrantTypeId = $this->resolveValue($row['registrant_type'], $this->registrantTypeMap);
            $mealTypeId = $this->resolveValue($row['meal_type'], $this->mealTypeMap);

            ConferenceRegistration::create([
                'user_id' => $user->id,
                'conference_id' => $this->conference->id,
                'registrant_type' => $registrantTypeId,
                'meal_type' => $mealTypeId,
                'amount' => $row['amount'],
                'transaction_id' => $row['transaction_id'],
            ]);

            // Mark as registered to prevent duplicates in same batch
            $existingRegistrations[$user->id] = true;
        }
    }


    protected $modelColumnMap = [
        NamePrefix::class => 'prefix',
        Country::class => 'country_name',
        MemberType::class => 'type',
        Institution::class => 'name',
        Department::class => 'name',
        Designation::class => 'designation', 
    ];

    protected function resolveId($model, $value)
    {
        $value = trim($value);

        if (!$value) return null;

        if (is_numeric($value)) return (int) $value;

        // Use cached lookup data instead of querying database
        $cacheKey = $this->getCacheKey($model);
        if (isset($this->lookupCache[$cacheKey])) {
            $item = $this->lookupCache[$cacheKey][strtolower($value)] ?? null;
            return $item ? $item->id : null;
        }

        // Fallback to database query if not in cache
        $column = $this->modelColumnMap[$model] ?? 'name';
        $record = $model::whereRaw("LOWER({$column}) = ?", [strtolower($value)])->first();

        return $record ? $record->id : null;
    }

    /**
     * Get cache key for a model
     */
    protected function getCacheKey($model)
    {
        return match ($model) {
            NamePrefix::class => 'prefixes',
            Country::class => 'countries',
            MemberType::class => 'member_types',
            Institution::class => 'institutions',
            Department::class => 'departments',
            Designation::class => 'designations',
            default => null,
        };
    }


    protected function resolveValue($value, $map)
    {
        $value = trim($value);

        if (!$value) return null;

        if (is_numeric($value)) return (int) $value;

        $key = strtolower($value);

        return $map[$key] ?? null;
    }
}
