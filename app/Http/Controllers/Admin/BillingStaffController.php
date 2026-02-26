<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class BillingStaffController extends Controller
{
    public function index()
    {
        $billingStaff = BillingStaff::paginate(10);
        return view('admin.billing-staff', compact('billingStaff'));
    }

    public function create()
    {
        return view('admin.billing-staff-create');
    }

    public function show($id)
    {
        $billingStaff = BillingStaff::findOrFail($id);
        return view('admin.billing-staff-view', compact('billingStaff'));
    }

    public function edit($id)
    {
        $billingStaff = BillingStaff::findOrFail($id);
        return view('admin.billing-staff-edit', compact('billingStaff'));
    }

    public function update(Request $request, $id)
    {
        $billingStaff = BillingStaff::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:billing_staff,email,' . $id,
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'bank_account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'village' => $request->village,
            'district' => $request->district,
            'pincode' => $request->pincode,
            'bank_account_number' => $request->bank_account_number,
            'ifsc_code' => $request->ifsc_code,
            'bank_name' => $request->bank_name,
        ];

        // Only update password if provided
        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $billingStaff->update($updateData);

        return redirect()->route('admin.billing.staff')
            ->with('success', 'Billing staff member updated successfully!');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:billing_staff,email',
            'phone' => 'required|string|max:15',
            'address' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'bank_account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        BillingStaff::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'village' => $request->village,
            'district' => $request->district,
            'pincode' => $request->pincode,
            'bank_account_number' => $request->bank_account_number,
            'ifsc_code' => $request->ifsc_code,
            'bank_name' => $request->bank_name,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.billing.staff')
            ->with('success', 'Billing staff member created successfully!');
    }

    public function destroy($id)
    {
        $billingStaff = BillingStaff::findOrFail($id);
        $billingStaff->delete();

        return response()->json(['success' => 'Billing staff member deleted successfully']);
    }
}
