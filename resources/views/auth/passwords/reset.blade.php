@extends('layout.auth')

@section('body')
<style>
    .login-container {
        max-width: 400px;
        margin: 50px auto;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        background: white;
    }
    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-control {
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        width: 100%;
    }
    .btn-reset {
        background: linear-gradient(135deg, #2c1a2a 0%, #6e3967 100%);
        color: white;
        border: none;
        padding: 12px;
        width: 100%;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-reset:hover {
        opacity: 0.9;
    }
    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 5px;
    }
    .back-to-login {
        text-align: center;
        margin-top: 20px;
    }
    .back-to-login a {
        color: #6e3967;
        text-decoration: none;
        font-size: 0.9rem;
    }
</style>

<div class="login-container">
    <div class="login-header">
        <h2>Reset Password</h2>
        <p>Enter your new password</p>
    </div>
    
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token ?? '' }}">
        
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" required value="{{ old('email') }}">
            @if($errors->has('email'))
                <div class="error-message">{{ $errors->first('email') }}</div>
            @endif
        </div>
        
        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
            @if($errors->has('password'))
                <div class="error-message">{{ $errors->first('password') }}</div>
            @endif
        </div>
        
        <div class="form-group">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>
        
        <button type="submit" class="btn-reset">Reset Password</button>
    </form>
    
    <div class="back-to-login">
        <a href="{{ route('login') }}">Back to Login</a>
    </div>
</div>
@endsection