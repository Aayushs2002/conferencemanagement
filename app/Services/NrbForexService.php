<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Nepal Rastra Bank published exchange rates.
 *
 * Response shape (verified against the live API):
 *   data.payload[].date
 *   data.payload[].rates[].currency.iso3   e.g. "USD", "INR"
 *   data.payload[].rates[].currency.unit   e.g. 1 for USD, 100 for INR
 *   data.payload[].rates[].buy / .sell
 *
 * `unit` matters: INR is quoted per 100, so a sell of 160.15 means 1 INR =
 * 1.6015 NPR. Participant\ConferenceRegistrationController divides the USD rate
 * by a hardcoded 1.6 to approximate this — that is a fudge; the correct figure
 * is always rate ÷ unit, which is what this service returns.
 *
 * Rates are NPR per one unit of the foreign currency, so NPR itself is always 1.
 */
class NrbForexService
{
    private const ENDPOINT = 'https://www.nrb.org.np/api/forex/v1/rates';

    /** Longest span we will ask NRB for in one go. */
    private const MAX_DAYS = 400;

    /** NRB rejects anything above 100 with a 400. */
    private const PER_PAGE = 100;

    /** Safety stop; MAX_DAYS / PER_PAGE rounded up, plus slack. */
    private const MAX_PAGES = 6;

    /**
     * Rates for a single day, falling back to the most recent prior publication
     * (NRB does not publish every calendar day).
     *
     * @return array<string,float> ['USD' => 152.92, 'INR' => 1.6015, 'NPR' => 1.0]
     */
    public function ratesOn(Carbon $date): array
    {
        // Look back a short window so weekends and holidays resolve.
        $map = $this->ratesBetween($date->copy()->subDays(10), $date);

        return end($map) ?: [];
    }

    /** Today's rates. Convenience wrapper. */
    public function currentRates(): array
    {
        return $this->ratesOn(Carbon::today());
    }

    /**
     * Daily rates across a range, gap-filled forward so every date in the range
     * has a value.
     *
     * @return array<string,array<string,float>> ['2026-08-01' => ['USD' => …], …]
     */
    public function ratesBetween(Carbon $from, Carbon $to): array
    {
        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        if ($from->diffInDays($to) > self::MAX_DAYS) {
            $from = $to->copy()->subDays(self::MAX_DAYS);
        }

        $key = 'nrb.rates.'.$from->toDateString().'.'.$to->toDateString();

        // Past ranges never change; anything touching today expires quickly.
        $ttl = $to->isToday() || $to->isFuture() ? now()->addHours(3) : now()->addDays(30);

        $published = Cache::remember($key, $ttl, fn () => $this->fetch($from, $to));

        return $this->gapFill($published, $from, $to);
    }

    /**
     * @return array<string,array<string,float>> keyed by date, only days NRB published
     */
    private function fetch(Carbon $from, Carbon $to): array
    {
        $type = config('finance.nrb_rate_type', 'sell');
        $out = [];

        try {
            for ($page = 1; $page <= self::MAX_PAGES; $page++) {
                $response = Http::timeout(12)->retry(2, 200)->get(self::ENDPOINT, [
                    'page' => $page,
                    'per_page' => self::PER_PAGE,
                    'from' => $from->toDateString(),
                    'to' => $to->toDateString(),
                ]);

                if (! $response->successful()) {
                    Log::warning('NRB forex request failed', ['status' => $response->status(), 'page' => $page]);
                    break;
                }

                $payload = $response->json('data.payload') ?? [];

                foreach ($payload as $day) {
                    if (! empty($day['date'])) {
                        $out[$day['date']] = $this->parseDay($day, $type);
                    }
                }

                $pages = (int) ($response->json('pagination.pages') ?? 1);

                if (empty($payload) || $page >= $pages) {
                    break;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('NRB forex fetch threw', ['message' => $e->getMessage()]);
        }

        ksort($out);

        return $out;
    }

    /**
     * Normalise one day's `rates` array to NPR-per-single-unit.
     *
     * Public because the unit division is the one thing here that fails
     * silently and expensively: INR is quoted per 100, so skipping the division
     * overstates every Indian payment by 100×.
     *
     * @return array<string,float>
     */
    public function parseDay(array $day, string $type = 'sell'): array
    {
        $rates = ['NPR' => 1.0];

        foreach ($day['rates'] ?? [] as $rate) {
            $iso = $rate['currency']['iso3'] ?? null;
            $unit = (float) ($rate['currency']['unit'] ?? 1);
            $value = (float) ($rate[$type] ?? 0);

            if ($iso && $unit > 0 && $value > 0) {
                $rates[$iso] = $value / $unit;
            }
        }

        return $rates;
    }

    /**
     * Carry the last published rate forward over unpublished days, and backfill
     * the head of the range with the first known set.
     */
    private function gapFill(array $published, Carbon $from, Carbon $to): array
    {
        if (empty($published)) {
            return [];
        }

        $first = reset($published);
        $carry = $first;
        $out = [];

        for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
            $key = $d->toDateString();

            if (isset($published[$key])) {
                $carry = $published[$key];
            }

            $out[$key] = $carry;
        }

        return $out;
    }
}
