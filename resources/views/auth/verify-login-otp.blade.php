@extends('layout.master')

@section('body')
<div class="auth-modal-wrap">

  {{-- Overlay --}}
  <div class="auth-overlay"></div>

  {{-- Modal --}}
  <div class="auth-modal" role="dialog" aria-modal="true" aria-labelledby="otpTitle">

    {{-- Close --}}
    <a href="{{ route('login') }}" class="auth-close" aria-label="Close">
      <i class="fa-solid fa-xmark"></i>
    </a>

    {{-- Left Image Panel --}}
    <div class="auth-left">
      <div class="auth-left-overlay"></div>
      <div class="auth-left-text">
        <div class="auth-tag">WOMENS WEAR</div>
        <div class="auth-subtag">Elegant styles for women</div>
      </div>
    </div>

    {{-- Right Form Panel --}}
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

      <form method="POST" action="{{ route('verify-login-otp-submit') }}" class="auth-form" novalidate>
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

          @if($errors->has('otp'))
            <div class="auth-error">{{ $errors->first('otp') }}</div>
          @endif
        </div>

        <button type="submit" class="auth-btn">Verify OTP</button>

        <div class="auth-footer">
          <a href="{{ route('login') }}" class="auth-link">Back to Login</a>
        </div>

        <div class="auth-bottom">
          <span class="muted">Don't have an account?</span>
          <a href="{{ route('register') }}" class="auth-link strong">Sign Up</a>
        </div>
      </form>
    </div>

  </div>
</div>

{{-- ✅ Keep your redirect script, but clean + digits only --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  // OTP digits only
  const otp = document.getElementById('otp');
  if (otp) {
    otp.addEventListener('input', () => {
      otp.value = otp.value.replace(/\D/g,'').slice(0,6);
    });
  }

  // redirect after success (same logic you had)
  const urlParams = new URLSearchParams(window.location.search);
  const redirectUrl = urlParams.get('redirect');

  const successDiv = document.querySelector('.auth-alert.success');
  if (successDiv) {
    const dummyToken = 'auth_token_' + Date.now();
    localStorage.setItem('token', dummyToken);
    localStorage.setItem('authToken', dummyToken);

    if (redirectUrl) {
      setTimeout(function() {
        window.location.href = decodeURIComponent(redirectUrl);
      }, 1500);
    }
  }

  // ESC close
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
      window.location.href = "{{ route('login') }}";
    }
  });
});
</script>

@endsection
