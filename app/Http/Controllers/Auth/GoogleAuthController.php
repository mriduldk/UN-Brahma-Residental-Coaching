<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle(Request $request)
    {
        // Store intended role in session if it's passed during registration
        if ($request->has('role')) {
            $request->session()->put('google_auth_role', $request->query('role'));
        }

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Google authentication failed: ' . $e->getMessage()]);
        }

        // Find existing user by google_id or email
        $user = User::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();

        if ($user) {
            // Update google_id and avatar if missing/changed
            $user->update([
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar
            ]);
        } else {
            // Create a new user
            $role = $request->session()->pull('google_auth_role', 'student');
            
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'password' => null, // No password for SSO users
                'role' => $role,
            ]);

            // Mark email as verified since it came from Google
            $user->markEmailAsVerified();
            
            // Trigger the registered event if needed
            event(new \Illuminate\Auth\Events\Registered($user));
        }

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
