<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Msg91Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OtpLoginController extends Controller
{
    protected $msg91;

    public function __construct(Msg91Service $msg91)
    {
        $this->msg91 = $msg91;
    }

    public function show()
    {
        return view('auth.login-otp');
    }

    /**
     * Verify the access token from the MSG91 widget and log in the user.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
            'mobile_number' => 'required|string',
        ]);

        $verification = $this->msg91->verifyAccessToken($request->access_token);

        if ($verification) {
            // Find user by mobile number
            $user = User::where('mobile_number', $request->mobile_number)->first();

            if (!$user) {
                return back()->withErrors(['mobile_number' => 'No account found with this mobile number.']);
            }

            Auth::login($user);

            // Mark as verified if not already
            if (!$user->mobile_verified_at) {
                $user->update(['mobile_verified_at' => now()]);
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['mobile_number' => 'Mobile verification failed. Please try again.']);
    }
}
