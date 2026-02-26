<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Admin Login</title>

  <!-- Bootstrap + FontAwesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

  <style>
    :root{
      --brand-yellow:#f6c43c;
      --brand-yellow-2:#ffd86b;
      --dark:#1f2a33;
      --muted:#7b8794;
      --card-radius:18px;
    }

    body{
      min-height:100vh;
      display:flex;
      align-items:center;
      justify-content:center;
      background: linear-gradient(135deg, #24313a 0%, #0f1a22 100%);
      font-family: Arial, sans-serif;
      padding: 24px;
    }

    /* Top title like image */
    .page-title{
      text-align:center;
      color:#e9eef3;
      margin-bottom:18px;
      letter-spacing:0.5px;
    }
    .page-title h1{
      font-size:22px;
      font-weight:700;
      margin:0;
      opacity:.95;
    }
    .page-title .line{
      width:190px;
      height:2px;
      background:rgba(233,238,243,.35);
      margin:10px auto 0;
      border-radius:999px;
    }

    /* Main card */
    .login-card{
      width: 100%;
      max-width: 860px;
      background:#fff;
      border-radius: var(--card-radius);
      box-shadow: 0 20px 55px rgba(0,0,0,.35);
      overflow:hidden;
      position:relative;
    }

    .login-grid{
      display:grid;
      grid-template-columns: 40% 60%;
      min-height: 420px;
    }

    /* Left yellow panel */
    .side-panel{
      position:relative;
      padding: 34px 28px;
      background: radial-gradient(1200px 500px at 20% 20%, rgba(255,255,255,.55) 0%, rgba(255,255,255,0) 60%),
                  linear-gradient(135deg, var(--brand-yellow) 0%, var(--brand-yellow-2) 100%);
      color:#fff;
      display:flex;
      align-items:center;
      justify-content:center;
    }

    /* decorative waves */
    .side-panel::before,
    .side-panel::after{
      content:"";
      position:absolute;
      left:-20%;
      right:-20%;
      height:140px;
      border-radius: 0 0 60% 60%;
      background: rgba(255,255,255,.18);
      transform: rotate(-6deg);
    }
    .side-panel::before{ bottom: 90px; }
    .side-panel::after{ bottom: 20px; opacity:.12; }

    .brand-box{
      position:relative;
      z-index:2;
      text-align:center;
      width:100%;
      max-width: 240px;
    }
    .brand-logo{
      width: 86px;
      height: 86px;
      border-radius: 50%;
      background: rgba(255,255,255,.25);
      display:flex;
      align-items:center;
      justify-content:center;
      margin: 0 auto 14px;
      box-shadow: 0 10px 22px rgba(0,0,0,.18);
      backdrop-filter: blur(2px);
    }
    .brand-logo i{ font-size: 34px; }

    .brand-name{
      font-weight:800;
      letter-spacing:.5px;
      margin: 2px 0 6px;
    }
    .brand-sub{
      font-size: 12px;
      opacity:.95;
      margin:0;
    }

    /* little sparkles */
    .sparkle{
      position:absolute;
      width:8px;height:8px;
      background: rgba(255,255,255,.85);
      border-radius:2px;
      transform: rotate(45deg);
      opacity:.9;
      z-index:1;
    }
    .sparkle.s1{ top:26px; left:26px; width:10px; height:10px; }
    .sparkle.s2{ top:90px; right:34px; width:7px; height:7px; opacity:.7;}
    .sparkle.s3{ bottom:80px; left:42px; width:6px; height:6px; opacity:.7;}
    .sparkle.s4{ bottom:34px; right:54px; width:9px; height:9px; opacity:.8;}

    /* Right form panel */
    .form-panel{
      padding: 34px 34px 28px;
      display:flex;
      flex-direction:column;
      justify-content:center;
    }

    .form-title{
      font-size: 18px;
      font-weight: 800;
      color: var(--dark);
      margin-bottom: 6px;
    }
    .form-desc{
      color: var(--muted);
      font-size: 12.5px;
      margin-bottom: 22px;
      line-height: 1.4;
    }

    .input-wrap{
      position:relative;
      margin-bottom: 14px;
    }
    .input-wrap i{
      position:absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color:#9aa6b2;
      font-size: 14px;
      pointer-events:none;
    }
    .form-control.custom{
      padding: 12px 14px 12px 40px;
      border: 1.8px solid #e6e9ee;
      border-radius: 10px;
      font-size: 15px;
      transition: .2s ease;
    }
    .form-control.custom:focus{
      border-color: rgba(246,196,60,.85);
      box-shadow: 0 0 0 4px rgba(246,196,60,.18);
    }

    .row-mini{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap: 12px;
      margin: 6px 0 18px;
    }

    .form-check-label{
      font-size: 13px;
      color:#5b6670;
      cursor:pointer;
    }
    .link-mini{
      font-size: 13px;
      text-decoration:none;
      color:#5b6670;
    }
    .link-mini:hover{ text-decoration:underline; }

    .btn-login{
      width: 100%;
      border:none;
      border-radius: 12px;
      padding: 12px 14px;
      background: linear-gradient(135deg, #ffd34a 0%, #f5b51f 100%);
      color:#1a222b;
      font-weight: 800;
      letter-spacing:.2px;
      box-shadow: 0 12px 22px rgba(245,181,31,.35);
      position:relative;
      overflow:hidden;
      transition:.2s ease;
    }
    .btn-login:hover{
      transform: translateY(-1px);
      box-shadow: 0 16px 28px rgba(245,181,31,.42);
    }
    .btn-login:active{ transform: translateY(0); }

    /* Ripple */
    .btn-login .ripple{
      position:absolute;
      border-radius:50%;
      transform: scale(0);
      animation: ripple 650ms linear;
      background: rgba(255,255,255,.55);
      pointer-events:none;
    }
    @keyframes ripple{
      to{
        transform: scale(4);
        opacity:0;
      }
    }

    .divider{
      text-align:center;
      position:relative;
      margin: 18px 0 14px;
      color:#98a3ad;
      font-size: 12px;
    }
    .divider::before,
    .divider::after{
      content:"";
      position:absolute;
      top:50%;
      width: 38%;
      height: 1px;
      background:#edf0f4;
    }
    .divider::before{ left:0; }
    .divider::after{ right:0; }

    .footer-mini{
      margin-top: 10px;
      text-align:center;
      font-size: 12.5px;
      color:#7c8792;
    }
    .footer-mini a{
      color:#6b7785;
      text-decoration:none;
      font-weight:700;
    }
    .footer-mini a:hover{ text-decoration:underline; }

    /* Alerts style */
    .alert-danger{
      border-radius: 12px;
      font-size: 13px;
    }

    /* Responsive */
    @media (max-width: 820px){
      .login-grid{
        grid-template-columns: 1fr;
      }
      .side-panel{
        min-height: 220px;
      }
      .form-panel{
        padding: 26px 22px 22px;
      }
    }
  </style>
</head>

<body>
  <div class="w-100" style="max-width: 900px;">
    <div class="page-title">
      <h1>Admin Dashboard Log in Form</h1>
      <div class="line"></div>
    </div>

    <div class="login-card">
      <div class="login-grid">
        <!-- LEFT -->
        <div class="side-panel">
          <span class="sparkle s1"></span>
          <span class="sparkle s2"></span>
          <span class="sparkle s3"></span>
          <span class="sparkle s4"></span>

          <div class="brand-box">
            <div class="brand-logo">
              <!-- Change icon / replace with your logo image if needed -->
              <i class="fa-solid fa-user-astronaut"></i>
            </div>
            <div class="brand-name">SHOPLOC</div>
            <p class="brand-sub">WELCOME BACK • SECURE LOGIN</p>
          </div>
        </div>

        <!-- RIGHT -->
        <div class="form-panel">
          <div class="form-title">Admins Log in</div>
          <div class="form-desc">
            Please enter your credentials to access the admin portal.
          </div>

          {{-- Laravel Session + Validation Errors --}}
          @if(session('error'))
            <div class="alert alert-danger mb-3">
              {{ session('error') }}
            </div>
          @endif

          @if($errors->any())
            <div class="alert alert-danger mb-3">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="input-wrap">
              <i class="fa-solid fa-user"></i>
              <input
                type="email"
                name="email"
                value="{{ old('email') }}"
                class="form-control custom"
                placeholder="Username / Email"
                required
                autocomplete="off"
              />
            </div>

            <div class="input-wrap">
              <i class="fa-solid fa-lock"></i>
              <input
                type="password"
                name="password"
                class="form-control custom"
                placeholder="Password"
                required
              />
            </div>

            <div class="row-mini">
              <!-- <div class="form-check m-0">
                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
              </div> -->

              <!-- If you have reset route, replace href -->
              <a class="link-mini" href="{{ route('admin.password.request') }}">
                Forgot Password?
              </a>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">
              Log in
            </button>

            <div class="divider">or</div>

            <div class="footer-mini">
              <a href="/"><i class="fa-solid fa-arrow-left-long"></i> Back to Website</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Ripple on button (like modern UI)
    document.addEventListener("DOMContentLoaded", () => {
      const btn = document.getElementById("loginBtn");
      if(!btn) return;

      btn.addEventListener("click", function(e){
        const rect = btn.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size/2;
        const y = e.clientY - rect.top - size/2;

        const ripple = document.createElement("span");
        ripple.className = "ripple";
        ripple.style.width = ripple.style.height = size + "px";
        ripple.style.left = x + "px";
        ripple.style.top = y + "px";

        btn.appendChild(ripple);
        setTimeout(() => ripple.remove(), 700);
      });
    });
  </script>
</body>
</html>
