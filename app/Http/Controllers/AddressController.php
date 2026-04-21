<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;

class AddressController extends Controller
{
    public function create()
    {
        return view('address.create');
    }

public function store(Request $request)
{
    $request->validate([
        'address' => 'required',
        'city' => 'required',
        'state' => 'required',
        'zip' => 'required',
        'phone' => 'required',
    ]);

    // 🔍 Check duplicate
    $exists = Address::where('user_id', Auth::id())
        ->where('address', $request->address)
        ->where('city', $request->city)
        ->where('state', $request->state)
        ->where('zip', $request->zip)
        ->where('phone', $request->phone)
        ->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'Address already exists!');
    }

    Address::create([
        'user_id' => Auth::id(),
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'address' => $request->address,
        'city' => $request->city,
        'state' => $request->state,
        'zip' => $request->zip,
        'phone' => $request->phone,
    ]);

    return redirect()->back()->with('success', 'Address added successfully');
}

    public function edit($id)
    {
        $address = Address::findOrFail($id);
        return view('address.edit', compact('address'));
    }

public function update(Request $request, $id)
{
    $address = Address::findOrFail($id);

    // 🔍 Check duplicate (exclude current record)
    $exists = Address::where('user_id', Auth::id())
        ->where('address', $request->address)
        ->where('city', $request->city)
        ->where('state', $request->state)
        ->where('zip', $request->zip)
        ->where('phone', $request->phone)
        ->where('id', '!=', $id)
        ->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'Address already exists!');
    }

    $address->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'address' => $request->address,
        'city' => $request->city,
        'state' => $request->state,
        'zip' => $request->zip,
        'phone' => $request->phone,
    ]);

    return redirect('/profile')->with('success', 'Address updated');
}
    public function destroy($id)
    {
        Address::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Address deleted');
    }
}
