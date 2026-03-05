<?php

namespace App\Http\Controllers\Admin;
use App\Models\User;
use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;

class ListController extends Controller
{
     public function users(){


            $users = User::latest()->paginate(10);
            return view('admin.users', compact('users'));

    }

    public function orders(){


            $orders = Order::with('items')->latest()->paginate(10);
            return view('admin.orders', compact('orders'));

    }

    public function orderdetails($id){

            $order = Order::with('items.product')->findOrFail($id);
            return view('admin.order-details', compact('order'));
    }
    public function updatestatus(Request $request, $id){
 try {
                $order = Order::findOrFail($id);
                $oldStatus = $order->status;
                $newStatus = $request->status;

                $shouldUpdateStaffScore = ($newStatus === 'completed' && $oldStatus !== 'completed');
                $shouldRemoveStaffScore = ($oldStatus === 'completed' && $newStatus !== 'completed');

                $order->status = $newStatus;
                $order->save();

                if ($shouldUpdateStaffScore && $order->staff_id) {
                    $staff = Staff::find($order->staff_id);
                    if ($staff) {
                        $commission = $order->total_amount * 0.10; 
                        $staff->increment('score', $commission);
                    }
                } elseif ($shouldRemoveStaffScore && $order->staff_id) {
                    $staff =Staff::find($order->staff_id);
                    if ($staff) {
                        $commission = $order->total_amount * 0.10; 
                        $staff->decrement('score', $commission);
                    }
                }

                return redirect()->back()->with('success', 'Order status updated successfully from ' . ucfirst($oldStatus) . ' to ' . ucfirst($request->status));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to update order status: ' . $e->getMessage());
            }



    }
}
