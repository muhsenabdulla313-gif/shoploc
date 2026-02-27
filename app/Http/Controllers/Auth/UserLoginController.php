<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use App\Models\Staff;
use App\Models\ReferralTracking;

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

        if (!$user) {
            return redirect()->route('register')
                ->with('error', 'No account found with this email address. Please register first.');
        }

        if (!$user->email_verified) {
            return back()->with('error', 'Please verify your email address before logging in.');
        }

        $otp = random_int(100000, 999999);

        session([
            'login_otp' => $otp,
            'login_user_id' => $user->id,
            'login_otp_expires_at' => now()->addMinutes(5),
        ]);

        if ($request->filled('redirect')) {
            session(['login_redirect' => $request->redirect]);
        }

        Mail::to($user->email)->send(new OtpMail($user->name, $user->email, $otp));

        return redirect()->route('verify.login.otp.form')
            ->with('success', 'Please check your email for the OTP.');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        $otp = random_int(100000, 999999);

        session([
            'registration_otp' => $otp,
            'registration_data' => $validated,
            'registration_otp_expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($validated['email'])
            ->send(new OtpMail($validated['name'], $validated['email'], $otp));

        return redirect()->route('verify.otp.form')
            ->with('success', 'Please check your email for the OTP.');
    }

    public function verifyotp()
    {
        if (!session('registration_data')) {
            return redirect()->route('register')
                ->with('error', 'Please register first');
        }

        return view('auth.verify-otp');
    }

    public function otpsubmit(Request $request)
    {
        $validated = $request->validate([
            'otp' => 'required|string',
        ]);

        $storedOtp = session('registration_otp');
        $expiresAt = session('registration_otp_expires_at');
        $registrationData = session('registration_data');

        if (!$storedOtp || !$registrationData) {
            return redirect('/register')
                ->with('error', 'Session expired. Please register again.');
        }

        if (now()->gt($expiresAt)) {
            return redirect('/register')
                ->with('error', 'OTP expired. Please register again.');
        }

        if ($validated['otp'] != $storedOtp) {
            return back()->with('error', 'Invalid OTP. Please try again.');
        }

        $existingUser = User::where('email', $registrationData['email'])->first();

        if ($existingUser) {
            Auth::login($existingUser);
            session()->forget(['registration_otp', 'registration_data', 'registration_otp_expires_at']);

            return redirect('/')->with('success', 'Welcome back!');
        }

        $user = User::create([
            'name' => $registrationData['name'],
            'email' => $registrationData['email'],
            'password' => Hash::make(Str::random(12)),
            'referral_code' => Str::random(10),
            'email_verified' => true,
        ]);

        $referralCode = session('referral_code') ?? request()->cookie('referral_code');

        if ($referralCode) {
            $referringStaff = Staff::where('referral_code', $referralCode)
                ->where('is_active', true)
                ->first();

            if ($referringStaff) {
                ReferralTracking::create([
                    'staff_id' => $referringStaff->id,
                    'referral_code' => $referralCode,
                    'referral_type' => 'signup',
                    'referred_user_email' => $user->email,
                    'used_at' => now(),
                ]);

                $user->update([
                    'referred_by_staff_id' => $referringStaff->id,
                    'referred_by_code' => $referralCode,
                ]);
            }
        }

        session()->forget(['registration_otp', 'registration_data', 'registration_otp_expires_at']);

        Auth::login($user);

        return redirect('/')->with('success', 'Registration successful!');
    }

    public function verifyloginotp()
    {
        if (!session('login_user_id')) {
            return redirect('/')->with('error', 'Session expired. Please try again.');
        }

        return view('auth.verify-login-otp');
    }

    public function verifyloginotpsubmit(Request $request)
    {
        $validated = $request->validate([
            'otp' => 'required|string',
        ]);

        $storedOtp = session('login_otp');
        $expiresAt = session('login_otp_expires_at');
        $userId = session('login_user_id');

        if (!$storedOtp || !$userId) {
            return redirect('/')->with('error', 'Session expired. Please try again.');
        }

        if (now()->gt($expiresAt)) {
            return redirect('/login')
                ->with('error', 'OTP expired. Please login again.');
        }

        if ($validated['otp'] != $storedOtp) {
            return back()->with('error', 'Invalid OTP. Please try again.');
        }

        session()->forget(['login_otp', 'login_user_id', 'login_otp_expires_at']);

        Auth::loginUsingId($userId);

        if (session()->has('login_redirect')) {
            $redirect = session('login_redirect');
            session()->forget('login_redirect');
            return redirect($redirect)->with('success', 'Login successful!');
        }

        return redirect()->intended(route('wishlist'))
            ->with('success', 'Login successful!');
    }

    public function logout()
    {

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}