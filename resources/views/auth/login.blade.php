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
    .btn-login {
        background: linear-gradient(135deg, #2c1a2a 0%, #6e3967 100%);
        color: white;
        border: none;
        padding: 12px;
        width: 100%;
        border-radius: 5px;
        font-weight: 600;
        cursor: pointer;
    }
    .btn-login:hover {
        opacity: 0.9;
    }
    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 5px;
    }
</style>

<div class="login-container">
    <div class="login-header">
        <h2>Staff Login</h2>
        <p>Please login to access the staff dashboard</p>
    </div>
    
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" required autofocus>
            @if($errors->has('email'))
                <div class="error-message">{{ $errors->first('email') }}</div>
            @endif
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        
        <div class="form-group text-center mb-3">
            <a href="{{ route('password.request') }}" style="color: #6e3967; text-decoration: none; font-size: 0.9rem;">
                Forgot Password?
            </a>
        </div>
        
        <button type="submit" class="btn-login">Login</button>
    </form>
    
    <div class="mt-3 text-center">
        <a href="{{ route('home') }}">Back to Store</a>
    </div>
</div>
@endsection