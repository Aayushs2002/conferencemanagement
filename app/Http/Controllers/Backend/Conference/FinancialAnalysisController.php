<?php

namespace App\Http\Controllers\Backend\Conference;

use App\Exports\FinancialAnalysisExport;
use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceRegistration;
use App\Models\Conference\RegistrantType;
use App\Models\User\MemberType;
use App\Models\Workshop\Workshop;
use App\Services\NrbForexService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Revenue reporting for a single conference.
 *
 * Everything here is SQL aggregation. The registrant page hydrates the whole
 * result set and sorts it in PHP, which is a known memory problem on the VPS —
 * do not copy that pattern into this page. The only hydration is the paginated
 * transactions table at the bottom.
 */
class FinancialAnalysisController extends Controller
{
    /** Buckets a registration falls into. Order matters: credit wins over verified. */
    private const BUCKET_SQL = "CASE
        WHEN conference_registrations.payment_type = 9 THEN 'credit'
        WHEN conference_registrations.verified_status = 1 THEN 'collected'
        WHEN conference_registrations.verified_status = 2 THEN 'rejected'
        ELSE 'pending' END";

    /** Flat currency => NPR multiplier, used by the fixed and current modes. */
    private array $rates = [];

    /** 'Y-m-d' => [currency => multiplier], only populated in payment-date mode. */
    private array $dateRates = [];

    private bool $useDateRates = false;

    /** What to tell the user about how figures were converted. */
    private array $rateBasis = [];

    public function __construct(private NrbForexService $forex)
    {
        // Seeded here, not only in resolveRates(), so a convert() that somehow
        // runs before the basis is resolved falls back to the configured rate
        // rather than silently treating every currency as 1:1.
        $this->rates = config('finance.rates', []);
    }

    public function index(Request $request, $society, $conference)
    {
        $this->resolveRates($request, $conference, $society->id);

        $currency = finance_currency_sql('conference_registrations');
        $amount = finance_amount_sql('conference_registrations');
        $rateDate = $this->rateDateSql('conference_registrations');

        // One query covers the stat tiles, the currency table and the source split.
        $buckets = $this->base($request, $conference, $society->id)->toBase()
            ->select(
                DB::raw($currency.' as currency'),
                DB::raw(self::BUCKET_SQL.' as bucket'),
                DB::raw('COUNT(*) as cnt'),
                DB::raw('SUM('.$amount.') as total')
            )
            ->groupByRaw($currency)
            ->groupByRaw(self::BUCKET_SQL)
            ->when($rateDate, fn ($q) => $q->addSelect(DB::raw($rateDate.' as rate_date'))->groupByRaw($rateDate))
            ->get();

        $registrationIds = $this->base($request, $conference, $society->id)->toBase()
            ->select('conference_registrations.id');

        $addons = $this->addonRevenue($registrationIds);
        $workshops = $this->workshopRevenue($request, $conference);
        $bundledWorkshops = $this->workshopRevenue($request, $conference, bundledOnly: true);
        $registrantTypeLabels = RegistrantType::getLabelMap();

        return view('backend.conference.financial-analysis.index', [
            'society' => $society,
            'conference' => $conference,
            'filters' => $request->all(),

            'summary' => $this->summarise($buckets),
            'byCurrency' => $this->byCurrency($buckets),
            'byPaymentMethod' => $this->groupedRevenue(
                $request, $conference, $society->id,
                'conference_registrations.payment_type',
                fn ($key) => finance_payment_type_label($key)
            ),
            'byRegistrantType' => $this->groupedRevenue(
                $request, $conference, $society->id,
                'conference_registrations.registrant_type',
                fn ($key) => $registrantTypeLabels[(int) $key] ?? 'Unknown'
            ),
            'byCountry' => $this->byCountry($request, $conference, $society->id),
            'timeline' => $this->timeline($request, $conference, $society->id),
            'composition' => $this->composition(
                $this->registrationRevenue($buckets),
                $addons,
                $bundledWorkshops
            ),
            'bundledWorkshopTotal' => array_sum(array_column($bundledWorkshops, 'base')),
            'addons' => $addons,
            'workshops' => $workshops,
            'transactions' => $this->transactions($request, $conference, $society->id),

            // The per-row converter, so the transactions table honours the same
            // basis as the aggregates above it.
            'rateBasis' => $this->rateBasis,
            'convert' => fn ($amount, $currency, $date = null) => $this->convert($amount, $currency, $date),
            'activeRates' => $this->rates,

            // Filter dropdown data. countries + name_prefiexs are View::shared globally.
            'registrantTypes' => RegistrantType::forConference($conference->id),
            'memberTypes' => MemberType::where(['society_id' => $society->id, 'status' => 1])->get(),
            'workshopList' => Workshop::where('conference_id', $conference->id)->where('status', 1)->orderBy('workshop_title')->get(),
            'totalUnfiltered' => ConferenceRegistration::where('conference_id', $conference->id)->where('status', 1)->count(),
        ]);
    }

    public function export(Request $request, $society, $conference)
    {
        $this->resolveRates($request, $conference, $society->id);

        return Excel::download(
            new FinancialAnalysisExport(
                $this->transactionQuery($request, $conference, $society->id)->get(),
                fn ($amount, $currency, $date = null) => $this->convert($amount, $currency, $date),
                $this->rateBasis['label'] ?? 'Fixed rate'
            ),
            'financial-analysis-'.now()->format('Y-m-d').'.csv',
            \Maatwebsite\Excel\Excel::CSV
        );
    }

    /**
     * Work out which exchange rates this request should use.
     *
     *  fixed         — rates typed into the filter form, falling back to config.
     *  current       — today's NRB publication.
     *  payment_date  — each registration converted at the NRB rate published on
     *                  the day it was created. This is the only mode that needs
     *                  a per-row rate, so it is also the only one that forces an
     *                  extra GROUP BY on the aggregates.
     *
     * NRB is reachable over the network and can fail. Any failure degrades to
     * the configured fixed rates and sets a warning the page displays — a
     * reporting screen must never 500 because a third party is down.
     */
    private function resolveRates(Request $request, $conference, int $societyId): void
    {
        $mode = in_array($request->input('rate_mode'), ['fixed', 'current', 'payment_date'], true)
            ? $request->input('rate_mode')
            : 'fixed';

        $configured = config('finance.rates');
        $this->rates = $configured;

        if ($mode === 'fixed') {
            foreach (['USD', 'INR'] as $code) {
                $typed = $request->input('rate_'.strtolower($code));

                if (is_numeric($typed) && (float) $typed > 0) {
                    $this->rates[$code] = (float) $typed;
                }
            }

            $this->rateBasis = [
                'mode' => 'fixed',
                'label' => 'Fixed rate',
                'detail' => $this->describeRates($this->rates),
            ];

            return;
        }

        if ($mode === 'current') {
            $live = $this->forex->currentRates();

            if (empty($live)) {
                $this->rateBasis = [
                    'mode' => 'current',
                    'label' => 'Fixed rate',
                    'detail' => $this->describeRates($this->rates),
                    'warning' => 'Live NRB rates were unavailable, so the configured fixed rates were used instead.',
                ];

                return;
            }

            $this->rates = array_merge($configured, array_intersect_key($live, $configured));
            $this->rateBasis = [
                'mode' => 'current',
                'label' => "Today's NRB rate",
                'detail' => $this->describeRates($this->rates),
            ];

            return;
        }

        // payment_date — fetch the whole span the filtered data covers, once.
        $span = $this->base($request, $conference, $societyId)->toBase()
            ->select(
                DB::raw('MIN(conference_registrations.created_at) as first_at'),
                DB::raw('MAX(conference_registrations.created_at) as last_at')
            )
            ->first();

        if (! $span || ! $span->first_at) {
            $this->rateBasis = [
                'mode' => 'payment_date',
                'label' => 'Rate on registration date',
                'detail' => 'No registrations in range.',
            ];

            return;
        }

        $this->dateRates = $this->forex->ratesBetween(
            Carbon::parse($span->first_at)->startOfDay(),
            Carbon::parse($span->last_at)->startOfDay()
        );

        if (empty($this->dateRates)) {
            $this->rateBasis = [
                'mode' => 'payment_date',
                'label' => 'Fixed rate',
                'detail' => $this->describeRates($this->rates),
                'warning' => 'Historical NRB rates were unavailable, so the configured fixed rates were used instead.',
            ];

            return;
        }

        $this->useDateRates = true;
        $this->rateBasis = [
            'mode' => 'payment_date',
            'label' => 'Rate on registration date',
            'detail' => 'Each payment converted at the NRB rate published on the day it was registered ('
                .Carbon::parse($span->first_at)->format('d M Y').' – '
                .Carbon::parse($span->last_at)->format('d M Y').').',
        ];
    }

    private function describeRates(array $rates): string
    {
        $parts = [];

        foreach ($rates as $code => $rate) {
            if ($code !== config('finance.base_currency')) {
                $parts[] = '1 '.$code.' = '.rtrim(rtrim(number_format((float) $rate, 4), '0'), '.').' '.config('finance.base_currency');
            }
        }

        return implode(' · ', $parts);
    }

    /**
     * The date expression aggregates must group by, or null when a flat rate
     * applies and the extra grouping would only bloat the result set.
     */
    private function rateDateSql(string $table): ?string
    {
        return $this->useDateRates ? "to_char({$table}.created_at, 'YYYY-MM-DD')" : null;
    }

    /** Convert one amount into the base currency using the active rate basis. */
    private function convert($amount, ?string $currency, ?string $date = null): float
    {
        $currency = strtoupper($currency ?: config('finance.base_currency'));

        $rate = $this->useDateRates && $date
            ? ($this->dateRates[substr($date, 0, 10)][$currency] ?? $this->rates[$currency] ?? 1)
            : ($this->rates[$currency] ?? 1);

        return (float) $amount * (float) $rate;
    }

    /**
     * Filtered registration query — the single source of truth every aggregate
     * below is derived from. Rebuilt per call rather than cloned because the
     * aggregates each replace the select list.
     */
    private function base(Request $request, $conference, int $societyId): Builder
    {
        $query = ConferenceRegistration::query()
            ->where('conference_registrations.conference_id', $conference->id)
            ->where('conference_registrations.status', 1)
            ->applyRegistrantFilters($request->all(), $conference->id, $societyId);

        // Dummy registrants carry amounts but are not real revenue.
        if (! $request->boolean('include_dummy')) {
            $query->whereNotNull('conference_registrations.user_id');
        }

        if ($request->filled('payment_status')) {
            match ($request->payment_status) {
                'collected' => $query->where('verified_status', 1)->where('payment_type', '!=', config('finance.credit_payment_type')),
                'pending' => $query->where('verified_status', 0)->where('payment_type', '!=', config('finance.credit_payment_type')),
                'credit' => $query->where('payment_type', config('finance.credit_payment_type')),
                'rejected' => $query->where('verified_status', 2),
                default => null,
            };
        }

        if ($request->filled('currency')) {
            $query->whereRaw(finance_currency_sql('conference_registrations').' = ?', [$request->currency]);
        }

        $amount = finance_amount_sql('conference_registrations');

        if ($request->filled('amount_min')) {
            $query->whereRaw($amount.' >= ?', [(float) $request->amount_min]);
        }

        if ($request->filled('amount_max')) {
            $query->whereRaw($amount.' <= ?', [(float) $request->amount_max]);
        }

        return $query;
    }

    /** Tiles: collected / pending / credit / count, per currency and converted. */
    private function summarise($buckets): array
    {
        $out = [
            'collected' => ['base' => 0.0, 'count' => 0],
            'pending' => ['base' => 0.0, 'count' => 0],
            'credit' => ['base' => 0.0, 'count' => 0],
            'rejected' => ['base' => 0.0, 'count' => 0],
        ];

        foreach ($buckets as $row) {
            if (! isset($out[$row->bucket])) {
                continue;
            }
            $out[$row->bucket]['base'] += $this->convert($row->total, $row->currency, $row->rate_date ?? null);
            $out[$row->bucket]['count'] += (int) $row->cnt;
        }

        $out['registrations'] = array_sum(array_column($out, 'count'));
        $out['average'] = $out['collected']['count'] > 0
            ? $out['collected']['base'] / $out['collected']['count']
            : 0.0;

        return $out;
    }

    /** Exact per-currency figures. These are authoritative; the converted total is not. */
    private function byCurrency($buckets): array
    {
        $rows = [];

        foreach ($buckets as $row) {
            $rows[$row->currency] ??= ['currency' => $row->currency, 'gross' => 0.0, 'collected' => 0.0, 'count' => 0, 'base' => 0.0];
            $rows[$row->currency]['gross'] += (float) $row->total;
            $rows[$row->currency]['count'] += (int) $row->cnt;

            // Accumulated per row, not multiplied at the end: in payment-date
            // mode each row carries its own rate.
            $rows[$row->currency]['base'] += $this->convert($row->total, $row->currency, $row->rate_date ?? null);

            if ($row->bucket === 'collected') {
                $rows[$row->currency]['collected'] += (float) $row->total;
            }
        }

        foreach ($rows as &$row) {
            $row['average'] = $row['count'] > 0 ? $row['gross'] / $row['count'] : 0.0;
        }

        return array_values($rows);
    }

    /** Total registration revenue in base currency, reusing the bucket query. */
    private function registrationRevenue($buckets): float
    {
        $total = 0.0;

        foreach ($buckets as $row) {
            $total += $this->convert($row->total, $row->currency, $row->rate_date ?? null);
        }

        return $total;
    }

    /** Revenue grouped by an arbitrary column, converted to base for comparability. */
    private function groupedRevenue(Request $request, $conference, int $societyId, string $column, ?callable $label = null): array
    {
        $currency = finance_currency_sql('conference_registrations');

        $rateDate = $this->rateDateSql('conference_registrations');

        $rows = $this->base($request, $conference, $societyId)->toBase()
            ->select(
                DB::raw($column.' as bucket_key'),
                DB::raw($currency.' as currency'),
                DB::raw('COUNT(*) as cnt'),
                DB::raw('SUM('.finance_amount_sql('conference_registrations').') as total')
            )
            ->groupByRaw($column)
            ->groupByRaw($currency)
            ->when($rateDate, fn ($q) => $q->addSelect(DB::raw($rateDate.' as rate_date'))->groupByRaw($rateDate))
            ->get();

        $folded = $this->foldToBase($rows, 'bucket_key', $label);
        usort($folded, fn ($a, $b) => $b['base'] <=> $a['base']);

        return $folded;
    }

    private function byCountry(Request $request, $conference, int $societyId): array
    {
        $currency = finance_currency_sql('conference_registrations');

        // Correlated subquery for the same reason as finance_currency_sql: joining
        // user_details would collide with the filter scope's unqualified columns.
        $countrySql = '(SELECT c.country_name FROM user_details ud
            JOIN countries c ON c.id = ud.country_id
            WHERE ud.user_id = conference_registrations.user_id LIMIT 1)';

        $rateDate = $this->rateDateSql('conference_registrations');

        $rows = $this->base($request, $conference, $societyId)->toBase()
            ->select(
                DB::raw($countrySql.' as bucket_key'),
                DB::raw($currency.' as currency'),
                DB::raw('COUNT(*) as cnt'),
                DB::raw('SUM('.finance_amount_sql('conference_registrations').') as total')
            )
            ->groupByRaw($countrySql)
            ->groupByRaw($currency)
            ->when($rateDate, fn ($q) => $q->addSelect(DB::raw($rateDate.' as rate_date'))->groupByRaw($rateDate))
            ->get();

        $folded = $this->foldToBase($rows, 'bucket_key');
        usort($folded, fn ($a, $b) => $b['base'] <=> $a['base']);

        // Top 10 plus an "Other" roll-up, so the chart stays readable.
        if (count($folded) > 10) {
            $rest = array_splice($folded, 10);
            $folded[] = [
                'key' => 'Other',
                'base' => array_sum(array_column($rest, 'base')),
                'count' => array_sum(array_column($rest, 'count')),
            ];
        }

        return $folded;
    }

    /**
     * Monthly revenue. Uses Postgres date_trunc — this is not exercised by the
     * SQLite phpunit config.
     */
    private function timeline(Request $request, $conference, int $societyId): array
    {
        $currency = finance_currency_sql('conference_registrations');
        $month = "to_char(date_trunc('month', conference_registrations.created_at), 'YYYY-MM')";

        // In payment-date mode the day is grouped alongside the month and folded
        // back up, so each day contributes at its own rate.
        $rateDate = $this->rateDateSql('conference_registrations');

        $rows = $this->base($request, $conference, $societyId)->toBase()
            ->select(
                DB::raw($month.' as bucket_key'),
                DB::raw($currency.' as currency'),
                DB::raw('COUNT(*) as cnt'),
                DB::raw('SUM('.finance_amount_sql('conference_registrations').') as total')
            )
            ->groupByRaw($month)
            ->groupByRaw($currency)
            ->when($rateDate, fn ($q) => $q->addSelect(DB::raw($rateDate.' as rate_date'))->groupByRaw($rateDate))
            ->get();

        $folded = $this->foldToBase($rows, 'bucket_key');
        usort($folded, fn ($a, $b) => strcmp((string) $a['key'], (string) $b['key']));

        return $folded;
    }

    /**
     * Add-on revenue.
     *
     * Rows flagged include_for_guests hold the PER-GUEST unit price, not the
     * line total — the participant flow charges `price × (total_attendee - 1)`
     * but stores the unit price (see the insert in
     * Participant\ConferenceRegistrationController). Summing the raw column
     * therefore undercounts every guest add-on. The multiplier is applied here.
     */
    private function addonRevenue($registrationIds): array
    {
        $lineTotal = 'CASE WHEN cra.include_for_guests
            THEN '.finance_amount_sql('cra').' * GREATEST(COALESCE(cr.total_attendee, 1) - 1, 0)
            ELSE '.finance_amount_sql('cra').' END';

        $rows = DB::table('conference_registration_addons as cra')
            ->join('conference_registrations as cr', 'cr.id', '=', 'cra.conference_registration_id')
            ->leftJoin('conference_addons as ca', 'ca.id', '=', 'cra.conference_addon_id')
            ->whereIn('cra.conference_registration_id', $registrationIds)
            ->where('cra.status', 1)
            ->select(
                DB::raw("COALESCE(ca.addon_name, 'Unnamed add-on') as bucket_key"),
                DB::raw(finance_currency_sql('cr').' as currency'),
                DB::raw('COUNT(*) as cnt'),
                DB::raw('SUM('.$lineTotal.') as total')
            )
            ->groupBy('ca.addon_name')
            ->groupByRaw(finance_currency_sql('cr'))
            // Add-ons follow their parent registration's date, not their own.
            ->when($this->rateDateSql('cr'), fn ($q, $d) => $q->addSelect(DB::raw($d.' as rate_date'))->groupByRaw($d))
            ->get();

        $folded = $this->foldToBase($rows, 'bucket_key');
        usort($folded, fn ($a, $b) => $b['base'] <=> $a['base']);

        return $folded;
    }

    /**
     * What the registration total is actually made of.
     *
     * `conference_registrations.amount` is a single grand total computed in the
     * browser (create.blade.php ~L2261): base fee + guest fees + workshop fees +
     * add-on fees + a 3.5% service charge for international non-bank-transfer
     * payments. Nothing is stored line by line.
     *
     * So this does NOT add sources together — that would double-count, since the
     * total already contains them. It subtracts the parts we can evidence from
     * the total and reports the rest as a labelled balance.
     */
    private function composition(float $registrationTotal, array $addons, array $bundledWorkshops): array
    {
        $addonTotal = array_sum(array_column($addons, 'base'));
        $workshopTotal = array_sum(array_column($bundledWorkshops, 'base'));

        $balance = $registrationTotal - $addonTotal - $workshopTotal;

        return [
            'Registration & guests' => max(0, $balance),
            'Add-ons' => $addonTotal,
            'Workshops (bundled)' => $workshopTotal,
            // Deliberately no total key — the view sums these, and they sum to
            // the registration total by construction.
        ];
    }

    /**
     * Workshop revenue. Only the filters that make sense across both tables are
     * applied: date range, payment method, verification, and the workshop picker.
     * Registrant-page filters like member type do not carry over.
     *
     * `bundledOnly` narrows to workshops booked through the conference
     * registration flow, which stamps the parent registration's transaction_id
     * onto the workshop row. Those fees are already inside
     * conference_registrations.amount; standalone workshop sign-ups are not.
     */
    private function workshopRevenue(Request $request, $conference, bool $bundledOnly = false): array
    {
        $currency = finance_currency_sql('wr', hasCurrencyColumn: false, hasTransactionId: false);

        $query = DB::table('workshop_registrations as wr')
            ->join('workshops as w', 'w.id', '=', 'wr.workshop_id')
            ->where('w.conference_id', $conference->id)
            ->where('wr.status', 1);

        $bundled = fn ($q) => $q->whereNotNull('wr.transaction_id')
            ->whereExists(fn ($sub) => $sub->selectRaw('1')
                ->from('conference_registrations as cr2')
                ->where('cr2.conference_id', $conference->id)
                ->where('cr2.status', 1)
                ->whereColumn('cr2.transaction_id', 'wr.transaction_id'));

        $bundledOnly ? $bundled($query) : null;

        if ($request->filled('workshop_id')) {
            $query->where('wr.workshop_id', $request->workshop_id);
        }

        if ($request->filled('payment_type')) {
            $query->where('wr.payment_type', $request->payment_type);
        }

        if ($request->filled('from')) {
            $query->whereDate('wr.created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('wr.created_at', '<=', $request->to);
        }

        if ($request->filled('payment_status')) {
            match ($request->payment_status) {
                'collected' => $query->where('wr.verified_status', 1),
                'pending' => $query->where('wr.verified_status', 0),
                'rejected' => $query->where('wr.verified_status', 2),
                // workshop_registrations has no credit concept — exclude it entirely
                'credit' => $query->whereRaw('1 = 0'),
                default => null,
            };
        }

        $rows = $query->select(
            DB::raw("COALESCE(w.workshop_title, 'Untitled workshop') as bucket_key"),
            DB::raw($currency.' as currency'),
            DB::raw('COUNT(*) as cnt'),
            DB::raw('SUM('.finance_amount_sql('wr').') as total')
        )
            ->groupBy('w.workshop_title')
            ->groupByRaw($currency)
            ->when($this->rateDateSql('wr'), fn ($q, $d) => $q->addSelect(DB::raw($d.' as rate_date'))->groupByRaw($d))
            ->get();

        $folded = $this->foldToBase($rows, 'bucket_key');
        usort($folded, fn ($a, $b) => $b['base'] <=> $a['base']);

        return $folded;
    }

    private function transactions(Request $request, $conference, int $societyId)
    {
        $page = $this->transactionQuery($request, $conference, $societyId)
            ->with(['addons.ConferenceAddon', 'accompanyPersons'])
            ->paginate(50)
            ->withQueryString();

        $this->attachBundledWorkshops($page->getCollection(), $conference);

        return $page;
    }

    /**
     * Attach each registration's bundled workshops in one query.
     *
     * Not an Eloquent relation on purpose: the join key is transaction_id, and a
     * hasMany on a nullable column would match every other null-transaction row
     * to every registration. Matching explicitly, and skipping nulls, avoids that.
     */
    private function attachBundledWorkshops($registrations, $conference): void
    {
        $transactionIds = $registrations->pluck('transaction_id')->filter()->unique();

        if ($transactionIds->isEmpty()) {
            $registrations->each(fn ($r) => $r->setAttribute('bundled_workshops', collect()));

            return;
        }

        $workshops = DB::table('workshop_registrations as wr')
            ->join('workshops as w', 'w.id', '=', 'wr.workshop_id')
            ->where('w.conference_id', $conference->id)
            ->where('wr.status', 1)
            ->whereIn('wr.transaction_id', $transactionIds)
            ->select('wr.transaction_id', 'wr.amount', 'w.workshop_title')
            ->get()
            ->groupBy('transaction_id');

        $registrations->each(function ($r) use ($workshops) {
            $r->setAttribute('bundled_workshops', $workshops->get($r->transaction_id, collect()));
        });
    }

    /**
     * Break one registration's grand total into the parts we can evidence.
     *
     * Returns line items plus the unexplained balance, so the panel always adds
     * up to the amount the registrant was actually charged — no line is ever
     * invented, and any gap is shown rather than hidden.
     */
    public static function lineItems($registration, ?string $currency = null): array
    {
        $total = is_numeric($registration->amount) ? (float) $registration->amount : 0.0;
        $guests = max(0, (int) ($registration->total_attendee ?? 1) - 1);
        $items = [];

        foreach ($registration->addons ?? [] as $addon) {
            $unit = is_numeric($addon->amount) ? (float) $addon->amount : 0.0;
            $forGuests = (bool) $addon->include_for_guests;
            $qty = $forGuests ? $guests : 1;

            if ($forGuests && $guests === 0) {
                continue;
            }

            $items[] = [
                'label' => $addon->ConferenceAddon->addon_name ?? 'Add-on',
                'note' => $forGuests ? $guests.' '.Str::plural('guest', $guests).' × '.number_format($unit, 2) : 'Participant',
                'amount' => $unit * $qty,
                'kind' => 'addon',
            ];
        }

        foreach ($registration->bundled_workshops ?? [] as $workshop) {
            $items[] = [
                'label' => $workshop->workshop_title ?: 'Workshop',
                'note' => 'Booked with this registration',
                'amount' => is_numeric($workshop->amount) ? (float) $workshop->amount : 0.0,
                'kind' => 'workshop',
            ];
        }

        // 3.5% is added for international registrants unless they paid by bank
        // transfer (create.blade.php: excludeServiceCharge). Not stored as a
        // flag anywhere, so this is derived and labelled as such.
        $isInternational = strtoupper((string) $currency) !== 'NPR';
        $chargeable = $isInternational && (int) $registration->payment_type !== 6;
        $serviceCharge = 0.0;

        if ($chargeable && $total > 0) {
            $serviceCharge = round($total - ($total / 1.035), 2);
        }

        $accountedFor = array_sum(array_column($items, 'amount')) + $serviceCharge;

        return [
            'items' => $items,
            'service_charge' => $serviceCharge,
            'balance' => $total - $accountedFor,
            'guests' => $guests,
            'total' => $total,
        ];
    }

    private function transactionQuery(Request $request, $conference, int $societyId): Builder
    {
        return $this->base($request, $conference, $societyId)
            ->with(['user.userDetail.country', 'user.userDetail.namePrefix'])
            ->select('conference_registrations.*')
            ->selectRaw(finance_currency_sql('conference_registrations').' as resolved_currency')
            ->orderByDesc('conference_registrations.created_at');
    }

    /**
     * Collapse rows of {bucket_key, currency, cnt, total} into one entry per key
     * with amounts converted to the base currency.
     */
    private function foldToBase($rows, string $keyColumn, ?callable $label = null): array
    {
        $out = [];

        foreach ($rows as $row) {
            $key = $row->{$keyColumn} ?? 'Unknown';
            $key = $label ? $label($key) : $key;
            $out[$key] ??= ['key' => $key, 'base' => 0.0, 'count' => 0];
            $out[$key]['base'] += $this->convert($row->total, $row->currency, $row->rate_date ?? null);
            $out[$key]['count'] += (int) $row->cnt;
        }

        return array_values($out);
    }
}
