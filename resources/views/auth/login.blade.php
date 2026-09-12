<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    @include('partials.theme')

    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        /* ===== AMBIENT GLOW BLOBS ===== */
        .glow-blob {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .glow-blob-1 {
            width: 260px;
            height: 260px;
            top: -60px;
            right: -40px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 70%);
        }

        .glow-blob-2 {
            width: 200px;
            height: 200px;
            bottom: 60px;
            left: -50px;
            background: radial-gradient(circle, rgba(14, 95, 138, 0.10) 0%, rgba(14, 95, 138, 0) 70%);
        }

        .glow-blob-3 {
            width: 160px;
            height: 160px;
            top: 40%;
            right: -30px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.08) 0%, rgba(139, 92, 246, 0) 70%);
        }

        /* ===== LOGIN CARD ===== */
        .login-card {
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.80);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
            position: relative;
            z-index: 1;
        }

        /* ===== BRAND HEADER ===== */
        .brand-logo-box {
            width: 80px;
            height: 80px;
            padding: 14px;
            background: #E0F2FE;
            border-radius: 22px;
            border: 1px solid rgba(186, 230, 253, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.12);
        }

        .brand-logo-box img {
            height: 100%;
            width: auto;
        }

        .brand-name {
            font-size: 28px;
            font-weight: 900;
            background: linear-gradient(135deg, #0E5F8A, #0EA5E9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 12px;
            font-weight: 600;
            color: #94A3B8;
            letter-spacing: 0.5px;
        }

        /* ===== WELCOME TEXT ===== */
        .welcome-title {
            font-size: 24px;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.3px;
        }

        .welcome-subtitle {
            color: #64748B;
            font-size: 13.5px;
            line-height: 1.4;
        }

        /* ===== FORM INPUTS ===== */
        .login-card .form-control {
            font-size: 15px;
            min-height: 50px;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border-radius: 16px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            color: #1E293B;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .login-card .form-control::placeholder {
            color: #94A3B8;
            font-size: 14px;
        }

        .login-card .form-control:focus {
            border-color: #0EA5E9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
            background: #fff;
        }

        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #0EA5E9;
            font-size: 18px;
            z-index: 2;
        }

        .input-icon-wrapper .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94A3B8;
            font-size: 18px;
            cursor: pointer;
            padding: 4px;
            z-index: 2;
            transition: color 0.2s;
        }

        .input-icon-wrapper .toggle-password:hover {
            color: #64748B;
        }

        /* ===== REMEMBER ME & FORGOT PASSWORD ===== */
        .remember-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .remember-row .form-check-input {
            width: 18px;
            height: 18px;
            border-radius: 5px;
            border: 1.5px solid #CBD5E1;
        }

        .remember-row .form-check-input:checked {
            background-color: #0EA5E9;
            border-color: #0EA5E9;
        }

        .remember-row .form-check-label {
            font-size: 13px;
            color: #475569;
            font-weight: 500;
            margin-left: 4px;
        }

        .forgot-link {
            font-size: 13px;
            font-weight: 600;
            color: #0E5F8A;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #0EA5E9;
        }

        /* ===== SIGN IN BUTTON ===== */
        .btn-signin {
            width: 100%;
            min-height: 52px;
            font-size: 16px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #0EA5E9, #0284C7);
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 14px rgba(14, 165, 233, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }

        .btn-signin:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.4);
        }

        .btn-signin:active {
            transform: translateY(0);
        }

        /* ===== DIVIDER ===== */
        .or-divider {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .or-divider hr {
            flex: 1;
            border: none;
            border-top: 1px solid rgba(226, 232, 240, 0.8);
            margin: 0;
        }

        .or-divider span {
            font-size: 12.5px;
            font-weight: 500;
            color: #94A3B8;
        }

        /* ===== REGISTER LINK ===== */
        .register-text {
            font-size: 13.5px;
            color: #64748B;
        }

        .register-link {
            font-size: 13.5px;
            font-weight: 700;
            color: #0EA5E9;
            text-decoration: none;
            transition: color 0.2s;
        }

        .register-link:hover {
            color: #0284C7;
        }

        /* ===== ERROR ALERT ===== */
        .alert-login-error {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #DC2626;
            border-radius: 14px;
            padding: 12px 16px;
            font-size: 14px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 576px) {
            body {
                padding: 1rem;
                align-items: flex-start;
                padding-top: 2rem;
            }

            .login-card {
                padding: 1.75rem 1.5rem;
                border-radius: 24px;
            }

            .brand-logo-box {
                width: 64px;
                height: 64px;
                padding: 12px;
                border-radius: 18px;
            }

            .brand-name {
                font-size: 24px;
            }

            .welcome-title {
                font-size: 20px;
            }
        }

        @media (max-width: 400px) {
            body {
                padding: 0.75rem;
                padding-top: 1.5rem;
            }

            .login-card {
                padding: 1.5rem 1.25rem;
            }

            .brand-logo-box {
                width: 56px;
                height: 56px;
                padding: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <!-- Ambient Glow Blobs -->
    <div class="glow-blob glow-blob-1"></div>
    <div class="glow-blob glow-blob-2"></div>
    <div class="glow-blob glow-blob-3"></div>

    <div class="login-card">
        <!-- Brand Header -->
        <div class="text-center mb-3">
            <div class="brand-logo-box">
                <img src="{{ asset(env('APP_LOGO', 'images/logo.png')) }}" alt="{{ env('APP_NAME', 'Swaratani') }}">
            </div>
            <div class="brand-name">Swaratani</div>
            <div class="brand-subtitle">Smart IoT Farming Platform</div>
        </div>

        <!-- Welcome Text -->
        <div class="text-center mb-4">
            <div class="welcome-title">Selamat Datang! &#x1F44B;</div>
            <div class="welcome-subtitle mt-1">Masuk untuk memantau greenhouse<br>& perangkat IoT Anda</div>
        </div>

        @if ($errors->any())
            <div class="alert-login-error mb-3">
                <ul class="mb-0 small ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login.perform') }}{{ request('pwa') ? '?pwa=1' : '' }}" method="POST">
            @csrf
            @if(request('pwa'))
                <input type="hidden" name="pwa" value="1">
            @endif

            <!-- Username -->
            <div class="mb-3">
                <div class="input-icon-wrapper">
                    <i class="bi bi-at input-icon"></i>
                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}"
                        placeholder="Username atau email" required autofocus>
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <div class="input-icon-wrapper">
                    <i class="bi bi-lock input-icon"></i>
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Password" minlength="6" required style="padding-right: 2.75rem;">
                    <button class="toggle-password" type="button" id="togglePassword" tabindex="-1">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="remember-row mb-4">
                <div class="form-check d-flex align-items-center m-0">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
                <a href="{{ route('password.request') }}" class="forgot-link">Lupa Password?</a>
            </div>

            <!-- Sign In Button -->
            <button type="submit" class="btn btn-signin mb-3">
                Masuk
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <!-- Divider -->
        <div class="or-divider my-4">
            <hr>
            <span>atau</span>
            <hr>
        </div>

        <!-- Register Link -->
        <div class="text-center">
            <span class="register-text">Belum punya akun? </span>
            <a href="{{ route('register') }}" class="register-link">Daftar Sekarang</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    </script>
    @include('partials.pwa-scripts')
</body>

</html>