<?php

namespace App\Http\Middleware;

use App\Models\User\Society;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectSubdomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        // dd($host);
        $parts = explode('.', $host);

        // List of route names that should redirect to main domain when accessed from subdomain
        $mainDomainRouteNames = [
            'home',
            'about-us',
            'solution',
            'our-client',
            'our-client.detail',
            'blog',
            'blog.single-page',
            'contact-us',
            'contact-us.store',
            'conference',
            'conference.filter',
        ];

        if (count($parts) >= 3) {
            $subdomain = $parts[0];

            if (!in_array($subdomain, ['www', 'admin'])) {
                $society = Society::where('sub_domain_name', $subdomain)->first();

                if (!$society) {
                    $mainDomain = preg_replace('/^' . preg_quote($subdomain, '/') . '\./', '', $host);
                    // return redirect()->to("http://$mainDomain" . ':8000' . $request->getRequestUri());
                    return redirect()->to("http://$mainDomain" . $request->getRequestUri());
                }

                // Check if current route name matches main domain routes
                $currentRouteName = $request->route()?->getName();
                
                if ($currentRouteName && in_array($currentRouteName, $mainDomainRouteNames)) {
                    // Redirect to main domain
                    $mainDomain = implode('.', array_slice($parts, 1));
                    $scheme = $request->getScheme();
                    return redirect()->to("{$scheme}://{$mainDomain}" . $request->getRequestUri());
                }

                $request->attributes->set('societyDomainDetail', $society);
                view()->share('societyDomainDetail', $society);
            }
        }
        return $next($request);
    }
}
