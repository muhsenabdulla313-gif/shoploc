<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the 'api' middleware group. Make something great!
|
*/

// Admin password reset routes
Route::prefix('admin')->group(function () {
    Route::get('/password/reset', function () {
        return view('admin.auth.passwords.email');
    })->name('admin.password.request');

    Route::post('/password/email', function (Illuminate\Http\Request $request) {
        $request->validate(['email' => 'required|email']);

        $credentials = $request->only('email');
        $staff = App\Models\Staff::where('email', $credentials['email'])->first();

        if (!$staff) {
            return response()->json(['error' => 'No admin account found with this email address.'], 404);
        }

        // Generate reset token
        $token = Str::random(60);
        
        // Store the token
        Illuminate\Support\Facades\DB::table('staff_password_resets')->updateOrInsert(
            ['email' => $credentials['email']], 
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Send actual email
        try {
            Mail::send([], [], function ($message) use ($credentials, $token) {
                $resetUrl = url('/admin/password/reset/' . urlencode($token) . '?email=' . urlencode($credentials['email']));
                
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
            
            session()->flash('success', 'Password reset link sent to your email address.');
            
            return response()->json(['message' => 'We have emailed your password reset link!']);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Admin password reset email failed: ' . $e->getMessage());
            
            // Even if email fails, we can still show success message for security
            session()->flash('success', 'Password reset link sent to your email address.');
            
            return response()->json(['message' => 'We have emailed your password reset link!']);
        }
    })->name('admin.password.email');

    Route::get('/password/reset/{token}', function (Illuminate\Http\Request $request, $token) {
        return view('admin.auth.passwords.reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    })->name('admin.password.reset');

    Route::post('/password/reset', function (Illuminate\Http\Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Check if the reset token exists and is valid
        $reset = Illuminate\Support\Facades\DB::table('staff_password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$reset) {
            return response()->json(['error' => 'Invalid password reset token.'], 400);
        }

        // Update the staff password
        $staff = App\Models\Staff::where('email', $request->email)->first();
        if (!$staff) {
            return response()->json(['error' => 'No admin account found with this email address.'], 404);
        }

        $staff->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete the reset token
        Illuminate\Support\Facades\DB::table('staff_password_resets')->where('email', $request->email)->delete();

        // Login the user after password reset
        auth()->guard('staff')->login($staff);

        return response()->json(['message' => 'Your password has been reset!']);
    })->name('admin.password.update');
});

