<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\User\NamePrefix;
use App\Models\User\UserDetail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $society = $request->attributes->get('societyDomainDetail');

        // Get only the name prefixes that the society has selected
        if ($society && $society->namePrefixes()->exists()) {
            $name_prefiexs = $society->namePrefixes()->where('status', 1)->get();
        } else {
            // Fallback to all active prefixes if society hasn't selected any
            $name_prefiexs = NamePrefix::whereStatus(1)->get();
        }

        return view('auth.register', compact('society', 'name_prefiexs'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $society = $request->attributes->get('societyDomainDetail');

            $validated = $request->validate(
                [
                    'f_name' => ['required', 'string', 'max:255'],
                    'm_name' => ['nullable', 'string', 'max:255'],
                    'l_name' => ['required', 'string', 'max:255'],
                    'gender' => 'required',
                    'country_id' => 'required',
                    // 'phone' => 'required|regex:/^\d{10}$/',
                    'phone' => 'required',
                    'name_prefix_id' => 'required',
                    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                    'password' => ['required', 'confirmed', Rules\Password::defaults()],
                    'website' => ['nullable', 'max:0'],
                    'member_type_id' => $society ? 'required' : 'nullable',
                ],
                [
                    'gender.required' => 'Gender is required',
                    'country_id.required' => 'Country is required',
                    'name_prefix_id.required' => 'Name Prefix is required',
                    'member_type_id.required' => 'Member Type is required'
                ]
            );
            if ($request->filled('website')) {
                abort(403, 'Spam detected.');
            }
            DB::beginTransaction();
            $user = User::create([
                'f_name' => $request->f_name,
                'm_name' => $request->m_name,
                'l_name' => $request->l_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'type' => 3
            ]);

            $validated['user_id'] = $user->id;

            UserDetail::create($validated);
            // $society = $request->attributes->get('societyDomainDetail');

            if ($society) {
                $user->societies()->attach($society->id, [
                    'member_type_id' => $request->member_type_id,
                ]);
            }
            DB::commit();

            event(new Registered($user));

            Auth::login($user);

            if (current_user()->type == 3 && current_user()->is_profile_updated == null) {
                session(['show_profile_update_modal' => true]);
            }
            if ($society && $user->societies->contains('id', $society->id)) {
                // Redirect only if user's societies include this one
                return redirect()->intended(route('society.dashboard', $society));
            }
            return redirect(route('dashboard', absolute: false))->with('status', 'Successfully registered. Login to proceed further');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
