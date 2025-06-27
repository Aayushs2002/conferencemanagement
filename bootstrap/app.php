<?php

use App\Http\Middleware\AutoCheckConferencePermission;
use App\Http\Middleware\SocietyAdminPermission;
use App\Http\Middleware\SuperAdminPermission;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auto.conf.permission' => AutoCheckConferencePermission::class,
            'check.superadmin' => SuperAdminPermission::class,
            'check.societyadmin' => SocietyAdminPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
