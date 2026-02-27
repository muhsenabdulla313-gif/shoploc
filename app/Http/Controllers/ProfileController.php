<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\ReferralTracking;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile(){

    if (!Auth::check()) {
        return redirect()->guest('/login');
    }
  
    $user = Auth::user();
    return view('auth.profile', compact('user'));

    }

    public function delelte(){

    if (!Auth::check()) {
        return redirect()->guest('/login');
    }

    $user = Auth::user();

    Order::where('user_id', $user->id)->delete();
    ReferralTracking::where('referral_code', $user->referral_code)->delete();

    User::destroy($user->id);

    Auth::logout();

    return redirect('/')->with('success', 'Your account has been successfully deleted.');
    }
}
