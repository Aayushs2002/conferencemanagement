<?php

use Carbon\Carbon;

if (!function_exists('human_date')) {
    function human_date($date)
    {
        return Carbon::parse($date)->diffForHumans();
    }
}

if (!function_exists('format_date')) {
    function format_date($date, $format = 'Y-m-d')
    {
        return Carbon::parse($date)->format($format);
    }
}

if (!function_exists('is_today')) {
    function is_today($date)
    {
        return Carbon::parse($date)->isToday();
    }
}
if (!function_exists('is_past')) {
    function is_past($date)
    {
        return Carbon::parse($date)->isPast();
    }
}
