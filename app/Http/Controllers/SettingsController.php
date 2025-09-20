<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function profile()
    {
        return view('settings.profile');
    }

    public function security()
    {
        return view('settings.security');
    }

    public function application()
    {
        return view('settings.application');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    public function updateApplication(Request $request)
    {
        $request->validate([
            'theme' => 'required|string|in:light,dark',
            'language' => 'required|string|in:en,es,ur',
        ]);

        $user = auth()->user();
        $user->theme = $request->theme;
        $user->language = $request->language;
        $user->save();

        return back()->with('success', 'Application settings updated successfully.');
    }
}
