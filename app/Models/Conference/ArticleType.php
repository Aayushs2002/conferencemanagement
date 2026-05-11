<?php

namespace App\Models\Conference;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class ArticleType extends Model
{
    protected $fillable = [
        'conference_id',
        'name',
        'display_order',
        'status' 
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'article_type_id', 'id');
    }

    public function setting()
    {
        return $this->hasOne(ArticleTypeSetting::class, 'article_type_id', 'id');
    }

    public function managers()
    {
        return $this->belongsToMany(User::class, 'article_type_user', 'article_type_id', 'user_id')
            ->withTimestamps();
    }
}
