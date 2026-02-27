<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
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
}
