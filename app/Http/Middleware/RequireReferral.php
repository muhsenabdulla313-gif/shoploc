<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RequireReferral
{
    public function handle(Request $request, Closure $next): Response
    {
        $refCode = Session::get('referral_code') ?? $request->cookie('referral_code');
        $staffId = Session::get('referral_staff_id') ?? $request->cookie('referral_staff_id');

        if (!$refCode || !$staffId) {

            // ✅ Admin / Support WhatsApp number (digits only, country code included)
            $whatsappNumber = '918848748469';

            // ✅ English-only message
            $message = urlencode(
                "Hello 👋\n".
                "I need a referral code.\n".
                "Login, checkout, or purchase is not allowed without a referral link.\n".
                "Please share a valid referral code."
            );

            return redirect()->away("https://wa.me/{$whatsappNumber}?text={$message}");
        }

        return $next($request);
    }
}
