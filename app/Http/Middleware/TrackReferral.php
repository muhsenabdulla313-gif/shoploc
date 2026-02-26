<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackReferral
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if there's a referral code in the session
        $referralCode = session('referral_code');
        
        // If no referral code in session, check if it's in the URL
        if (!$referralCode) {
            $referralCode = $request->query('ref');
            if ($referralCode) {
                session(['referral_code' => $referralCode]);
            }
        }
        
        if ($referralCode && !$request->session()->get('referral_processed')) {
            // Find the staff member associated with this referral code
            $staff = \App\Models\Staff::where('referral_code', $referralCode)->first();
            
            if ($staff) {
                // Track the referral for signup (if not already tracked)
                $existingReferral = \App\Models\ReferralTracking::where('staff_id', $staff->id)
                    ->where('referral_code', $referralCode)
                    ->where('referral_type', 'signup')
                    ->first();
                
                if (!$existingReferral) {
                    \App\Models\ReferralTracking::create([
                        'staff_id' => $staff->id,
                        'referral_code' => $referralCode,
                        'referral_type' => 'signup',
                        'referral_description' => 'User signup via staff referral',
                    ]);
                }
                
                // Mark referral as processed for this session to avoid duplicate tracking
                $request->session()->put('referral_processed', true);
            }
        }
        
        return $next($request);
    }
}
