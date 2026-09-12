<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi Email - {{ env('APP_NAME', 'Swaratani') }}</title>
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
            max-width: 460px;
            padding: 2.5rem;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(226, 232, 240, 0.6);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
            position: relative;
            z-index: 1;
            text-align: center;
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

        /* ===== WELCOME / VERIFY TEXT ===== */
        .welcome-title {
            font-size: 24px;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.3px;
        }

        .welcome-subtitle {
            color: #64748B;
            font-size: 14px;
            line-height: 1.5;
        }

        .email-highlight {
            color: #0EA5E9;
            font-weight: 700;
            word-break: break-all;
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

        /* ===== ALERTS ===== */
        .alert-login-error {
            background: rgba(239, 68, 68, 0.08);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #DC2626;
            border-radius: 14px;
            padding: 12px 16px;
            font-size: 14px;
            text-align: left;
        }

        .alert-custom-success {
            background: rgba(16, 185, 129, 0.08);
            border: 1px solid rgba(16, 185, 129, 0.25);
            color: #059669;
            border-radius: 14px;
            padding: 12px 16px;
            font-size: 14px;
            text-align: left;
        }

        .alert-custom-warning {
            background: rgba(245, 158, 11, 0.08);
            border: 1px solid rgba(245, 158, 11, 0.25);
            color: #D97706;
            border-radius: 14px;
            padding: 12px 16px;
            font-size: 14px;
            text-align: left;
        }

        .tips-box {
            background: rgba(248, 250, 252, 0.8);
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 14px 16px;
            margin-top: 1.5rem;
            text-align: left;
        }

        .tips-box h6 {
            color: #0F172A;
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tips-box ul {
            margin: 0;
            padding-left: 1.25rem;
            color: #64748B;
            font-size: 12.5px;
            line-height: 1.5;
        }

        .tips-box li {
            margin-bottom: 2px;
        }

        /* ===== BACK LINK ===== */
        .auth-back-link {
            font-size: 13.5px;
            font-weight: 600;
            color: #64748B;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }

        .auth-back-link:hover {
            color: #0EA5E9;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 576px) {
            body {
                padding: 1rem;
                align-items: flex-start;
                padding-top: 2rem;
            }

            .auth-card {
                padding: 1.75rem 1.25rem;
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
            <div class="welcome-title">Cek Email Kamu! &#x2709;&#xFE0F;</div>
            @php
                $email = session('pending_verification_email') ?? (Auth::check() ? Auth::user()->email : 'email Anda');
            @endphp
            <div class="welcome-subtitle mt-2">
                Silakan verifikasi email Anda untuk mengaktifkan akun.<br>
                Kirim link verifikasi ke <span class="email-highlight">{{ $email }}</span>
            </div>
        </div>

        @if (session('status'))
            <div class="alert-custom-success mb-3 d-flex align-items-center">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('status') }}</div>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert-custom-warning mb-3 d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>{{ session('warning') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-login-error mb-3 d-flex align-items-center">
                <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                <div>{{ $errors->first() }}</div>
            </div>
        @endif

        <form action="{{ route('verification.resend') }}" method="POST" id="resendForm">
            @csrf
            <button type="submit" class="btn btn-signin mb-3" id="resendBtn">
                <i class="bi bi-envelope-paper" id="resendIcon"></i>
                <span id="btnText">Kirim Email Verifikasi</span>
            </button>
        </form>

        <div class="tips-box">
            <h6><i class="bi bi-lightbulb text-warning"></i>Tips Verifikasi:</h6>
            <ul>
                <li>Cek folder <strong>Spam/Junk</strong> jika email tidak ada di inbox utama.</li>
                <li>Pastikan alamat email <strong>{{ $email }}</strong> sudah benar.</li>
                <li>Tautan verifikasi berlaku selama 60 menit.</li>
            </ul>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('login') }}" class="auth-back-link">
                <i class="bi bi-arrow-left"></i>
                Kembali ke Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('resendBtn');
            const btnText = document.getElementById('btnText');
            const icon = document.getElementById('resendIcon');
            const form = document.getElementById('resendForm');
            const COOLDOWN_KEY = 'emailResendCooldown';
            const COOLDOWN_SECONDS = 60;

            function startCooldown() {
                const endTime = Date.now() + (COOLDOWN_SECONDS * 1000);
                localStorage.setItem(COOLDOWN_KEY, endTime);
                updateButton();
            }

            function updateButton() {
                const endTime = localStorage.getItem(COOLDOWN_KEY);
                if (endTime && Date.now() < parseInt(endTime)) {
                    const remaining = Math.ceil((parseInt(endTime) - Date.now()) / 1000);
                    btn.disabled = true;
                    btn.style.opacity = '0.7';
                    btn.style.cursor = 'not-allowed';
                    btnText.textContent = `Tunggu ${remaining} detik`;
                    icon.className = 'bi bi-hourglass-split';
                    setTimeout(updateButton, 1000);
                } else {
                    localStorage.removeItem(COOLDOWN_KEY);
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                    btnText.textContent = 'Kirim Email Verifikasi';
                    icon.className = 'bi bi-envelope-paper';
                }
            }

            form.addEventListener('submit', function () {
                startCooldown();
            });

            updateButton();
        });
    </script>
    @include('partials.pwa-scripts')
</body>

</html>