<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Aktivitas - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @include('partials.theme')

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        /* Glassmorphism Panel Base */
        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04);
            position: relative;
            overflow: hidden;
        }

        /* Header Hero Section */
        .hero-banner {
            padding: 1.75rem 2rem;
            margin-bottom: 2rem;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.02) 100%), var(--glass-bg);
            position: relative;
        }

        .hero-banner::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 180px;
            height: 180px;
            background: var(--glow-1);
            filter: blur(50px);
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .hero-banner .hero-content {
            position: relative;
            z-index: 1;
        }

        .btn-nav-back {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 1rem;
            border-radius: 50px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .btn-nav-back:hover {
            color: var(--primary);
            border-color: var(--primary);
            transform: translateX(-3px);
            background: var(--glass-bg);
        }

        .badge-live-pulse {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .pulse-dot-wrap {
            position: relative;
            width: 8px;
            height: 8px;
            display: inline-block;
        }

        .pulse-core {
            position: absolute;
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
        }

        .pulse-ring {
            position: absolute;
            width: 16px;
            height: 16px;
            top: -4px;
            left: -4px;
            background-color: rgba(16, 185, 129, 0.4);
            border-radius: 50%;
            animation: pulse-ring-anim 2s cubic-bezier(0.24, 0, 0.38, 1) infinite;
        }

        @keyframes pulse-ring-anim {
            0% { transform: scale(0.6); opacity: 0.9; }
            100% { transform: scale(1.8); opacity: 0; }
        }

        .page-title {
            color: var(--text-main);
            font-weight: 800;
            font-size: 1.85rem;
            letter-spacing: -0.5px;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .page-title-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--primary-gradient);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            box-shadow: 0 8px 16px var(--glow-1);
            flex-shrink: 0;
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 0.92rem;
            margin-top: 0.35rem;
            margin-bottom: 0;
            max-width: 600px;
        }

        /* Stat Highlight Cards */
        .stat-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 18px;
            padding: 1.25rem 1.4rem;
            backdrop-filter: blur(20px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 1.1rem;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
        }

        .stat-card:hover {
            transform: translateY(-3px);
            border-color: rgba(14, 165, 233, 0.4);
            box-shadow: 0 12px 30px -5px rgba(0, 0, 0, 0.08);
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: 80px;
            background: radial-gradient(circle, var(--stat-glow, rgba(14, 165, 233, 0.15)) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .stat-icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: #ffffff;
            flex-shrink: 0;
            box-shadow: 0 8px 16px -2px rgba(0, 0, 0, 0.15);
        }

        .stat-number {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .stat-label {
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* Filter Toolbar */
        .filter-toolbar {
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
        }

        .category-pills-scroll {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            scrollbar-width: thin;
            -webkit-overflow-scrolling: touch;
        }

        .category-pills-scroll::-webkit-scrollbar {
            height: 4px;
        }

        .category-pills-scroll::-webkit-scrollbar-thumb {
            background: var(--glass-border);
            border-radius: 4px;
        }

        .btn-filter-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .btn-filter-pill:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: rgba(14, 165, 233, 0.06);
        }

        .btn-filter-pill.active {
            background: var(--primary-gradient);
            color: #ffffff !important;
            border-color: transparent;
            box-shadow: 0 4px 14px var(--glow-1);
        }

        .search-input-wrap {
            position: relative;
        }

        .search-input-wrap i.bi-search {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 0.95rem;
            pointer-events: none;
        }

        .form-control-modern, .form-select-modern {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            border-radius: 12px;
            padding: 0.6rem 1rem 0.6rem 2.6rem;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all 0.25s ease;
            backdrop-filter: blur(10px);
        }

        .form-select-modern {
            padding-left: 1rem;
        }

        .form-control-modern:focus, .form-select-modern:focus {
            background: var(--glass-bg);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--glow-1);
            color: var(--text-main);
            outline: none;
        }

        .form-control-modern::placeholder {
            color: var(--text-secondary);
            opacity: 0.7;
        }

        .btn-action-filter {
            padding: 0.6rem 1.25rem;
            border-radius: 12px;
            font-size: 0.88rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.25s ease;
            white-space: nowrap;
        }

        .btn-filter-submit {
            background: var(--primary-gradient);
            border: none;
            color: #ffffff;
            box-shadow: 0 4px 12px var(--glow-1);
        }

        .btn-filter-submit:hover {
            opacity: 0.95;
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-filter-reset {
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
        }

        .btn-filter-reset:hover {
            color: #ef4444;
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.05);
        }

        /* Timeline Feed */
        .timeline-container {
            position: relative;
        }

        .timeline-date-divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 2rem 0 1.25rem 0;
        }

        .timeline-date-divider::before,
        .timeline-date-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--glass-border), transparent);
        }

        .timeline-date-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1.1rem;
            border-radius: 50px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            backdrop-filter: blur(15px);
        }

        .timeline-feed {
            position: relative;
            padding-left: 1.5rem;
        }

        .timeline-feed::before {
            content: '';
            position: absolute;
            top: 15px;
            bottom: 15px;
            left: 27px;
            width: 2px;
            background: linear-gradient(180deg, var(--primary) 0%, rgba(14, 165, 233, 0.2) 100%);
            border-radius: 2px;
            z-index: 0;
        }

        @media (max-width: 767.98px) {
            .timeline-feed {
                padding-left: 0;
            }
            .timeline-feed::before {
                display: none;
            }
        }

        /* Timeline Log Card */
        .log-item {
            position: relative;
            z-index: 1;
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 1.25rem;
        }

        .log-avatar {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #ffffff;
            flex-shrink: 0;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            position: relative;
            transition: all 0.3s ease;
        }

        @media (max-width: 767.98px) {
            .log-avatar {
                display: none;
            }
        }

        .log-card {
            flex-grow: 1;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 1.15rem 1.4rem;
            backdrop-filter: blur(20px);
            transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
            position: relative;
        }

        .log-card:hover {
            transform: translateY(-2px);
            border-color: rgba(14, 165, 233, 0.4);
            box-shadow: 0 10px 28px -4px rgba(14, 165, 233, 0.12);
        }

        .log-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 0.6rem;
        }

        .log-title {
            color: var(--text-main);
            font-weight: 700;
            font-size: 1rem;
            line-height: 1.4;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            flex-wrap: wrap;
        }

        .log-time-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-secondary);
            background: rgba(100, 116, 139, 0.08);
            border: 1px solid var(--glass-border);
            padding: 0.25rem 0.65rem;
            border-radius: 8px;
            white-space: nowrap;
        }

        .badges-flow {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.6rem;
        }

        .pill-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.28rem 0.75rem;
            border-radius: 8px;
            font-size: 0.76rem;
            font-weight: 600;
            letter-spacing: 0.2px;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        /* Specific Status Chip Colors */
        .chip-on {
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            border-color: rgba(16, 185, 129, 0.25);
        }

        .chip-off {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.25);
        }

        .chip-target {
            background: rgba(14, 165, 233, 0.12);
            color: #0ea5e9;
            border-color: rgba(14, 165, 233, 0.25);
        }

        .chip-param {
            background: rgba(168, 85, 247, 0.12);
            color: #a855f7;
            border-color: rgba(168, 85, 247, 0.25);
        }

        .chip-neutral {
            background: rgba(100, 116, 139, 0.1);
            color: var(--text-secondary);
            border-color: var(--glass-border);
        }

        .chip-device {
            background: rgba(245, 158, 11, 0.12);
            color: #d97706;
            border-color: rgba(245, 158, 11, 0.25);
            text-decoration: none;
        }

        .chip-device:hover {
            background: rgba(245, 158, 11, 0.2);
            color: #b45309;
            transform: translateY(-1px);
        }

        /* Action Link */
        .btn-link-device {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            margin-left: auto;
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .btn-link-device:hover {
            color: var(--primary);
            background: rgba(14, 165, 233, 0.08);
            transform: translateX(2px);
        }

        /* Empty State */
        .empty-state-panel {
            text-align: center;
            padding: 4.5rem 1.5rem;
            margin: 2rem 0;
            border: 2px dashed var(--glass-border);
            border-radius: 24px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
        }

        .empty-icon-box {
            width: 80px;
            height: 80px;
            border-radius: 22px;
            background: var(--glow-1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 10px 25px var(--glow-1);
            animation: float-soft 3s ease-in-out infinite;
        }

        @keyframes float-soft {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .empty-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .empty-desc {
            color: var(--text-secondary);
            font-size: 0.9rem;
            max-width: 420px;
            margin: 0 auto 1.5rem auto;
            line-height: 1.5;
        }

        /* Pagination Glass */
        .pagination-wrap {
            display: flex;
            justify-content: center;
            margin-top: 2.5rem;
        }

        .pagination {
            gap: 0.35rem;
            margin: 0;
        }

        .pagination .page-link {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            padding: 0.55rem 0.95rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.88rem;
            backdrop-filter: blur(10px);
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: rgba(14, 165, 233, 0.08);
            transform: translateY(-1px);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-gradient);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 4px 12px var(--glow-1);
        }

        .pagination .page-item.disabled .page-link {
            background: rgba(100, 116, 139, 0.05);
            color: var(--text-secondary);
            opacity: 0.5;
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <!-- Navbar Global -->
    @include('partials.navbar')

    <div class="container py-4">
        <!-- Hero Header -->
        <div class="glass-panel hero-banner">
            <div class="hero-content">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <a href="{{ route('monitoring.index') }}" class="btn-nav-back">
                        <i class="bi bi-arrow-left"></i> Kembali ke Monitoring
                    </a>
                    <div class="badge-live-pulse">
                        <span class="pulse-dot-wrap">
                            <span class="pulse-ring"></span>
                            <span class="pulse-core"></span>
                        </span>
                        <span>Sinkronisasi Otomatis</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="page-title-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <div>
                        <h1 class="page-title">Riwayat Aktivitas</h1>
                        <p class="page-subtitle">
                            Rekam jejak komprehensif kontrol saklar, irigasi, pompa nutrisi, dan aktivitas sistem IoT Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 KPI Stat Highlight Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card" style="--stat-glow: rgba(2, 132, 199, 0.2);">
                    <div class="stat-icon-wrap" style="background: linear-gradient(135deg, #0284c7, #38bdf8);">
                        <i class="bi bi-activity"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ number_format($stats['total']) }}</div>
                        <div class="stat-label">Total Aktivitas</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card" style="--stat-glow: rgba(16, 185, 129, 0.2);">
                    <div class="stat-icon-wrap" style="background: linear-gradient(135deg, #059669, #10b981);">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ number_format($stats['today']) }}</div>
                        <div class="stat-label">Aktivitas Hari Ini</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card" style="--stat-glow: rgba(79, 70, 229, 0.2);">
                    <div class="stat-icon-wrap" style="background: linear-gradient(135deg, #4f46e5, #06b6d4);">
                        <i class="bi bi-toggle2-on"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ number_format($stats['control']) }}</div>
                        <div class="stat-label">Kontrol Output</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card" style="--stat-glow: rgba(168, 85, 247, 0.2);">
                    <div class="stat-icon-wrap" style="background: linear-gradient(135deg, #8b5cf6, #ec4899);">
                        <i class="bi bi-droplet-half"></i>
                    </div>
                    <div>
                        <div class="stat-number">{{ number_format($stats['pump_dosing']) }}</div>
                        <div class="stat-label">Pompa & Dosing</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="glass-panel filter-toolbar">
            <!-- Category Pills -->
            <div class="category-pills-scroll mb-3">
                @php
                    $activeCat = request('category', 'all');
                    $baseParams = request()->except(['category', 'page']);
                @endphp
                <a href="{{ route('riwayat.index', array_merge($baseParams, ['category' => 'all'])) }}" 
                   class="btn-filter-pill {{ $activeCat === 'all' ? 'active' : '' }}">
                    <i class="bi bi-grid-fill"></i> Semua Aktivitas
                </a>
                <a href="{{ route('riwayat.index', array_merge($baseParams, ['category' => 'control'])) }}" 
                   class="btn-filter-pill {{ $activeCat === 'control' ? 'active' : '' }}">
                    <i class="bi bi-toggle2-on"></i> Kontrol Output
                </a>
                <a href="{{ route('riwayat.index', array_merge($baseParams, ['category' => 'pump'])) }}" 
                   class="btn-filter-pill {{ $activeCat === 'pump' ? 'active' : '' }}">
                    <i class="bi bi-droplet-fill"></i> Pompa & Irigasi
                </a>
                <a href="{{ route('riwayat.index', array_merge($baseParams, ['category' => 'dosing'])) }}" 
                   class="btn-filter-pill {{ $activeCat === 'dosing' ? 'active' : '' }}">
                    <i class="bi bi-funnel-fill"></i> Dosing Nutrisi
                </a>
                <a href="{{ route('riwayat.index', array_merge($baseParams, ['category' => 'device'])) }}" 
                   class="btn-filter-pill {{ $activeCat === 'device' ? 'active' : '' }}">
                    <i class="bi bi-cpu-fill"></i> Kelola Perangkat
                </a>
                <a href="{{ route('riwayat.index', array_merge($baseParams, ['category' => 'account'])) }}" 
                   class="btn-filter-pill {{ $activeCat === 'account' ? 'active' : '' }}">
                    <i class="bi bi-shield-check"></i> Akun & Sistem
                </a>
            </div>

            <!-- Search Form Row -->
            <form action="{{ route('riwayat.index') }}" method="GET">
                <input type="hidden" name="category" value="{{ request('category', 'all') }}">
                
                <div class="row g-2 align-items-center">
                    <!-- Search Input -->
                    <div class="col-12 col-md-5">
                        <div class="search-input-wrap">
                            <i class="bi bi-search"></i>
                            <input type="text" name="q" value="{{ request('q') }}" 
                                   class="form-control form-control-modern" 
                                   placeholder="Cari nama output, perangkat, atau aksi...">
                        </div>
                    </div>

                    <!-- Device Selector -->
                    <div class="col-12 col-sm-6 col-md-3">
                        <select name="device_id" class="form-select form-select-modern">
                            <option value="">Semua Perangkat</option>
                            @foreach($userDevices as $ud)
                                @php
                                    $dev = $ud->device;
                                    $devName = $ud->custom_name ?: ($dev ? $dev->name : 'Perangkat #'.$ud->device_id);
                                @endphp
                                <option value="{{ $ud->device_id }}" {{ request('device_id') == $ud->device_id ? 'selected' : '' }}>
                                    {{ $devName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range Selector -->
                    <div class="col-12 col-sm-6 col-md-2">
                        <select name="date_range" class="form-select form-select-modern">
                            <option value="all" {{ request('date_range') == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="7days" {{ request('date_range') == '7days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                            <option value="30days" {{ request('date_range') == '30days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                        </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-action-filter btn-filter-submit flex-grow-1">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        @if(request()->hasAny(['q', 'device_id', 'date_range', 'category']))
                            <a href="{{ route('riwayat.index') }}" class="btn btn-action-filter btn-filter-reset" title="Reset Semua Filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Activity Timeline Feed -->
        @if($logs->count() > 0)
            @php
                // Kelompokkan log berdasarkan tanggal pembuatan
                $groupedLogs = $logs->groupBy(function($item) {
                    return $item->created_at->format('Y-m-d');
                });
            @endphp

            <div class="timeline-container">
                @foreach($groupedLogs as $dateKey => $dayLogs)
                    @php
                        $firstDate = $dayLogs->first()->created_at;
                        if ($firstDate->isToday()) {
                            $dateTitle = 'Hari Ini (' . $firstDate->isoFormat('D MMMM Y') . ')';
                        } elseif ($firstDate->isYesterday()) {
                            $dateTitle = 'Kemarin (' . $firstDate->isoFormat('D MMMM Y') . ')';
                        } else {
                            $dateTitle = $firstDate->isoFormat('dddd, D MMMM Y');
                        }
                    @endphp

                    <!-- Date Group Divider -->
                    <div class="timeline-date-divider">
                        <span class="timeline-date-pill">
                            <i class="bi bi-calendar3 text-primary"></i> {{ $dateTitle }}
                        </span>
                    </div>

                    <div class="timeline-feed">
                        @foreach($dayLogs as $log)
                            @php
                                $details = $log->details ?? [];
                                $action = $log->action;

                                // Deteksi Status ON/OFF
                                $isTurnOn = false;
                                $isTurnOff = false;
                                $statusLabel = null;

                                if (isset($details['new_value'])) {
                                    $val = $details['new_value'];
                                    if ($val == 1 || $val === '1' || $val === true || strtolower((string)$val) === 'on') {
                                        $isTurnOn = true;
                                        $statusLabel = 'ON (Aktif)';
                                    } else {
                                        $isTurnOff = true;
                                        $statusLabel = 'OFF (Mati)';
                                    }
                                } elseif (isset($details['action_type'])) {
                                    if ($details['action_type'] === 'on') {
                                        $isTurnOn = true;
                                        $statusLabel = 'ON (Aktif)';
                                    } else {
                                        $isTurnOff = true;
                                        $statusLabel = 'OFF (Mati)';
                                    }
                                } elseif (isset($details['turn_on'])) {
                                    if ($details['turn_on']) {
                                        $isTurnOn = true;
                                        $statusLabel = 'ON (Aktif)';
                                    } else {
                                        $isTurnOff = true;
                                        $statusLabel = 'OFF (Mati)';
                                    }
                                }

                                // Konfigurasi Visual Ikon & Gradien
                                $avatarBg = 'linear-gradient(135deg, #0284c7, #38bdf8)';
                                $avatarIcon = 'bi-sliders';

                                if ($action === 'device_control') {
                                    if ($isTurnOn) {
                                        $avatarBg = 'linear-gradient(135deg, #059669, #10b981)';
                                        $avatarIcon = 'bi-lightning-charge-fill';
                                    } else {
                                        $avatarBg = 'linear-gradient(135deg, #ef4444, #f87171)';
                                        $avatarIcon = 'bi-power';
                                    }
                                } elseif ($action === 'pump_control') {
                                    $avatarBg = 'linear-gradient(135deg, #0284c7, #06b6d4)';
                                    $avatarIcon = 'bi-fan';
                                } elseif ($action === 'irrigation_control') {
                                    $avatarBg = 'linear-gradient(135deg, #0d9488, #14b8a6)';
                                    $avatarIcon = 'bi-droplet-half';
                                } elseif ($action === 'dosing_control') {
                                    $avatarBg = 'linear-gradient(135deg, #8b5cf6, #ec4899)';
                                    $avatarIcon = 'bi-funnel-fill';
                                } elseif (in_array($action, ['add_device', 'update_device', 'remove_device'])) {
                                    $avatarBg = 'linear-gradient(135deg, #f59e0b, #d97706)';
                                    $avatarIcon = 'bi-cpu-fill';
                                } elseif (in_array($action, ['login', 'logout'])) {
                                    $avatarBg = 'linear-gradient(135deg, #6366f1, #3b82f6)';
                                    $avatarIcon = 'bi-shield-check';
                                }

                                $deviceId = $details['device_id'] ?? null;
                            @endphp

                            <div class="log-item">
                                <!-- Avatar Ikon Samping (Desktop) -->
                                <div class="log-avatar" style="background: {{ $avatarBg }};">
                                    <i class="bi {{ $avatarIcon }}"></i>
                                </div>

                                <!-- Kartu Log Konten -->
                                <div class="log-card">
                                    <div class="log-header">
                                        <div class="log-title">
                                            <span>{{ $log->description }}</span>
                                        </div>
                                        <div class="log-time-badge" title="{{ $log->created_at->format('d M Y, H:i:s') }} WIB">
                                            <i class="bi bi-clock"></i>
                                            <span>{{ $log->created_at->diffForHumans() }}</span>
                                            <span class="d-none d-md-inline opacity-75">({{ $log->created_at->format('H:i') }})</span>
                                        </div>
                                    </div>

                                    <!-- Detail Chips & Parameters -->
                                    <div class="badges-flow">
                                        {{-- Chip Status ON/OFF --}}
                                        @if($statusLabel)
                                            <span class="pill-chip {{ $isTurnOn ? 'chip-on' : 'chip-off' }}">
                                                <i class="bi {{ $isTurnOn ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                {{ $statusLabel }}
                                            </span>
                                        @endif

                                        {{-- Chip Target Output --}}
                                        @if(isset($details['output_name']) || isset($details['target']))
                                            <span class="pill-chip chip-target">
                                                <i class="bi bi-tag-fill"></i>
                                                {{ strtoupper($details['output_name'] ?? $details['target']) }}
                                            </span>
                                        @endif

                                        {{-- Chip Dosing Volume --}}
                                        @if(isset($details['volume']))
                                            <span class="pill-chip chip-param">
                                                <i class="bi bi-cup-straw"></i>
                                                {{ $details['volume'] }} mL
                                            </span>
                                        @endif

                                        {{-- Chip Pompa Nutrisi Type --}}
                                        @if(isset($details['pump_type']))
                                            @php
                                                $pLabels = ['dosing' => 'Dosing AB', 'ph_up' => 'pH Up', 'ph_down' => 'pH Down'];
                                            @endphp
                                            <span class="pill-chip chip-param">
                                                <i class="bi bi-eyedropper"></i>
                                                {{ $pLabels[$details['pump_type']] ?? ucfirst($details['pump_type']) }}
                                            </span>
                                        @endif

                                        {{-- Chip Zona Irigasi --}}
                                        @if(isset($details['zone']) && $details['zone'])
                                            <span class="pill-chip chip-neutral">
                                                <i class="bi bi-geo-alt-fill"></i>
                                                Zona {{ $details['zone'] }}
                                            </span>
                                        @endif

                                        {{-- Chip Water Type --}}
                                        @if(isset($details['water_type']) && $details['water_type'])
                                            <span class="pill-chip chip-neutral">
                                                <i class="bi bi-water"></i>
                                                Air: {{ ucfirst($details['water_type']) }}
                                            </span>
                                        @endif

                                        {{-- Tautan Langsung ke Halaman Device Monitoring --}}
                                        @if($deviceId)
                                            <a href="{{ route('monitoring.show', $deviceId) }}" class="btn-link-device">
                                                <span>Buka Monitoring</span>
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <!-- Glass Pagination -->
            <div class="pagination-wrap">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>

        @else
            <!-- Empty State -->
            <div class="empty-state-panel">
                <div class="empty-icon-box">
                    <i class="bi bi-inbox"></i>
                </div>
                <h4 class="empty-title">Tidak Ada Riwayat Ditemukan</h4>
                <p class="empty-desc">
                    @if(request()->hasAny(['q', 'device_id', 'date_range', 'category']))
                        Tidak ada aktivitas yang sesuai dengan filter pencarian Anda. Coba ubah kata kunci atau reset filter.
                    @else
                        Belum ada rekam jejak aktivitas kontrol yang tercatat pada akun Anda.
                    @endif
                </p>
                @if(request()->hasAny(['q', 'device_id', 'date_range', 'category']))
                    <a href="{{ route('riwayat.index') }}" class="btn btn-action-filter btn-filter-submit">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Semua Filter
                    </a>
                @else
                    <a href="{{ route('monitoring.index') }}" class="btn btn-action-filter btn-filter-submit">
                        <i class="bi bi-speedometer2"></i> Menuju Dashboard Monitoring
                    </a>
                @endif
            </div>
        @endif
    </div>

    @include('partials.pwa-scripts')
</body>
</html>
