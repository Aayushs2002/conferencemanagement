<?php

namespace Tests\Feature;

use App\Http\Controllers\Backend\Conference\FinancialAnalysisController;
use App\Services\NrbForexService;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Covers the money path only.
 *
 * The aggregation itself is Postgres SQL (`~` regex casts, date_trunc, to_char)
 * and phpunit.xml runs on in-memory SQLite, so the queries cannot execute here.
 * What is testable — and what actually loses money when it breaks — is the
 * currency resolution, the conversion, and the bucketing that decides whether a
 * registration counts as collected.
 *
 * Deliberately extends PHPUnit's TestCase rather than Tests\TestCase: booting the
 * full app runs AppServiceProvider::boot(), which queries `countries` before
 * RefreshDatabase has migrated. That already breaks the whole stock suite on this
 * repo and is not this feature's to fix. Nothing under test needs more than the
 * config repository, so a bare container is enough.
 */
class FinancialAnalysisTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Real config, with the rates pinned so assertions survive a rate change.
        $finance = require __DIR__.'/../../config/finance.php';
        $finance['base_currency'] = 'NPR';
        $finance['rates'] = ['NPR' => 1, 'USD' => 100, 'INR' => 2];
        $finance['domestic_country_id'] = 125;

        $container = new Container;
        Container::setInstance($container);
        $container->instance('config', new Repository(['finance' => $finance]));
    }

    protected function tearDown(): void
    {
        Container::setInstance(null);
        parent::tearDown();
    }

    private function controller(): FinancialAnalysisController
    {
        return new FinancialAnalysisController(new NrbForexService);
    }

    private function invoke(string $method, ...$args)
    {
        $ref = new ReflectionMethod(FinancialAnalysisController::class, $method);
        $ref->setAccessible(true);

        return $ref->invoke($this->controller(), ...$args);
    }

    /** Drive convert() with an explicit rate basis, bypassing the network. */
    private function convertWith(array $state, $amount, ?string $currency, ?string $date = null): float
    {
        $controller = $this->controller();
        $ref = new \ReflectionClass($controller);

        foreach ($state as $property => $value) {
            $p = $ref->getProperty($property);
            $p->setAccessible(true);
            $p->setValue($controller, $value);
        }

        $m = $ref->getMethod('convert');
        $m->setAccessible(true);

        return $m->invoke($controller, $amount, $currency, $date);
    }

    private function bucket(string $currency, string $bucket, $total, int $cnt = 1): object
    {
        return (object) compact('currency', 'bucket', 'total', 'cnt');
    }

    public function test_currencies_convert_to_base_at_configured_rates(): void
    {
        $this->assertSame(1000.0, finance_to_base(1000, 'NPR'));
        $this->assertSame(1000.0, finance_to_base(10, 'USD'));
        $this->assertSame(1000.0, finance_to_base(500, 'INR'));

        // An unknown currency must not silently vanish or get a wild rate.
        $this->assertSame(50.0, finance_to_base(50, 'XYZ'));
    }

    public function test_per_currency_totals_are_exact_and_never_cross_added(): void
    {
        $rows = $this->invoke('byCurrency', collect([
            $this->bucket('NPR', 'collected', 3000, 3),
            $this->bucket('USD', 'collected', 40, 2),
            $this->bucket('USD', 'pending', 10, 1),
        ]));

        $byCode = collect($rows)->keyBy('currency');

        // Gross stays in its own currency — 3000 NPR and 50 USD are never summed.
        $this->assertSame(3000.0, $byCode['NPR']['gross']);
        $this->assertSame(50.0, $byCode['USD']['gross']);

        // Only the verified portion counts as collected.
        $this->assertSame(40.0, $byCode['USD']['collected']);

        // The converted column is the only place they meet.
        $this->assertSame(5000.0, $byCode['USD']['base']);
    }

    public function test_credit_is_never_counted_as_collected(): void
    {
        $summary = $this->invoke('summarise', collect([
            $this->bucket('NPR', 'collected', 1000, 1),
            $this->bucket('NPR', 'credit', 900, 3),
            $this->bucket('NPR', 'pending', 500, 2),
            $this->bucket('NPR', 'rejected', 700, 1),
        ]));

        $this->assertSame(1000.0, $summary['collected']['base']);
        $this->assertSame(900.0, $summary['credit']['base']);
        $this->assertSame(500.0, $summary['pending']['base']);

        // Average is per collected registration, not per row in view.
        $this->assertSame(1000.0, $summary['average']);
        $this->assertSame(7, $summary['registrations']);
    }

    public function test_currency_sql_checks_inr_before_falling_back_to_country(): void
    {
        $sql = finance_currency_sql('conference_registrations');

        // Precedence matters: payment_currency='USD' is the column default, so the
        // country check must be allowed to override it to NPR — but an explicit
        // INR must win over both.
        $inr = strpos($sql, "'INR'");
        $npr = strpos($sql, "'NPR'");

        $this->assertNotFalse($inr);
        $this->assertLessThan($npr, $inr, 'INR must be tested before the country fallback');
        $this->assertStringContainsString('NAT-DUMMY-%', $sql);
        $this->assertStringContainsString('ud.country_id', $sql);
        $this->assertStringContainsString('= 125 THEN \'NPR\'', $sql);
    }

    public function test_workshop_currency_sql_omits_the_column_it_does_not_have(): void
    {
        $sql = finance_currency_sql('wr', hasCurrencyColumn: false, hasTransactionId: false);

        $this->assertStringNotContainsString('payment_currency', $sql);
        $this->assertStringNotContainsString('NAT-DUMMY-%', $sql);
        $this->assertStringContainsString('wr.user_id', $sql);
    }

    public function test_amount_cast_survives_junk_in_the_varchar_column(): void
    {
        // amount is a VARCHAR; a blank or non-numeric value must read as 0
        // rather than aborting the whole aggregate.
        $sql = finance_amount_sql('conference_registrations');

        $this->assertStringContainsString('ELSE 0', $sql);
        $this->assertStringContainsString('::numeric', $sql);
    }

    public function test_credit_payment_type_is_labelled(): void
    {
        // The registrant page's hardcoded dropdown was missing 8 and 9.
        $this->assertSame('Static QR', finance_payment_type_label(8));
        $this->assertSame('Credit', finance_payment_type_label(9));
        $this->assertSame('Unknown', finance_payment_type_label(99));
    }

    public function test_fixed_and_current_modes_apply_one_flat_rate(): void
    {
        $flat = ['rates' => ['NPR' => 1, 'USD' => 150.0, 'INR' => 1.6]];

        $this->assertSame(1500.0, $this->convertWith($flat, 10, 'USD'));
        $this->assertSame(160.0, $this->convertWith($flat, 100, 'INR'));

        // The date is ignored unless payment-date mode is on.
        $this->assertSame(1500.0, $this->convertWith($flat, 10, 'USD', '2026-02-01'));
    }

    public function test_payment_date_mode_uses_the_rate_from_that_day(): void
    {
        $state = [
            'rates' => ['NPR' => 1, 'USD' => 133.0, 'INR' => 1.6],
            'useDateRates' => true,
            'dateRates' => [
                '2026-02-01' => ['NPR' => 1.0, 'USD' => 147.48, 'INR' => 1.6015],
                '2026-08-14' => ['NPR' => 1.0, 'USD' => 153.01, 'INR' => 1.6015],
            ],
        ];

        // Same 100 USD is worth more in August than in February.
        $this->assertEqualsWithDelta(14748.0, $this->convertWith($state, 100, 'USD', '2026-02-01'), 0.001);
        $this->assertEqualsWithDelta(15301.0, $this->convertWith($state, 100, 'USD', '2026-08-14'), 0.001);

        // A timestamp, not just a date, must still resolve.
        $this->assertEqualsWithDelta(14748.0, $this->convertWith($state, 100, 'USD', '2026-02-01 10:30:00'), 0.001);

        // A date outside the fetched range falls back to the flat rate rather
        // than silently converting at 1:1.
        $this->assertEqualsWithDelta(13300.0, $this->convertWith($state, 100, 'USD', '2019-01-01'), 0.001);
    }

    public function test_nrb_response_is_divided_by_the_quoted_unit(): void
    {
        // INR is quoted per 100 and USD per 1. Skipping the division would
        // overstate every Indian payment by 100x.
        $day = [
            'date' => '2026-08-01',
            'rates' => [
                ['currency' => ['iso3' => 'INR', 'unit' => 100], 'buy' => '160.00', 'sell' => '160.15'],
                ['currency' => ['iso3' => 'USD', 'unit' => 1], 'buy' => '152.32', 'sell' => '152.92'],
                ['currency' => ['iso3' => 'JPY', 'unit' => 10], 'buy' => '9.52', 'sell' => '9.56'],
            ],
        ];

        $rates = (new NrbForexService)->parseDay($day, 'sell');

        $this->assertEqualsWithDelta(1.6015, $rates['INR'], 1e-9);
        $this->assertEqualsWithDelta(152.92, $rates['USD'], 1e-9);
        $this->assertEqualsWithDelta(0.956, $rates['JPY'], 1e-9);
        $this->assertSame(1.0, $rates['NPR']);

        // The buy side is the other half of the quote, not a different currency.
        $this->assertEqualsWithDelta(1.6, (new NrbForexService)->parseDay($day, 'buy')['INR'], 1e-9);
    }

    /** Minimal stand-in for a hydrated registration, enough for lineItems(). */
    private function registration(array $attributes, array $addons = [], array $workshops = []): object
    {
        return new class($attributes, $addons, $workshops)
        {
            public $addons;

            public $bundled_workshops;

            public $accompanyPersons;

            // Declared, not dynamic — PHP 8.2 deprecates creating these on the fly.
            public $amount;

            public $total_attendee;

            public $payment_type;

            public function __construct(array $attributes, array $addons, array $workshops)
            {
                foreach ($attributes as $k => $v) {
                    $this->$k = $v;
                }
                $this->addons = collect($addons)->map(fn ($a) => (object) [
                    'amount' => $a['amount'],
                    'include_for_guests' => $a['guests'] ?? false,
                    'ConferenceAddon' => (object) ['addon_name' => $a['name']],
                ]);
                $this->bundled_workshops = collect($workshops)->map(fn ($w) => (object) $w);
                $this->accompanyPersons = collect();
            }
        };
    }

    public function test_line_items_always_add_up_to_the_amount_charged(): void
    {
        // 1000 participant add-on + 500/guest x 2 + 5000 workshop + balance.
        $registration = $this->registration(
            ['amount' => '20000', 'total_attendee' => 3, 'payment_type' => 6],
            [
                ['name' => 'Gala Dinner', 'amount' => '1000'],
                ['name' => 'Gala Dinner', 'amount' => '500', 'guests' => true],
            ],
            [['workshop_title' => 'Ultrasound', 'amount' => '5000']]
        );

        $break = FinancialAnalysisController::lineItems($registration, 'NPR');

        $this->assertCount(3, $break['items']);

        // The guest row holds a UNIT price; the line is price x guests.
        $guestLine = collect($break['items'])->firstWhere('note', '2 guests × 500.00');
        $this->assertNotNull($guestLine, 'guest add-on line missing');
        $this->assertSame(1000.0, $guestLine['amount']);

        // Domestic bank transfer carries no service charge.
        $this->assertSame(0.0, $break['service_charge']);

        // 20000 - 1000 - 1000 - 5000
        $this->assertSame(13000.0, $break['balance']);

        $sum = array_sum(array_column($break['items'], 'amount')) + $break['service_charge'] + $break['balance'];
        $this->assertEqualsWithDelta($break['total'], $sum, 0.01, 'parts must reconcile to the total charged');
    }

    public function test_service_charge_only_applies_to_international_non_bank_transfer(): void
    {
        $make = fn (string $currency, int $paymentType) => FinancialAnalysisController::lineItems(
            $this->registration(['amount' => '10350', 'total_attendee' => 1, 'payment_type' => $paymentType]),
            $currency
        );

        // International card payment: 3.5% is inside the total.
        $this->assertEqualsWithDelta(350.0, $make('USD', 5)['service_charge'], 0.01);

        // International bank transfer (6) is exempt.
        $this->assertSame(0.0, $make('USD', 6)['service_charge']);

        // Domestic never carries it, whatever the method.
        $this->assertSame(0.0, $make('NPR', 5)['service_charge']);
    }

    public function test_guest_addon_is_skipped_when_there_are_no_guests(): void
    {
        $registration = $this->registration(
            ['amount' => '5000', 'total_attendee' => 1, 'payment_type' => 6],
            [['name' => 'Gala Dinner', 'amount' => '500', 'guests' => true]]
        );

        $break = FinancialAnalysisController::lineItems($registration, 'NPR');

        // A per-guest charge with zero guests is not a zero line, it is no line.
        $this->assertCount(0, $break['items']);
        $this->assertSame(5000.0, $break['balance']);
    }

    public function test_compact_money_keeps_the_currency_visible(): void
    {
        $this->assertSame('NPR 4.2M', finance_money_compact(4_200_000));
        $this->assertSame('NPR 12K', finance_money_compact(12_000));
        $this->assertSame('NPR 940', finance_money_compact(940));
        $this->assertSame('USD 1.5K', finance_money_compact(1_500, 'USD'));
    }
}
