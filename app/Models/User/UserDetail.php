<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    //gender 1 = male
    //gender 2 = female
    //gender 3 = other

    protected $fillable = [
        'user_id',
        'country_id',
        'name_prefix_id',
        'institution_id',
        'designation_id',
        'department_id',
        'institute_address',
        'gender',
        'phone',
        'image',
        'council_number',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function namePrefix()
    {
        return $this->belongsTo(NamePrefix::class, 'name_prefix_id', 'id');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_id', 'id');
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}
