<?php

namespace App\Http\Controllers;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
  public function index()
    {
        $addresses = Address::where('user_id', Auth::id())->get();

        return view('checkout', compact('addresses'));
    }
   public function store(Request $request)
{
    $addressId = $request->address_id;

    if (!$addressId) {
        $address = Address::create([
            'user_id' => auth()->id(),
            'first_name' => $request->address['first_name'],
            'last_name'  => $request->address['last_name'],
            'phone'      => $request->address['phone'],
            'address'    => $request->address['address'],
            'city'       => $request->address['city'],
            'zip'        => $request->address['zip'],
        ]);

        $addressId = $address->id;
    }

    // 👉 continue order creation using $addressId

    return response()->json([
        'success' => true
    ]);
}
}
