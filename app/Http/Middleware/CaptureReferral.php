<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Staff;

class CaptureReferral
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1) Get referral code from URL first
        $referralCode = $request->query('ref');

        // 2) If no URL ref, take from session/cookie
        if (!$referralCode) {
            $referralCode = Session::get('referral_code') ?? $request->cookie('referral_code');
        }

        $staff = null;

        // 3) Validate referral code (must belong to active staff)
        if ($referralCode) {
            $staff = Staff::where('referral_code', $referralCode)
                ->where('is_active', true)
                ->first();

            if ($staff) {
                // store in session
                Session::put('referral_code', $referralCode);
                Session::put('referral_staff_id', $staff->id);
            } else {
                // invalid code -> clear session (optional, but good)
                Session::forget(['referral_code', 'referral_staff_id']);
            }
        }

        // ✅ call next only once
        $response = $next($request);

        // 4) Set cookies if valid staff found
        if ($staff) {
            $minutes = 30 * 24 * 60; // 30 days
            $response->cookie('referral_code', $referralCode, $minutes);
            $response->cookie('referral_staff_id', $staff->id, $minutes);
        }

        return $response;
    }
}
