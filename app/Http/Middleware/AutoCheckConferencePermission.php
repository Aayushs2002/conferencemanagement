<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoCheckConferencePermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        $routeName = $request->route()->getName();
        $conference = $request->route('conference');
        $conferenceId = $conference->id;
        // dd($conferenceId);

        if (!$conferenceId || !$user) {
            abort(403, 'Invalid access');
        }

        $routePermissions = config('permissions.route_permissions');

        $requiredPermission = $routePermissions[$routeName] ?? null;

        if ($requiredPermission && !$user->hasConferencePermission($conferenceId, $requiredPermission)) {
            // return redirect()->back()->with('delete', 'Permission denied');
            abort(403, 'Unauthorized: missing permission "' . $requiredPermission . '"');
        }

        return $next($request);
    }
}
