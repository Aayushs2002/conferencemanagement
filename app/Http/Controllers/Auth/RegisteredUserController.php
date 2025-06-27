<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate(
                [
                    'f_name' => ['required', 'string', 'max:255'],
                    'm_name' => ['nullable', 'string', 'max:255'],
                    'l_name' => ['required', 'string', 'max:255'],
                    'gender' => 'required',
                    'country_id' => 'required',
                    'phone' => 'required|regex:/^\d{10}$/',
                    'name_prefix_id' => 'required',
                    'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
                    'password' => ['required', 'confirmed', Rules\Password::defaults()],
                ],
                [
                    'gender.required' => 'Gender is required',
                    'country_id.required' => 'Country is required',
                    'name_prefix_id.required' => 'Name Prefix is required'
                ]
            );
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
            DB::commit();

            event(new Registered($user));

            Auth::login($user);

            if (current_user()->type == 3 && current_user()->is_profile_updated == null) {
                session(['show_profile_update_modal' => true]);
            }
            return redirect(route('dashboard', absolute: false))->with('status', 'Successfully registered. Login to proceed further');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
