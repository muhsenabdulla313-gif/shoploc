<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function checkout(Request $request){

    if (!$request->session()->has('referral_code')) {
        return response()->json(['success' => false, 'message' => 'Referral code is required to place an order'], 403);
    }

    try {
        $input = $request->all();
        $cartItems = $input['cart'] ?? [];
        $address = $input['address'] ?? [];

        if (empty($cartItems)) {
            return response()->json(['success' => false, 'message' => 'Cart is empty']);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($input, $cartItems, $address) {

            foreach ($cartItems as $item) {
                $product = \App\Models\Product::find($item['id']);
                if (!$product) {
                    throw new \Exception('Product not found: ' . $item['id']);
                }
                if ($product->stock < $item['qty']) {
                    throw new \Exception('Insufficient stock for ' . $product->name . '. Available: ' . $product->stock . ', Requested: ' . $item['qty']);
                }
            }

            $order = \App\Models\Order::create([
                'user_id' => Auth::check() ? Auth::user()->id : null,
                'status' => 'pending',
                'subtotal' => $input['subtotal'] ?? 0,
                'discount_amount' => $input['discount'] ?? 0,
                'total_amount' => $input['total'] ?? 0,

                'first_name' => $address['first_name'] ?? 'Guest',
                'last_name' => $address['last_name'] ?? '',
                'email' => $address['email'] ?? (Auth::check() ? Auth::user()->email : 'guest@example.com'),
                'phone' => $address['phone'] ?? '',

                'address' => $address['address'] ?? '',
                'city' => $address['city'] ?? '',
                'state' => $address['state'] ?? '',
                'zip' => $address['zip'] ?? '',

                'payment_method' => $input['payment_method'] ?? 'cod',
                'payment_status' => ($input['payment_method'] == 'online') ? 'paid' : 'pending',

                'referral_code' => session('referral_code'),
            ]);

            foreach ($cartItems as $item) {
                \App\Models\OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'product_image' => $item['image'] ?? null,
                    'price' => $item['price'],
                    'quantity' => $item['qty'],
                    'total' => $item['price'] * $item['qty'],
                    'color' => $item['color'] ?? null,
                    'size' => $item['size'] ?? null,
                ]);

                $product = \App\Models\Product::find($item['id']);
                if ($product) {
                    $product->decrement('stock', $item['qty']);
                }
            }

            $referralCode = session('referral_code');
            $referralStaffId = session('referral_staff_id');

            if ($referralCode && $referralStaffId) {
                $order->ref_code = $referralCode;
                $order->staff_id = $referralStaffId;
                $order->save();
            }

            if ($referralCode && $referralStaffId) {
                \App\Models\ReferralTracking::create([
                    'staff_id' => $referralStaffId,
                    'referral_code' => $referralCode,
                    'referral_type' => 'purchase',
                    'amount' => $order->total_amount,
                    'used_at' => now(),
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Order placed successfully!', 'order_id' => $order->id]);
        });

    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Order Error: ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Order failed: ' . $e->getMessage()], 500);
    }




    }
}
