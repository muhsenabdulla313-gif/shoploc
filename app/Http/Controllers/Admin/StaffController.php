<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Models\StaffMessage;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
{
    public function index()
    {
        $staff = \App\Models\Staff::all();
        return view('admin.staff.index', compact('staff'));
    }

    public function create()
    {
        return view('admin.staff.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'village' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'bank_account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        \App\Models\Staff::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'village' => $request->village,
            'district' => $request->district,
            'pincode' => $request->pincode,
            'bank_account_number' => $request->bank_account_number,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
            'password' => Hash::make($request->password),
            'uniqueCode' => $this->generateUniqueCode(),
        ]);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member created successfully!');
    }

    private function generateUniqueCode()
    {
        do {
            $code = 'STAFF' . strtoupper(uniqid());
        } while (\App\Models\Staff::where('uniqueCode', $code)->exists());
        
        return $code;
    }

    public function edit($id)
    {
        $staff = \App\Models\Staff::findOrFail($id);
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $staff = \App\Models\Staff::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $id,
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'village' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'bank_account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'village' => $request->village,
            'district' => $request->district,
            'pincode' => $request->pincode,
            'bank_account_number' => $request->bank_account_number,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
        ]);

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
            $staff->save();
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully!');
    }

    public function destroy($id)
    {
        $staff = \App\Models\Staff::findOrFail($id);
        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully!');
    }
    
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);
            
            // Check if user is authenticated
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Authentication required'], 401);
            }
            
            // Get all staff members
            $staffMembers = \App\Models\Staff::all();
            
            if ($staffMembers->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No staff members found']);
            }
            
            // Create message for each staff member
            foreach ($staffMembers as $staff) {
                StaffMessage::create([
                    'from_admin_id' => Auth::id(),
                    'to_staff_id' => $staff->id,
                    'subject' => $request->subject,
                    'message' => $request->message,
                ]);
            }
            
            return response()->json(['success' => true, 'message' => 'Message sent to all staff members successfully!']);
            
        } catch (\Exception $e) {
            Log::error('Message sending error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send message: ' . $e->getMessage()], 500);
        }
    }
    
    public function getMessages($staffId)
    {
        $messages = StaffMessage::where('to_staff_id', $staffId)
            ->with(['sender:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json(['success' => true, 'messages' => $messages]);
    }
    
    public function getAllMessages()
    {
        $messages = StaffMessage::with(['recipient:id,name,email', 'sender:id,name,email'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json(['success' => true, 'messages' => $messages]);
    }
}
