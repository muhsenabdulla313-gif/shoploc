<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\Staff\StaffLoginController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ListController as AdminListController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\BillingStaffController as AdminBillingStaffController;



Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/hero', function () {
    return view('hero');
});
Route::get('/products', [ProductController::class, 'products'])->name('products');
Route::get('/shop', [ProductController::class, 'shop'])->name('shop');
Route::get('/trendy', [ProductController::class, 'trendy'])->name('trendy');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::get('/admin/products/search-suggestions', [ProductController::class, 'searchSuggestions']);
Route::get('/womens', [ProductController::class, 'women'])->name('women.page');
Route::get('/mens', [ProductController::class, 'mens'])->name('men.page');
Route::get('/kids', [ProductController::class, 'kids'])->name('kids.page');
Route::get('/cart', function () {return view('cart');})->name('cart')->middleware('auth');
Route::get('/wishlist', function () { return view('wishlist');})->name('wishlist')->middleware('auth');
Route::get('/api/products/related/{category}/{excludeId}', [ProductController::class, 'getRelatedProducts'])->name('products.related');
Route::get('/checkout', function () {return view('checkout');})->name('checkout.page')->middleware([App\Http\Middleware\RequireReferral::class]);
Route::get('/payment', function () {return view('payment');})->name('payment');
Route::get('/contact', function () {return view('contact');})->name('contact');
Route::post('/contact/submit', [HomeController::class, 'contact'])->name('contact.submit');





Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
Route::post('/user-login', [UserLoginController::class, 'login'])->name('user.login.submit');
Route::get('/register', function () {return view('auth.register');})->name('register');
Route::post('/register/submit', [UserLoginController::class, 'register'])->name('register.submit');
Route::get('/verify-otp', [UserLoginController::class, 'verifyotp'])->name('verify.otp.form');
Route::post('/verify-otp/submit', [UserLoginController::class, 'otpsubmit'])->name('user.otp.submit');
Route::get('/verify-login-otp', [UserLoginController::class, 'verifyloginotp'])->name('verify.login.otp.form');
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



Route::get('/password/reset', [AdminLoginController::class, 'reset'])->name('password.request');
Route::post('/password/email', [AdminLoginController::class, 'updatepassword'])->name('password.email');
Route::get('/password/reset/{token}', [AdminLoginController::class, 'resettoken'])->name('password.request');
Route::post('/password/reset', [AdminLoginController::class, 'updateresetpassword'])->name('password.update');






Route::get('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'authenticate'])->name('admin.login.submit');
Route::get('/admin/password/reset', function () {
    return view('admin.auth.passwords.email'); })->name('admin.password.request');
Route::post('/admin/password/email', [AdminLoginController::class, 'reset_new'])->name('admin.password.email');



// Route::get('/admin/password/reset/{token}', [AdminLoginController::class, 'resettoken'])->name('admin.password.reset');


// Route::get('/admin/password/reset/{token}', function (\Illuminate\Http\Request $request, $token) {
//     return view('admin.auth.passwords.reset', [
//         'token' => $token,
//         'email' => $request->query('email'),
//     ]);

// })->name('admin.password.reset');

// Route::post('/admin/password/reset', function (\Illuminate\Http\Request $request) {
//     $request->validate([
//         'token' => 'required',
//         'email' => 'required|email',
//         'password' => 'required|min:8|confirmed',
//     ]);

//     $resetRecord = \Illuminate\Support\Facades\DB::table('staff_password_resets')
//         ->where('email', $request->email)
//         ->first();

//     if (!$resetRecord) {
//         return back()->withErrors(['email' => 'Invalid password reset token.']);
//     }

//     $staff = \App\Models\Staff::where('email', $request->email)->first();
//     if (!$staff) {
//         return back()->withErrors(['email' => 'No admin account found with this email address.']);
//     }

//     $staff->update([
//         'password' => \Illuminate\Support\Facades\Hash::make($request->password),
//     ]);

//     // Delete the reset token
//     \Illuminate\Support\Facades\DB::table('staff_password_resets')->where('email', $request->email)->delete();

//     // Login the user after password reset
//     auth()->guard('staff')->login($staff);

//     return redirect('/admin')->with('status', 'Your password has been reset!');
// })->name('admin.password.update');







Route::get('/referral-required', [ReferralController::class, 'show'])->name('referral.required');
Route::post('/referral-required', [ReferralController::class, 'submit'])->name('referral.submit');

Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');

// Route::middleware(['web', 'auth:staff'])->group(function () {
  
// });














Route::prefix('admin')->name('admin.')->middleware(['web', 'auth:admin'])
    ->group(function () {

    Route::get('/dashboard', [AdminLoginController::class, 'dashboard'])->name('dashboard');

        Route::get('/', [AdminLoginController::class, 'dashboard'])->name('dashboard');
        Route::get('/products', [AdminProductController::class, 'display'])->name('products');
        Route::get('/products/list', [AdminProductController::class, 'index'])->name('products.list');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}', [AdminProductController::class, 'show'])->name('products.show');
        Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');

        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.list');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{id}', [AdminCategoryController::class, 'show'])->name('categories.show');
        Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');





        Route::get('/users', [AdminListController::class, 'users'])->name('users');
        Route::get('/orders', [AdminListController::class, 'orders'])->name('orders');
        Route::get('/orders/{id}', [AdminListController::class, 'orderdetails'])->name('orders.details');
        Route::match(['post', 'put', 'patch'], '/orders/{id}/update-status', [AdminListController::class, 'updateStatus'])->name('orders.updateStatus');


        
        Route::post('/staff/message', [AdminStaffController::class, 'staffmessage'])->name('staff.message');
  Route::get('billing-staff', [AdminBillingStaffController::class, 'index'])->name('billing.staff');
    Route::get('billing-staff/create', [AdminBillingStaffController::class, 'create'])->name('billing.staff.create');
    Route::post('billing-staff', [AdminBillingStaffController::class, 'store'])->name('billing.staff.store');
    Route::get('billing-staff/{id}', [AdminBillingStaffController::class, 'show'])->name('billing.staff.show');
    Route::get('billing-staff/{id}/edit', [AdminBillingStaffController::class, 'edit'])->name('billing.staff.edit');
    Route::put('billing-staff/{id}', [AdminBillingStaffController::class, 'update'])->name('billing.staff.update');
    Route::delete('billing-staff/{id}', [AdminBillingStaffController::class, 'destroy'])->name('billing.staff.destroy');








        Route::delete('/orders/{id}', [AdminOrderController::class, 'destroy'])->name('orders.delete');
        Route::get('/staff/manage', [AdminStaffController::class, 'manage'])->name('staff.manage');
        Route::post('/staff', [AdminStaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{id}/edit', [AdminStaffController::class, 'staffedit'])->name('staff.edit');
        Route::put('/staff/{id}', [AdminStaffController::class, 'update'])->name('staff.update');
        Route::delete('/staff/{id}', [AdminStaffController::class, 'destroy'])->name('staff.delete');
        Route::post('/staff/bulk-message', [AdminStaffController::class, 'bulkmessage'])->name('staff.bulk-message');
        Route::get('/staff-messages', [AdminStaffController::class, 'allmessages'])->name('staff.messages.index');
        Route::delete('/staff-messages/{id}', [AdminStaffController::class, 'deletemessages'])->name('staff.messages.delete');



        Route::get('/trendy-products', function () {return view('admin.trendy-products');})->name('trendy-products.index');
        Route::get('/trendy-products/list', [AdminProductController::class, 'listTrendyProducts'])->name('trendy-products.list');
        Route::post('/trendy-products', [AdminProductController::class, 'storeTrendyProduct'])->name('trendy-products.store');

        Route::put('/trendy-products/{id}', [AdminProductController::class, 'updateTrendyProduct'])->name('trendy-products.update');
        Route::delete('/trendy-products/{id}', [AdminProductController::class, 'removeFromTrendy'])->name('trendy-products.remove');

    });




Route::get('/staff/login', function () {
    return view('staff.login');
})->name('staff.login');

Route::post('/staff/login', [StaffLoginController::class, 'authenticate'])->name('staff.login.submit');


Route::post('/staff/logout', [StaffLoginController::class, 'logout'])->name('staff.logout');




Route::get('/staff', [StaffController::class, 'dashboard'])->middleware(['web', 'auth:staff'])->name('staff.dashboard');

Route::prefix('staff')->name('staff.')->middleware(['web', 'auth:staff'])->group(function () {
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



Route::prefix('billing')->name('billing.')->group(function () {


    Route::get('/login', [BillingController::class, 'login'])->name('login');
    Route::post('/login', [BillingController::class, 'authenticate'])->name('login.submit');
    Route::post('/logout', [BillingController::class, 'logout'])->name('logout');









    Route::middleware(['auth:staff'])->group(function () {

        Route::get('/', [BillingController::class, 'index'])->name('dashboard');

        Route::get('/orders', [BillingController::class, 'orders'])->name('orders');
        Route::get('/orders/{id}', [BillingController::class, 'orderdetails'])->name('orders.show');

    });
});



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


// Route::get('/staff/dashboard', function () {
//     return redirect()->route('admin.dashboard');
// })->name('admin.staff.dashboard');

// Route::get('/trendy-products', function () {
//     return view('admin.trendy-products');
// })->name('trendy-products.index');
