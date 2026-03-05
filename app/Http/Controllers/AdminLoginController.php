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
    public function reset_new(Request $request){


    $request->validate(['email' => 'required|email']);

    $credentials = $request->only('email');
    $staff = \App\Models\Staff::where('email', $credentials['email'])->first();

    if (!$staff) {
        return back()->withErrors(['email' => 'No admin account found with this email address.']);
    }

    $token = \Illuminate\Support\Str::random(60);

    \Illuminate\Support\Facades\DB::table('staff_password_resets')->updateOrInsert(
        ['email' => $credentials['email']],
        [
            'token' => \Illuminate\Support\Facades\Hash::make($token),
            'created_at' => now(),
        ]
    );

    $resetUrl = url('/admin/password/reset/' . urlencode($token) . '?email=' . urlencode($credentials['email']));

    try {
        Mail::send([], [], function ($message) use ($credentials, $resetUrl) {
            $message->to($credentials['email'])
                ->subject('Admin Password Reset')
                ->html("
                        <h2>Admin Password Reset Request</h2>
                        <p>You are receiving this email because we received a password reset request for your admin account.</p>
                        <p>Click the button below to reset your password:</p>
                        <p><a href='{$resetUrl}' style='background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a></p>
                        <p>If you did not request a password reset, no further action is required.</p>
                        <p>This link will expire in 60 minutes.</p>
                    ");
        });

        return back()->with('status', 'We have emailed your password reset link!');
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Admin password reset email failed: ' . $e->getMessage());

        // Even if email fails, we can still show success message for security
        return back()->with('status', 'We have emailed your password reset link!');
    }

    }
   
}
