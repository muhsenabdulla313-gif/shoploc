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
    
    // Store the token
    \Illuminate\Support\Facades\DB::table('password_resets')->updateOrInsert(
        ['email' => $credentials['email']],
        [
            'token' => \Illuminate\Support\Facades\Hash::make($token),
            'created_at' => now(),
        ]
    );

    // In production, send actual email
    session()->flash('success', 'Password reset link sent to your email address.');
    
    return back()->with('status', 'We have emailed your password reset link!');
})->name('admin.password.email');

// Admin password reset form
Route::get('/admin/password/reset/{token}', function (\Illuminate\Http\Request $request, $token) {
    return view('admin.auth.passwords.reset', [
        'token' => $token,
        'email' => $request->query('email'),
    ]);
})->name('admin.password.reset');

// Update admin password
Route::post('/admin/password/reset', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    // Check if the reset token exists and is valid
    $reset = \Illuminate\Support\Facades\DB::table('password_resets')
        ->where('email', $request->email)
        ->first();

    if (!$reset) {
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
    \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $request->email)->delete();

    // Login the user after password reset
    auth()->guard('staff')->login($staff);

    return redirect('/admin')->with('status', 'Your password has been reset!');
})->name('admin.password.update');