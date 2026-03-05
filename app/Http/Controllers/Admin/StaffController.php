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
use Illuminate\Pagination\LengthAwarePaginator;
class StaffController extends Controller
{
   

   

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'required|string|max:15',
            'bank_account_number' => 'nullable|string|max:50',
            'ifsc_code' => 'nullable|string|max:20',
            'bank_name' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'village' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Staff::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'bank_account_number' => $request->bank_account_number,
            'ifsc_code' => $request->ifsc_code,
            'bank_name' => $request->bank_name,
            'city' => $request->city,
            'village' => $request->village,
            'address' => $request->address,
            'district' => $request->district,
            'pincode' => $request->pincode,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Staff member created successfully!');
    }

    private function generateUniqueCode()
    {
        do {
            $code = 'STAFF' . strtoupper(uniqid());
        } while (Staff::where('uniqueCode', $code)->exists());

        return $code;
    }

    public function edit($id)
    {
        $staff = Staff::findOrFail($id);
        return view('admin.staff.edit', compact('staff'));
    }

    public function update(Request $request, $id)
    {
        $staff = Staff::findOrFail($id);

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
        $staff = Staff::findOrFail($id);
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

            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Authentication required'], 401);
            }

            $staffMembers = Staff::all();

            if ($staffMembers->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No staff members found']);
            }

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
    public function manage()
    {
        $staffMembers = Staff::all();

        foreach ($staffMembers as $staff) {
            $referredOrders = \App\Models\Order::where('referral_code', $staff->referral_code)->get();
            $staff->purchases_referred = $referredOrders->count();
            $staff->total_earnings = $referredOrders->sum('total_amount') * 0.1;
        }

        $currentPage = request()->get('page', 1);
        $perPage = 10;
        $offset = ($currentPage - 1) * $perPage;
        $paginatedStaff = collect($staffMembers)->slice($offset, $perPage)->values();
        $staffMembers = new LengthAwarePaginator(
            $paginatedStaff,
            count($staffMembers),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        $latestBulkMessageIds = StaffMessage::where('recipient_type', 'all')
            ->selectRaw('MAX(id) as id')
            ->groupBy('subject', 'message')
            ->pluck('id');

        $bulkMessages = StaffMessage::whereIn('id', $latestBulkMessageIds)->get();

        $individualMessages = StaffMessage::where('recipient_type', '!=', 'all')
            ->with('staff')
            ->latest()
            ->get();

        $sentMessages = $bulkMessages->concat($individualMessages);

        $currentPageMsg = request()->get('page', 1);
        $perPageMsg = 10;
        $offsetMsg = ($currentPageMsg - 1) * $perPageMsg;

        $paginatedMessages = collect($sentMessages)->slice($offsetMsg, $perPageMsg)->values();
        $messagesPaginator = new LengthAwarePaginator(
            $paginatedMessages,
            count($sentMessages),
            $perPageMsg,
            $currentPageMsg,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );

        return view('admin.staff', compact('staffMembers', 'messagesPaginator'));
    }
    public function staffedit($id)
    {

        $staff = Staff::findOrFail($id);
        return response()->json(['success' => true, 'data' => $staff]);


    }
    public function bulkmessage(Request $request)
    {


        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $staffMembers = Staff::all();
        $messageCount = $staffMembers->count();

        foreach ($staffMembers as $staff) {
            \App\Models\StaffMessage::create([
                'staff_id' => $staff->id,
                'recipient_type' => 'all',
                'subject' => $request->subject,
                'message' => $request->message,
            ]);
        }

        return redirect()->back()->with('success', "Bulk message sent successfully to {$messageCount} staff members!");
    }
    public function allmessages()
    {
        $messages = StaffMessage::with('staff')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.staff-messages', compact('messages'));

    }
    public function deletemessages($id)
    {



        $message = StaffMessage::findOrFail($id);
        $message->delete();

        return response()->json(['success' => true]);
    }
    public function staffmessage(Request $request){
    $request->validate([
        'staff_id' => 'required|exists:staff,id',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    $staff = Staff::findOrFail($request->staff_id);


    return redirect()->back()->with('success', 'Message sent successfully to ' . $staff->name . '!');



    }

}
