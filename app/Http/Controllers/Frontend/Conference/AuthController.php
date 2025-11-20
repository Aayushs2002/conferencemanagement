<?php

namespace App\Http\Controllers\Frontend\Conference;

use App\Http\Controllers\Controller;
use App\Models\User\NamePrefix;
use Illuminate\Http\Request;

class AuthController extends BaseConferenceController
{
    public function register()
    {
         if ($this->conference->society && $this->conference->society->namePrefixes()->exists()) {
            $name_prefiexs = $this->conference->society->namePrefixes()->where('status', 1)->get();
            // dd($name_prefiexs);
        } else {
            // Fallback to all active prefixes if society hasn't selected any
            $name_prefiexs = NamePrefix::whereStatus(1)->get();
        }
        return view('frontend.conference.auth.register', compact('name_prefiexs'));
    }
}
