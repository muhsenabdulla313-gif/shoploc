@extends('layout.master')

@section('body')
<div class="auth-modal-wrap">

  {{-- Overlay --}}
  <div class="auth-overlay"></div>

  {{-- Modal --}}
  <div class="auth-modal" role="dialog" aria-modal="true" aria-labelledby="otpTitle">

    {{-- Close --}}
    <a href="{{ url('/') }}" class="auth-close" aria-label="Close">
      <i class="fa-solid fa-xmark"></i>
    </a>

    {{-- Left Image --}}
    <div class="auth-left">
      <div class="auth-left-overlay"></div>

      <div class="auth-left-text">
        <div class="auth-tag">WOMENS WEAR</div>
        <div class="auth-subtag">Elegant styles for women</div>
      </div>
    </div>

    {{-- Right Form --}}
    <div class="auth-right">
      <div class="auth-head">
        <div class="auth-welcome">WELCOME BACK</div>
        <h2 class="auth-title" id="otpTitle">Login Verification</h2>
        <p class="auth-desc">Please check your email for the OTP code</p>
      </div>

      {{-- Alerts --}}
      @if(session('success'))
        <div class="auth-alert success">{{ session('success') }}</div>
      @endif

      @if(session('error'))
        <div class="auth-alert error">{{ session('error') }}</div>
      @endif

      @if($errors->has('otp'))
        <div class="auth-alert error">{{ $errors->first('otp') }}</div>
      @endif

      <form method="POST" action="{{route('user.otp.submit') }}" class="auth-form" novalidate>
        @csrf

        <div class="auth-field">
          <label for="otp" class="auth-label">OTP Code <span class="req">*</span></label>

          <div class="auth-input-wrap {{ $errors->has('otp') ? 'has-error' : '' }}">
            <input
              type="text"
              id="otp"
              name="otp"
              required
              inputmode="numeric"
              autocomplete="one-time-code"
              maxlength="6"
              placeholder="Enter 6-digit OTP"
              class="auth-input"
              value="{{ old('otp') }}"
            />
          </div>
        </div>

        <button type="submit" class="auth-btn">Verify OTP</button>

        <div class="auth-footer">
          <a href="{{ url('/login') }}" class="auth-link">Back to Login</a>
        </div>

        <div class="auth-bottom">
          <span class="muted">Don't have an account?</span>
          <a href="{{ route('register') }}" class="auth-link strong">Sign Up</a>
        </div>
      </form>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const otp = document.getElementById('otp');
  if (otp) {
    otp.addEventListener('input', () => {
      otp.value = otp.value.replace(/\D/g,'').slice(0,6);
    });
  }

  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
      window.location.href = "{{ url('/') }}";
    }
  });
});
</script>
@endsection
