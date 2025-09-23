<?php

namespace App\Models\User;

use App\Models\Cms\Feature;
use App\Models\Conference\Conference;
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
}
