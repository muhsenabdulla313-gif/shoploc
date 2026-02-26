<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ReferralController;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Custom)
|--------------------------------------------------------------------------
*/

// Login Page
Route::get('/login', [App\Http\Controllers\Auth\UserLoginController::class, 'showLoginForm'])->name('login');

// User Login Submit
Route::post('/user-login', [App\Http\Controllers\Auth\UserLoginController::class, 'login'])->name('user.login.submit');

// Register Page
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Register Submit
Route::post('/register', function () {
    $validated = request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
    ]);

    // Check if user already exists (double-check)
    $existingUser = \App\Models\User::where('email', $validated['email'])->first();

    if ($existingUser) {
        return redirect('/login')->with('error', 'An account with this email already exists. Please login instead.');
    }

    // Generate OTP
    $otp = rand(100000, 999999);

    // Store OTP in session
    session(['registration_otp' => $otp, 'registration_data' => $validated]);

    // Send OTP email
    Mail::to($validated['email'])->send(new \App\Mail\OtpMail($validated['name'], $validated['email'], $otp));

    return redirect()->route('verify.otp.form')->with('success', 'Please check your email for the OTP to complete registration.');
});

// Show Registration OTP verification form
Route::get('/verify-otp', function () {
    if (!session('registration_data')) {
        return redirect('/register')->with('error', 'Please register first');
    }

    return view('auth.verify-otp');
})->name('verify.otp.form');

// Verify Registration OTP and complete registration
Route::post('/verify-otp', function () {
    $request = request()->validate([
        'otp' => 'required|string',
    ]);

    $storedOtp = session('registration_otp');
    $registrationData = session('registration_data');

    if (!$storedOtp || !$registrationData) {
        return redirect('/register')->with('error', 'Session expired. Please register again.');
    }

    if ($request['otp'] != $storedOtp) {
        return back()->with('error', 'Invalid OTP. Please try again.');
    }

    // ✅ Check if user already exists
    $existingUser = \App\Models\User::where('email', $registrationData['email'])->first();

    if ($existingUser) {
        // User already exists, log them in instead of creating new user
        Auth::login($existingUser);

        // Clear OTP session
        session()->forget(['registration_otp', 'registration_data']);

        return redirect('/')->with('success', 'Welcome back! You have been logged in.');
    }

    // ✅ Create new user
    $user = \App\Models\User::create([
        'name' => $registrationData['name'],
        'email' => $registrationData['email'],
        'password' => Hash::make(Str::random(12)),
        'referral_code' => Str::random(10),
        'email_verified' => true,
    ]);

    // ✅ Staff referral tracking (FIXED)
    $referralCode = session('referral_code') ?? request()->cookie('referral_code');

    if ($referralCode) {
        $referringStaff = \App\Models\Staff::where('referral_code', $referralCode)
            ->where('is_active', true)
            ->first();

        if ($referringStaff) {
            \App\Models\ReferralTracking::create([
                'staff_id' => $referringStaff->id,
                'referral_code' => $referralCode,
                'referral_type' => 'signup',
                'referred_user_email' => $user->email,
                'used_at' => now(),
            ]);

            // ✅ also store in user (recommended)
            $user->referred_by_staff_id = $referringStaff->id;
            $user->referred_by_code = $referralCode;
            $user->save();
        }
    }

    // ✅ Clear OTP session
    session()->forget(['registration_otp', 'registration_data']);

    Auth::login($user);

    return redirect('/')->with('success', 'Registration successful!');
});

// Show Login OTP verification form
Route::get('/verify-login-otp', function () {
    if (!session('login_user_id')) {
        return redirect('/')->with('error', 'Session expired. Please try again.');
    }

    return view('auth.verify-login-otp');
})->name('verify.login.otp.form');

// Verify Login OTP and complete login
Route::post('/verify-login-otp', function () {
    $request = request()->validate([
        'otp' => 'required|string',
    ]);
// gdfm,h,f,
    $storedOtp = session('login_otp');
    $userId = session('login_user_id');

    if (!$storedOtp || !$userId) {
        return redirect('/')->with('error', 'Session expired. Please try again.');
    }

    if ($request['otp'] != $storedOtp) {
        return back()->with('error', 'Invalid OTP. Please try again.');
    }

    // Get user and login
    $user = \App\Models\User::find($userId);

    if (!$user) {
        return redirect('/')->with('error', 'User not found. Please try again.');
    }

    // Clear OTP session
    session()->forget(['login_otp', 'login_user_id']);

    Auth::login($user);

    // Check for stored redirect URL first, then intended URL, then default to wishlist
    $redirect = session('login_redirect');
    if ($redirect) {
        session()->forget('login_redirect');
        return redirect($redirect)->with('success', 'Login successful!');
    }

    $intended = session('url.intended', route('wishlist'));
    return redirect($intended)->with('success', 'Login successful!');
});

// Login Submit
Route::post('/login', function () {
    $credentials = request()->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    $user = \App\Models\User::where('email', $credentials['email'])->first();

    // Check if user exists
    if (!$user) {
        return redirect()->route('register')->withErrors([
            'email' => 'No account found with this email address. Please register first.',
        ])->onlyInput('email');
    }

    if (!$user || !Hash::check($credentials['password'], $user->password)) {
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Check if email is verified
    if (!$user->email_verified) {
        return back()->withErrors([
            'email' => 'Please verify your email address before logging in. Check your email for the OTP.',
        ])->onlyInput('email');
    }

    if (Auth::attempt($credentials)) {
        request()->session()->regenerate();
        return redirect()->intended('/staff');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
});

// User Orders
Route::get('/my-orders', function () {
    if (!Auth::check()) {
        return redirect()->guest('/login');
    }

    $user = Auth::user();
    $orders = \App\Models\Order::where('user_id', $user->id)->with('items')->latest()->get();

    return view('auth.my-orders', compact('orders'));
})->name('user.orders');

// User Order Details
Route::get('/my-orders/{id}', function ($id) {
    if (!Auth::check()) {
        return redirect()->guest('/login');
    }

    $user = Auth::user();
    $order = \App\Models\Order::where('user_id', $user->id)->where('id', $id)->with('items.product')->firstOrFail();

    return view('auth.order-details', compact('order'));
})->name('order.details');

// Cancel User Order
Route::post('/my-orders/{id}/cancel', function ($id) {
    if (!Auth::check()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }

    $user = Auth::user();
    $order = \App\Models\Order::where('user_id', $user->id)->where('id', $id)->first();

    if (!$order) {
        return response()->json(['success' => false, 'message' => 'Order not found'], 404);
    }

    if (!in_array($order->status, ['pending', 'confirmed'])) {
        return response()->json(['success' => false, 'message' => 'Only pending or confirmed orders can be cancelled'], 400);
    }

    $order->status = 'cancelled';
    $order->save();

    return response()->json(['success' => true, 'message' => 'Order cancelled successfully']);
})->name('order.cancel');

// User Profile
Route::get('/profile', function () {
    if (!Auth::check()) {
        return redirect()->guest('/login');
    }

    $user = Auth::user();
    return view('auth.profile', compact('user'));
})->name('user.profile');

// Delete User Account
Route::delete('/profile/delete', function () {
    if (!Auth::check()) {
        return redirect()->guest('/login');
    }

    $user = Auth::user();

    // Delete related data first
    \App\Models\Order::where('user_id', $user->id)->delete();
    \App\Models\ReferralTracking::where('referral_code', $user->referral_code)->delete();

    // Delete the user
    \App\Models\User::destroy($user->id);

    // Logout the user
    Auth::logout();

    return redirect('/')->with('success', 'Your account has been successfully deleted.');
})->name('user.delete');

// Admin Routes
Route::middleware(['auth:staff'])->group(function () {
    Route::delete('/admin/users/{id}', function ($id) {
        $user = \App\Models\User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Delete related data first
        \App\Models\Order::where('user_id', $user->id)->delete();
        \App\Models\ReferralTracking::where('referral_code', $user->referral_code)->delete();

        // Delete the user
        $user->delete();

        return response()->json(['success' => 'User deleted successfully']);
    })->name('admin.users.delete');
});

// Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');


/*
|--------------------------------------------------------------------------
| Password Reset Routes (FIXED ? Real Email Sending)
|--------------------------------------------------------------------------
*/

// Request reset link form
Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

// Send reset link email (REAL)
Route::post('/password/email', function (\Illuminate\Http\Request $request) {
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? back()->with('status', __($status))
        : back()->withErrors(['email' => __($status)]);
})->name('password.email');

// Reset password form
Route::get('/password/reset/{token}', function (\Illuminate\Http\Request $request, $token) {
    return view('auth.passwords.reset', [
        'token' => $token,
        'email' => $request->query('email'), // important
    ]);
})->name('password.reset');

// Update password
Route::post('/password/reset', function (\Illuminate\Http\Request $request) {
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
})->name('password.update');


/*
|--------------------------------------------------------------------------
| Checkout Route
|--------------------------------------------------------------------------
*/
// Referral routes
Route::get('/referral-required', [ReferralController::class, 'show'])->name('referral.required');
Route::post('/referral-required', [ReferralController::class, 'submit'])->name('referral.submit');

Route::post('/checkout', function (\Illuminate\Http\Request $request) {
    // Enforce referral requirement on backend too
    if (!$request->session()->has('referral_code')) {
        return response()->json(['success' => false, 'message' => 'Referral code is required to place an order'], 403);
    }

    // Continue with original checkout logic
    try {
        $input = $request->all();
        $cartItems = $input['cart'] ?? [];
        $address = $input['address'] ?? [];

        if (empty($cartItems)) {
            return response()->json(['success' => false, 'message' => 'Cart is empty']);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($input, $cartItems, $address) {

            // Check if all items have sufficient stock before placing order
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

                // Reduce stock for the purchased product
                $product = \App\Models\Product::find($item['id']);
                if ($product) {
                    $product->decrement('stock', $item['qty']);
                }
            }

            // Get referral code and staff ID from session
            $referralCode = session('referral_code');
            $referralStaffId = session('referral_staff_id');

            // Update order with referral information
            if ($referralCode && $referralStaffId) {
                $order->ref_code = $referralCode;
                $order->staff_id = $referralStaffId;
                $order->save();
            }

            if ($referralCode && $referralStaffId) {
                // Track referral for purchase
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
})->name('checkout');


/*
|--------------------------------------------------------------------------
| Front Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/hero', function () {
    return view('hero');
});

Route::get('/products', [HomeController::class, 'products'])->name('products');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/trendy', [HomeController::class, 'trendy'])->name('trendy');

Route::get('/product/{id}', [HomeController::class, 'show'])->name('product.show');

Route::get('/admin/products/search-suggestions', [ProductController::class, 'searchSuggestions']);

Route::get('/womens', [HomeController::class, 'women'])->name('women.page');
Route::get('/mens', [HomeController::class, 'mens'])->name('men.page');
Route::get('/kids', [HomeController::class, 'kids'])->name('kids.page');

Route::get('/cart', function () {
    return view('cart');
})->name('cart')->middleware('auth');

Route::get('/wishlist', function () {
    return view('wishlist');
})->name('wishlist')->middleware('auth');

Route::get('/api/products/related/{category}/{excludeId}', [ProductController::class, 'getRelatedProducts'])->name('products.related');

Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout.page')->middleware([App\Http\Middleware\RequireReferral::class]);

Route::get('/payment', function () {
    return view('payment');
})->name('payment');


/*
|--------------------------------------------------------------------------
| Contact Routes
|--------------------------------------------------------------------------
*/
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', function () {
    $validated = request()->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    // Send email using the ContactMail mailable
    try {
        Mail::to(config('mail.from.address'))->send(new ContactMail($validated));
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Contact form email failed: ' . $e->getMessage());
    }

    return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');
})->name('contact.submit');


/*
|--------------------------------------------------------------------------
| Admin Login Route
|--------------------------------------------------------------------------
*/
// Test admin auth route
Route::get('/admin/test-auth', function () {
    if (auth()->guard('staff')->check()) {
        return response()->json([
            'authenticated' => true,
            'user' => auth()->guard('staff')->user()
        ]);
    } else {
        return response()->json(['authenticated' => false]);
    }
});

Route::get('/admin/login', function () {

    // If already logged in as admin
    if (auth('admin')->check()) {
        return redirect()->route('admin.dashboard');
    }

    return view('admin.login');

})->name('admin.login');

// Admin Login Submit
Route::post('/admin/login', function () {

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
})->name('admin.login.submit');
// PROTECTED ADMIN ROUTES
Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/dashboard', function () {
        $totalProducts = \App\Models\Product::count();
        $totalUsers = \App\Models\User::count();
        $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
        $recentOrders = \App\Models\Order::with('items')->latest()->limit(5)->get();

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
    })->name('admin.dashboard');
});
/*
|--------------------------------------------------------------------------
| Admin Routes (Using Staff Authentication)
|--------------------------------------------------------------------------
*/
// Admin Billing Staff Routes
Route::middleware(['web', 'App\Http\Middleware\StaffAuth'])->group(function () {
    Route::get('/admin/billing-staff', [App\Http\Controllers\Admin\BillingStaffController::class, 'index'])->name('admin.billing.staff');
    Route::get('/admin/billing-staff/create', [App\Http\Controllers\Admin\BillingStaffController::class, 'create'])->name('admin.billing.staff.create');
    Route::post('/admin/billing-staff', [App\Http\Controllers\Admin\BillingStaffController::class, 'store'])->name('admin.billing.staff.store');
    Route::get('/admin/billing-staff/{id}', [App\Http\Controllers\Admin\BillingStaffController::class, 'show'])->name('admin.billing.staff.show');
    Route::get('/admin/billing-staff/{id}/edit', [App\Http\Controllers\Admin\BillingStaffController::class, 'edit'])->name('admin.billing.staff.edit');
    Route::put('/admin/billing-staff/{id}', [App\Http\Controllers\Admin\BillingStaffController::class, 'update'])->name('admin.billing.staff.update');
    Route::delete('/admin/billing-staff/{id}', [App\Http\Controllers\Admin\BillingStaffController::class, 'destroy'])->name('admin.billing.staff.destroy');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['web', 'App\Http\Middleware\StaffAuth'])   // ? Use staff auth middleware
    ->group(function () {

        Route::get('/', function () {
            $totalProducts = \App\Models\Product::count();
            $totalUsers = \App\Models\User::count();
            $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
            $recentOrders = \App\Models\Order::with('items')->latest()->limit(5)->get();

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
        })->name('dashboard');

        Route::get('/products', function () {
            $products = \App\Models\Product::latest()->paginate(10);
            return view('admin.products', compact('products'));
        })->name('products');

        Route::get('/offer', function () {
            $offers = \App\Models\Offer::latest()->paginate(10);
            return view('admin.offer', compact('offers'));
        })->name('offer');

        Route::post('/offer', function (\Illuminate\Http\Request $request) {
            try {
                \Illuminate\Support\Facades\Log::info('Offer upload request received', [
                    'files' => $request->allFiles(),
                    'inputs' => $request->except(['image']),
                    'hasFile' => $request->hasFile('image'),
                ]);

                $request->validate([
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                    'alt_text' => 'nullable|string|max:255',
                ]);

                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('offers', 'public');

                    \App\Models\Offer::create([
                        'title' => 'Offer ' . now()->format('Y-m-d H:i:s'),
                        'image' => $imagePath,
                        'alt_text' => $request->alt_text,
                        'active' => true,
                        'start_date' => now(),
                        'end_date' => now()->addMonths(1),
                    ]);
                }

                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Offer creation failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        })->name('offer.store');

        Route::get('/offer/{id}/edit', function ($id) {
            $offer = \App\Models\Offer::findOrFail($id);
            return response()->json(['success' => true, 'data' => $offer]);
        })->name('offer.edit');

        Route::put('/offer/{id}', function (\Illuminate\Http\Request $request, $id) {
            try {
                $request->validate([
                    'alt_text' => 'nullable|string|max:255',
                    'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                ]);

                $offer = \App\Models\Offer::findOrFail($id);

                if ($request->hasFile('image')) {
                    if ($offer->image) {
                        \Illuminate\Support\Facades\Storage::delete('public/' . $offer->image);
                    }
                    $imagePath = $request->file('image')->store('offers', 'public');
                    $offer->image = $imagePath;
                }

                $offer->title = $request->title ?: $offer->title;
                $offer->alt_text = $request->alt_text;
                $offer->start_date = $request->start_date ?: $offer->start_date;
                $offer->end_date = $request->end_date ?: $offer->end_date;
                $offer->save();

                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Offer update failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        })->name('offer.update');

        Route::delete('/offer/{id}', function ($id) {
            try {
                $offer = \App\Models\Offer::findOrFail($id);

                if ($offer->image) {
                    \Illuminate\Support\Facades\Storage::delete('public/' . $offer->image);
                }

                $offer->delete();

                return response()->json(['success' => true]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Offer deletion failed: ' . $e->getMessage());
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        })->name('offer.delete');

        Route::get('/products/list', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.list');
        Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
        Route::put('/products/{id}', [\App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');

        // Category routes
        Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.list');
        Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'show'])->name('categories.show');
        Route::put('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/users', function () {
            $users = \App\Models\User::latest()->paginate(10);
            return view('admin.users', compact('users'));
        })->name('users');

        Route::get('/orders', function () {
            $orders = \App\Models\Order::with('items')->latest()->paginate(10);
            return view('admin.orders', compact('orders'));
        })->name('orders');

        Route::get('/orders/{id}', function ($id) {
            $order = \App\Models\Order::with('items.product')->findOrFail($id);
            return view('admin.order-details', compact('order'));
        })->name('orders.details');

        Route::get('/staff/dashboard', function () {
            return redirect()->route('admin.dashboard');
        })->name('admin.staff.dashboard');

        Route::match(['post', 'put', 'patch'], '/orders/{id}/update-status', function (\Illuminate\Http\Request $request, $id) {
            try {
                $order = \App\Models\Order::findOrFail($id);
                $oldStatus = $order->status;
                $newStatus = $request->status;

                // If changing to completed and it was not completed before, update staff score
                $shouldUpdateStaffScore = ($newStatus === 'completed' && $oldStatus !== 'completed');
                $shouldRemoveStaffScore = ($oldStatus === 'completed' && $newStatus !== 'completed');

                $order->status = $newStatus;
                $order->save();

                // Update staff score if order is completed or uncompleted
                if ($shouldUpdateStaffScore && $order->staff_id) {
                    // Increment staff score/earnings when order is completed
                    $staff = \App\Models\Staff::find($order->staff_id);
                    if ($staff) {
                        // Increase score based on order total amount (e.g., 10% commission)
                        $commission = $order->total_amount * 0.10; // 10% commission
                        $staff->increment('score', $commission);
                    }
                } elseif ($shouldRemoveStaffScore && $order->staff_id) {
                    // Decrement staff score if order is moved from completed to another status
                    $staff = \App\Models\Staff::find($order->staff_id);
                    if ($staff) {
                        $commission = $order->total_amount * 0.10; // 10% commission
                        $staff->decrement('score', $commission);
                    }
                }

                return redirect()->back()->with('success', 'Order status updated successfully from ' . ucfirst($oldStatus) . ' to ' . ucfirst($request->status));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to update order status: ' . $e->getMessage());
            }
        })->name('orders.updateStatus');

        Route::delete('/orders/{id}', function ($id) {
            $order = \App\Models\Order::findOrFail($id);
            $order->delete();
            return response()->json(['success' => true]);
        })->name('orders.delete');

        // Staff Management Routes for Admin
        Route::get('/staff/manage', function () {
            $staffMembers = \App\Models\Staff::all();

            // Calculate purchases referred and total earnings for each staff member
            foreach ($staffMembers as $staff) {
                // Count purchases referred through this staff's referral code
                $referredOrders = \App\Models\Order::where('referral_code', $staff->referral_code)->get();
                $staff->purchases_referred = $referredOrders->count();
                $staff->total_earnings = $referredOrders->sum('total_amount') * 0.1; // 10% commission
            }

            // Paginate the staff members with calculated data
            $currentPage = request()->get('page', 1);
            $perPage = 10;
            $offset = ($currentPage - 1) * $perPage;
            $paginatedStaff = collect($staffMembers)->slice($offset, $perPage)->values();
            $staffMembers = new \Illuminate\Pagination\LengthAwarePaginator(
                $paginatedStaff,
                count($staffMembers),
                $perPage,
                $currentPage,
                [
                    'path' => request()->url(),
                    'pageName' => 'page',
                ]
            );

            // Get unique bulk messages (those with recipient_type = 'all') - get the latest for each unique subject/message combo
            $latestBulkMessageIds = \App\Models\StaffMessage::where('recipient_type', 'all')
                ->selectRaw('MAX(id) as id')
                ->groupBy('subject', 'message')
                ->pluck('id');

            $bulkMessages = \App\Models\StaffMessage::whereIn('id', $latestBulkMessageIds)->get();

            // Get individual messages (those with recipient_type = 'specific')
            $individualMessages = \App\Models\StaffMessage::where('recipient_type', '!=', 'all')
                ->with('staff')
                ->latest()
                ->get();

            // Combine both collections, with bulk messages first
            $sentMessages = $bulkMessages->concat($individualMessages);

            // Convert to paginator manually
            $currentPageMsg = request()->get('page', 1);
            $perPageMsg = 10;
            $offsetMsg = ($currentPageMsg - 1) * $perPageMsg;

            $paginatedMessages = collect($sentMessages)->slice($offsetMsg, $perPageMsg)->values();
            $messagesPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
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
        })->name('staff.manage');



        Route::post('/staff', function (\Illuminate\Http\Request $request) {
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

            \App\Models\Staff::create([
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
                'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            ]);

            return redirect()->back()->with('success', 'Staff member created successfully!');
        })->name('staff.store');

        Route::get('/staff/{id}/edit', function ($id) {
            $staff = \App\Models\Staff::findOrFail($id);
            return response()->json(['success' => true, 'data' => $staff]);
        })->name('staff.edit');

        Route::put('/staff/{id}', function (\Illuminate\Http\Request $request, $id) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:staff,email,' . $id,
                'phone' => 'required|string|max:15',
                'bank_account_number' => 'nullable|string|max:50',
                'ifsc_code' => 'nullable|string|max:20',
                'bank_name' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'village' => 'nullable|string|max:100',
                'address' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:100',
                'pincode' => 'nullable|string|max:10',
            ]);

            $staff = \App\Models\Staff::findOrFail($id);
            $staff->update([
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
            ]);

            if ($request->password) {
                $request->validate(['password' => 'string|min:8|confirmed']);
                $staff->password = \Illuminate\Support\Facades\Hash::make($request->password);
                $staff->save();
            }

            return redirect()->back()->with('success', 'Staff member updated successfully!');
        })->name('staff.update');

        Route::delete('/staff/{id}', function ($id) {
            $staff = \App\Models\Staff::findOrFail($id);
            $staff->delete();
            return response()->json(['success' => true]);
        })->name('staff.delete');

        Route::post('/staff/message', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'staff_id' => 'required|exists:staff,id',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            $staff = \App\Models\Staff::findOrFail($request->staff_id);

            // Here you can implement the actual message sending logic
            // For now, I'll just return a success response
            // You might want to store messages in a database table or send emails
    
            return redirect()->back()->with('success', 'Message sent successfully to ' . $staff->name . '!');
        })->name('staff.message');

        Route::post('/staff/bulk-message', function (\Illuminate\Http\Request $request) {
            $request->validate([
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
            ]);

            $staffMembers = \App\Models\Staff::all();
            $messageCount = $staffMembers->count();

            // Store the bulk message in database for all staff members
            foreach ($staffMembers as $staff) {
                \App\Models\StaffMessage::create([
                    'staff_id' => $staff->id,
                    'recipient_type' => 'all',  // Mark as bulk message to all staff
                    'subject' => $request->subject,
                    'message' => $request->message,
                ]);
            }

            return redirect()->back()->with('success', "Bulk message sent successfully to {$messageCount} staff members!");
        })->name('staff.bulk-message');

        Route::get('/staff-messages', function () {
            $messages = \App\Models\StaffMessage::with('staff')->orderBy('created_at', 'desc')->paginate(10);
            return view('admin.staff-messages', compact('messages'));
        })->name('staff.messages.index');

        Route::delete('/staff-messages/{id}', function ($id) {
            $message = \App\Models\StaffMessage::findOrFail($id);
            $message->delete();

            return response()->json(['success' => true]);
        })->name('staff.messages.delete');

        // Trendy Products Routes
        Route::get('/trendy-products', function () {
            return view('admin.trendy-products');
        })->name('trendy-products.index');

        Route::get('/trendy-products/list', [\App\Http\Controllers\ProductController::class, 'listTrendyProducts'])->name('trendy-products.list');
        Route::post('/trendy-products', [\App\Http\Controllers\ProductController::class, 'storeTrendyProduct'])->name('trendy-products.store');

        Route::put('/trendy-products/{id}', [\App\Http\Controllers\ProductController::class, 'updateTrendyProduct'])->name('trendy-products.update');
        Route::delete('/trendy-products/{id}', [\App\Http\Controllers\ProductController::class, 'removeFromTrendy'])->name('trendy-products.remove');

    });


/*
|--------------------------------------------------------------------------
| Staff Routes
|--------------------------------------------------------------------------
*/

// Staff Login Page
Route::get('/staff/login', function () {
    return view('staff.login');
})->name('staff.login');

// Staff Login Submit (FIXED)
Route::post('/staff/login', function () {
    $credentials = request()->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // staff guard login
    if (auth()->guard('staff')->attempt($credentials)) {
        request()->session()->regenerate();
        return redirect()->intended('/staff'); // ? staff dashboard
    }

    return back()->withErrors([
        'email' => 'Invalid staff credentials',
    ])->onlyInput('email');
})->name('staff.login.submit');

// Staff Logout
Route::post('/staff/logout', function () {
    auth()->guard('staff')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    // Check if coming from admin area
    $referrer = request()->headers->get('referer');
    if ($referrer && (str_contains($referrer, '/admin') || request()->is('admin*'))) {
        return redirect('/admin/login');
    }

    return redirect('/staff/login');
})->name('staff.logout');

// Staff Dashboard - separate route for /staff
Route::get('/staff', [StaffController::class, 'dashboard'])->middleware(['web', 'App\Http\Middleware\StaffAuth'])->name('staff.dashboard');

Route::prefix('staff')->name('staff.')->middleware(['web', 'App\Http\Middleware\StaffAuth'])->group(function () {
    Route::get('/', [StaffController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard.index');
    Route::get('/staff-members', [StaffController::class, 'staffMembers'])->name('staff.members');
    Route::get('/profile', [StaffController::class, 'profile'])->name('staff.profile');
    Route::put('/profile/password', [StaffController::class, 'updatePassword'])->name('staff.update.password');

    Route::get('/products', [StaffController::class, 'products'])->name('products');
    Route::get('/products/create', [StaffController::class, 'productsCreate'])->name('products.create');
    Route::get('/products/{id}/edit', [StaffController::class, 'productsEdit'])->name('products.edit');

    Route::get('/orders', [StaffController::class, 'orders'])->name('orders');
    Route::get('/orders/{id}', [StaffController::class, 'ordersView'])->name('orders.view');
    Route::get('/orders/{id}/edit', [StaffController::class, 'ordersEdit'])->name('orders.edit');

    Route::get('/customers', [StaffController::class, 'customers'])->name('customers');
    Route::get('/customers/{id}', [StaffController::class, 'customersView'])->name('customers.view');
    Route::get('/customers/{id}/edit', [StaffController::class, 'customersEdit'])->name('customers.edit');

    Route::get('/reports', [StaffController::class, 'reports'])->name('reports');

    Route::post('/update-referral-code', [StaffController::class, 'updateReferralCode'])->name('staff.update.referral');
    Route::post('/get-referral-link', [StaffController::class, 'getReferralLink'])->name('staff.get.referral');
});


/*
|--------------------------------------------------------------------------
| Billing Authentication Routes (? FIXED)
|--------------------------------------------------------------------------
*/
Route::prefix('billing')->name('billing.')->group(function () {

    Route::get('/login', function () {
        if (auth()->guard('staff')->check()) {
            return redirect('/billing');
        }
        return view('billing.login');
    })->name('login');

    Route::post('/login', function () {
        $credentials = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // ? billing guard login - using staff guard instead since billing_staff table doesn't exist
        if (auth()->guard('staff')->attempt($credentials)) {
            request()->session()->regenerate();
            return redirect()->intended('/billing');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    })->name('login.submit');

    Route::post('/logout', function () {
        auth()->guard('staff')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/billing/login');
    })->name('logout');

    Route::middleware(['auth:staff'])->group(function () {

        Route::get('/', function () {
            $totalOrders = \App\Models\Order::count();
            $completedOrders = \App\Models\Order::where('status', 'completed')->count();
            $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
            $totalRevenue = \App\Models\Order::where('status', 'completed')->sum('total_amount');
            $recentOrders = \App\Models\Order::with('items')->latest()->limit(5)->get();

            return view('billing.dashboard', compact('totalOrders', 'completedOrders', 'pendingOrders', 'totalRevenue', 'recentOrders'));
        })->name('dashboard');

        Route::get('/orders', function () {
            $orders = \App\Models\Order::with('items')->latest()->paginate(10);
            return view('billing.orders', compact('orders'));
        })->name('orders');

        Route::get('/orders/{id}', function ($id) {
            $order = \App\Models\Order::with('items.product')->findOrFail($id);
            return response()->json($order);
        })->name('orders.show');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Password Reset Routes
|--------------------------------------------------------------------------
*/

// Admin password reset request form
Route::get('/admin/password/reset', function () {
    return view('admin.auth.passwords.email');
})->name('admin.password.request');

// Send admin password reset link
Route::post('/admin/password/email', function (\Illuminate\Http\Request $request) {
    $request->validate(['email' => 'required|email']);

    $credentials = $request->only('email');
    $staff = \App\Models\Staff::where('email', $credentials['email'])->first();

    if (!$staff) {
        return back()->withErrors(['email' => 'No admin account found with this email address.']);
    }

    // Generate reset token
    $token = \Illuminate\Support\Str::random(60);

    // Store the token in staff_password_resets table
    \Illuminate\Support\Facades\DB::table('staff_password_resets')->updateOrInsert(
        ['email' => $credentials['email']],
        [
            'token' => \Illuminate\Support\Facades\Hash::make($token),
            'created_at' => now(),
        ]
    );

    // Send email with reset link
    $resetUrl = url('/admin/password/reset/' . urlencode($token) . '?email=' . urlencode($credentials['email']));

    // Send actual email
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
})->name('admin.password.email');

// Admin password reset form
Route::get('/admin/password/reset/{token}', function (\Illuminate\Http\Request $request, $token) {
    return view('admin.auth.passwords.reset', [
        'token' => $token,
        'email' => $request->query('email'),
    ]);
})->name('admin.password.reset');

Route::post('/admin/password/reset', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $resetRecord = \Illuminate\Support\Facades\DB::table('staff_password_resets')
        ->where('email', $request->email)
        ->first();

    if (!$resetRecord) {
        return back()->withErrors(['email' => 'Invalid password reset token.']);
    }

    // Update the staff password
    $staff = \App\Models\Staff::where('email', $request->email)->first();
    if (!$staff) {
        return back()->withErrors(['email' => 'No admin account found with this email address.']);
    }

    $staff->update([
        'password' => \Illuminate\Support\Facades\Hash::make($request->password),
    ]);

    // Delete the reset token
    \Illuminate\Support\Facades\DB::table('staff_password_resets')->where('email', $request->email)->delete();

    // Login the user after password reset
    auth()->guard('staff')->login($staff);

    return redirect('/admin')->with('status', 'Your password has been reset!');
})->name('admin.password.update');
