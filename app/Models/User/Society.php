<?php

namespace App\Models\User;

use App\Models\Cms\Feature;
use App\Models\Conference\Conference;
use App\Models\Payment\InternationalPayment;
use App\Models\Payment\NationalPayment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Society extends Model
{
    protected $fillable = [
        'slug',
        'abbreviation',
        'address',
        'phone',
        'contact_person',
        'contact_person_phone',
        'contact_person_email',
        'sub_domain_name',
        'description',
        'logo',
        'token',
        'status'
    ];

    public function getRouteKey()
    {
        // return $this->attributes['slug'];
        return Hashids::encode($this->attributes['id']);
    }

    public static function findByHashid($hashid)
    {
        $id = Hashids::decode($hashid)[0] ?? null;
        return static::findOrFail($id);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_societies', 'society_id', 'user_id');
    }
    public function conferences()
    {
        return $this->hasMany(Conference::class, 'society_id', 'id');
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'society_features');
    }

    public function nationalPaymentSetting()
    {
        return $this->hasOne(NationalPayment::class, 'society_id', 'id');
    }

    public function internationalPaymentSetting()
    {
        return $this->hasOne(InternationalPayment::class, 'society_id', 'id');
    }

    public function namePrefixes()
    {
        return $this->belongsToMany(NamePrefix::class, 'society_name_prefixes');
    }

    public function institutions()
    {
        return $this->belongsToMany(Institution::class, 'society_institution');
    }

    public function designations()
    {
        return $this->belongsToMany(Designation::class, 'society_designation')
            ->orderBy('society_designation.display_order', 'asc')
            ->orderBy('society_designation.designation_id', 'asc');
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'society_department')
            ->orderBy('society_department.display_order', 'asc')
            ->orderBy('society_department.department_id', 'asc');
    }

    public function societySetting()
    {
        return $this->hasOne(\App\Models\Society\SocietySetting::class, 'society_id', 'id');
    }

    public function memberTypes()
    {
        return $this->hasMany(MemberType::class, 'society_id', 'id');
    }
}
