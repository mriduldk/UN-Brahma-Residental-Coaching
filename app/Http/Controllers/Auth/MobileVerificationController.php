<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Msg91Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileVerificationController extends Controller
{
    /**
     * Show the mobile verification prompt.
     */
    public function show(Request $request)
    {
        return view('auth.verify-mobile');
    }

    /**
     * Verify the user's mobile number using MSG91 token.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'mobile_number' => ['required', 'string', 'max:15', 'unique:' . User::class],
            'access_token' => ['required', 'string'],
        ]);

        $msg91 = app(Msg91Service::class);
        $verification = $msg91->verifyAccessToken($request->access_token);

        if (!$verification) {
            return back()->withErrors(['mobile_number' => 'Mobile verification failed. Please try again.']);
        }

        $user = $request->user();
        
        // Update user
        $user->mobile_number = $request->mobile_number;
        $user->mobile_verified_at = now();
        $user->save();

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
