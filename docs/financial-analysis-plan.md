# Financial Analysis — Implementation Plan

Conference-level revenue reporting page with the same filter vocabulary as the Registrant page, plus finance-specific filters.

**Route:** `/society/{society}/conference/{conference}/financial-analysis`
**Decisions taken:** per-currency totals *plus* an estimated converted grand total · sources = registrations + add-ons + workshops · conference-level page (no society roll-up).

---

## 1. Where the money actually lives

Verified against the code, not assumed:

| Source | Table / model | Amount column | Notes |
|---|---|---|---|
| Registration fee | `conference_registrations` (`App\Models\Conference\ConferenceRegistration`) | `amount`, `payment_currency` | **A single grand total, not a base fee** — see below. `total_attendee` tells you how many people it covers; `AccompanyPerson` has no amount column. |
| Add-ons | `conference_registration_addons` (`ConferenceRegistration_addon`) | `amount`, `include_for_guests` | Per registration, FK `conference_registration_id` → `conference_addon_id`. `ConferenceAddon` holds the *price list* (early_bird/regular/late/on_site/guest), not the charged amount. |
| Workshops | `workshop_registrations` (`Workshop\WorkshopRegistration`) | `amount` | FK `workshop_id`; conference reached via `workshops.conference_id`. |

Not included (per scope decision): `conference_payment_statuses` gateway log, sponsors, accommodation.

### `conference_registrations.amount` is a grand total — never add anything to it

The single most important fact on this page, and one this plan originally got wrong. The amount is computed in the browser (`participant/conference-registration/create.blade.php` ~L2261) and already contains:

```
base fee + guest fees + workshop fees + add-on fees (participant and guest) + 3.5% service charge
```

The 3.5% applies to international registrants unless they paid by bank transfer. Nothing is stored line by line — only the total.

Two bugs came from missing this, both since fixed:

1. **"Revenue by source" double-counted.** It added Registration + Add-ons + Workshops as separate income streams, but all three are already inside the registration amount. Replaced by **"What the registration total is made of"**, which *subtracts* the evidenced parts from the total and reports the rest as a labelled balance — so the parts sum to the total by construction instead of inflating it.
2. **Guest add-on rows were undercounted.** A row with `include_for_guests = true` stores the **per-guest unit price**; the participant flow charged `price × (total_attendee − 1)`. Summing the raw column dropped the multiplier. `addonRevenue()` and `lineItems()` both apply it now.

Related schema notes:

- `include_for_guests` (migration `2025_12_09_115148`) was **missing from `ConferenceRegistration_addon::$fillable`**. The participant flow writes it through a raw `DB::table` insert so it worked there, but mass assignment via the model silently dropped it. Added, with a boolean cast.
- **Workshops booked through the conference flow carry the parent registration's `transaction_id`.** That is the reliable way to separate a bundled workshop fee (already inside the registration total) from a standalone sign-up (genuinely separate revenue). `workshopRevenue(bundledOnly: true)` uses it.
- `is_dummy` is in `WorkshopRegistration::$fillable` but **no migration ever created it** — using it in a `where` throws. Dummy detection is `user_id IS NULL`.

### Per-registration breakdown

Each Transactions row expands to an itemised panel: every add-on (participant, or `n guests × unit`), every bundled workshop, the derived service charge where it applies, and **"Registration & guests"** as the remaining balance. The panel always reconciles to the amount charged. If itemised parts ever exceed the total the balance goes negative and is flagged in red rather than clamped — that means the record is wrong and someone should look at it.

Base and guest fees are shown as a balance rather than reconstructed from `ConferenceMemberTypePrice`: prices change over time, so a historical registration would reconstruct wrong and then silently disagree with its own total.

### Gotchas this design must respect

1. **`payment_currency` cannot be trusted on its own.** *(Corrected during implementation — the original assumption here, "null means NPR", was wrong.)* The column is `->default('USD')` (`2026_03_10_110000_add_payment_currency_to_conference_registrations`), validation only permits `USD|INR`, and the participant controller coerces anything else to `'USD'`. So a Nepali registrant paying in rupees is stored as `'USD'`. The app's own voucher logic already resolves this correctly:

   ```php
   $currencySymbol = $paymentCurrency === 'INR' ? 'INR' : ($user->userDetail->country_id == 125 ? 'Rs.' : '$');
   ```

   The rule is therefore **INR if `payment_currency = 'INR'`, else NPR if the country is Nepal, else USD** — and it must live in exactly one helper, in both SQL and PHP form. `workshop_registrations` has no currency column at all, so it drops the first clause.

1b. **`amount` is a VARCHAR on all three tables**, not a numeric. `SUM()` needs an explicit cast, and the cast must tolerate empty/junk values instead of aborting the whole aggregate.

1c. **`is_dummy` does not exist.** It is listed in `WorkshopRegistration::$fillable` and referenced in a commented-out line of `WorkshopRegistrationController`, but no migration ever created it. Using it in a `where` throws. Dummy detection is `user_id IS NULL`.
2. **`payment_type` 1–9 is the payment-method map**, and it is currently hardcoded in three places (`ConferenceRegistrationController:2075`, the registrant Blade, the export). The Blade dropdown is also *missing* 8 and 9:
   `1 FonePay · 2 Moco · 3 Esewa · 4 Khalti · 5 Card Payment · 6 Bank Transfer/Voucher · 7 ConnectIPS · 8 Static QR · 9 Credit`
   Move it to `config/finance.php` and read it from there. `9 Credit` matters a lot here — it is money **owed, not collected**.
3. **`verified_status`** is the paid/unpaid signal for voucher-based payments. Revenue must be splittable into verified vs unverified, otherwise the headline number is fiction.
4. **`status = 1`** filters out soft-deleted/rejected registrations — every existing query does this and so must ours.
5. **Dummy registrants** have `user_id = NULL` and `transaction_id LIKE 'NAT-DUMMY-%'` / `'INT-DUMMY-%'`. They carry amounts but are not real revenue — exclude by default, with a toggle.
6. **Memory.** The registrant page does `$query->get()` and sorts a full collection in PHP. On the VPS that is already a known pain point (see `QUICK_START.md`). This page must use **SQL aggregates only** — `SUM`/`COUNT`/`GROUP BY` — and never hydrate the full registration set. The detail table at the bottom is the only paginated hydration.

---

## 2. Currency handling

Per-currency is authoritative; the converted total is clearly labelled an estimate.

### Conversion basis is a user choice

The page offers three bases, selected in the filter panel under **Currency conversion**. They change only the estimated NPR figures — stored amounts are never touched.

| Mode | `rate_mode` | Behaviour |
|---|---|---|
| **Fixed rate** (default) | `fixed` | Uses `rate_usd` / `rate_inr` typed into the form, falling back to `config/finance.php`. Deterministic and offline. |
| **Today's NRB rate** | `current` | One live lookup of the current NRB publication, cached 3 hours. |
| **NRB rate on registration date** | `payment_date` | Every payment converted at the rate NRB published the day it was registered. |

`payment_date` is the only mode needing a per-row rate, so it is the only one that adds a date column to the aggregates' `GROUP BY` — everything else stays as cheap as before. `created_at` is the date used: it is the only payment date a registration carries (`conference_payment_statuses.payment_completed_at` exists but that table is out of scope), which is why the UI says "registration date" rather than "payment date".

**NRB API facts learned the hard way:**

- `per_page` is capped at **100** — a larger request returns HTTP 400, not a truncated list. `NrbForexService` paginates.
- Each rate carries a **`unit`**: USD is quoted per 1, INR per 100. The correct multiplier is always `rate ÷ unit`. The existing voucher code in `Participant\ConferenceRegistrationController` divides the USD rate by a hardcoded `1.6` to approximate this; that is a fudge and is not reproduced here.
- NRB does not publish every calendar day. Weekends and holidays are gap-filled forward from the last publication.
- `config('finance.nrb_rate_type')` picks `buy` or `sell`. Default is `sell`, matching the existing voucher code — but `buy` is arguably the truer figure for revenue actually landing in an NPR account, since that is what a bank pays you for foreign currency. One config flip if finance disagrees.

Any NRB failure degrades to the configured fixed rates and shows a warning banner. A reporting page must never 500 because a third party is down.

```php
// config/finance.php
return [
    'base_currency' => env('FINANCE_BASE_CURRENCY', 'NPR'),
    // ponytail: static rates in config. Move to a conference_settings column or a
    // rates table when someone actually needs to change these without a deploy.
    'rates' => [ 'NPR' => 1, 'USD' => env('FINANCE_RATE_USD', 133), 'INR' => env('FINANCE_RATE_INR', 1.6) ],
    'rates_updated_at' => env('FINANCE_RATES_UPDATED_AT', '2026-08-14'),
    'payment_types' => [ 1 => 'FonePay', /* … 9 => 'Credit' */ ],
];
```

- Every card showing a converted figure renders `≈ NPR 1,234,567` with a tooltip: *"Estimated at rates as of {date}. Per-currency figures below are exact."*
- Because routes/config are cached in production, changing a rate needs `config:cache` — note this in the docs block at the top of the config file.

---

## 3. Filters

**Carried over from the Registrant page** (identical names so URLs and muscle memory transfer):
`registrant_type` · `prefix` · `is_invited` · `payment_type` · `country_id` · `country_scope` (national/international) · `member_type_id` · `from` · `to`

**New, finance-specific:**

| Filter | Param | Values |
|---|---|---|
| Revenue source | `source` | all · registration · addon · workshop |
| Payment status | `payment_status` | all · verified · unverified · credit (`payment_type = 9`) |
| Currency | `currency` | all · NPR · USD · INR |
| Amount range | `amount_min`, `amount_max` | numeric |
| Date basis | `date_basis` | registration date (default) · payment date |
| Group by | `group_by` | payment method (default) · registrant type · country · member type · month |
| Include dummy | `include_dummy` | off by default |
| Workshop | `workshop_id` | only shown when source = workshop/all |

`from`/`to` keep the existing `whereDate('created_at', …)` semantics when `date_basis=registration`.

---

## 4. Code layout

Deliberately small. No service layer, no repository, no new dependency.

```
app/Http/Controllers/Backend/Conference/FinancialAnalysisController.php   NEW  (~250 lines: index, export)
app/Models/Conference/ConferenceRegistration.php                          EDIT (+scopeApplyRegistrantFilters)
app/Exports/FinancialAnalysisExport.php                                   NEW  (Maatwebsite, mirrors ConferenceRegistrationExport)
config/finance.php                                                        NEW
resources/views/backend/conference/financial-analysis/index.blade.php     NEW
routes/web/conference.php                                                 EDIT (2 routes)
config/permissions.php                                                    EDIT (2 entries)
database/seeders/PermissionSeeder.php                                     EDIT (1 permission)
resources/views/backend/layouts/conference/sidebar.blade.php              EDIT (1 menu item)
```

### The one refactor worth doing

The registrant filter block (`registrant_type` + committee-member carve-out, `meal_type`, `is_invited`, `payment_type`, dates, country, prefix, member type) is **already copy-pasted five times** in `ConferenceRegistrationController` — lines ~88, ~1516, ~1641, ~2754, ~2880. Adding this page naively makes six.

Extract it once as a query scope on the model:

```php
// ConferenceRegistration.php
public function scopeApplyRegistrantFilters(Builder $q, Request $request, int $conferenceId, int $societyId): Builder
```

Move the existing `applyCountryScopeFilter()` body (currently private at `:2668`, hardcodes Nepal as `country_id = 125`) inside it. Then:

- The new controller uses the scope.
- Swap `index()` (line 88) and `excelExport()` (line 1516) over to it — mechanical, behaviour-identical, easy to eyeball in review.
- **Leave the other three call sites alone.** They are pass/email flows with their own quirks; migrating them is a separate change with its own risk, not this feature's job.

### Controller shape

`index()` runs a handful of aggregate queries against the filtered base query and returns scalars/small arrays — never a hydrated collection:

```
totalsByCurrency      SELECT payment_currency, SUM(amount), COUNT(*) … GROUP BY payment_currency
byPaymentMethod       GROUP BY payment_type
byRegistrantType      GROUP BY registrant_type
byCountry             JOIN user_details GROUP BY country_id           (top 10 + "Other")
verifiedSplit         GROUP BY verified_status
timeline              GROUP BY date_trunc('month', created_at)        -- Postgres
addonRevenue          JOIN conference_registration_addons GROUP BY conference_addon_id
workshopRevenue       workshop_registrations JOIN workshops GROUP BY workshop_id
```

`date_trunc` is Postgres-specific. Consistent with existing raw `DB::select` usage in this codebase, but it means the timeline is **untestable under the SQLite phpunit config** — noted, not solved.

The detail table is a separate **paginated** query (50/page), never `->get()` on the whole set.

---

## 5. UI

Sneat/Bootstrap 5 theme, `backend.layouts.conference.main`. **ApexCharts is already loaded globally** in that layout (`main.blade.php:224`) — no new JS dependency.

```
┌─ Filters ─────────────────────────────────────────────────────────────┐
│  collapsible card, same grid as registrant page; active filters shown │
│  as dismissible chips.  [Reset] [Export CSV] [Apply]                  │
└───────────────────────────────────────────────────────────────────────┘

┌ Collected ───┬ Pending ─────┬ Credit ──────┬ Registrations ┐
│ ≈ NPR 4.2M   │ ≈ NPR 310K   │ ≈ NPR 88K    │ 1,204         │   ← 4 stat tiles
│ NPR 3.1M     │ 42 unverif.  │ 12 on credit │ avg NPR 3,488 │     exact per-currency
│ USD 8,240    │              │              │               │     on the second line
└──────────────┴──────────────┴──────────────┴───────────────┘

┌─ Revenue over time (area) ──────────┬─ By payment method (donut) ─────┐
├─ By registrant type (horizontal bar)┴─ Top countries (horizontal bar) ┤
├─ Revenue by source: registration / add-ons / workshops (stacked bar) ─┤
└─ Currency breakdown table: currency · gross · count · avg · ≈ base ───┘

┌─ Transactions ────────────────────── paginated, sortable, 50/rows ────┐
│ Name · Type · Country · Method · Status · Currency · Amount · Date    │
└───────────────────────────────────────────────────────────────────────┘
```

Design rules for "professional and clean":

- **One accent colour per chart**, sequential shades within a series — not a rainbow. Semantic colour reserved for meaning: green = collected, amber = pending, red = failed/credit.
- Money is **right-aligned, tabular-nums**, thousands-separated, currency code as a prefix — `NPR 1,234,567`. No fractional cents on aggregates.
- Every card states its scope ("Filtered: 1,204 of 1,510 registrations") so a filtered view is never mistaken for the whole.
- Empty state per chart ("No revenue matches these filters"), not a blank canvas.
- Charts get `foreColor` and grid colours from CSS variables so they hold up if the theme switches.
- Print stylesheet: filters and nav hidden, charts and tables kept — finance people print these.

---

## 6. Access control

- `config/permissions.php`: `conference.financial-analysis.index` → `'View Financial Analysis'`, `conference.financial-analysis.export` → `'Export'` (reuse the existing Export permission).
- `PermissionSeeder`: add `View Financial Analysis` under `parent => 'Conference Registration'`.
- **Reuse the `conference-registration-management` feature flag.** A new flag needs a `features` row per society and someone remembering to enable it; not worth it for a page that is a view over registration data.
- Route group: `['auto.conf.permission', 'feature:conference-registration-management']`, same as the registrant group. Without the `config/permissions.php` entry the route is silently unguarded — that entry is not optional.
- Sidebar item goes under the existing Conference Registration submenu (`sidebar.blade.php:42`), wrapped in `hasConferencePermissionBlade(…, 'View Financial Analysis')`.

### Creating the permission is not enough to make the page reachable

**There is no super-admin bypass anywhere in this app.** `AutoCheckConferencePermission` and `User::hasConferencePermission()` both require an explicit `conference_user_permission` row for that exact (user, conference, permission) triple. A brand-new permission is therefore invisible to everyone, super admin included, until it is granted.

`PermissionSeeder` cannot be used to add one after launch either — it ends in a raw `Permission::insert()`, so re-running it against a populated database fails on the unique index. Deploy runs `migrate --force`, so migrations are the only path that reaches production. Two shipped:

| Migration | What it does |
|---|---|
| `2026_08_14_120000_add_financial_analysis_permission` | Creates the permission row (no-op if present) |
| `2026_08_14_130000_grant_financial_analysis_permission` | Attaches it to the `SuperAdmin` and `society admin` roles, then backfills grants |

The backfill is deliberately narrow: it grants only to **type 1 (super admin) and type 2 (society admin) users who already hold `View Conference Registration`** on that conference. Type-3 users on narrow conference roles are excluded — registrant access does not imply entitlement to revenue figures. Verified: users 1, 2 and 36 gained it across their conferences; users 5 and 6 (type 3, on the "conference role" / "submission" roles) still cannot see the menu item. Widen it per user in the role/permission UI.

The two admin roles held 134 of 135 permissions before this — attaching the new one restores that "these roles hold everything" invariant for future role assignments.

---

## 7. Build order

| # | Step | Output |
|---|---|---|
| 1 | `config/finance.php` + currency/payment-method helpers | rates, labels, NPR-default resolution in one place |
| 2 | `scopeApplyRegistrantFilters` on the model; migrate `index()` + `excelExport()` | duplication down from 5 to 3, new page has a base to build on |
| 3 | Controller `index()` with aggregate queries, no view yet | verify numbers against the registrant page manually |
| 4 | Blade: filters + 4 stat tiles + currency table | usable page, no charts |
| 5 | ApexCharts: timeline, methods, types, countries, sources | the visual layer |
| 6 | Paginated transactions table | drill-down |
| 7 | CSV export honouring active filters | `FinancialAnalysisExport` |
| 8 | Routes, permissions, seeder, sidebar | shipped |

Steps 1–4 are the feature; 5–8 finish it. Stopping after 4 still leaves something useful in production.

---

## 8. The check

One `tests/Feature/FinancialAnalysisTest.php` seeding a handful of registrations across NPR/USD, verified/unverified, with add-ons and a workshop, asserting:

- per-currency sums are exact and not cross-added,
- converted total matches the configured rates,
- `payment_type = 9` (Credit) lands in Credit, not Collected,
- dummy registrants are excluded unless `include_dummy=1`,
- an unverified voucher registration is not counted as collected.

Money logic without a test is how you find out in front of a treasurer. The `date_trunc` timeline is excluded from the test — it does not run on the SQLite test config.

---

---

## What actually shipped, where it differs from the plan

Built and verified against the live Postgres database. Four deviations, all made during implementation:

1. **Most breakdowns are HTML bar rows, not ApexCharts.** Payment method / registrant type / country / add-ons / workshops render as plain `div` bars with the label, fill and value in a CSS grid. They can't clip a label, they read without JS, the markup *is* the table view the accessibility rules ask for, and it is less code than the chart config would have been. ApexCharts is used for the one place a chart earns its keep — the monthly revenue area chart — and only when there are ≥ 2 months to plot.

2. **No donut for payment methods.** Nine payment methods in a donut is unreadable and exceeds the 8-slot categorical palette. Sorted horizontal bars in a single hue instead.

3. **Palette.** Light mode only — the admin shell is pinned to `data-bs-theme="light"`, so no dark variant was written. The three-hue source split (`#2a78d6 / #eb6834 / #1baf7a`) was validated with the dataviz validator against the `#ffffff` card surface: all checks pass, all-pairs. The aqua slot sits below 3:1 contrast, which obliges visible labels — every segment is direct-labeled with its value and percentage.

4. **The test does not boot the app.** `AppServiceProvider::boot()` queries `countries` on every boot, before `RefreshDatabase` has migrated — this already fails all four stock Breeze tests on this repo and is not this feature's to fix. `FinancialAnalysisTest` extends PHPUnit's `TestCase` with a bare container holding only the config repository, so the money path is genuinely covered: 8 tests, 30 assertions, passing.

**Behaviour change to an existing page:** `excelExport()` was missing the `meal_type` and `member_type_id` filters that `index()` had, even though the Export button submits the same filter form — exporting with either set silently returned the wrong rows. Both call sites now share `ConferenceRegistration::scopeApplyRegistrantFilters()`, so the export honours them. Verified the registrant page, its export and `generatePass` all still respond 200/302 with filters applied.

**Verified end to end:** all aggregates execute on Postgres; every filter branch runs and measurably narrows results; the page renders 200 (104 KB) with the permission granted and 403 without; CSV export returns correct headers, currency resolution and conversion.

---

## Skipped, and when to add it

- **Society-wide roll-up across conferences** — build it when a society actually has multiple concurrent conferences to compare. The controller's aggregate queries are the reusable part.
- ~~**Live FX rates from an API**~~ — *built.* `App\Services\NrbForexService` fetches, paginates, caches and gap-fills NRB publications; the page exposes fixed / current / per-payment-date bases. Rates are cached, not stored, so there is still no rates table.
- **PDF export** — CSV opens in Excel, which is where these numbers go anyway.
- **Refund / settlement reconciliation** — `Services/HBL/Refund` and `Settlement` exist but nothing persists their results; reconciliation needs a data model first, and that is a separate feature.
- **Gateway success-rate analytics** from `conference_payment_statuses` — explicitly out of scope; the table is there when it is wanted.
