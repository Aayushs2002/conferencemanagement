<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (!function_exists('is_logged_in')) {
    function is_logged_in()
    {
        return Auth::check();
    }
}

if (!function_exists('is_expert')) {
    function is_expert($conference)
    {
        // dd( auth()->user()->id);
        $isExpert = DB::table('experts')
            ->where([
                'user_id' => auth()->user()->id,
                'conference_id' => $conference->id,
            ])
            ->first();
        return $isExpert ? true : false;
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

if (! function_exists('feature_enabled')) {
    function feature_enabled(string $slug, $society): bool
    {
        // dd($society);
        // $user = Auth::user();
        // if (Auth::check()) {
        //     if (! $user) {
        //         return false;
        //     }

        //     if ($user->type === 1) {
        //         return true;
        //     }

        //     if (! $society) {
        //         return false;
        //     }
        // }

        $society = $society->loadMissing('features');
        // dd($society);
        static $features = [];

        if (! isset($features[$society->id])) {
            $features[$society->id] = $society->features->pluck('slug')->toArray();
        }


        return in_array($slug, $features[$society->id]);
    }
}
