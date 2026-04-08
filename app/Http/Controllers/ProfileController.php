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

  
}
