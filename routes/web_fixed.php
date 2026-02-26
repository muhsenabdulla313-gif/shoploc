<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StaffController;
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
    
    // Create user
    $referralCode = session('referral_code');
    
    $user = \App\Models\User::create([
        'name' => $registrationData['name'],
        'email' => $registrationData['email'],
        'password' => Hash::make(Str::random(12)), // Generate a random password
        'referral_code' => Str::random(10),
        'email_verified' => true,
    ]);

    // If user signed up with a referral, track it
    if ($referralCode) {
        $referringStaff = \App\Models\User::where('referral_code', $referralCode)->first();

        if ($referringStaff) {
            \App\Models\ReferralTracking::create([
                'staff_id' => $referringStaff->id,
                'referral_code' => $referralCode,
                'referral_type' => 'signup',
                'referred_user_email' => $user->email,
                'used_at' => now(),
            ]);
        }
    }

    // Clear OTP session
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

// Logout
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');


/*
|--------------------------------------------------------------------------
| Password Reset Routes (FIXED âœ… Real Email Sending)
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
Route::post('/checkout', function (Illuminate\Http\Request $request) {
    try {
        $input = $request->all();
        $cartItems = $input['cart'] ?? [];
        $address = $input['address'] ?? [];

        if (empty($cartItems)) {
            return response()->json(['success' => false, 'message' => 'Cart is empty']);
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($input, $cartItems, $address) {

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
            }

            $referralCode = session('referral_code');
            if ($referralCode) {
                // Check if referral code belongs to a staff member
                $referringStaff = \App\Models\Staff::where('referral_code', $referralCode)->first();
                
                if ($referringStaff) {
                    \App\Models\ReferralTracking::create([
                        'staff_id' => $referringStaff->id,
                        'referral_code' => $referralCode,
                        'referral_type' => 'purchase',
                        'amount' => $order->total_amount,
                        'used_at' => now(),
                    ]);
                }
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
})->name('checkout');

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
        'name'    => 'required|string|max:255',
        'email'   => 'required|email|max:255',
        'subject' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    // Send email using the ContactMail mailable
    try {
        Mail::to(config('mail.from.address'))->send(new ContactMail($validated));
    } catch (\Exception $e) {
        // Log the error but don't expose it to the user
        \Illuminate\Support\Facades\Log::error('Contact form email failed: ' . $e->getMessage());
    }

    return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');
})->name('contact.submit');


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

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
        
        return view('admin.dashboard', compact('totalProducts', 'totalUsers', 'pendingOrders', 'recentOrders', 'topSellingProducts'));
    })->name('dashboard');

    Route::get('/products', function () {
        return view('admin.products');
    })->name('products');
    
    Route::get('/offer', function () {
        return view('admin.offer');
    })->name('offer');
    
    Route::post('/offer', function (\Illuminate\Http\Request $request) {
        try {
            // Debug: Log request data
            \Illuminate\Support\Facades\Log::info('Offer upload request received', [
                'files' => $request->allFiles(),
                'inputs' => $request->except(['image']),
                'hasFile' => $request->hasFile('image'),
                'file' => $request->file('image') ? [
                    'name' => $request->file('image')->getClientOriginalName(),
                    'size' => $request->file('image')->getSize(),
                    'extension' => $request->file('image')->getClientOriginalExtension(),
                    'mimeType' => $request->file('image')->getMimeType(),
                ] : null
            ]);
            
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'alt_text' => 'nullable|string|max:255',
            ]);
            
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('offers', 'public');
                \App\Models\Offer::create([
                    'title' => 'Offer ' . now()->format('Y-m-d H:i:s'), // Default title
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
            // Debug: Log request data
            \Illuminate\Support\Facades\Log::info('Offer update request received', [
                'files' => $request->allFiles(),
                'inputs' => $request->except(['image']),
                'hasFile' => $request->hasFile('image'),
                'file' => $request->file('image') ? [
                    'name' => $request->file('image')->getClientOriginalName(),
                    'size' => $request->file('image')->getSize(),
                    'extension' => $request->file('image')->getClientOriginalExtension(),
                    'mimeType' => $request->file('image')->getMimeType(),
                ] : null
            ]);
            
            $request->validate([
                'alt_text' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);
            
            $offer = \App\Models\Offer::findOrFail($id);
            
            if ($request->hasFile('image')) {
                // Delete old image if needed
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
            
            // Delete the image file
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

    Route::get('/products/list', [ProductController::class, 'index'])->name('products.list');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/trendy-products/list', [ProductController::class, 'listTrendyProducts'])->name('trendy-products.list');
    Route::post('/trendy-products', [ProductController::class, 'store'])->name('trendy-products.store');

    Route::get('/test-db', function () {
        try {
            $count = \App\Models\Product::count();
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to database',
                'error' => $e->getMessage()
            ], 500);
        }
    })->name('test.db');

    Route::get('/users', function () {
        return view('admin.users');
    })->name('users');

    Route::get('/orders', function () {
        $orders = \App\Models\Order::with('items')->latest()->paginate(10);
        return view('admin.orders', compact('orders'));
    })->name('orders');

    Route::get('/orders/{id}', function ($id) {
        $order = \App\Models\Order::with('items.product')->findOrFail($id);
        return view('admin.order-details', compact('order'));
    })->name('orders.details');

    Route::post('/orders/{id}/update-status', function (Illuminate\Http\Request $request, $id) {
        $order = \App\Models\Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();
        return response()->json(['success' => true]);
    })->name('orders.updateStatus');

    Route::delete('/orders/{id}', function ($id) {
        $order = \App\Models\Order::findOrFail($id);
        $order->delete();
        return response()->json(['success' => true]);
    })->name('orders.delete');

    Route::get('/testimonials', function () {
        return view('admin.testimonials');
    })->name('testimonials');

    Route::get('/trendy-products', function () {
        return view('admin.trendy-products');
    })->name('trendy-products');

    Route::get('/staff', function () {
        $staff = \App\Models\Staff::all();
        return view('admin.staff.index', compact('staff'));
    })->name('staff.index');
    
    Route::get('/billing', function () {
        $totalOrders = \App\Models\Order::count();
        $completedOrders = \App\Models\Order::where('status', 'completed')->count();
        $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
        $totalRevenue = \App\Models\Order::where('status', 'completed')->sum('total_amount');
        $recentOrders = \App\Models\Order::with('items')->latest()->limit(5)->get();
        
        return view('admin.billing-dashboard', compact('totalOrders', 'completedOrders', 'pendingOrders', 'totalRevenue', 'recentOrders'));
    })->name('billing');
    
    Route::get('/staff/create', function () {
        return view('admin.staff.create');
    })->name('staff.create');
    
    Route::post('/staff', function (\Illuminate\Http\Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'village' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'bank_account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        \App\Models\Staff::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'village' => $request->village,
            'district' => $request->district,
            'pincode' => $request->pincode,
            'bank_account_number' => $request->bank_account_number,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);
        
        return redirect()->route('admin.staff.index')->with('success', 'Staff member created successfully!');
    })->name('staff.store');
    
    Route::get('/staff/{id}/edit', function ($id) {
        $staff = \App\Models\Staff::findOrFail($id);
        return view('admin.staff.edit', compact('staff'));
    })->name('staff.edit');
    
    Route::get('/staff/{id}/view', function ($id) {
        $staff = \App\Models\Staff::findOrFail($id);
        
        // Get referral statistics for staff
        $signupReferrals = \App\Models\ReferralTracking::where('staff_id', $staff->id)
            ->where('referral_type', 'signup')
            ->count();
        
        $purchaseReferrals = \App\Models\ReferralTracking::where('staff_id', $staff->id)
            ->where('referral_type', 'purchase')
            ->count();
        
        $totalPoints = ($signupReferrals * 10) + ($purchaseReferrals * 10); // 10 points each
        
        return view('admin.staff.view', compact('staff', 'signupReferrals', 'purchaseReferrals', 'totalPoints'));
    })->name('staff.view');
    
    Route::put('/staff/{id}', function (\Illuminate\Http\Request $request, $id) {
        $staff = \App\Models\Staff::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $id,
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'village' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'bank_account_number' => 'required|string|max:50',
            'bank_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        
        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'village' => $request->village,
            'district' => $request->district,
            'pincode' => $request->pincode,
            'bank_account_number' => $request->bank_account_number,
            'bank_name' => $request->bank_name,
            'ifsc_code' => $request->ifsc_code,
        ]);
        
        if ($request->filled('password')) {
            $staff->password = \Illuminate\Support\Facades\Hash::make($request->password);
            $staff->save();
        }
        
        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully!');
    })->name('staff.update');
    
    Route::delete('/staff/{id}', function ($id) {
        $staff = \App\Models\Staff::findOrFail($id);
        $staff->delete();
        
        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully!');
    })->name('staff.destroy');
});




/*
|--------------------------------------------------------------------------
| Staff Routes
|--------------------------------------------------------------------------
*/
// Staff Authentication Routes

// Staff Login Page
Route::get('/staff/login', function () {
    return view('staff.login');
})->name('staff.login');

// Staff Login Submit
Route::post('/staff/login', function () {
    $credentials = request()->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);
    
    $staff = \App\Models\Staff::where('email', $credentials['email'])->first();
    
    if (!$staff || !\Illuminate\Support\Facades\Hash::check($credentials['password'], $staff->password)) {
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    
    // Login the staff member
    auth()->guard('staff')->login($staff);
    
    return redirect()->intended('/staff');
})->name('staff.login.submit');

// Staff Logout
Route::post('/staff/logout', function () {
    auth()->guard('staff')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    
    return redirect('/staff/login');
})->name('staff.logout');

// Staff Dashboard - separate route for /staff
Route::get('/staff', [StaffController::class, 'dashboard'])->middleware(['web', 'App\Http\Middleware\StaffAuth'])->name('staff.dashboard');

Route::prefix('staff')->name('staff.')->middleware(['web', 'App\Http\Middleware\StaffAuth'])->group(function () {
    Route::get('/', [StaffController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');

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


/*
|--------------------------------------------------------------------------
| Billing Routes
|--------------------------------------------------------------------------
*/

// Billing Authentication Routes
Route::prefix('billing')->name('billing.')->group(function () {
    Route::get('/login', function () {
        return view('billing.login');
    })->name('login');
    
    Route::post('/login', function () {
        $credentials = request()->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        // Check if user is a staff member with billing access
        $staff = \App\Models\Staff::where('email', $credentials['email'])->first();
        
        if (!$staff || !\Illuminate\Support\Facades\Hash::check($credentials['password'], $staff->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }
        
        // Check if staff member has billing permissions
        // For now, we'll assume all staff can access billing
        // In a real system, you might want to check specific permissions
        
        // Login the staff member
        \Illuminate\Support\Facades\Auth::login($staff);
        
        return redirect()->intended('/billing');
    })->name('login.submit');
    
    Route::post('/logout', function () {
        \Illuminate\Support\Facades\Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        
        return redirect('/billing/login');
    })->name('logout');
    
    // Protected billing routes
    Route::middleware(['auth'])->group(function () {
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
        
        Route::get('/invoices', function () {
            return view('billing.invoices');
        })->name('invoices');
        
        Route::get('/reports', function () {
            return view('billing.reports');
        })->name('reports');
        
        Route::get('/payments', function () {
            return view('billing.payments');
        })->name('payments');
    });
});


 ? > 
 
 
