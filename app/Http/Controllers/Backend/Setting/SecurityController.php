<?php

namespace App\Http\Controllers\Backend\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class SecurityController extends Controller
{
    public function index($society = null, $conference = null)
    {
        if (empty($society) && empty($conference)) {
            $layout = 'backend.layouts.main';
        } elseif (!empty($society) && empty($conference)) {
            $layout = 'backend.layouts.society.main';
        } elseif (!empty($society) && !empty($conference)) {
            $layout = 'backend.layouts.conference.main';
        }

        return view('backend.setting.security.index', compact('layout', 'society', 'conference'));
    }

    public function passwordChange(Request $request)
    {
        $request->validate([
            'currentPassword' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'currentPassword' => 'Your current password is incorrect.',
            ]);
        }
        $user->password = hash_password($request->new_password);
        $user->save();

        return back()->with('status', 'Password changed successfully.');
    }
}
