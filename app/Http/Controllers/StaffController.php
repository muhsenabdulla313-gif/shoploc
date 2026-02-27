<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\ReferralTracking;
class StaffController extends Controller
{
    public function dashboard()
    {
        // Check if staff member is authenticated
        if (Auth::guard('staff')->check()) {
            $staff = Auth::guard('staff')->user();

            // Ensure staff member has a referral code
            if (!$staff->referral_code) {
                $referralCode = $this->generateStaffReferralCode();
                \App\Models\Staff::where('id', $staff->id)->update(['referral_code' => $referralCode]);
                $staff->referral_code = $referralCode; // Update the local object as well
            }

            // Get referral statistics for current staff - only count completed orders
            $completedReferredOrders = \App\Models\Order::where('staff_id', $staff->id)
                ->where('status', 'completed')
                ->get();

            $purchaseReferrals = $completedReferredOrders->count();

            // Calculate total earnings from completed referred purchases (10% commission)
            $totalEarnings = $completedReferredOrders->sum('total_amount') * 0.10;

            $referralStats = [
                'purchase_referrals' => $purchaseReferrals,
                'total_earnings' => $totalEarnings,
            ];

            // Get recent activities for the current staff member - show completed referred orders
            $recentCompletedOrders = \App\Models\Order::where('staff_id', $staff->id)
                ->where('status', 'completed')
                ->with('user')
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($order) {
                    $description = 'Purchase completed';

                    if ($order->user && $order->user->name) {
                        $description .= ' from ' . $order->user->name;
                    } elseif ($order->email) {
                        $description .= ' from ' . $order->email;
                    } else {
                        $description .= ' from customer';
                    }

                    $earnings = $order->total_amount * 0.10; // 10% commission on order amount
    
                    return [
                        'date' => \Carbon\Carbon::parse($order->updated_at)->format('d M Y'),
                        'description' => $description,
                        'type' => 'Purchase',
                        'earnings' => $earnings,
                    ];
                })
                ->toArray();

            // If no completed orders, show recent referral tracking records
            $recentActivities = $recentCompletedOrders;
            if (empty($recentActivities)) {
                $recentActivities = \App\Models\ReferralTracking::where('staff_id', $staff->id)
                    ->with('user')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($referral) {
                        $description = ucfirst($referral->referral_type) . ' referral';

                        if ($referral->user && $referral->user->name) {
                            $description .= ' from ' . $referral->user->name;
                        } elseif ($referral->referred_user_email) {
                            $description .= ' from ' . $referral->referred_user_email;
                        } else {
                            $description .= ' from Unknown User';
                        }

                        $earnings = 0;
                        if ($referral->referral_type === 'purchase' && $referral->amount) {
                            $earnings = $referral->amount * 0.10;
                        } else {
                            $earnings = 10;
                        }

                        return [
                            'date' => \Carbon\Carbon::parse($referral->created_at)->format('d M Y'),
                            'description' => $description,
                            'type' => ucfirst($referral->referral_type),
                            'earnings' => $earnings,
                        ];
                    })
                    ->toArray();
            }

            // Get admin messages for the current staff member
            $adminMessages = \App\Models\StaffMessage::where('staff_id', $staff->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->toArray();

            return view('staff.dashboard', compact('staff', 'referralStats', 'recentActivities', 'adminMessages'));
        } else {
            // If not authenticated as staff, redirect to staff login
            return redirect()->route('staff.login');
        }
    }

    public function products()
    {
        return view('staff.products');
    }

    public function orders()
    {
        return view('staff.orders');
    }

    public function customers()
    {
        return view('staff.customers');
    }

    public function reports()
    {
        return view('staff.reports');
    }

    public function staffMembers_old()
    {
        $staffMembers = \App\Models\Staff::withCount('referrals')
            ->get();

        foreach ($staffMembers as $staff) {
            $referredOrders = \App\Models\Order::where('referral_code', $staff->referral_code)->get();
            $staff->total_earnings = $referredOrders->sum('total_amount') * 0.1;
        }

        return view('staff.staff-members', compact('staffMembers'));
    }
    public function staffMembers()
    {
        $loggedInStaff = auth('staff')->user();

        $staffMembers = \App\Models\Staff::withCount('referrals')
            ->where('district', $loggedInStaff->district)
            ->where('id', '!=', $loggedInStaff->id)
            ->get();

        foreach ($staffMembers as $staff) {
            $referredOrders = \App\Models\Order::where('referral_code', $staff->referral_code)->get();
            $staff->total_earnings = $referredOrders->sum('total_amount') * 0.1;
        }

        return view('staff.staff-members', compact('staffMembers'));
    }

    public function profile()
    {
        $staff = auth()->guard('staff')->user();
        return view('staff.profile', compact('staff'));
    }

    public function updatePassword(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $staff = auth()->guard('staff')->user();

        // Check if current password is correct
        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $staff->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        // Update password
        \App\Models\Staff::where('id', $staff->id)->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', 'Password updated successfully');
    }

    public function productsCreate()
    {
        // Return a view for creating a new product
        return view('staff.products-create');
    }

    public function productsEdit($id)
    {
        // Return a view for editing a product
        return view('staff.products-edit', compact('id'));
    }

    public function ordersView($id)
    {
        // Return a view for viewing an order
        return view('staff.orders-view', compact('id'));
    }

    public function ordersEdit($id)
    {
        // Return a view for editing an order
        return view('staff.orders-edit', compact('id'));
    }

    public function customersView($id)
    {
        // Return a view for viewing a customer
        return view('staff.customers-view', compact('id'));
    }

    public function customersEdit($id)
    {
        // Return a view for editing a customer
        return view('staff.customers-edit', compact('id'));
    }

    private function generateUniqueReferralCode()
    {
        do {
            $referralCode = Str::random(10);
        } while (User::where('referral_code', $referralCode)->exists());

        return $referralCode;
    }

    private function generateStaffReferralCode()
    {
        do {
            $referralCode = 'STAFF' . strtoupper(Str::random(8));
        } while (\App\Models\Staff::where('referral_code', $referralCode)->exists());

        return $referralCode;
    }

    public function getReferralLink(Request $request)
    {
        $user = Auth::user();
        if (!$user->referral_code) {
            User::where('id', $user->id)->update(['referral_code' => $this->generateUniqueReferralCode()]);
            $user->referral_code = $this->generateUniqueReferralCode(); // Update the local object as well
        }

        $referralLink = url('/?ref=' . $user->referral_code);
        return response()->json(['referral_link' => $referralLink]);
    }

    public function updateReferralCode(Request $request)
    {
        $user = Auth::user();
        User::where('id', $user->id)->update(['referral_code' => $this->generateUniqueReferralCode()]);
        $user->referral_code = $this->generateUniqueReferralCode(); // Update the local object as well

        $referralLink = url('/?ref=' . $user->referral_code);
        return response()->json(['referral_link' => $referralLink]);
    }

    public function deleteuser($id){


        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        Order::where('user_id', $user->id)->delete();
        ReferralTracking::where('referral_code', $user->referral_code)->delete();

        $user->delete();

        return response()->json(['success' => 'User deleted successfully']);


    }
}