@extends('staff.layout')

@section('content')
<div class="login-page">
    <div class="login-wrap">
        <h1>USER LOGIN</h1>

        {{-- Error message --}}
        @if(session('error'))
            <div class="login-alert">{{ session('error') }}</div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="login-alert">
                <ul style="margin:0; padding-left:18px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('staff.login.submit') }}">
            @csrf

            <!-- EMAIL -->
            <div class="input-row">
                <span class="icon-left">
                    <i class="fa-regular fa-envelope"></i>
                </span>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Email Address"
                    required
                >
            </div>

            <!-- PASSWORD -->
            <div class="input-row">
                <span class="icon-left">
                    <i class="fa-solid fa-key"></i>
                </span>

                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required
                >

                <span class="icon-right">
                    <i class="fa-solid fa-lock"></i>
                </span>
            </div>

            <button type="submit" class="login-btn">LOGIN</button>

            <div class="back">
                <a href="{{ url('/') }}">← Back to Website</a>
            </div>
        </form>
    </div>
</div>
@endsection
