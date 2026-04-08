<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Address;

use App\Models\Staff;
use App\Models\ReferralTracking;
use Illuminate\Support\Facades\Log;
class CartController extends Controller
{



    public function applyReferral(Request $request)
    {
        $staff = Staff::where('referral_code', $request->code)->first();

        if ($staff) {
            return response()->json([
                'success' => true,
                'staff_id' => $staff->id
            ]);
        }

        return response()->json(['success' => false]);
    }

    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }

        $user = Auth::user();

        $cart = Cart::firstOrCreate([
            'user_id' => $user->id
        ]);

        $product = \App\Models\Product::with('variants')->findOrFail($request->id);

        $variant = $product->variants()
            ->where('color_id', $request->color_id)
            ->where('size', $request->size)
            ->first();

        if (!$variant) {
            return response()->json(['error' => 'Invalid variant'], 400);
        }

        $price = $variant->price ?? $product->price;
        $stock = $variant->stock;

        if ($request->qty > $stock) {
            return response()->json(['error' => 'Stock exceeded'], 400);
        }

        $item = $cart->items()->where([
            'product_id' => $product->id,
            'color_id' => $request->color_id,
            'size' => $request->size
        ])->first();

        if ($item) {
            $newQty = min($item->quantity + $request->qty, $stock);

            $item->update([
                'quantity' => $newQty,
                'price' => $price // always DB price
            ]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'color_id' => $request->color_id,
                'size' => $request->size,
                'quantity' => min($request->qty, $stock),
                'price' => $price
            ]);
        }

        return response()->json(['success' => true]);
    }
    public function syncCart(Request $request)
    {
        $user = Auth::user();

        $cart = Cart::firstOrCreate([
            'user_id' => $user->id
        ]);

        foreach ($request->cart as $item) {

            // ✅ Get variant stock
            $variant = \App\Models\ProductVariant::where([
                'product_id' => $item['id'],
                'color_id' => $item['color_id'],
                'size' => $item['size']
            ])->first();

            $availableStock = $variant ? $variant->stock : 0;

            // ✅ Clamp quantity (IMPORTANT)
            $finalQty = min($item['qty'], $availableStock);

            // If stock is 0, skip
            if ($finalQty <= 0)
                continue;

            $existing = $cart->items()->where([
                'product_id' => $item['id'],
                'color_id' => $item['color_id'],
                'size' => $item['size']
            ])->first();

            if ($existing) {
                $existing->update([
                    'quantity' => $finalQty
                ]);
            } else {
                $cart->items()->create([
                    'product_id' => $item['id'],
                    'quantity' => $finalQty,
                    'color_id' => $item['color_id'],
                    'size' => $item['size']
                ]);
            }
        }

        return response()->json(['success' => true]);
    }


    public function checkout(Request $request)
    {


        try {
            $input = $request->all();
            $cartItems = $input['cart'] ?? [];
            $address = $input['address'] ?? [];

            if (empty($cartItems)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ]);
            }

            return DB::transaction(function () use ($input, $cartItems, $address, $request) {
                $subtotal = 0;

                // ✅ Create order FIRST (empty totals)
                $staffId = $request->staff_id;

                // ✅ handle no referral
                if ($staffId === "NO_REFERRAL" || empty($staffId)) {
                    $staffId = null;
                }
                $addressId = $request->address_id;

                if ($addressId) {
                    $savedAddress = Address::where('id', $addressId)
                        ->where('user_id', Auth::id())
                        ->firstOrFail();
                } else {
                    // ✅ Create new address
                    $savedAddress = Address::create([
                        'user_id' => Auth::id(),
                        'first_name' => $address['first_name'],
                        'last_name' => $address['last_name'],
                        'phone' => $address['phone'],
                        'address' => $address['address'],
                        'city' => $address['city'],
                        'zip' => $address['zip'],
                    ]);
                }

                // ✅ Create order
                $order = \App\Models\Order::create([
                    'user_id' => Auth::id(),
                    'address_id' => $savedAddress->id, // ✅ ADD THIS LINE

                    'staff_id' => $staffId, // ✅ correct placement
                    'status' => 'pending',
                    'subtotal' => 0,
                    'total_amount' => 0,
                    'discount_amount' => $input['discount'] ?? 0,

                    'first_name' => $savedAddress->first_name,
                    'last_name' => $savedAddress->last_name,
                    'phone' => $savedAddress->phone,
                    'address' => $savedAddress->address,
                    'city' => $savedAddress->city,
                    'zip' => $savedAddress->zip,
                    'state' => $address['state'] ?? '',

                    'payment_method' => $input['payment_method'] ?? 'cod',
                    'payment_status' => ($input['payment_method'] == 'online') ? 'paid' : 'pending',
                ]);

                // ✅ Process each cart item
                foreach ($cartItems as $item) {

                    $variant = \App\Models\ProductVariant::where([
                        'product_id' => $item['id'],
                        'color_id' => $item['color_id'],
                        'size' => $item['size']
                    ])->first();

                    if (!$variant) {
                        throw new \Exception('Invalid product variant');
                    }

                    if ($variant->stock < $item['qty']) {
                        throw new \Exception('Stock not available');
                    }

                    $price = $variant->price;
                    $total = $price * $item['qty'];

                    // ✅ Add to subtotal
                    $subtotal += $total;

                    // ✅ Create order item
                    \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'product_name' => $item['name'],
                        'product_image' => $item['image'] ?? null,
                        'price' => $price,
                        'quantity' => $item['qty'],
                        'total' => $total,
                        'color' => $item['color'] ?? null,
                        'size' => $item['size'] ?? null,
                    ]);

                    // ✅ Reduce stock ONCE
                    $variant->decrement('stock', $item['qty']);
                }

                // ✅ Final totals
                $discount = $input['discount'] ?? 0;
                $finalTotal = $subtotal;
                $order->update([
                    'subtotal' => $subtotal,
                    'total_amount' => $finalTotal,
                ]);

                // ✅ Referral tracking
                if ($staffId) {
                    ReferralTracking::create([
                        'staff_id' => $staffId,
                        'referral_type' => 'purchase',
                        'amount' => $finalTotal,
                        'used_at' => now(),
                    ]);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully!',
                    'order_id' => $order->id
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Order Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Order failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
