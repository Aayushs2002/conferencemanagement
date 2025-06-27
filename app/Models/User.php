<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\Submission;
use App\Models\User\Society;
use App\Models\User\UserDetail;
use App\Models\User\UserSociety;
use App\Models\Workshop\WorkshopRegistration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    //type = 1 (super-admin)
    //type = 2 (society_admin)
    //type = 3 (society_user)

    protected $fillable = [
        'f_name',
        'm_name',
        'l_name',
        'email',
        'type',
        'password',
        'status',
        'last_login_at',
        'is_profile_updated'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function fullName($user)
    {
        $name = $user->f_name . ' ' . $user->m_name . ' ' . $user->l_name;
        return $name;
    }

    public function userDetail()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function societies()
    {
        return $this->belongsToMany(Society::class, 'user_societies', 'user_id', 'society_id')->using(UserSociety::class)->withPivot('member_type_id');
    }

    public function societyUsers()
    {
        return $this->hasMany(UserSociety::class);
    }

    public function conferenceRegistration()
    {
        return $this->hasMany(ConferenceRegistration::class)->where('status', 1);
    }

    public function workshopRegistration()
    {
        return $this->hasMany(WorkshopRegistration::class)->where('status', 1);
    }

    public function submission()
    {
        return $this->hasMany(Submission::class)->where('status', 1);
    }

    public function conferencePermissions()
    {
        return $this->belongsToMany(
            \Spatie\Permission\Models\Permission::class,
            'conference_user_permission'
        )->withPivot('conference_id')->withTimestamps();
    }


    public function conferenceRoles()
    {
        return $this->belongsToMany(Role::class, 'conference_user_roles')
            ->withPivot('conference_id')
            ->withTimestamps();
    }

    public function hasConferencePermission($conferenceId, $permissionName)
    {
        return $this->conferencePermissions()
            ->wherePivot('conference_id', $conferenceId)
            ->where('name', $permissionName)
            ->exists();
    }

    public function hasConferencePermissionBlade($conference, $permission)
    {
        return $this->hasConferencePermission($conference->id, $permission);
    }

    public function hasAnyConferencePermission($conference, array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasConferencePermission($conference->id, $permission)) {
                return true;
            }
        }
        return false;
    }
}
