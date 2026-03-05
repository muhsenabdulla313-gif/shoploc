<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function destroy($id){


            $order = \App\Models\Order::findOrFail($id);
            $order->delete();
            return response()->json(['success' => true]);



    }
}
