<?php

if (!function_exists('society_detail')) {
    function society_detail()
    {
        return session()->get('societyDetail');
    }
}

if (!function_exists('conference_detail')) {
    function conference_detail()
    {
        return  session()->get('conferenceDetail');
    }
}
