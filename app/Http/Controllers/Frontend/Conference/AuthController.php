<?php

namespace App\Http\Controllers\Frontend\Conference;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends BaseConferenceController
{
    public function register()
    {
        return view('frontend.conference.auth.register');
    }
}
