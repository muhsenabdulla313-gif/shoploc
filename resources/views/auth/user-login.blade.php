@extends('layout.master')

@section('body')
<div class="login-modal is-open" id="loginModal" aria-hidden="false">

    {{-- backdrop -> home --}}
    <div class="login-modal__backdrop" id="loginModalBackdrop"
         onclick="window.location.href='{{ url('/') }}';"></div>

    <div class="login-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="loginTitle">

        {{-- close -> home --}}
        <button type="button" class="login-modal__close"
                onclick="window.location.href='{{ url('/') }}';"
                aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>

        <div class="auth-wrap">
            <div class="auth-card">

                {{-- TOP/RIGHT IMAGE --}}
                <div class="auth-right">
                    <div class="auth-right-bg"></div>
                    <div class="auth-right-overlay"></div>

                    <div class="auth-right-text">
                        <p class="brand">WOMENS WEAR</p>
                        <p class="tagline">Elegant styles for women</p>
                    </div>
                </div>

                {{-- FORM --}}
                <div class="auth-left glass">
                    <div class="auth-form-scroll">
                        <div class="auth-head">
                            <p class="auth-kicker">WELCOME BACK</p>
                            <h2 id="loginTitle">User Login</h2>
                            <p class="auth-sub">Login with your email to continue.</p>
                        </div>

                        @if(session('error'))
                            <div class="alert-box">{{ session('error') }}</div>
                        @endif

                        <form method="POST" action="{{ route('user.login.submit') }}">
                            @csrf

                            @if(request('redirect'))
                                <input type="hidden" name="redirect" value="{{ request('redirect') }}">
                            @endif

                            <div class="form-group">
                                <label>Email Address <span class="req">*</span></label>
                                <input type="email" id="modal_email" name="email" required
                                       placeholder="Enter your email address">
                            </div>

                            <button type="submit" class="submit-btn">Login with Email</button>

                            <div class="login-links">
                                <a href="{{ route('register') }}">Don't have an account? <b>Sign Up</b></a>
                                {{-- Forgot Password removed --}}
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection

<style>
/* ============ MODAL ============ */
.login-modal{
    position:fixed;
    inset:0;
    z-index:999999;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:18px;
}

/* safer viewport units for mobile browser bars */
@supports (height: 100dvh){
  .login-modal{ min-height: 100dvh; }
}
@supports not (height: 100dvh){
  .login-modal{ min-height: 100vh; }
}

.login-modal__backdrop{
    position:absolute;
    inset:0;
    background:rgba(0,0,0,.60);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
}

/* IMPORTANT FIX:
   - give dialog a max-height of viewport
   - make dialog scrollable (not clipped)
*/
.login-modal__dialog{
    position:relative;
    z-index:10;
    width:min(980px, 94vw);
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 30px 90px rgba(0,0,0,.40);
    background:transparent;

    /* full visible on all screens */
    max-height: calc(100dvh - 36px);
}
@supports not (height: 100dvh){
  .login-modal__dialog{ max-height: calc(100vh - 36px); }
}

/* close */
.login-modal__close{
    position:absolute;
    top:12px;
    right:12px;
    z-index:9999999;
    width:42px;
    height:42px;
    border:none;
    border-radius:999px;
    cursor:pointer;
    font-size:30px;
    line-height:42px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:rgba(255,255,255,.96);
    color:#111;
    pointer-events:auto;
}

/* ============ CARD ============ */
.auth-wrap{
    padding:0;
    display:flex;
    justify-content:center;

    /* make inner content respect dialog height */
    height: 100%;
}
.auth-card{
    width:980px;
    max-width:100%;
    display:grid;
    grid-template-columns:.95fr 1.05fr; /* image | form */
    background:transparent;

    /* key: fill dialog height so nothing is cut */
    height: 100%;
    min-height: 520px;
}

/* left glass */
.glass{
    background:rgba(255,255,255,0.78);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
}
.auth-left{
    padding:0;

    /* allow scrolling area to work properly */
    min-height: 0;
}

/* scroll container (this will scroll when needed) */
.auth-form-scroll{
    height: 100%;
    overflow-y: auto;
    padding: 34px;

    scrollbar-width: thin;
    scrollbar-color: #0f5c50 rgba(255,255,255,0.3);
}

.auth-form-scroll::-webkit-scrollbar{ width: 6px; }
.auth-form-scroll::-webkit-scrollbar-track{
    background: rgba(255,255,255,0.3);
    border-radius: 3px;
}
.auth-form-scroll::-webkit-scrollbar-thumb{
    background: #0f5c50;
    border-radius: 3px;
}
.auth-form-scroll::-webkit-scrollbar-thumb:hover{ background: #0a3d35; }

/* text */
.auth-kicker{
    font-size:12px;
    letter-spacing:.12em;
    font-weight:800;
    color:#0f5c50;
    margin:0 0 8px;
}
.auth-head h2{ margin:0 0 6px; font-size:26px; font-weight:900; }
.auth-sub{ color:#6b7280; margin:0 0 18px; }

/* alert */
.alert-box{
    padding:10px 12px;
    margin-bottom:14px;
    background:#f8d7da;
    color:#721c24;
    border-radius:10px;
    border:1px solid #f5c6cb;
    font-weight:600;
    font-size:14px;
}

/* form */
.form-group{ margin-bottom:14px; }
label{ font-weight:700; font-size:13px; display:block; margin-bottom:6px; }
.req{ color:red; }

input{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:1px solid #e5e7eb;
    background:rgba(255,255,255,.92);
}
.submit-btn{
    width:100%;
    padding:12px;
    background:#0f5c50;
    color:#fff;
    border:none;
    border-radius:10px;
    font-weight:800;
    margin-top:6px;
    cursor:pointer;
}
.login-links{ margin-top:16px; text-align:center; }
.login-links a{ display:block; margin:10px 0; color:#0f5c50; font-weight:700; }

/* right image */
.auth-right{
    position:relative;
    pointer-events:none;

    /* fill height same as card */
    height: 100%;
    min-height: 520px;
}
.auth-right-bg{
    position:absolute;
    inset:0;
    background:url("https://images.unsplash.com/photo-1548365365-89e071c87a36?q=80&w=900&auto=format&fit=crop")
        center/cover no-repeat;
}
.auth-right-overlay{
    position:absolute;
    inset:0;
    background:linear-gradient(to bottom, rgba(0,0,0,.15), rgba(0,0,0,.60));
}
.auth-right-text{
    position:absolute;
    left:24px;
    bottom:22px;
    color:#fff;
}
.brand{ font-size:28px; font-weight:900; color:#f2c200; margin:0 0 8px; }
.tagline{ font-size:12px; letter-spacing:.12em; margin:0; }

/* ============ MOBILE FIX ============ */
@media(max-width:900px){
    /* top aligned modal + full height */
    .login-modal{
        padding:12px;
        align-items:flex-start;
    }

    .login-modal__dialog{
        width: min(520px, 96vw);
        margin-top:12px;

        /* full height on mobile without cutting */
        max-height: calc(100dvh - 24px);
    }
    @supports not (height: 100dvh){
      .login-modal__dialog{ max-height: calc(100vh - 24px); }
    }

    .auth-card{
        grid-template-columns:1fr;
        grid-template-rows: 230px 1fr; /* image then form */
        min-height: 0;
        height: 100%;
    }

    .auth-right{
        min-height:230px;
        height:230px;
    }

    .auth-left{
        min-height: 0;
    }

    /* keep padding but allow full scroll */
    .auth-form-scroll{
        padding: 22px 18px;
    }
}
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const email = document.getElementById('modal_email');
  if(email) setTimeout(()=>email.focus(), 120);

  // ESC -> home
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
      window.location.href = "{{ url('/') }}";
    }
  });
});
</script>
@endsection
