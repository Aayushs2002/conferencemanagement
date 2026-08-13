# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

MedconAlert — a multi-tenant conference management platform (Laravel 12, PHP 8.2+, PostgreSQL, Blade + jQuery/Bootstrap). Societies host conferences; conferences have registrations, submissions/abstracts, workshops, committees, accommodation, sponsors, passes, certificates and payments.

## Commands

```bash
composer dev          # serve + queue:listen + vite concurrently
composer test         # config:clear then artisan test
php artisan test --filter=AuthenticationTest   # single test class
vendor/bin/pint       # formatter (Laravel Pint)
npm run dev / build   # Vite (app.css/app.js only — see Assets below)
php artisan migrate --seed
```

Queue is `database` and mail is queued — a worker (`php artisan queue:work`) must be running for registration/bulk emails to send.

Tests are only the stock Breeze auth suite; there is no coverage for the conference domain. `phpunit.xml` runs on in-memory SQLite while the app runs on PostgreSQL, so anything using Postgres-specific SQL (there are raw `DB::select` queries in controllers) will not be exercised by tests.

## Tenancy and route model binding

URL shape for all admin work: `/society/{society}/conference/{conference}/<feature>`.

`RouteServiceProvider::bindHashid()` binds `society`, `conference`, `submission`, `author`, `workshop` **through Hashids** (salt = `APP_KEY`, length 10). Consequences:

- These params arrive in controllers as **already-resolved models**, even though most controller signatures declare them untyped (`public function index($society, $conference)`). Use `$conference->id`, never treat them as strings.
- Building links: pass the model (or its hashid), never the raw integer id — `Hashids::encode()` / the model itself.
- Changing `APP_KEY` invalidates every existing URL.

The **public-facing** frontend deliberately uses different parameter names to bypass hashid binding and resolve by slug: `{society_front:slug}`, `{conference_front:slug}`, `{workshop_front:slug}`. Frontend conference pages extend `Frontend\Conference\BaseConferenceController`, which resolves `$this->conference` from the slug in a constructor middleware, enforces the portal close date, and `view()->share`s `conference` + `workshops`.

`DetectSubdomain` (`check.subdomain`) maps `<sub>.domain.tld` to a `Society` via `sub_domain_name`, shares it as `societyDomainDetail`, and bounces main-site route names (home, blog, contact-us…) back to the apex domain. Unknown subdomain → redirect to apex.

## Authorization — three independent layers

1. **`users.type`**: `1` super admin, `2` society admin, `3` normal user. Checked via `is_super_admin()` / `is_society_admin()` helpers and the `check.superadmin` / `check.societyadmin` / `super.admin` middleware.
2. **Per-conference permissions**: `conference_user_permission` pivot (Spatie permission + `conference_id`), queried by `User::hasConferencePermission()`. The `auto.conf.permission` middleware maps **route name → permission label** using `config/permissions.php`. **A new protected admin route needs an entry there or it is silently unguarded.** In Blade use `hasConferencePermissionBlade($conference, '…')` / `hasAnyConferencePermission()`.
3. **Society feature flags**: `feature:<slug>` middleware + `feature_enabled($slug, $society)`; super admins bypass. Note `CheckFeature` resolves the society from `request()->segment(2)`, so it only works on `/society/{society}/…` URLs.

Spatie roles are also used per-conference via `conference_user_roles`.

## Conventions worth knowing

- **Flash messages** render as Notyf toasts in the layouts: `->with('status', …)` = success toast, `->with('delete', …)` = error toast. `'success'`/`'error'` keys are used inconsistently in a few places and mostly do not render.
- **Global helpers** in `app/Helpers/` are autoloaded (`helpers.php` is in composer `files`, and it globs `*_helpers.php`). `current_user()`, `getSociety($hashid)`, `getConference($hashid)`, `checkRegistration()`, `slugify()`, `is_expert()` are available everywhere including Blade.
- **Routes** are split: `routes/web.php` globs `routes/web/*.php` (`auth`, `society`, `conference`, `participant`). Backend/admin controllers live under `App\Http\Controllers\Backend\<Domain>`, participant-facing ones under `Backend\Participant`, public site under `Frontend\`.
- **Controllers are fat**: validation is mostly inline `$request->validate()` in try/catch with `redirect()->back()->with('delete', …)`, not Form Requests (a few exist in `app/Http/Requests`). File uploads go through injected `App\Services\File\FileService` (`fileUpload($file, $name, $location)` → public disk).
- **Layouts** mirror the scope: `backend.layouts.main` (super admin), `backend.layouts.society.main`, `backend.layouts.conference.main` (the common one), plus `frontend.conference.layouts.main` and `frontend.main-page.layouts.main`.
- **Global side effects in `AppServiceProvider`**: countries/prefixes/departments/designations/institutions/societies are `View::share`d on every request, and `User::retrieved` eager-loads `userDetail` + `conferencePermissions` on **every** user model hydration.
- **Assets**: the admin UI is a vendored Sneat/Bootstrap theme served from `public/backend/assets` via `asset()`. Vite only handles `resources/css/app.css` + `resources/js/app.js`; you rarely need to touch it.

## Payments

Gateway integrations live in `app/Services`: `ConnectIPSService` and `Services/HBL/*` (Api/Payment, Refund, Inquiry, Settlement, VoidRequest). Domestic vs foreign registrants are separate models (`Payment\NationalPayment`, `Payment\InternationalPayment`) with separate pricing paths.

## Deployment

Pushing to `master` triggers `.github/workflows/main.yml`: rsync to the VPS, `migrate --force`, then `config:cache`/`route:cache`/`view:cache`. Because routes are cached in production, **never put closures in route files**, and any change to `config/*.php` or `.env` requires the caches to be rebuilt. Migrations run automatically on deploy — they must be safe to run against live data.

Errors report to Sentry (`config/sentry.php`, wired in `bootstrap/app.php`).

## Repo notes

Numerous feature write-ups live at the repo root (`WORKSHOP_PASS_*`, `PRESENTATION_TYPE_CHANGE_*`, `EMAIL_CC_*`, `STATIC_QR_*`, `SUBMISSION_EXPERT_FILTER_*`, `SERVER_OPTIMIZATION_GUIDE`, `URGENT_SERVER_CONFIG`). They are point-in-time implementation notes, useful for intent but verify against code before trusting. `README.md` is the untouched Laravel stock readme.

Batch operations (pass generation, bulk mail) are memory-sensitive on the VPS — controllers page these in small batches (e.g. 10) on purpose; see `QUICK_START.md`.
