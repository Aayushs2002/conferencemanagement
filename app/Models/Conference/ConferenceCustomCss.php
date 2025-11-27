<?php

namespace App\Models\Conference;
 
use Illuminate\Database\Eloquent\Model;

class ConferenceCustomCss extends Model
{
    protected $table = 'conference_custom_css';
    
    protected $fillable = [
        'conference_id',
        'custom_css',
        'status'
    ];

    public function conference()
    {
        return $this->belongsTo(Conference::class, 'conference_id', 'id');
    }
}
