<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0e5f8a">
    <meta name="description" content="{{ env('APP_NAME', 'Swaratani') }} - Sistem monitoring pertanian cerdas berbasis">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ env('APP_NAME', 'Swaratani') }}">
    <link rel="icon" href="{{ asset(env('APP_LOGO', 'images/logo.png')) }}">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="{{ asset(env('APP_LOGO', 'images/logo.png')) }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <title>{{ env('APP_NAME', 'Swaratani') }} - Dashboard</title>

    @include('partials.theme')

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
            background: var(--nature-gradient);
        }

        /* ===== NAVBAR ===== */
        .navbar-main {
            background: var(--navbar-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 0.6rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 10px rgba(0, 0, 0, 0.06);
        }

        .navbar-main .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            text-decoration: none;
        }

        .navbar-main .navbar-brand img {
            height: 36px;
            width: auto;
        }

        .navbar-main .navbar-brand span {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .nav-links a,
        .nav-links button {
            color: var(--navbar-text);
            text-decoration: none;
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.25s ease;
            border: none;
            background: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .nav-links a:hover,
        .nav-links button:hover {
            background: rgba(0, 0, 0, 0.05);
            color: var(--primary);
        }

        .nav-links a.active {
            background: var(--primary);
            color: #fff;
        }

        .nav-user-pill {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.3rem 0.75rem 0.3rem 0.35rem;
            border-radius: 50px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            margin-left: 0.5rem;
        }

        .nav-user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .nav-user-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-main);
        }

        /* ===== HERO SECTION ===== */
        .hero-section {
            position: relative;
            padding: 4rem 0 3rem;
            text-align: center;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 140%;
            height: 200%;
            background:
                radial-gradient(ellipse at 30% 50%, var(--glow-1) 0%, transparent 55%),
                radial-gradient(ellipse at 70% 30%, var(--glow-2) 0%, transparent 55%),
                radial-gradient(ellipse at 50% 80%, var(--glow-3) 0%, transparent 50%);
            z-index: 0;
            animation: heroGlow 8s ease-in-out infinite alternate;
        }

        @keyframes heroGlow {
            0% { opacity: 0.6; transform: scale(1); }
            100% { opacity: 1; transform: scale(1.05); }
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.35rem 1rem;
            border-radius: 50px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--primary);
            margin-bottom: 1.25rem;
        }

        .hero-badge .dot {
            width: 6px;
            height: 6px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.75rem;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .hero-title .highlight {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.05rem;
            color: var(--text-secondary);
            max-width: 550px;
            margin: 0 auto 2rem;
            line-height: 1.6;
        }

        .hero-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            background: var(--primary-gradient);
            color: #fff;
            border: none;
            padding: 0.75rem 1.75rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.2);
            color: #fff;
        }

        .btn-hero-secondary {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            color: var(--text-main);
            border: 1px solid var(--glass-border);
            padding: 0.75rem 1.75rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-hero-secondary:hover {
            transform: translateY(-3px);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* ===== STATS SECTION ===== */
        .stats-section {
            padding: 0 0 3rem;
            position: relative;
            z-index: 1;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            max-width: 900px;
            margin: 0 auto;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
            border-color: var(--primary);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
            flex-shrink: 0;
        }

        .stat-icon.devices { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .stat-icon.sensors { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-icon.online { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-icon.alerts { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }

        .stat-info h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            line-height: 1.2;
        }

        .stat-info p {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin: 0;
        }

        /* ===== FEATURES SECTION ===== */
        .features-section {
            padding: 0 0 3rem;
            position: relative;
            z-index: 1;
        }

        .section-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .section-header h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.35rem;
        }

        .section-header p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1rem;
            max-width: 900px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.75rem;
            text-decoration: none;
            color: var(--text-main);
            transition: all 0.35s ease;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            display: block;
            position: relative;
            overflow: hidden;
        }

        .feature-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: var(--primary);
            color: var(--text-main);
        }

        .feature-card:hover::after {
            opacity: 1;
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: white;
            margin-bottom: 1rem;
        }

        .feature-icon.monitoring { background: linear-gradient(135deg, #3b82f6, #1d4ed8); box-shadow: 0 6px 18px rgba(59, 130, 246, 0.3); }
        .feature-icon.automasi { background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 6px 18px rgba(16, 185, 129, 0.3); }
        .feature-icon.riwayat { background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 6px 18px rgba(245, 158, 11, 0.3); }
        .feature-icon.schedule { background: linear-gradient(135deg, #8b5cf6, #6d28d9); box-shadow: 0 6px 18px rgba(139, 92, 246, 0.3); }
        .feature-icon.ticket { background: linear-gradient(135deg, #ec4899, #be185d); box-shadow: 0 6px 18px rgba(236, 72, 153, 0.3); }
        .feature-icon.admin { background: linear-gradient(135deg, #f97316, #c2410c); box-shadow: 0 6px 18px rgba(249, 115, 22, 0.3); }

        .feature-card h3 {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.4rem;
        }

        .feature-card p {
            font-size: 0.82rem;
            color: var(--text-secondary);
            margin-bottom: 0.75rem;
            line-height: 1.5;
        }

        .feature-link {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--primary);
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: gap 0.3s ease;
        }

        .feature-card:hover .feature-link {
            gap: 0.6rem;
        }

        /* ===== QUICK ACCESS (for guest) ===== */
        .quick-access {
            padding: 0 0 3rem;
            position: relative;
            z-index: 1;
        }

        .quick-grid {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .quick-card {
            width: 220px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem 1.5rem;
            text-decoration: none;
            color: var(--text-main);
            text-align: center;
            transition: all 0.35s ease;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        .quick-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            color: var(--text-main);
        }

        .quick-card .quick-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin: 0 auto 0.75rem;
        }

        .quick-card h3 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .quick-card .quick-arrow {
            font-size: 0.8rem;
            color: var(--text-secondary);
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.3s ease;
        }

        .quick-card:hover .quick-arrow {
            color: var(--primary);
            gap: 0.5rem;
        }

        /* ===== FOOTER ===== */
        .page-footer {
            text-align: center;
            padding: 1.5rem;
            color: var(--text-secondary);
            font-size: 0.8rem;
            position: relative;
            z-index: 1;
            border-top: 1px solid var(--glass-border);
        }

        .page-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        /* ===== MOBILE RESPONSIVE ===== */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 1.75rem;
            }

            .hero-subtitle {
                font-size: 0.9rem;
            }

            .hero-section {
                padding: 2.5rem 0 2rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .nav-links .nav-text {
                display: none;
            }

            .nav-user-name {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .hero-title {
                font-size: 1.5rem;
            }

            .quick-card {
                width: calc(50% - 0.5rem);
            }
        }

        /* ===== ANIMATIONS ===== */
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.3); }
        }

        .fade-up {
            opacity: 0;
            transform: translateY(25px);
            animation: fadeUp 0.6s ease forwards;
        }

        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-up:nth-child(1) { animation-delay: 0.05s; }
        .fade-up:nth-child(2) { animation-delay: 0.1s; }
        .fade-up:nth-child(3) { animation-delay: 0.15s; }
        .fade-up:nth-child(4) { animation-delay: 0.2s; }
        .fade-up:nth-child(5) { animation-delay: 0.25s; }
        .fade-up:nth-child(6) { animation-delay: 0.3s; }

        /* Hamburger toggler for mobile */
        .navbar-toggler {
            border: none;
            padding: 0.3rem;
        }
        .navbar-toggler:focus {
            box-shadow: none;
        }
        .navbar-toggler-icon {
            filter: none;
            width: 1.25em;
            height: 1.25em;
        }

        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: var(--navbar-bg);
                backdrop-filter: blur(20px);
                border-radius: 12px;
                margin-top: 0.75rem;
                padding: 0.75rem;
                border: 1px solid var(--glass-border);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .nav-links {
                flex-direction: column;
                align-items: stretch;
            }

            .nav-links a,
            .nav-links button {
                justify-content: flex-start;
                padding: 0.6rem 0.85rem;
            }

            .nav-user-pill {
                margin-left: 0;
                margin-top: 0.5rem;
                justify-content: flex-start;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <!-- ===== NAVBAR ===== -->
    @include('partials.navbar')

    @auth
        @php
            $user = Auth::user();
            $userDevices = $user->userDevices()->with('device.sensors')->get();
            $totalDevices = $userDevices->count();
            $totalSensors = $userDevices->sum(function($ud) {
                return $ud->device ? $ud->device->sensors->count() : 0;
            });
            $onlineDevices = $userDevices->filter(function($ud) {
                return $ud->device && $ud->device->isOnline();
            })->count();
            $automationKeys = ['ats_suhu', 'ats_kelem', 'ats_lux', 'ats_co2', 'ats_tds', 'ats_ph', 'ats_wlevel'];
            $totalAutomations = \App\Models\DeviceSetting::whereIn('device_id', $userDevices->pluck('device_id'))
                ->whereIn('key', $automationKeys)
                ->select('device_id')
                ->distinct()
                ->count();
        @endphp

        <!-- ===== HERO SECTION ===== -->
        <section class="hero-section">
            <div class="container hero-content">
                <div class="hero-badge fade-up">
                    <span class="dot"></span>
                    Sistem Aktif & Terhubung
                </div>
                <h1 class="hero-title fade-up">
                    Selamat Datang, <span class="highlight">{{ $user->name }}</span>
                </h1>
                <p class="hero-subtitle fade-up">
                    Pantau, kendalikan, dan otomatisasi seluruh perangkat pertanian Anda dari satu dashboard terpadu.
                </p>
                <div class="hero-actions fade-up">
                    <a href="{{ route('monitoring.index') }}" class="btn-hero-primary">
                        <i class="bi bi-graph-up-arrow"></i> Buka Monitoring
                    </a>
                    @if($user->role === 'admin')
                        <a href="{{ route('admin.devices.index') }}" class="btn-hero-secondary">
                            <i class="bi bi-gear"></i> Admin Panel
                        </a>
                    @else
                        <a href="{{ route('riwayat.index') }}" class="btn-hero-secondary">
                            <i class="bi bi-clock-history"></i> Lihat Riwayat
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <!-- ===== STATS SECTION ===== -->
        <section class="stats-section">
            <div class="container">
                <div class="stats-grid">
                    <div class="stat-card fade-up">
                        <div class="stat-icon devices"><i class="bi bi-cpu"></i></div>
                        <div class="stat-info">
                            <h4>{{ $totalDevices }}</h4>
                            <p>Total Device</p>
                        </div>
                    </div>
                    <div class="stat-card fade-up">
                        <div class="stat-icon sensors"><i class="bi bi-thermometer-half"></i></div>
                        <div class="stat-info">
                            <h4>{{ $totalSensors }}</h4>
                            <p>Sensor Terhubung</p>
                        </div>
                    </div>
                    <div class="stat-card fade-up">
                        <div class="stat-icon online"><i class="bi bi-wifi"></i></div>
                        <div class="stat-info">
                            <h4>{{ $onlineDevices }}</h4>
                            <p>Device Online</p>
                        </div>
                    </div>
                    <div class="stat-card fade-up">
                        <div class="stat-icon alerts"><i class="bi bi-lightning-charge"></i></div>
                        <div class="stat-info">
                            <h4>{{ $totalAutomations }}</h4>
                            <p>Automasi Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== FEATURES SECTION ===== -->
        <section class="features-section">
            <div class="container">
                <div class="section-header fade-up">
                    <h2>Fitur Utama</h2>
                    <p>Akses cepat ke semua fitur {{ env('APP_NAME', 'Swaratani') }}</p>
                </div>
                <div class="features-grid">
                    <a href="{{ route('monitoring.index') }}" class="feature-card fade-up">
                        <div class="feature-icon monitoring"><i class="bi bi-graph-up-arrow"></i></div>
                        <h3>Monitoring Real-time</h3>
                        <p>Pantau data sensor suhu, kelembapan, pH, dan lainnya secara langsung dari perangkat Anda.</p>
                        <span class="feature-link">Buka Monitoring <i class="bi bi-arrow-right"></i></span>
                    </a>
                    <a href="{{ route('monitoring.index') }}" class="feature-card fade-up">
                        <div class="feature-icon automasi"><i class="bi bi-gear-wide-connected"></i></div>
                        <h3>Automasi Cerdas</h3>
                        <p>Atur automasi berdasarkan kondisi sensor. Kontrol pompa, kipas, dan aktuator otomatis.</p>
                        <span class="feature-link">Kelola Automasi <i class="bi bi-arrow-right"></i></span>
                    </a>
                    <a href="{{ route('riwayat.index') }}" class="feature-card fade-up">
                        <div class="feature-icon riwayat"><i class="bi bi-clock-history"></i></div>
                        <h3>Riwayat & Laporan</h3>
                        <p>Lihat riwayat aktivitas, analisis tren data sensor, dan ekspor laporan dalam format CSV.</p>
                        <span class="feature-link">Lihat Riwayat <i class="bi bi-arrow-right"></i></span>
                    </a>
                    <a href="{{ route('monitoring.index') }}" class="feature-card fade-up">
                        <div class="feature-icon schedule"><i class="bi bi-calendar-check"></i></div>
                        <h3>Penjadwalan</h3>
                        <p>Jadwalkan penyiraman, pemberian nutrisi, dan aktivitas lainnya sesuai waktu yang diinginkan.</p>
                        <span class="feature-link">Atur Jadwal <i class="bi bi-arrow-right"></i></span>
                    </a>
                    <a href="{{ route('tickets.index') }}" class="feature-card fade-up">
                        <div class="feature-icon ticket"><i class="bi bi-headset"></i></div>
                        <h3>Bantuan & Tiket</h3>
                        <p>Ada kendala? Buat tiket bantuan dan tim support kami akan segera merespon.</p>
                        <span class="feature-link">Buat Tiket <i class="bi bi-arrow-right"></i></span>
                    </a>
                    @if($user->role === 'admin')
                        <a href="{{ route('admin.devices.index') }}" class="feature-card fade-up">
                            <div class="feature-icon admin"><i class="bi bi-shield-lock"></i></div>
                            <h3>Admin Panel</h3>
                            <p>Kelola semua device, user, sensor rules, dan broadcast pengumuman ke seluruh pengguna.</p>
                            <span class="feature-link">Buka Admin <i class="bi bi-arrow-right"></i></span>
                        </a>
                    @endif
                </div>
            </div>
        </section>

    @else
        <!-- ===== GUEST HERO ===== -->
        <section class="hero-section" style="padding: 5rem 0 3rem;">
            <div class="container hero-content">
                <div class="hero-badge fade-up">
                    <span class="dot"></span>
                    #SmartFarming
                </div>
                <h1 class="hero-title fade-up">
                    Monitoring Pertanian<br><span class="highlight">Cerdas Berbasis</span>
                </h1>
                <p class="hero-subtitle fade-up">
                    {{ env('APP_NAME', 'Swaratani') }} menghadirkan solusi monitoring dan automasi pertanian terintegrasi untuk mendorong produktivitas dan efisiensi budidaya tanaman Anda.
                </p>
                <div class="hero-actions fade-up">
                    <a href="{{ route('login') }}" class="btn-hero-primary">
                        <i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang
                    </a>
                    <a href="{{ route('register') }}" class="btn-hero-secondary">
                        <i class="bi bi-person-plus"></i> Daftar Akun
                    </a>
                </div>
            </div>
        </section>

        <!-- ===== GUEST QUICK ACCESS ===== -->
        <section class="quick-access">
            <div class="container">
                <div class="quick-grid">
                    <a href="{{ route('login') }}" class="quick-card fade-up">
                        <div class="quick-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <i class="bi bi-box-arrow-in-right"></i>
                        </div>
                        <h3>Login</h3>
                        <span class="quick-arrow">Masuk <i class="bi bi-arrow-right"></i></span>
                    </a>
                    <a href="{{ route('register') }}" class="quick-card fade-up">
                        <div class="quick-icon" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h3>Daftar Akun</h3>
                        <span class="quick-arrow">Buat Akun <i class="bi bi-arrow-right"></i></span>
                    </a>
                </div>
            </div>
        </section>
    @endauth

    <!-- ===== FOOTER ===== -->
    <footer class="page-footer">
        <p>&copy; {{ date('Y') }} <a href="{{ route('home') }}">{{ env('APP_NAME', 'Swaratani') }}</a> &bull; Tim Engineering Pertanian</p>
    </footer>

    @include('partials.chatbot')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => { });
        }
    </script>
</body>

</html>
