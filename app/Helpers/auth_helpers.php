<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('is_logged_in')) {
    function is_logged_in()
    {
        return Auth::check();
    }
}

if (!function_exists('current_user')) {
    function current_user()
    {
        return Auth::user();
    }
}

if (!function_exists('is_super_admin')) {
    function is_super_admin()
    {
        return Auth::check() && Auth::user()->type === 1;
    }
}
 
if (!function_exists('is_society_admin')) {
    function is_society_admin()
    {
        return Auth::check() && Auth::user()->type === 2;
    }
}
