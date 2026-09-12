<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun - {{ env('APP_NAME', 'Swaratani') }}</title>
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
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
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

        /* ===== AUTH CARD ===== */
        .auth-card {
            width: 100%;
            max-width: 480px;
            padding: 2.5rem;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.82);
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
        .auth-card .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
        }

        .auth-card .form-control {
            font-size: 15px;
            min-height: 50px;
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border-radius: 16px;
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            color: #1E293B;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .auth-card .form-control::placeholder {
            color: #94A3B8;
            font-size: 14px;
        }

        .auth-card .form-control:focus {
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

        /* ===== PRIMARY BUTTON ===== */
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

        /* ===== REGISTER/LOGIN LINK ===== */
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
                padding-top: 1.75rem;
                padding-bottom: 2rem;
            }

            .auth-card {
                padding: 1.75rem 1.25rem;
                border-radius: 24px;
            }

            .auth-card .form-control {
                font-size: 16px; /* Prevents auto zoom in iOS */
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
                padding-top: 1.25rem;
            }

            .auth-card {
                padding: 1.5rem 1rem;
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

    <div class="auth-card">
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
            <div class="welcome-title">Buat Akun Baru &#x1F680;</div>
            <div class="welcome-subtitle mt-1">Daftar untuk mulai memantau greenhouse<br>& perangkat IoT Anda</div>
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

        <form action="{{ route('register.perform') }}{{ request('pwa') ? '?pwa=1' : '' }}" method="POST">
            @csrf
            @if(request('pwa'))
                <input type="hidden" name="pwa" value="1">
            @endif

            <!-- Full Name -->
            <div class="mb-3">
                <label class="form-label" for="name">Nama Lengkap</label>
                <div class="input-icon-wrapper">
                    <i class="bi bi-person input-icon"></i>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                        placeholder="Masukkan nama lengkap" required autofocus>
                </div>
            </div>

            <!-- Username -->
            <div class="mb-3">
                <label class="form-label" for="username">Username</label>
                <div class="input-icon-wrapper">
                    <i class="bi bi-at input-icon"></i>
                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}"
                        placeholder="Pilih username unik" required>
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label" for="email">Alamat Email</label>
                <div class="input-icon-wrapper">
                    <i class="bi bi-envelope input-icon"></i>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                        placeholder="nama@email.com" required>
                </div>
            </div>

            <!-- Password & Confirm Password -->
            <div class="row g-2 mb-4">
                <div class="col-12 col-sm-6">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-lock input-icon"></i>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Min. 6 karakter" minlength="6" required style="padding-right: 2.75rem;">
                        <button class="toggle-password" type="button" id="togglePassword" tabindex="-1">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="col-12 col-sm-6">
                    <label class="form-label" for="password_confirmation">Ulangi Password</label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-shield-lock input-icon"></i>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            placeholder="Ulangi password" minlength="6" required style="padding-right: 2.75rem;">
                        <button class="toggle-password" type="button" id="togglePasswordConfirm" tabindex="-1">
                            <i class="bi bi-eye" id="toggleIconConfirm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sign Up Button -->
            <button type="submit" class="btn btn-signin mb-3">
                Daftar Sekarang
                <i class="bi bi-arrow-right"></i>
            </button>
        </form>

        <!-- Divider -->
        <div class="or-divider my-3">
            <hr>
            <span>atau</span>
            <hr>
        </div>

        <!-- Login Link -->
        <div class="text-center">
            <span class="register-text">Sudah punya akun? </span>
            <a href="{{ route('login') }}{{ request('pwa') ? '?pwa=1' : '' }}" class="register-link">Masuk Disini</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function setupPasswordToggle(toggleId, inputId, iconId) {
            const btn = document.getElementById(toggleId);
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!btn || !input || !icon) return;

            btn.addEventListener('click', function () {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });
        }

        setupPasswordToggle('togglePassword', 'password', 'toggleIcon');
        setupPasswordToggle('togglePasswordConfirm', 'password_confirmation', 'toggleIconConfirm');
    </script>
    @include('partials.pwa-scripts')
</body>

</html>