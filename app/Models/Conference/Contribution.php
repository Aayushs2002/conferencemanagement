<?php

namespace App\Models\Conference;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'conference_id',
        'name',
        'description',
        'status'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }

    public function authors()
    {
        return $this->belongsToMany(Author::class, 'author_contributions')
                    ->withTimestamps();
    }
}
