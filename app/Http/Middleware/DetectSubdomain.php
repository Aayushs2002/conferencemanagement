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
        if (count($parts) >= 3) {
            $subdomain = $parts[0]; 

            if (!in_array($subdomain, ['www', 'admin'])) {
                $society = Society::where('sub_domain_name', $subdomain)->first();

                if (!$society) {
                    $mainDomain = preg_replace('/^' . preg_quote($subdomain, '/') . '\./', '', $host);
                    // return redirect()->to("http://$mainDomain" . ':8000' . $request->getRequestUri());
                    return redirect()->to("http://$mainDomain" . $request->getRequestUri());
                }

                $request->attributes->set('societyDomainDetail', $society);
                view()->share('societyDomainDetail', $society);
            }
        }
        return $next($request);
    }
}
