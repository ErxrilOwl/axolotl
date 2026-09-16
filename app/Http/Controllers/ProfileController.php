<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit()
    {
        return Inertia::render('profile/Profile', [
            'status' => session('status'),
        ]);
    }

    public function update(Request $request)
    {
        $fields = $request->validate([
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
        ]);

        $request->user()->update($fields);

        return redirect()->route('profile.edit')->with('status', 'Profile updated successfully');
    }

    public function updatePassword(Request $request)
    {
        $fields = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // The User model casts `password` as 'hashed', so assigning the
        // plain value here is hashed automatically on save.
        $request->user()->update([
            'password' => $fields['password'],
        ]);

        return redirect()->route('profile.edit')->with('status', 'Password updated successfully');
    }
}
