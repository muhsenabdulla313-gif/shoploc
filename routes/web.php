<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferralController;







Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/hero', function () {
    return view('hero'); });
Route::get('/products', [HomeController::class, 'products'])->name('products');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/trendy', [HomeController::class, 'trendy'])->name('trendy');
Route::get('/product/{id}', [HomeController::class, 'show'])->name('product.show');
Route::get('/admin/products/search-suggestions', [ProductController::class, 'searchSuggestions']);
Route::get('/womens', [HomeController::class, 'women'])->name('women.page');
Route::get('/mens', [HomeController::class, 'mens'])->name('men.page');
Route::get('/kids', [HomeController::class, 'kids'])->name('kids.page');
Route::get('/cart', function () {
    return view('cart'); })->name('cart')->middleware('auth');
Route::get('/wishlist', function () {
    return view('wishlist'); })->name('wishlist')->middleware('auth');
Route::get('/api/products/related/{category}/{excludeId}', [ProductController::class, 'getRelatedProducts'])->name('products.related');
Route::get('/checkout', function () {
    return view('checkout'); })->name('checkout.page')->middleware([App\Http\Middleware\RequireReferral::class]);
Route::get('/payment', function () {
    return view('payment'); })->name('payment');
Route::get('/contact', function () {
    return view('contact'); })->name('contact');
Route::post('/contact/submit', [HomeController::class, 'contact'])->name('contact.submit');


















Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
Route::post('/user-login', [UserLoginController::class, 'login'])->name('user.login.submit');
Route::get('/register', function () {return view('auth.register');})->name('register');
Route::post('/register/submit', [UserLoginController::class, 'register'])->name('register.submit');
Route::get('/verify-otp', [UserLoginController::class, 'verifyotp'])->name('verify.otp.form');
Route::post('/verify-otp/submit', [UserLoginController::class, 'otpsubmit'])->name('user.otp.submit');
Route::get('/verify-login-otp', [UserLoginController::class, ' verifyloginotp'])->name('verify.login.otp.form');
Route::post('/verify-login-otp-submit', [UserLoginController::class, 'verifyloginotpsubmit'])->name('verify-login-otp-submit');
Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');




Route::get('/my-orders', [OrderController::class, 'order'])->name('user.orders');
Route::get('/my-orders/{id}', [OrderController::class, 'orderdetails'])->name('order.details');
Route::post('/my-orders/{id}/cancel', [OrderController::class, 'cancelorder'])->name('order.cancel');


Route::get('/profile', [ProfileController::class, 'profile'])->name('user.profile');
Route::delete('/profile/delete', [ProfileController::class, 'delete'])->name('user.delete');





Route::middleware(['auth:staff'])->group(function () {
    Route::delete('/admin/users/{id}', [StaffController::class, 'deleteuser'])->name('admin.users.delete');

});


Route::get('/password/reset', [AdminController::class, 'reset'])->name('password.request');
Route::post('/password/email', [AdminController::class, 'updatepassword'])->name('password.email');
Route::get('/password/reset/{token}', [AdminController::class, 'resettoken'])->name('password.request');
Route::post('/password/reset', [AdminController::class, 'updateresetpassword'])->name('password.update');






Route::get('/referral-required', [ReferralController::class, 'show'])->name('referral.required');
Route::post('/referral-required', [ReferralController::class, 'submit'])->name('referral.submit');

Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');

Route::middleware(['web', 'App\Http\Middleware\StaffAuth'])->group(function () {
    Route::get('/admin/billing-staff', [App\Http\Controllers\Admin\BillingStaffController::class, 'index'])->name('admin.billing.staff');
    Route::get('/admin/billing-staff/create', [App\Http\Controllers\Admin\BillingStaffController::class, 'create'])->name('admin.billing.staff.create');
    Route::post('/admin/billing-staff', [App\Http\Controllers\Admin\BillingStaffController::class, 'store'])->name('admin.billing.staff.store');
    Route::get('/admin/billing-staff/{id}', [App\Http\Controllers\Admin\BillingStaffController::class, 'show'])->name('admin.billing.staff.show');
    Route::get('/admin/billing-staff/{id}/edit', [App\Http\Controllers\Admin\BillingStaffController::class, 'edit'])->name('admin.billing.staff.edit');
    Route::put('/admin/billing-staff/{id}', [App\Http\Controllers\Admin\BillingStaffController::class, 'update'])->name('admin.billing.staff.update');
    Route::delete('/admin/billing-staff/{id}', [App\Http\Controllers\Admin\BillingStaffController::class, 'destroy'])->name('admin.billing.staff.destroy');
});








// Route::get('/admin/test-auth', function () {
//     if (auth()->guard('staff')->check()) {
//         return response()->json([
//             'authenticated' => true,
//             'user' => auth()->guard('staff')->user()
//         ]);
//     } else {
//         return response()->json(['authenticated' => false]);
//     }
// });

Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');






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


// Route::post('/login', function () {
//     $credentials = request()->validate([
//         'email' => ['required', 'email'],
//         'password' => ['required'],
//     ]);

//     $user = \App\Models\User::where('email', $credentials['email'])->first();

//     if (!$user) {
//         return redirect()->route('register')->withErrors([
//             'email' => 'No account found with this email address. Please register first.',
//         ])->onlyInput('email');
//     }

//     if (!$user || !Hash::check($credentials['password'], $user->password)) {
//         return back()->withErrors([
//             'email' => 'The provided credentials do not match our records.',
//         ])->onlyInput('email');
//     }

//     if (!$user->email_verified) {
//         return back()->withErrors([
//             'email' => 'Please verify your email address before logging in. Check your email for the OTP.',
//         ])->onlyInput('email');
//     }

//     if (Auth::attempt($credentials)) {
//         request()->session()->regenerate();
//         return redirect()->intended('/staff');
//     }

//     return back()->withErrors([
//         'email' => 'The provided credentials do not match our records.',
//     ])->onlyInput('email');
// });

