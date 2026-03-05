<?php

namespace App\Http\Controllers\staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffLoginController extends Controller
{
    public function authenticate()
    {
        $credentials = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (auth()->guard('staff')->attempt($credentials)) {
            request()->session()->regenerate();
            return redirect()->intended('/staff');
        }

        return back()->withErrors([
            'email' => 'Invalid staff credentials',
        ])->onlyInput('email');
    }
    public function logout()
    {

        auth()->guard('staff')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        // Check if coming from admin area
        $referrer = request()->headers->get('referer');
        if ($referrer && (str_contains($referrer, '/admin') || request()->is('admin*'))) {
            return redirect('/admin/login');
        }

        return redirect('/staff/login');
    }
}
