<?php

/*
|--------------------------------------------------------------------------
| Financial Analysis
|--------------------------------------------------------------------------
|
| Routes and config are cached in production. After changing anything here
| (especially a rate) you must run `php artisan config:cache` on the server
| or the old value stays live.
|
*/

return [

    /*
    | Currency everything is converted to for the estimated grand total.
    */
    'base_currency' => env('FINANCE_BASE_CURRENCY', 'NPR'),

    /*
    | Nepal. Matches the hardcoded 125 used throughout the registration code.
    */
    'domestic_country_id' => 125,

    /*
    | How many units of the base currency one unit of each currency is worth.
    |
    | ponytail: static rates. The app already calls the NRB forex API inline
    | during voucher rendering (Participant\ConferenceRegistrationController),
    | but a reporting page should not make an HTTP call per request. Move these
    | to a conference_settings column or a rates table when someone needs to
    | change them without a deploy.
    */
    'rates' => [
        'NPR' => 1,
        'USD' => (float) env('FINANCE_RATE_USD', 152.53),
        'INR' => (float) env('FINANCE_RATE_INR', 1.6),
    ],

    'rates_updated_at' => env('FINANCE_RATES_UPDATED_AT', '2026-08-14'),

    /*
    | Which side of the NRB quote to use when converting foreign receipts to NPR.
    |
    | 'buy'  = what a bank pays you for foreign currency — arguably the truer
    |          figure for revenue actually landing in a NPR account.
    | 'sell' = what a bank charges you for it. Kept as the default only because
    |          the existing voucher code in Participant\ConferenceRegistrationController
    |          already uses `sell`, so the two agree. Flip it if finance disagrees.
    */
    'nrb_rate_type' => env('FINANCE_NRB_RATE_TYPE', 'sell'),

    /*
    | Payment methods. Previously hardcoded in three places
    | (ConferenceRegistrationController::generateIndividualPass, the registrant
    | Blade filter, the export) and the Blade was missing 8 and 9.
    */
    'payment_types' => [
        1 => 'FonePay',
        2 => 'Moco',
        3 => 'Esewa',
        4 => 'Khalti',
        5 => 'Card Payment',
        6 => 'Bank Transfer',
        7 => 'ConnectIPS',
        8 => 'Static QR',
        9 => 'Credit',
    ],

    /*
    | payment_type 9 means the registrant owes the money — it is not collected.
    */
    'credit_payment_type' => 9,

];
