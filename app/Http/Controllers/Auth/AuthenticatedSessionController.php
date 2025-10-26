<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View|RedirectResponse
    {
        // dd($request->attributes->get('society'));
        $society = $request->attributes->get('societyDomainDetail');
        $subdomain = $society?->sub_domain_name; // null-safe
        $host = $request->getHost(); // use the same request instance

        // If we have a society and we're NOT already on its subdomain
        if ($society && !str_starts_with($host, $subdomain . '.')) {
            // Remove 'www.' if present
            $mainDomain = preg_replace('/^www\./', '', $host);

            // Ensure we don't duplicate the subdomain
            if (!str_starts_with($mainDomain, $subdomain . '.')) {
                $url = $request->getScheme() . "://{$subdomain}.{$mainDomain}" . $request->getRequestUri();
                return redirect()->to($url);
            }
        }

        // dd($society);
        return view('auth.login', compact('society'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = current_user();
        $society = $request->attributes->get('societyDomainDetail');

        // Optional profile update modal
        if ($user->type == 3 && $user->is_profile_updated == null) {
            session(['show_profile_update_modal' => true]);
        }

        // If user is society admin (type 2)
        if ($user->type == 2) {
            return redirect()->intended(route('society.dashboard', $user->societies->first()));
        }

        // For other user types:
        if ($society && $user->societies->contains('id', $society->id)) {
            // Redirect only if user's societies include this one
            return redirect()->intended(route('society.dashboard', $society));
        }

        // Default redirect if not matched
        return redirect()->intended(route('dashboard', absolute: false));
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
