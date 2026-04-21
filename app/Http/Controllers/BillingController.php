<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
class BillingController extends Controller
{
    public function login()
    {
        if (auth()->guard('billing')->check()) {
            return redirect('/billing');
        }
        return view('billing.login');
    }
    public function authenticate()
    {



        $credentials = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (auth()->guard('billing')->attempt($credentials)) {
            request()->session()->regenerate();
            return redirect()->intended('/billing');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    public function logout()
    {
        auth()->guard('billing')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('billing.login');
    }
    public function index(){
            $totalOrders = Order::count();
            $completedOrders = Order::where('status', 'completed')->count();
            $pendingOrders = Order::where('status', 'pending')->count();
            $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
            $recentOrders = Order::with('items')->latest()->limit(5)->get();






            return view('billing.dashboard', compact('totalOrders', 'completedOrders', 'pendingOrders', 'totalRevenue', 'recentOrders'));


    }
    public function orders(){

            $orders = Order::with('items')->latest()->paginate(10);
            return view('billing.orders', compact('orders'));


    }
    public function orderdetails($id){





            $order = Order::with('items.product')->findOrFail($id);
            return response()->json($order);
    }
}
