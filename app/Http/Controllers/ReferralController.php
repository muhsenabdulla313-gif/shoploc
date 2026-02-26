<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ReferralController extends Controller
{
    /**
     * Show the referral code required form.
     */
    public function show()
    {
        return view('referral.required');
    }

    /**
     * Handle the referral code submission.
     */
    public function submit(Request $request)
    {
        $request->validate([
            'referral_code' => 'required|string|exists:staff,ref_code'
        ]);

        $referralCode = $request->input('referral_code');

        // Verify that the staff member is active
        $staff = \App\Models\Staff::where('ref_code', $referralCode)
            ->where('is_active', true)
            ->first();

        if (!$staff) {
            return redirect()->back()
                ->withErrors(['referral_code' => 'Invalid or inactive referral code'])
                ->withInput();
        }

        // Set session and cookie
        Session::put('referral_code', $referralCode);
        Session::put('referral_staff_id', $staff->id);

        // Set cookie for 30 days
        $response = redirect()->route('checkout.page');
        $response->cookie('referral_code', $referralCode, 30 * 24 * 60); // 30 days in minutes
        $response->cookie('referral_staff_id', $staff->id, 30 * 24 * 60); // 30 days in minutes

        return $response;
    }
}