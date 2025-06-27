<?php

namespace App\Models;

use App\Models\User\Society;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'guard_name', 'society_id'];

    public function society()
    {
        return $this->belongsTo(Society::class);
    }
}
