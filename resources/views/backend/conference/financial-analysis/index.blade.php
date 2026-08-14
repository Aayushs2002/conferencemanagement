@extends('backend.layouts.conference.main')

@section('title')
    Financial Analysis
@endsection

@php
    $base = config('finance.base_currency');
    $ratesAt = config('finance.rates_updated_at');
    $paymentTypes = config('finance.payment_types');
    $creditType = (int) config('finance.credit_payment_type');
    $routeArgs = [$society, $conference];

    $chipLabels = [
        'registrant_type' => 'Registrant type',
        'prefix' => 'Prefix',
        'is_invited' => 'Invited',
        'payment_type' => 'Payment method',
        'payment_status' => 'Status',
        'currency' => 'Currency',
        'country_id' => 'Country',
        'country_scope' => 'Scope',
        'member_type_id' => 'Member type',
        'workshop_id' => 'Workshop',
        'amount_min' => 'Min amount',
        'amount_max' => 'Max amount',
        'from' => 'From',
        'to' => 'To',
        'include_dummy' => 'Dummy included',
    ];
    $activeCount = collect(array_keys($chipLabels))->filter(fn($k) => request()->filled($k))->count();

    $rateMode = in_array(request('rate_mode'), ['fixed', 'current', 'payment_date'], true) ? request('rate_mode') : 'fixed';
    $rateModeLabels = [
        'fixed' => 'Fixed rate',
        'current' => "Today's NRB rate",
        'payment_date' => 'NRB rate on registration date',
    ];

    $shown = $summary['registrations'];
    $collectedRate = $shown > 0 ? round(($summary['collected']['count'] / $shown) * 100) : 0;
@endphp

@section('styles')
    <style>
        /* Scoped to .fin-root so nothing leaks into the rest of the Sneat theme.
           Tokens are the dataviz reference palette, light mode — the admin shell is
           pinned to data-bs-theme="light", so no dark variant is defined. */
        .fin-root {
            --surface-1: #ffffff;
            --surface-2: #f7f7f5;
            --ink-1: #0b0b0b;
            --ink-2: #52514e;
            --ink-muted: #8f8d86;
            --hairline: rgba(11, 11, 11, .08);
            --grid: #e1e0d9;
            --series-1: #2a78d6;
            --series-2: #eb6834;
            --series-3: #1baf7a;
            --good: #0ca30c;
            --warning: #fab219;
            --critical: #d03b3b;
        }

        .fin-root .card {
            border: 1px solid var(--hairline);
            box-shadow: none;
            border-radius: 10px;
        }

        /* ---- page header ---- */
        .fin-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.25rem;
        }

        .fin-head h4 {
            margin: 0 0 .15rem;
            font-weight: 600;
            color: var(--ink-1);
        }

        .fin-sub {
            font-size: .8125rem;
            color: var(--ink-muted);
            margin: 0;
        }

        .fin-chip {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            background: var(--surface-2);
            border: 1px solid var(--hairline);
            border-radius: 999px;
            padding: .2rem .6rem;
            font-size: .75rem;
            color: var(--ink-2);
            margin: 0 .3rem .35rem 0;
        }

        /* ---- hero ---- */
        .fin-hero {
            display: grid;
            grid-template-columns: minmax(260px, 1.15fr) 2fr;
            gap: 0;
        }

        .fin-hero-main {
            padding: 1.5rem 1.75rem;
            border-right: 1px solid var(--hairline);
        }

        .fin-hero-label {
            display: flex;
            align-items: center;
            gap: .45rem;
            font-size: .8125rem;
            font-weight: 500;
            color: var(--ink-2);
            margin-bottom: .5rem;
        }

        .fin-hero-value {
            font-size: 2.5rem;
            line-height: 1.05;
            font-weight: 600;
            color: var(--ink-1);
            letter-spacing: -.02em;
        }

        /* The approximation sign is a qualifier, not part of the figure. */
        .fin-approx {
            font-size: .55em;
            font-weight: 400;
            color: var(--ink-muted);
            vertical-align: .18em;
            margin-right: .1em;
        }

        .fin-hero-note {
            font-size: .75rem;
            color: var(--ink-muted);
            margin-top: .45rem;
        }

        .fin-pills {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem;
            margin-top: .9rem;
        }

        .fin-pill {
            background: var(--surface-2);
            border-radius: 6px;
            padding: .3rem .55rem;
            font-size: .75rem;
            color: var(--ink-2);
            font-variant-numeric: tabular-nums;
        }

        .fin-pill strong {
            color: var(--ink-1);
            font-weight: 600;
        }

        .fin-hero-side {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
        }

        .fin-stat {
            padding: 1.5rem 1.25rem;
            border-right: 1px solid var(--hairline);
        }

        .fin-stat:last-child {
            border-right: 0;
        }

        .fin-stat-label {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .75rem;
            color: var(--ink-2);
            margin-bottom: .4rem;
        }

        .fin-stat-value {
            font-size: 1.375rem;
            font-weight: 600;
            color: var(--ink-1);
            line-height: 1.15;
        }

        .fin-stat-note {
            font-size: .6875rem;
            color: var(--ink-muted);
            margin-top: .3rem;
        }

        .fin-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            flex: none;
        }

        @media (max-width: 991.98px) {

            .fin-hero,
            .fin-hero-side {
                grid-template-columns: 1fr;
            }

            .fin-hero-main {
                border-right: 0;
                border-bottom: 1px solid var(--hairline);
            }

            .fin-stat {
                border-right: 0;
                border-bottom: 1px solid var(--hairline);
            }
        }

        /* ---- card chrome ---- */
        .fin-card-head {
            padding: 1rem 1.25rem .75rem;
            border-bottom: 1px solid var(--hairline);
        }

        .fin-card-title {
            font-size: .9375rem;
            font-weight: 600;
            color: var(--ink-1);
            margin: 0;
        }

        .fin-card-note {
            font-size: .75rem;
            color: var(--ink-muted);
            margin: .15rem 0 0;
        }

        .fin-card-body {
            padding: .75rem 1.25rem 1rem;
        }

        /* ---- ranked bar rows ----
           Plain HTML rather than a chart library: no label clipping, readable
           without JS, and the markup is its own table view. */
        .fin-bar-row {
            display: grid;
            grid-template-columns: minmax(86px, 26%) 1fr auto;
            align-items: center;
            gap: .85rem;
            padding: .45rem .5rem;
            margin: 0 -.5rem;
            border-radius: 6px;
        }

        .fin-bar-row:hover {
            background: var(--surface-2);
        }

        .fin-bar-label {
            font-size: .8125rem;
            color: var(--ink-2);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .fin-bar-track {
            min-width: 0;
        }

        .fin-bar-fill {
            height: 8px;
            background: var(--series-1);
            border-radius: 0 4px 4px 0;
            min-width: 3px;
        }

        .fin-bar-value {
            text-align: right;
            white-space: nowrap;
            font-variant-numeric: tabular-nums;
        }

        .fin-bar-amount {
            font-size: .8125rem;
            color: var(--ink-1);
            font-weight: 500;
        }

        .fin-bar-count {
            font-size: .6875rem;
            color: var(--ink-muted);
            display: block;
            line-height: 1.2;
        }

        /* ---- source split ---- */
        .fin-stack {
            display: flex;
            gap: 2px;
            height: 10px;
            margin-bottom: 1rem;
            border-radius: 2px;
            overflow: hidden;
        }

        .fin-legend-row {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 1rem;
            padding: .4rem 0;
            font-size: .8125rem;
            color: var(--ink-2);
            border-bottom: 1px solid var(--hairline);
        }

        .fin-legend-row:last-child {
            border-bottom: 0;
        }

        .fin-legend-key {
            width: 9px;
            height: 9px;
            border-radius: 2px;
            display: inline-block;
            margin-right: .5rem;
        }

        .fin-legend-amt {
            font-variant-numeric: tabular-nums;
            color: var(--ink-1);
            font-weight: 500;
        }

        /* ---- tables ---- */
        .fin-root table.fin-table {
            margin: 0;
        }

        .fin-root table.fin-table thead th {
            font-size: .6875rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--ink-muted);
            font-weight: 600;
            border-bottom: 1px solid var(--hairline);
            padding: .65rem 1.25rem;
            background: var(--surface-2);
        }

        .fin-root table.fin-table tbody td {
            padding: .6rem 1.25rem;
            font-size: .8125rem;
            color: var(--ink-2);
            border-bottom: 1px solid var(--hairline);
            vertical-align: middle;
            font-variant-numeric: tabular-nums;
        }

        .fin-root table.fin-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .fin-root table.fin-table tbody tr:hover td {
            background: var(--surface-2);
        }

        .fin-name {
            color: var(--ink-1);
            font-weight: 500;
        }

        .fin-ref {
            font-size: .6875rem;
            color: var(--ink-muted);
        }

        .fin-empty {
            color: var(--ink-muted);
            font-size: .8125rem;
            padding: 2rem 0;
            text-align: center;
        }

        /* ---- expandable transaction breakdown ---- */
        .fin-root tr.fin-tx-row {
            cursor: pointer;
        }

        .fin-root tr.fin-tx-row:focus-visible {
            outline: 2px solid var(--series-1);
            outline-offset: -2px;
        }

        .fin-tx-chevron {
            color: var(--ink-muted);
            font-size: 1rem;
            line-height: 1.35;
            transition: transform .15s ease;
        }

        .fin-root tr.fin-tx-row[aria-expanded="true"] .fin-tx-chevron {
            transform: rotate(90deg);
        }

        .fin-root tr.fin-tx-detail>td {
            background: var(--surface-2);
            padding: 0 1.25rem 1rem 1.25rem;
        }

        .fin-break {
            max-width: 620px;
            padding-top: .25rem;
        }

        .fin-break-title {
            font-size: .6875rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: var(--ink-muted);
            font-weight: 600;
            margin-bottom: .35rem;
        }

        .fin-break-line {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 1rem;
            padding: .4rem 0;
            border-bottom: 1px solid var(--hairline);
            font-size: .8125rem;
            color: var(--ink-2);
        }

        .fin-break-key {
            width: 8px;
            height: 8px;
            border-radius: 2px;
            display: inline-block;
            margin-right: .45rem;
        }

        .fin-break-note {
            display: block;
            font-size: .6875rem;
            color: var(--ink-muted);
            margin-left: 1.05rem;
        }

        .fin-break-amt {
            font-variant-numeric: tabular-nums;
            color: var(--ink-1);
            font-weight: 500;
            white-space: nowrap;
        }

        .fin-break-total {
            border-bottom: 0;
            border-top: 2px solid var(--hairline);
            margin-top: .2rem;
            font-weight: 600;
            color: var(--ink-1);
        }

        .fin-break-people {
            font-size: .6875rem;
            color: var(--ink-muted);
            padding-top: .5rem;
        }

        .fin-estimate {
            border-bottom: 1px dotted var(--ink-muted);
            cursor: help;
        }

        /* ---- filter panel ---- */
        .fin-filters .form-label {
            font-size: .75rem;
            color: var(--ink-2);
            margin-bottom: .25rem;
        }

        @media print {

            .fin-no-print,
            .layout-menu,
            .layout-navbar,
            #finFilters {
                display: none !important;
            }

            .fin-root .card {
                break-inside: avoid;
            }
        }
    </style>
@endsection

@section('content')
    <div class="fin-root">

        {{-- Page header. Filters collapse behind a button so the data leads. --}}
        <div class="fin-head">
            <div>
                <h4>Financial Analysis</h4>
                <p class="fin-sub">
                    {{ number_format($shown) }} of {{ number_format($totalUnfiltered) }} registrations
                    @if ($activeCount) · {{ $activeCount }} {{ Str::plural('filter', $activeCount) }} active @endif
                    · {{ $rateBasis['label'] ?? 'Fixed rate' }}: {{ $rateBasis['detail'] ?? '' }}
                </p>
            </div>
            <div class="d-flex gap-2 fin-no-print">
                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse"
                    data-bs-target="#finFilters" aria-expanded="{{ $activeCount ? 'true' : 'false' }}">
                    <i class="ti tabler-adjustments-horizontal me-1"></i> Filters
                    @if ($activeCount)
                        <span class="badge bg-primary ms-1">{{ $activeCount }}</span>
                    @endif
                </button>
                @if (auth()->user()->hasConferencePermissionBlade($conference, 'Export'))
                    <a href="{{ route('conference.financial-analysis.export', $routeArgs) }}?{{ http_build_query(request()->query()) }}"
                        class="btn btn-outline-secondary">
                        <i class="ti tabler-download me-1"></i> Export CSV
                    </a>
                @endif
            </div>
        </div>

        @if (!empty($rateBasis['warning']))
            <div class="alert alert-warning d-flex align-items-center gap-2 py-2">
                <i class="ti tabler-alert-triangle"></i>
                <span class="small">{{ $rateBasis['warning'] }}</span>
            </div>
        @endif

        @if ($activeCount)
            <div class="mb-3">
                @foreach ($chipLabels as $key => $label)
                    @if (request()->filled($key))
                        <span class="fin-chip">{{ $label }}: <strong>{{ request($key) }}</strong></span>
                    @endif
                @endforeach
                <a href="{{ route('conference.financial-analysis.index', $routeArgs) }}"
                    class="fin-chip text-decoration-none">Clear all</a>
            </div>
        @endif

        <div class="collapse {{ $activeCount ? 'show' : '' }} mb-4" id="finFilters">
            <div class="card fin-filters">
                <form method="GET" action="{{ route('conference.financial-analysis.index', $routeArgs) }}"
                    class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="registrant_type" class="form-label">Registrant type</label>
                            <select name="registrant_type" id="registrant_type" class="form-select form-select-sm">
                                <option value="">All types</option>
                                @foreach ($registrantTypes as $rType)
                                    <option value="{{ $rType->id }}" @selected(request('registrant_type') == $rType->id)>
                                        {{ $rType->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="payment_type" class="form-label">Payment method</label>
                            <select name="payment_type" id="payment_type" class="form-select form-select-sm">
                                <option value="">All methods</option>
                                @foreach ($paymentTypes as $id => $name)
                                    <option value="{{ $id }}" @selected(request('payment_type') == $id)>{{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="payment_status" class="form-label">Payment status</label>
                            <select name="payment_status" id="payment_status" class="form-select form-select-sm">
                                <option value="">All statuses</option>
                                <option value="collected" @selected(request('payment_status') === 'collected')>Collected (verified)
                                </option>
                                <option value="pending" @selected(request('payment_status') === 'pending')>Pending verification
                                </option>
                                <option value="credit" @selected(request('payment_status') === 'credit')>Credit (unpaid)</option>
                                <option value="rejected" @selected(request('payment_status') === 'rejected')>Rejected</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="currency" class="form-label">Currency</label>
                            <select name="currency" id="currency" class="form-select form-select-sm">
                                <option value="">All currencies</option>
                                @foreach (array_keys(config('finance.rates')) as $code)
                                    <option value="{{ $code }}" @selected(request('currency') === $code)>{{ $code }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="country_id" class="form-label">Country</label>
                            <select name="country_id" id="country_id" class="form-select form-select-sm select2">
                                <option value="">All countries</option>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @selected(request('country_id') == $country->id)>
                                        {{ $country->country_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="country_scope" class="form-label">Nationality scope</label>
                            <select name="country_scope" id="country_scope" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="national" @selected(request('country_scope') === 'national')>National</option>
                                <option value="international" @selected(request('country_scope') === 'international')>International
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="member_type_id" class="form-label">Member type</label>
                            <select name="member_type_id" id="member_type_id" class="form-select form-select-sm">
                                <option value="">All member types</option>
                                @foreach ($memberTypes as $memberType)
                                    <option value="{{ $memberType->id }}" @selected(request('member_type_id') == $memberType->id)>
                                        {{ $memberType->type }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="prefix" class="form-label">Prefix</label>
                            <select name="prefix" id="prefix" class="form-select form-select-sm">
                                <option value="">All prefixes</option>
                                @foreach ($name_prefiexs as $namePrefix)
                                    <option value="{{ $namePrefix->id }}" @selected(request('prefix') == $namePrefix->id)>
                                        {{ $namePrefix->prefix }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="workshop_id" class="form-label">Workshop</label>
                            <select name="workshop_id" id="workshop_id" class="form-select form-select-sm">
                                <option value="">All workshops</option>
                                @foreach ($workshopList as $workshop)
                                    <option value="{{ $workshop->id }}" @selected(request('workshop_id') == $workshop->id)>
                                        {{ $workshop->workshop_title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="is_invited" class="form-label">Invited</label>
                            <select name="is_invited" id="is_invited" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="1" @selected(request('is_invited') === '1')>Yes</option>
                                <option value="0" @selected(request('is_invited') === '0')>No</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="from" class="form-label">Registered from</label>
                            <input type="date" name="from" id="from" class="form-control form-control-sm"
                                value="{{ request('from') }}">
                        </div>

                        <div class="col-md-3">
                            <label for="to" class="form-label">Registered to</label>
                            <input type="date" name="to" id="to" class="form-control form-control-sm"
                                value="{{ request('to') }}">
                        </div>

                        <div class="col-md-3">
                            <label for="amount_min" class="form-label">Min amount</label>
                            <input type="number" step="any" min="0" name="amount_min" id="amount_min"
                                class="form-control form-control-sm" value="{{ request('amount_min') }}"
                                placeholder="0">
                        </div>

                        <div class="col-md-3">
                            <label for="amount_max" class="form-label">Max amount</label>
                            <input type="number" step="any" min="0" name="amount_max" id="amount_max"
                                class="form-control form-control-sm" value="{{ request('amount_max') }}"
                                placeholder="No limit">
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="include_dummy" value="1"
                                    id="include_dummy" @checked(request()->boolean('include_dummy'))>
                                <label class="form-check-label small" for="include_dummy">Include dummy
                                    registrants</label>
                            </div>
                        </div>

                        {{-- Conversion basis. Separated from the data filters because
                             it changes how figures are computed, not which rows count. --}}
                        <div class="col-12">
                            <hr class="my-2">
                            <p class="fin-card-title mb-1">Currency conversion</p>
                            <p class="fin-card-note mb-3">Foreign amounts are always stored as paid. This only affects
                                the estimated {{ $base }} figures.</p>
                        </div>

                        <div class="col-md-4">
                            <label for="rate_mode" class="form-label">Conversion basis</label>
                            <select name="rate_mode" id="rate_mode" class="form-select form-select-sm">
                                @foreach ($rateModeLabels as $value => $label)
                                    <option value="{{ $value }}" @selected($rateMode === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4" id="finRateUsdWrap">
                            <label for="rate_usd" class="form-label">1 USD = ? {{ $base }}</label>
                            <input type="number" step="0.0001" min="0" name="rate_usd" id="rate_usd"
                                class="form-control form-control-sm" value="{{ request('rate_usd') }}"
                                placeholder="{{ config('finance.rates.USD') }}">
                        </div>

                        <div class="col-md-4" id="finRateInrWrap">
                            <label for="rate_inr" class="form-label">1 INR = ? {{ $base }}</label>
                            <input type="number" step="0.0001" min="0" name="rate_inr" id="rate_inr"
                                class="form-control form-control-sm" value="{{ request('rate_inr') }}"
                                placeholder="{{ config('finance.rates.INR') }}">
                        </div>

                        <div class="col-12 d-flex align-items-end justify-content-end gap-2">
                            <a href="{{ route('conference.financial-analysis.index', $routeArgs) }}"
                                class="btn btn-sm btn-outline-secondary">Reset</a>
                            <button type="submit" class="btn btn-sm btn-primary px-3">Apply</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Hero: one dominant number, three supporting stats. --}}
        <div class="card mb-4">
            <div class="fin-hero">
                <div class="fin-hero-main">
                    <div class="fin-hero-label">
                        <span class="fin-dot" style="background: var(--good)"></span> Collected
                    </div>
                    <div class="fin-hero-value">
                        <span class="fin-estimate"
                            title="Estimated at rates as of {{ $ratesAt }}. The per-currency amounts below are exact."><span
                                class="fin-approx">≈</span>{{ finance_money_compact($summary['collected']['base']) }}</span>
                    </div>
                    <div class="fin-hero-note">
                        {{ number_format($summary['collected']['count']) }} verified
                        {{ Str::plural('payment', $summary['collected']['count']) }} · {{ $collectedRate }}% of
                        registrations in view
                    </div>

                    @if (count($byCurrency))
                        <div class="fin-pills">
                            @foreach ($byCurrency as $row)
                                <span class="fin-pill">{{ $row['currency'] }}
                                    <strong>{{ number_format($row['collected'], 0) }}</strong></span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="fin-hero-side">
                    <div class="fin-stat">
                        <div class="fin-stat-label"><span class="fin-dot" style="background: var(--warning)"></span>
                            Pending verification</div>
                        <div class="fin-stat-value"><span
                                class="fin-approx">≈</span>{{ finance_money_compact($summary['pending']['base']) }}</div>
                        <div class="fin-stat-note">{{ number_format($summary['pending']['count']) }} awaiting
                            confirmation</div>
                    </div>
                    <div class="fin-stat">
                        <div class="fin-stat-label"><span class="fin-dot" style="background: var(--critical)"></span>
                            Credit (unpaid)</div>
                        <div class="fin-stat-value"><span
                                class="fin-approx">≈</span>{{ finance_money_compact($summary['credit']['base']) }}</div>
                        <div class="fin-stat-note">{{ number_format($summary['credit']['count']) }} owed, not received
                        </div>
                    </div>
                    <div class="fin-stat">
                        <div class="fin-stat-label">Average payment</div>
                        <div class="fin-stat-value">{{ finance_money_compact($summary['average']) }}</div>
                        <div class="fin-stat-note">per collected registration</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Revenue over time --}}
        <div class="card mb-4">
            <div class="fin-card-head d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <p class="fin-card-title">Registration revenue over time</p>
                    <p class="fin-card-note">Monthly, converted to {{ $base }}</p>
                </div>
                @if (!empty($timeline))
                    @php $last = end($timeline); @endphp
                    <div class="text-end">
                        <p class="fin-card-note mb-0">
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $last['key'])->format('M Y') }}</p>
                        <span class="fin-bar-amount">≈ {{ finance_money(round($last['base'])) }}</span>
                    </div>
                @endif
            </div>
            <div class="fin-card-body">
                @if (count($timeline) < 2)
                    <div class="fin-empty">Not enough months of data to plot a trend.</div>
                @else
                    <div id="finTimeline"></div>
                @endif
            </div>
        </div>

        {{-- Breakdowns --}}
        @php
            $breakdowns = [
                ['Revenue by payment method', $byPaymentMethod, 'No payments match these filters'],
                ['Revenue by registrant type', $byRegistrantType, 'No registrations match these filters'],
                ['Top countries by revenue', $byCountry, 'No country data for these filters'],
                ['Add-on revenue', $addons, 'No add-ons sold in this selection'],
                ['Workshop revenue', $workshops, 'No workshop registrations match these filters'],
            ];
        @endphp

        <div class="row g-4 mb-4">
            @foreach ($breakdowns as $i => [$title, $rows, $emptyText])
                <div class="col-lg-6">
                    {{-- No h-100: a one-row card next to an eleven-row card would
                     otherwise stretch into a large empty box. --}}
                <div class="card">
                        <div class="fin-card-head">
                            <p class="fin-card-title">{{ $title }}</p>
                            <p class="fin-card-note">
                                @if ($title === 'Workshop revenue')
                                    Date, method, status and workshop filters apply; registrant filters do not
                                @else
                                    Converted to {{ $base }}, highest first
                                @endif
                            </p>
                        </div>
                        <div class="fin-card-body">
                            @php $max = collect($rows)->max('base') ?: 1; @endphp
                            @forelse ($rows as $row)
                                <div class="fin-bar-row">
                                    <div class="fin-bar-label" title="{{ $row['key'] }}">{{ $row['key'] }}</div>
                                    <div class="fin-bar-track">
                                        <div class="fin-bar-fill"
                                            style="width: {{ max(1, round(($row['base'] / $max) * 100, 2)) }}%"></div>
                                    </div>
                                    <div class="fin-bar-value">
                                        <span class="fin-bar-amount">{{ finance_money(round($row['base'])) }}</span>
                                        <span class="fin-bar-count">{{ number_format($row['count']) }}
                                            {{ Str::plural('payment', $row['count']) }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="fin-empty">{{ $emptyText }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Composition rides in the 6th slot so the grid stays even. --}}
            <div class="col-lg-6">
                {{-- No h-100: a one-row card next to an eleven-row card would
                     otherwise stretch into a large empty box. --}}
                <div class="card">
                    <div class="fin-card-head">
                        <p class="fin-card-title">What the registration total is made of</p>
                        <p class="fin-card-note">Parts of the registration total, not extra revenue on top of it</p>
                    </div>
                    <div class="fin-card-body">
                        @php
                            $compTotal = array_sum($composition) ?: 1;
                            $compColors = [
                                'Registration & guests' => 'series-1',
                                'Add-ons' => 'series-2',
                                'Workshops (bundled)' => 'series-3',
                            ];
                        @endphp

                        @if (array_sum($composition) <= 0)
                            <div class="fin-empty">No revenue recorded for these filters.</div>
                        @else
                            <div class="fin-stack">
                                @foreach ($composition as $name => $value)
                                    @if ($value > 0)
                                        <span style="flex: {{ $value }} 0 0; background: var(--{{ $compColors[$name] }})"
                                            title="{{ $name }}"></span>
                                    @endif
                                @endforeach
                            </div>
                            @foreach ($composition as $name => $value)
                                <div class="fin-legend-row">
                                    <span>
                                        <span class="fin-legend-key"
                                            style="background: var(--{{ $compColors[$name] }})"></span>{{ $name }}
                                    </span>
                                    <span>
                                        <span class="fin-legend-amt">{{ finance_money(round($value)) }}</span>
                                        <span class="fin-bar-count d-inline ms-1">{{ round(($value / $compTotal) * 100) }}%</span>
                                    </span>
                                </div>
                            @endforeach
                            <p class="fin-card-note mt-3 mb-0">
                                “Registration &amp; guests” is what is left after the add-ons and bundled workshops we
                                can itemise. Base and guest fees are never stored separately — only the grand total is.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Exact per-currency figures — the authoritative numbers on this page. --}}
        <div class="card mb-4">
            <div class="fin-card-head">
                <p class="fin-card-title">Currency breakdown</p>
                <p class="fin-card-note">Exact amounts as recorded. The {{ $base }} column is the only estimated figure
                    here — do not quote it as-is.</p>
            </div>
            <div class="table-responsive">
                <table class="table fin-table">
                    <thead>
                        <tr>
                            <th>Currency</th>
                            <th class="text-end">Gross</th>
                            <th class="text-end">Collected</th>
                            <th class="text-end">Registrations</th>
                            <th class="text-end">Average</th>
                            <th class="text-end">≈ {{ $base }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($byCurrency as $row)
                            <tr>
                                <td class="fin-name">{{ $row['currency'] }}</td>
                                <td class="text-end">{{ number_format($row['gross'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['collected'], 2) }}</td>
                                <td class="text-end">{{ number_format($row['count']) }}</td>
                                <td class="text-end">{{ number_format($row['average'], 2) }}</td>
                                <td class="text-end text-muted">{{ number_format($row['base'], 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="fin-empty">No revenue matches these filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Transaction detail --}}
        <div class="card">
            <div class="fin-card-head">
                <p class="fin-card-title">Transactions</p>
                <p class="fin-card-note">{{ number_format($transactions->total()) }} matching
                    {{ Str::plural('registration', $transactions->total()) }}</p>
            </div>
            <div class="table-responsive">
                <table class="table fin-table">
                    <thead>
                        <tr>
                            <th>Registrant</th>
                            <th>Country</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">≈ {{ $base }}</th>
                            <th class="text-end">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $row)
                            @php
                                $isCredit = (int) $row->payment_type === $creditType;
                                $statusTone = $isCredit
                                    ? 'danger'
                                    : match ((int) $row->verified_status) {
                                        1 => 'success',
                                        2 => 'secondary',
                                        default => 'warning',
                                    };
                                $statusText = $isCredit ? 'Credit' : $row->verified_status_text;
                                $amount = is_numeric($row->amount) ? (float) $row->amount : 0;
                                $cur = $row->resolved_currency;
                                $break = \App\Http\Controllers\Backend\Conference\FinancialAnalysisController::lineItems($row, $cur);
                                $itemCount = count($break['items']);
                            @endphp
                            <tr class="fin-tx-row" data-fin-toggle="finTx{{ $row->id }}" tabindex="0"
                                role="button" aria-expanded="false" aria-controls="finTx{{ $row->id }}">
                                <td>
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="ti tabler-chevron-right fin-tx-chevron"></i>
                                        <div>
                                            <div class="fin-name">
                                                {{ $row->user?->fullName($row->user) ?? 'Dummy registrant' }}</div>
                                            <div class="fin-ref">
                                                {{ $row->registration_id ?? $row->transaction_id ?? '—' }}
                                                @if ($itemCount)
                                                    · {{ $itemCount }} {{ Str::plural('item', $itemCount) }} included
                                                @endif
                                                @if ($break['guests'])
                                                    · {{ $break['guests'] }} {{ Str::plural('guest', $break['guests']) }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $row->user?->userDetail?->country?->country_name ?? '—' }}</td>
                                <td>{{ finance_payment_type_label($row->payment_type) }}</td>
                                <td><span class="badge bg-label-{{ $statusTone }}">{{ $statusText }}</span></td>
                                <td class="text-end fin-name">{{ $cur }} {{ number_format($amount, 2) }}</td>
                                <td class="text-end text-muted">
                                    {{ number_format($convert($amount, $cur, $row->created_at?->toDateString()), 0) }}
                                </td>
                                <td class="text-end">{{ $row->created_at?->format('d M Y') }}</td>
                            </tr>

                            {{-- Itemised panel. Always sums to the amount charged: any
                                 part we cannot evidence is shown as the balance. --}}
                            <tr class="fin-tx-detail" id="finTx{{ $row->id }}" hidden>
                                <td colspan="7">
                                    <div class="fin-break">
                                        <div class="fin-break-title">Included in this registration</div>

                                        @foreach ($break['items'] as $item)
                                            <div class="fin-break-line">
                                                <span>
                                                    <span class="fin-break-key"
                                                        style="background: var(--{{ $item['kind'] === 'workshop' ? 'series-3' : 'series-2' }})"></span>
                                                    {{ $item['label'] }}
                                                    <span class="fin-break-note">{{ $item['note'] }}</span>
                                                </span>
                                                <span class="fin-break-amt">{{ $cur }}
                                                    {{ number_format($item['amount'], 2) }}</span>
                                            </div>
                                        @endforeach

                                        @if ($break['service_charge'] > 0)
                                            <div class="fin-break-line">
                                                <span>
                                                    <span class="fin-break-key" style="background: var(--warning)"></span>
                                                    Service charge
                                                    <span class="fin-break-note">3.5% on international non-bank-transfer
                                                        payments · derived</span>
                                                </span>
                                                <span class="fin-break-amt">{{ $cur }}
                                                    {{ number_format($break['service_charge'], 2) }}</span>
                                            </div>
                                        @endif

                                        <div class="fin-break-line">
                                            <span>
                                                <span class="fin-break-key" style="background: var(--series-1)"></span>
                                                Registration &amp; guests
                                                <span class="fin-break-note">
                                                    @if ($break['balance'] < -0.01)
                                                        Itemised parts exceed the amount charged — check this record
                                                    @else
                                                        Balance; base and guest fees are not stored separately
                                                    @endif
                                                </span>
                                            </span>
                                            <span
                                                class="fin-break-amt {{ $break['balance'] < -0.01 ? 'text-danger' : '' }}">{{ $cur }}
                                                {{ number_format($break['balance'], 2) }}</span>
                                        </div>

                                        <div class="fin-break-line fin-break-total">
                                            <span>Total charged</span>
                                            <span class="fin-break-amt">{{ $cur }}
                                                {{ number_format($break['total'], 2) }}</span>
                                        </div>

                                        @if ($row->accompanyPersons?->count())
                                            <div class="fin-break-people">
                                                Accompanying:
                                                {{ $row->accompanyPersons->pluck('person_name')->filter()->implode(', ') ?: '—' }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="fin-empty">No transactions match these filters.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($transactions->hasPages())
                <div class="card-footer fin-no-print">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection

@section('scripts')
    <script>
        // Expand a transaction to see what its total is made of. Plain
        // hidden/aria toggling — no collapse plugin needed for a table row.
        (function() {
            document.querySelectorAll('[data-fin-toggle]').forEach(function(row) {
                function toggle() {
                    var panel = document.getElementById(row.dataset.finToggle);
                    if (!panel) return;
                    var open = row.getAttribute('aria-expanded') === 'true';
                    row.setAttribute('aria-expanded', open ? 'false' : 'true');
                    panel.hidden = open;
                }

                row.addEventListener('click', toggle);
                row.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        toggle();
                    }
                });
            });
        })();

        // The manual rate boxes only mean anything in fixed mode.
        (function() {
            var mode = document.querySelector('#rate_mode');
            var wraps = ['#finRateUsdWrap', '#finRateInrWrap'].map(function(s) {
                return document.querySelector(s);
            });
            if (!mode) return;

            function sync() {
                var fixed = mode.value === 'fixed';
                wraps.forEach(function(w) {
                    if (w) w.style.display = fixed ? '' : 'none';
                });
            }
            mode.addEventListener('change', sync);
            sync();
        })();
    </script>

    @if (count($timeline) >= 2)
        <script>
            (function() {
                var el = document.querySelector('#finTimeline');
                if (!el || typeof ApexCharts === 'undefined') return;

                new ApexCharts(el, {
                    chart: {
                        type: 'area',
                        height: 260,
                        toolbar: { show: false },
                        fontFamily: 'inherit',
                        animations: { enabled: false }
                    },
                    series: [{
                        name: 'Revenue ({{ $base }})',
                        data: @json(array_map(fn($r) => round($r['base']), $timeline))
                    }],
                    xaxis: {
                        categories: @json(array_map(fn($r) => \Carbon\Carbon::createFromFormat('Y-m', $r['key'])->format('M Y'), $timeline)),
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { style: { colors: '#8f8d86', fontSize: '11px' } },
                        tooltip: { enabled: false }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: '#8f8d86', fontSize: '11px' },
                            formatter: function(v) {
                                if (v >= 1e6) return (v / 1e6).toFixed(1).replace(/\.0$/, '') + 'M';
                                if (v >= 1e3) return Math.round(v / 1e3) + 'K';
                                return Math.round(v);
                            }
                        }
                    },
                    colors: ['#2a78d6'],
                    stroke: { width: 2, curve: 'smooth', lineCap: 'round' },
                    fill: { type: 'solid', opacity: 0.08 },
                    dataLabels: { enabled: false },
                    grid: { borderColor: '#e1e0d9', strokeDashArray: 0, padding: { left: 8, right: 8 } },
                    markers: { size: 0, hover: { size: 6 } },
                    tooltip: {
                        y: {
                            formatter: function(v) {
                                return '{{ $base }} ' + new Intl.NumberFormat().format(Math.round(v));
                            }
                        }
                    }
                }).render();
            })();
        </script>
    @endif
@endsection
