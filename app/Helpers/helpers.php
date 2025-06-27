<?php

use App\Models\Conference\Conference;
use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\ScientificSessionCategory;
use App\Models\User\Society;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

foreach (glob(__DIR__ . '/*_helpers.php') as $file) {
    require_once $file;
}


if (!function_exists('checkRegistration')) {
    function checkRegistration($conference)
    {
        // var_dump($conference);
        $conference_id =  Hashids::decode($conference);
        $checkRegistration = ConferenceRegistration::where(['conference_id' => $conference_id, 'user_id' => current_user()->id, 'status' => 1])->first();
        if ($checkRegistration) {
            return true;
        }
        return false;
    }
}
if (!function_exists('checkRegistrations')) {
    function checkRegistrations($conference)
    {
        // var_dump($conference);
        // $conference_id =  Hashids::decode($conference);
        $checkRegistration = ConferenceRegistration::where(['conference_id' => $conference->id, 'user_id' => current_user()->id, 'status' => 1])->first();
        if ($checkRegistration) {
            return true;
        }
        return false;
    }
}
if (!function_exists('getConference')) {
    function getConference($conference)
    {
        $conference_id =  Hashids::decode($conference);

        $conference = Conference::where('id', $conference_id)->first();

        if (!$conference) {
            return false;
        }
        return $conference;
    }
}
if (!function_exists('getSociety')) {
    function getSociety($society)
    {
        $society_id =  Hashids::decode($society);

        $society = Society::where('id', $society_id)->first();

        if (!$society) {
            return false;
        }
        return $society;
    }
}

if (!function_exists('getCategories')) {
    function getCategories($parent_id)
    {
        $conference_id =  Hashids::decode(request()->segment(4));

        $conference = Conference::where('id', $conference_id)->first();
        $categories = ScientificSessionCategory::where(function ($query) use ($conference, $parent_id) {
            $query->where('status', 1)
                ->where('parent_id', $parent_id)
                ->where(function ($q) use ($conference) {
                    $q->whereNull('conference_id')
                        ->orWhere('conference_id', $conference->id);
                });
        })->get();
        return $categories;
    }
}
