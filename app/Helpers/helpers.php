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

if (!function_exists('numberToWord')) {
    function numberToWord($num = '')
    {
        $num    = (string) ((int) $num);

        if ((int) ($num) && ctype_digit($num)) {
            $words  = array();

            $num    = str_replace(array(',', ' '), '', trim($num));

            $list1  = array(
                '',
                'one',
                'two',
                'three',
                'four',
                'five',
                'six',
                'seven',
                'eight',
                'nine',
                'ten',
                'eleven',
                'twelve',
                'thirteen',
                'fourteen',
                'fifteen',
                'sixteen',
                'seventeen',
                'eighteen',
                'nineteen'
            );

            $list2  = array(
                '',
                'ten',
                'twenty',
                'thirty',
                'forty',
                'fifty',
                'sixty',
                'seventy',
                'eighty',
                'ninety',
                'hundred'
            );

            $list3  = array(
                '',
                'thousand',
                'million',
                'billion',
                'trillion',
                'quadrillion',
                'quintillion',
                'sextillion',
                'septillion',
                'octillion',
                'nonillion',
                'decillion',
                'undecillion',
                'duodecillion',
                'tredecillion',
                'quattuordecillion',
                'quindecillion',
                'sexdecillion',
                'septendecillion',
                'octodecillion',
                'novemdecillion',
                'vigintillion'
            );

            $num_length = strlen($num);
            $levels = (int) (($num_length + 2) / 3);
            $max_length = $levels * 3;
            $num    = substr('00' . $num, -$max_length);
            $num_levels = str_split($num, 3);

            foreach ($num_levels as $num_part) {
                $levels--;
                $hundreds   = (int) ($num_part / 100);
                $hundreds   = ($hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ($hundreds == 1 ? '' : 's') . ' ' : '');
                $tens       = (int) ($num_part % 100);
                $singles    = '';

                if ($tens < 20) {
                    $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
                } else {
                    $tens = (int) ($tens / 10);
                    $tens = ' ' . $list2[$tens] . ' ';
                    $singles = (int) ($num_part % 10);
                    $singles = ' ' . $list1[$singles] . ' ';
                }
                $words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_part)) ? ' ' . $list3[$levels] . ' ' : '');
            }
            $commas = count($words);
            if ($commas > 1) {
                $commas = $commas - 1;
            }

            $words  = implode(', ', $words);

            $words  = trim(str_replace(' ,', ',', ucwords($words)), ', ');
            if ($commas) {
                $words  = str_replace(',', ' and', $words);
            }

            return $words;
        } else if (!((int) $num)) {
            return 'Zero';
        }
        return '';
    }
}
