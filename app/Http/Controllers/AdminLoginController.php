<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\User;
use App\Models\Order;
class AdminLoginController extends Controller
{
public function login(){


    if (auth('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }

    return view('admin.login');

}
    public function reset()
    {
        return view('auth.passwords.email');
    }

    public function updatepassword(Request $request)
    {


        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);

    }

    public function resettoken(Request $request, $token)
    {

        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);



    }
    public function updateresetpassword(Request $request)
    {

        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                // auto login after reset
                Auth::login($user);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect('/')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);


    }
    public function authenticate(){

    $credentials = request()->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (auth()->guard('admin')->attempt($credentials)) {
        request()->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }

    return back()->withErrors([
        'email' => 'Invalid admin credentials',
    ])->onlyInput('email');

    }

    public function dashboard(){

        $totalProducts = Product::count();
        $totalUsers = User::count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $recentOrders = Order::with('items')->latest()->limit(5)->get();

        $topSellingProducts = \App\Models\OrderItem::leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('order_items.product_name, products.badge, SUM(order_items.quantity) as total_quantity')
            ->groupBy('order_items.product_name', 'products.badge')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalUsers',
            'pendingOrders',
            'recentOrders',
            'topSellingProducts'
        ));


    }
}
