<?php

use App\Http\Middleware\AutoCheckConferencePermission;
use App\Http\Middleware\CheckFeature;
use App\Http\Middleware\DetectSubdomain;
use App\Http\Middleware\SocietyAdminPermission;
use App\Http\Middleware\SuperAdminMiddleware;
use App\Http\Middleware\SuperAdminPermission;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Sentry\Laravel\Integration;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function ($schedule) {
        // Run daily at 9 AM
        $schedule->command('participants:remind-accommodation')->dailyAt('09:00');
    })
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auto.conf.permission' => AutoCheckConferencePermission::class,
            'check.superadmin' => SuperAdminPermission::class,
            'check.societyadmin' => SocietyAdminPermission::class,
            'check.subdomain' => DetectSubdomain::class,
            'feature' => CheckFeature::class,
            'super.admin' => SuperAdminMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        Integration::handles($exceptions);
    })->create();
