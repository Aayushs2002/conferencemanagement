<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Model;
use Vinkla\Hashids\Facades\Hashids;

class Author extends Model
{
    protected $fillable = [
        'submission_id',
        'name',
        'email',
        'designation',
        'institution',
        'institution_address',
        'phone',
        'main_author',
        'status'
    ];

    public function getRouteKey()
    {
        return Hashids::encode($this->attributes['id']);
    }

    public static function findByHashid($hashid)
    {
        $id = Hashids::decode($hashid)[0] ?? null;
        return static::findOrFail($id);
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
