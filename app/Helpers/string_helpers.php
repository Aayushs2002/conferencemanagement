<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

if (!function_exists('limit_words')) {
    function limit_words($text, $limit = 10)
    {
        return Str::words($text, $limit);
    }
}

if (!function_exists('random_word')) {
    function random_word($length)
    {
        return Str::random($length);;
    }
}

if (!function_exists('slugify')) {
    function slugify($text)
    {
        return Str::slug($text) . '-' . time();
    }
}

if (!function_exists('hash_password')) {
    function hash_password($password)
    {
        return Hash::make($password);
    }
}

if (!function_exists('starts_with_http')) {
    function starts_with_http($url)
    {
        return Str::startsWith($url, ['http://', 'https://']);
    }
}
