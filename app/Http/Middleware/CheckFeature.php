<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckFeature
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $featureSlug)
    {
        if (auth()->check() && auth()->user()->type == 1) {
            return $next($request);
        }

        if (! feature_enabled($featureSlug, getSociety(request()->segment(2)))) {
            abort(403, 'This feature is not enabled for your society.');
        }

        return $next($request);
    }
}
