<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
class OrderController extends Controller
{
    public function order(){


    if (!Auth::check()) {
        return redirect()->guest('/login');
    }

    $user = Auth::user();
    $orders = Order::where('user_id', $user->id)->with('items')->latest()->get();

    return view('auth.my-orders', compact('orders'));

    }
    public function orderdetails($id){


    if (!Auth::check()) {
        return redirect()->guest('/login');
    }

    $user = Auth::user();
    $order = Order::where('user_id', $user->id)->where('id', $id)->with('items.product')->firstOrFail();

    return view('auth.order-details', compact('order'));


    }

    public function cancelorder($id){


    if (!Auth::check()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $user = Auth::user();
    $order = Order::where('user_id', $user->id)->where('id', $id)->first();

    if (!$order) {
        return response()->json(['success' => false, 'message' => 'Order not found'], 404);
    }

    if (!in_array($order->status, ['pending', 'confirmed'])) {
        return response()->json(['success' => false, 'message' => 'Only pending or confirmed orders can be cancelled'], 400);
    }

    $order->status = 'cancelled';
    $order->save();

    return response()->json(['success' => true, 'message' => 'Order cancelled successfully']);
    }
}
