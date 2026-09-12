<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Email Terverifikasi - {{ env('APP_NAME', 'Swaratani') }}</title>
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
            background: radial-gradient(circle, rgba(16, 185, 129, 0.14) 0%, rgba(16, 185, 129, 0) 70%);
        }

        .glow-blob-2 {
            width: 200px;
            height: 200px;
            bottom: 60px;
            left: -50px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.12) 0%, rgba(14, 165, 233, 0) 70%);
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
            max-width: 440px;
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

        /* ===== SUCCESS ICON BOX ===== */
        .success-icon-box {
            width: 80px;
            height: 80px;
            border-radius: 24px;
            background: linear-gradient(135deg, #10B981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 2.25rem;
            color: #fff;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);
        }

        /* ===== WELCOME / TITLE TEXT ===== */
        .welcome-title {
            font-size: 22px;
            font-weight: 800;
            color: #0F172A;
            letter-spacing: -0.3px;
        }

        .welcome-subtitle {
            color: #64748B;
            font-size: 14px;
            line-height: 1.5;
        }

        /* ===== ACTION BUTTONS ===== */
        .btn-app-action {
            width: 100%;
            min-height: 50px;
            font-size: 15px;
            font-weight: 700;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary-action {
            color: #fff;
            background: linear-gradient(135deg, #0EA5E9, #0284C7);
            border: none;
            box-shadow: 0 5px 14px rgba(14, 165, 233, 0.35);
        }

        .btn-primary-action:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.4);
        }

        .btn-outline-action {
            color: #0EA5E9;
            background: #F0F9FF;
            border: 1px solid rgba(14, 165, 233, 0.25);
        }

        .btn-outline-action:hover {
            color: #0284C7;
            background: #E0F2FE;
            transform: translateY(-1px);
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

            .success-icon-box {
                width: 68px;
                height: 68px;
                font-size: 1.85rem;
                border-radius: 20px;
            }

            .welcome-title {
                font-size: 19px;
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
        <div class="success-icon-box">
            <i class="bi bi-check-lg"></i>
        </div>

        <h3 class="welcome-title mb-2">Email Berhasil Diverifikasi!</h3>
        <p class="welcome-subtitle mb-4">
            Akun Anda kini telah aktif. Silakan pilih platform untuk melanjutkan.
        </p>

        <div class="d-flex flex-column gap-3">
            <button onclick="openSwarataniApp(event)" class="btn-app-action btn-primary-action">
                <i class="bi bi-phone"></i>
                Buka Aplikasi Android
            </button>
            <a href="{{ route('login') }}" class="btn-app-action btn-outline-action">
                <i class="bi bi-globe"></i>
                Lanjut di Web Browser
            </a>
        </div>
    </div>

    <script>
        function openSwarataniApp(e) {
            if (e) e.preventDefault();
            var packageName = "id.swaratani.swaratani_mobile";
            var playStoreLink = "https://play.google.com/store/apps/details?id=" + packageName;
            var userAgent = navigator.userAgent || navigator.vendor || window.opera;

            if (/android/i.test(userAgent)) {
                var intentUrl = "intent://#Intent;scheme=swaratani;package=" + packageName + ";S.browser_fallback_url=" + encodeURIComponent(playStoreLink) + ";end";
                window.location.href = intentUrl;
            } else {
                window.location.href = playStoreLink;
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('partials.pwa-scripts')
</body>

</html>