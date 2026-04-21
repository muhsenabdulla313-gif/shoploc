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
   if ($request->address_id) {
    // ✅ Use existing address
   $address = Address::where('user_id', auth()->id())
    ->findOrFail($request->address_id);
} else {
    // ✅ New address → check duplicate
    $exists = Address::where('user_id', auth()->id())
        ->where('address', $request->address['address'])
        ->where('city', $request->address['city'])
        ->where('zip', $request->address['zip'])
        ->exists();

    if ($exists) {
        return response()->json([
            'success' => false,
            'message' => 'Address already exists'
        ]);
    }

    $address = Address::create([
        'user_id' => auth()->id(),
        'first_name' => $request->address['first_name'],
        'last_name' => $request->address['last_name'],
        'phone' => $request->address['phone'],
        'address' => $request->address['address'],
        'city' => $request->address['city'],
        'zip' => $request->address['zip'],
    ]);


    }

    // 👉 continue order creation using $addressId

    return response()->json([
        'success' => true
    ]);
}
}
