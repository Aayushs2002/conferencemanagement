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
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ConferenceRegistationImport implements ToCollection, WithHeadingRow
{
    protected $society;
    protected $conference;
    public $log = [];

    public function __construct($society, $conference)
    {
        $this->society = $society;
        $this->conference = $conference;
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

            $user = User::where('email', $email)->first();

            if (!$user) {
                $user = User::create([
                    'f_name' => $row['first_name'],
                    'm_name' => $row['middle_name'],
                    'l_name' => $row['last_name'],
                    'email' => $email,
                    'password' => hash_password('password')
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
            }

            $existsInSociety = $user->societies()->where('society_id', $this->society->id)->exists();
            if (!$existsInSociety) {
                $user->societies()->attach($this->society->id, ['member_type_id' => $memberTypeId]);
            } else {
                $this->log[] = ['row' => $rowNumber, 'name' => $row['first_name'] . '' . $row['last_name'], 'reason' => 'User already exists in this society, skipped UserSociety.'];
            }

            $existsRegistration = ConferenceRegistration::where('user_id', $user->id)
                ->where('conference_id', $this->conference->id)
                ->exists();

            if ($existsRegistration) {
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

        $column = $this->modelColumnMap[$model] ?? 'name';
        $record = $model::whereRaw("LOWER({$column}) = ?", [strtolower($value)])->first();

        return $record ? $record->id : null;
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
