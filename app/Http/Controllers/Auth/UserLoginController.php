<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class UserLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.user-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();
        
        // Check if user exists
        if (!$user) {
            return redirect()->route('register')->with('error', 'No account found with this email address. Please register first.');
        }
        
        // Check if email is verified
        if (!$user->email_verified) {
            return redirect()->back()->with('error', 'Please verify your email address before logging in. Check your email for the OTP.');
        }
        
        // Generate OTP for existing user
        $otp = rand(100000, 999999);
        
        // Store OTP in session with user info
        session(['login_otp' => $otp, 'login_user_id' => $user->id]);
        
        // Store redirect URL if provided
        $redirect = $request->input('redirect');
        if ($redirect) {
            session(['login_redirect' => $redirect]);
        }
        
        // Send OTP email
        Mail::to($user->email)->send(new \App\Mail\OtpMail($user->name, $user->email, $otp));

        return redirect()->route('verify.login.otp.form')->with('success', 'Please check your email for the OTP to complete login.');
    }
}
