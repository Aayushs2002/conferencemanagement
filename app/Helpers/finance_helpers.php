<?php

/*
| Helpers for the Financial Analysis page.
|
| Two things here are load-bearing and easy to get wrong:
|
| 1. `amount` is a VARCHAR on conference_registrations, workshop_registrations
|    and conference_registration_addons. Summing it needs an explicit cast, and
|    the cast needs to survive junk/empty values without blowing up the query.
|
| 2. `payment_currency` does NOT tell you the currency on its own. The column
|    defaults to 'USD', validation only permits USD|INR, and the code coerces
|    anything else to 'USD' — so a Nepali registrant paying in rupees is stored
|    as 'USD'. The app's own voucher logic resolves it as:
|        INR if payment_currency = INR, else NPR if country is Nepal, else USD
|    (Participant\ConferenceRegistrationController ~L448, ~L753). That rule is
|    reproduced here, once, in both SQL and PHP form.
*/

if (! function_exists('finance_amount_sql')) {
    /**
     * Postgres expression that turns the VARCHAR `amount` into a number.
     * Non-numeric or empty values count as 0 rather than aborting the query.
     */
    function finance_amount_sql(string $table, string $column = 'amount'): string
    {
        return "CASE WHEN {$table}.{$column} ~ '^[0-9]+(\.[0-9]+)?$' THEN {$table}.{$column}::numeric ELSE 0 END";
    }
}

if (! function_exists('finance_currency_sql')) {
    /**
     * Postgres expression resolving a row to NPR / USD / INR.
     *
     * Uses a correlated subquery rather than a join to user_details on purpose:
     * the registrant filter scope references unqualified `status`, `user_id` and
     * `created_at`, all of which also exist on user_details, so joining that
     * table makes those predicates ambiguous and the query fails.
     *
     * @param  string  $table  table or alias holding the row
     * @param  bool  $hasCurrencyColumn  false for workshop_registrations,
     *                                   which has no payment_currency column
     * @param  bool  $hasTransactionId  false where dummy detection is not possible
     */
    function finance_currency_sql(string $table, bool $hasCurrencyColumn = true, bool $hasTransactionId = true): string
    {
        $domestic = (int) config('finance.domestic_country_id');

        $cases = [];

        if ($hasCurrencyColumn) {
            $cases[] = "WHEN {$table}.payment_currency = 'INR' THEN 'INR'";
        }

        $cases[] = "WHEN (SELECT ud.country_id FROM user_details ud WHERE ud.user_id = {$table}.user_id LIMIT 1) = {$domestic} THEN 'NPR'";

        // Dummy registrants have no user_details row; the transaction id carries the scope.
        if ($hasTransactionId) {
            $cases[] = "WHEN {$table}.user_id IS NULL AND {$table}.transaction_id LIKE 'NAT-DUMMY-%' THEN 'NPR'";
        }

        return 'CASE '.implode(' ', $cases)." ELSE 'USD' END";
    }
}

if (! function_exists('finance_rate')) {
    function finance_rate(?string $currency): float
    {
        $rates = config('finance.rates');

        return (float) ($rates[strtoupper($currency ?: '')] ?? 1);
    }
}

if (! function_exists('finance_to_base')) {
    /** Convert an amount into the base currency. Estimate — rates are static. */
    function finance_to_base($amount, ?string $currency): float
    {
        return (float) $amount * finance_rate($currency);
    }
}

if (! function_exists('finance_money')) {
    /** `NPR 1,234,567` — no decimals on aggregates, they are noise at this scale. */
    function finance_money($amount, ?string $currency = null, int $decimals = 0): string
    {
        $currency = strtoupper($currency ?: config('finance.base_currency'));

        return $currency.' '.number_format((float) $amount, $decimals);
    }
}

if (! function_exists('finance_money_compact')) {
    /** `NPR 4.2M` — for stat tiles, where the exact figure lives in the table below. */
    function finance_money_compact($amount, ?string $currency = null): string
    {
        $currency = strtoupper($currency ?: config('finance.base_currency'));
        $amount = (float) $amount;

        foreach ([1_000_000_000 => 'B', 1_000_000 => 'M', 1_000 => 'K'] as $unit => $suffix) {
            if (abs($amount) >= $unit) {
                return $currency.' '.rtrim(rtrim(number_format($amount / $unit, 1), '0'), '.').$suffix;
            }
        }

        return $currency.' '.number_format($amount);
    }
}

if (! function_exists('finance_payment_type_label')) {
    function finance_payment_type_label($type): string
    {
        return config('finance.payment_types')[(int) $type] ?? 'Unknown';
    }
}
