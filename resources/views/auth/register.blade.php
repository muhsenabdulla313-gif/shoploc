@extends('layout.master')

@section('body')

{{-- ✅ REGISTER MODAL (same scroll behaviour like fixed login modal) --}}
<div class="register-modal is-open" id="registerModal" aria-hidden="false">

    {{-- backdrop -> home --}}
    <div class="register-modal__backdrop" id="registerModalBackdrop"
         onclick="window.location.href='{{ url('/') }}';"></div>

    <div class="register-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="registerTitle">

        {{-- close -> home --}}
        <button type="button" class="register-modal__close"
                onclick="window.location.href='{{ url('/') }}';"
                aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>

        <div class="auth-wrap">
            <div class="auth-card">

                {{-- IMAGE FIRST --}}
                <div class="auth-right">
                    <div class="auth-right-overlay"></div>

                    <div class="auth-right-bg"
                         style="background-image:url('https://images.unsplash.com/photo-1615903162221-b757b14b8000?q=80&w=900&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');">
                    </div>

                    <div class="auth-right-text">
                        <p class="brand">WOMENS WEAR</p>
                        <p class="tagline">Elegant styles for women</p>
                    </div>
                </div>

                {{-- FORM SECOND --}}
                <div class="auth-left glass">
                    <div class="auth-form-scroll">
                        <div class="auth-head">
                            <p class="auth-kicker">WELCOME</p>
                            <h2 id="registerTitle">Create Account</h2>
                            <p class="auth-sub">Create your account to continue.</p>
                        </div>

                        <form method="POST" action="{{ route('register') }}" class="auth-form">
                            @csrf

                            <div class="form-group">
                                <label for="name">Full Name <span class="req">*</span></label>
                                <input
                                    type="text"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    placeholder="Enter your full name"
                                    class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                                >
                                @if($errors->has('name'))
                                    <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address <span class="req">*</span></label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="Enter your email address"
                                    class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                                >
                                @if($errors->has('email'))
                                    <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                @endif
                            </div>

                            <button type="submit" class="submit-btn">Create Account</button>

                            <div class="login-links">
                                <a href="{{ route('login') }}">Already have an account? <b>Login</b></a>
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
/* ============ MODAL (REGISTER) ============ */
.register-modal{
    position:fixed;
    inset:0;
    z-index:999999;
    display:flex;
    align-items:flex-start;
    justify-content:center;
    padding:12px;

    /* ✅ FULL MODAL SCROLL (same fix like login) */
    overflow-y:auto;
    -webkit-overflow-scrolling:touch;
}
.register-modal__backdrop{
    position:absolute;
    inset:0;
    background:rgba(0,0,0,.60);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
}
.register-modal__dialog{
    position:relative;
    z-index:10;
    width:min(980px, 94vw);
    border-radius:16px;
    overflow:hidden;
    box-shadow:0 30px 90px rgba(0,0,0,.40);
    background:transparent;
    margin:12px 0; /* ✅ keep inside scroll area */
}
.register-modal__close{
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

/* IMPORTANT: image/overlay should not block clicks */
.auth-right,
.auth-right-bg,
.auth-right-overlay{ pointer-events:none; }

/* ============ Layout ============ */
.auth-wrap{
    height:auto;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:0;
}
.auth-card{
    width:min(980px,100%);
    background:transparent;
    border-radius:14px;
    overflow:hidden;
    display:grid;
    grid-template-columns:.95fr 1.05fr; /* image | form */
    box-shadow:0 20px 60px rgba(0,0,0,.18);
}

/* ✅ Glass left (stronger so no overlap look) */
.glass{
    background:rgba(255,255,255,0.92);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
}
.auth-left{
    padding:0;
    min-height:0; /* ✅ important for scroll inside grid */
}

/* ✅ FORM SCROLL (same fix) */
.auth-form-scroll{
    max-height: calc(100dvh - 140px);
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    padding: 34px 34px 28px;

    scrollbar-width: thin;
    scrollbar-color: #0f5c50 rgba(255,255,255,0.3);
}
@supports not (height: 100dvh){
    .auth-form-scroll{ max-height: calc(100vh - 140px); }
}

.auth-form-scroll::-webkit-scrollbar{ width:6px; }
.auth-form-scroll::-webkit-scrollbar-track{
    background: rgba(255,255,255,0.3);
    border-radius: 3px;
}
.auth-form-scroll::-webkit-scrollbar-thumb{
    background:#0f5c50;
    border-radius:3px;
}
.auth-form-scroll::-webkit-scrollbar-thumb:hover{ background:#0a3d35; }

.auth-kicker{
    font-size:12px;
    letter-spacing:.12em;
    font-weight:800;
    color:#0f5c50;
    margin:0 0 8px;
}
.auth-head h2{
    margin:0 0 6px;
    font-size:26px;
    font-weight:800;
}
.auth-sub{
    color:#6b7280;
    font-size:14px;
    margin-bottom:18px;
}

.form-group{ margin-bottom:14px; }
label{
    font-weight:700;
    font-size:13px;
    color:#374151;
    margin-bottom:6px;
    display:block;
}
.req{ color:#e11d48; }

input{
    width:100%;
    padding:12px;
    border:1px solid #e5e7eb;
    border-radius:10px;
    font-size:15px;
    background:rgba(255,255,255,.96);
}
input:focus{
    outline:none;
    border-color:#0f5c50;
    box-shadow:0 0 0 4px rgba(15,92,80,.14);
}

.submit-btn{
    width:100%;
    padding:12px;
    background:#0f5c50;
    color:#fff;
    border:none;
    border-radius:10px;
    font-weight:800;
    cursor:pointer;
}
.submit-btn:hover{ background:#0a3d35; }

.login-links{
    margin-top:16px;
    text-align:center;
}
.login-links a{
    color:#0f5c50;
    font-weight:700;
    text-decoration:none;
}
.login-links a:hover{ text-decoration:underline; }

/* Errors */
.is-invalid{ border-color:#dc3545 !important; }
.invalid-feedback{
    color:#dc3545;
    font-size:13px;
    margin-top:4px;
}

/* Right Image */
.auth-right{
    position:relative;
    min-height:520px;
}
.auth-right-bg{
    position:absolute;
    inset:0;
    background-size:cover;
    background-position:center;
}
.auth-right-overlay{
    position:absolute;
    inset:0;
    background:linear-gradient(to bottom, rgba(0,0,0,.15), rgba(0,0,0,.55));
}
.auth-right-text{
    position:absolute;
    left:24px;
    bottom:22px;
    color:#fff;
}
.auth-right-text .brand{
    font-size:28px;
    font-weight:900;
    letter-spacing:.1em;
    color:#f2c200;
    margin-bottom:8px;
}
.auth-right-text .tagline{
    font-size:12px;
    letter-spacing:.12em;
    text-transform:uppercase;
}

/* ✅ Responsive: image first, form second + proper scroll */
@media(max-width:900px){
    .register-modal{ padding:12px; }

    .register-modal__dialog{
        width:min(520px, 96vw);
        margin:12px 0;
    }

    .auth-card{
        grid-template-columns:1fr;
        /* ✅ key fix: 2nd row flexible (no cut) */
        grid-template-rows:230px minmax(0, 1fr);
    }

    .auth-right{
        min-height:230px;
        height:230px;
    }

    .auth-left{ min-height:0; }

    .auth-form-scroll{
        padding:22px 18px;
        max-height: calc(100dvh - 320px);
    }
    @supports not (height: 100dvh){
        .auth-form-scroll{ max-height: calc(100vh - 320px); }
    }
}
</style>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  // focus first field
  const name = document.getElementById('name');
  if(name) setTimeout(()=>name.focus(), 120);

  // ESC -> home
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
      window.location.href = "{{ url('/') }}";
    }
  });
});
</script>
@endsection
