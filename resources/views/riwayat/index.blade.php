<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Riwayat Aktivitas - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @include('partials.theme')

    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            box-sizing: border-box;
        }

        body {
            color: var(--text-main);
            background-color: var(--bg-body, #f8fafc);
            -webkit-font-smoothing: antialiased;
        }

        /* Centered Compact Feed Container */
        .riwayat-container {
            max-width: 860px;
            margin: 0 auto;
            padding: 1.25rem 1rem 3rem 1rem;
        }

        /* Sleek Minimal Header */
        .header-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .header-title-wrap {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .btn-back-circle {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.2s ease;
            backdrop-filter: blur(10px);
            flex-shrink: 0;
        }

        .btn-back-circle:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: rgba(14, 165, 233, 0.08);
            transform: translateX(-2px);
        }

        .header-heading {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            margin: 0;
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .header-sub {
            font-size: 0.76rem;
            color: var(--text-secondary);
            margin: 0.15rem 0 0 0;
        }

        .live-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.65rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .live-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #10b981;
            box-shadow: 0 0 6px #10b981;
            animation: pulse-mini 2s infinite;
        }

        @keyframes pulse-mini {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(0.85); }
        }

        /* Compact Metrics Ribbon */
        .metrics-ribbon {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .metric-cell {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 0.55rem 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.55rem;
            backdrop-filter: blur(12px);
            transition: border-color 0.2s ease;
        }

        .metric-cell:hover {
            border-color: rgba(14, 165, 233, 0.3);
        }

        .metric-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .metric-content {
            min-width: 0;
            line-height: 1.15;
        }

        .metric-val {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-main);
            letter-spacing: -0.01em;
        }

        .metric-lbl {
            font-size: 0.66rem;
            font-weight: 500;
            color: var(--text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Category Filter Tabs (Scrollable Segmented) */
        .category-tabs-bar {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding-bottom: 0.25rem;
            margin-bottom: 0.75rem;
        }

        .category-tabs-bar::-webkit-scrollbar {
            display: none;
        }

        .category-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.32rem 0.7rem;
            border-radius: 20px;
            font-size: 0.74rem;
            font-weight: 600;
            white-space: nowrap;
            text-decoration: none;
            color: var(--text-secondary);
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            transition: all 0.2s ease;
            backdrop-filter: blur(8px);
        }

        .category-tab-btn:hover {
            color: var(--primary);
            border-color: rgba(14, 165, 233, 0.3);
            background: rgba(14, 165, 233, 0.05);
        }

        .category-tab-btn.active {
            background: var(--primary-gradient);
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.25);
        }

        /* Filter & Search Bar */
        .filter-bar {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 0.6rem 0.75rem;
            margin-bottom: 1.25rem;
            backdrop-filter: blur(12px);
        }

        .search-field-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-field-wrap i.bi-search {
            position: absolute;
            left: 0.75rem;
            color: var(--text-secondary);
            font-size: 0.8rem;
            pointer-events: none;
        }

        .form-control-compact,
        .form-select-compact {
            height: 34px;
            font-size: 0.78rem;
            border-radius: 8px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            transition: all 0.2s ease;
        }

        .form-control-compact {
            padding-left: 2.1rem;
            padding-right: 0.75rem;
        }

        .form-select-compact {
            padding-left: 0.65rem;
            padding-right: 1.8rem;
        }

        .form-control-compact:focus,
        .form-select-compact:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.15);
            background: var(--glass-bg);
            color: var(--text-main);
            outline: none;
        }

        .form-select-compact option {
            background: #ffffff;
            color: #1e293b;
        }

        .btn-compact-filter {
            height: 34px;
            padding: 0 0.85rem;
            font-size: 0.78rem;
            font-weight: 600;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .btn-compact-primary {
            background: var(--primary-gradient);
            color: #ffffff;
            border: none;
        }

        .btn-compact-primary:hover {
            opacity: 0.92;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-compact-reset {
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
        }

        .btn-compact-reset:hover {
            color: #ef4444;
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.05);
        }

        /* Activity Date Group & List Card */
        .date-section {
            margin-bottom: 1.25rem;
        }

        .date-section-header {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 0.45rem;
            padding-left: 0.25rem;
        }

        .date-section-header i {
            font-size: 0.75rem;
            color: var(--primary);
        }

        .activity-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 14px;
            overflow: hidden;
            backdrop-filter: blur(16px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        /* Activity Row Item */
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.75rem 0.9rem;
            border-bottom: 1px solid var(--glass-border);
            transition: background 0.18s ease;
            position: relative;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item:hover {
            background: rgba(14, 165, 233, 0.035);
        }

        /* Mini Action Icon Box */
        .action-icon-mini {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* Action Icon Color Themes */
        .icon-on {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
        }

        .icon-off {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .icon-pump {
            background: rgba(2, 132, 199, 0.12);
            color: #0284c7;
        }

        .icon-irrigation {
            background: rgba(13, 148, 136, 0.12);
            color: #0d9488;
        }

        .icon-dosing {
            background: rgba(147, 51, 234, 0.12);
            color: #7c3aed;
        }

        .icon-device {
            background: rgba(245, 158, 11, 0.12);
            color: #d97706;
        }

        .icon-system {
            background: rgba(99, 102, 241, 0.12);
            color: #4f46e5;
        }

        /* Activity Row Details */
        .activity-body {
            flex: 1;
            min-width: 0;
        }

        .activity-topline {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            gap: 0.5rem;
            flex-wrap: nowrap;
        }

        .activity-desc {
            font-size: 0.83rem;
            font-weight: 600;
            color: var(--text-main);
            margin: 0;
            line-height: 1.35;
            word-break: break-word;
        }

        .activity-time-stamp {
            font-size: 0.68rem;
            font-weight: 500;
            color: var(--text-secondary);
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* Activity Metadata Badges Line */
        .activity-meta-line {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: wrap;
            margin-top: 0.3rem;
        }

        .micro-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.15rem 0.45rem;
            border-radius: 4px;
            font-size: 0.68rem;
            font-weight: 600;
            line-height: 1.2;
            letter-spacing: 0.01em;
        }

        .micro-tag-on {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
        }

        .micro-tag-off {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        .micro-tag-target {
            background: rgba(14, 165, 233, 0.1);
            color: #0284c7;
        }

        .micro-tag-param {
            background: rgba(147, 51, 234, 0.1);
            color: #7c3aed;
        }

        .micro-tag-device {
            background: rgba(245, 158, 11, 0.1);
            color: #b45309;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .micro-tag-device:hover {
            background: rgba(245, 158, 11, 0.18);
            color: #92400e;
        }

        .micro-tag-neutral {
            background: rgba(100, 116, 139, 0.08);
            color: var(--text-secondary);
        }

        .btn-mini-goto {
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            font-size: 0.67rem;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
            margin-left: auto;
            padding: 0.15rem 0.4rem;
            border-radius: 4px;
            transition: background 0.15s ease;
        }

        .btn-mini-goto:hover {
            background: rgba(14, 165, 233, 0.08);
            color: var(--primary);
        }

        /* Empty State */
        .empty-compact {
            text-align: center;
            padding: 2.75rem 1rem;
            background: var(--glass-bg);
            border: 1px dashed var(--glass-border);
            border-radius: 14px;
            backdrop-filter: blur(12px);
        }

        .empty-compact i {
            font-size: 2rem;
            color: var(--text-secondary);
            opacity: 0.4;
            display: block;
            margin-bottom: 0.5rem;
        }

        .empty-compact-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.25rem;
        }

        .empty-compact-sub {
            font-size: 0.78rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
            max-width: 320px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Compact Pagination */
        .pagination-bar {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
        }

        .pagination-bar .pagination {
            gap: 0.25rem;
            margin: 0;
        }

        .pagination-bar .page-link {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            padding: 0.35rem 0.65rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            backdrop-filter: blur(6px);
        }

        .pagination-bar .page-link:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: rgba(14, 165, 233, 0.08);
        }

        .pagination-bar .page-item.active .page-link {
            background: var(--primary-gradient);
            color: #ffffff;
            border-color: transparent;
        }

        .pagination-bar .page-item.disabled .page-link {
            opacity: 0.4;
        }

        /* Mobile specific adjustments */
        @media (max-width: 640px) {
            .riwayat-container {
                padding: 0.75rem 0.65rem 2.5rem 0.65rem;
            }

            .metrics-ribbon {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.4rem;
            }

            .metric-cell {
                padding: 0.45rem 0.6rem;
            }

            .metric-val {
                font-size: 0.88rem;
            }

            .metric-lbl {
                font-size: 0.62rem;
            }

            .activity-item {
                padding: 0.65rem 0.75rem;
                gap: 0.65rem;
            }

            .action-icon-mini {
                width: 28px;
                height: 28px;
                font-size: 0.82rem;
                border-radius: 6px;
            }

            .activity-desc {
                font-size: 0.79rem;
            }

            .activity-time-stamp {
                font-size: 0.64rem;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <!-- Navbar Global -->
    @include('partials.navbar')

    <div class="riwayat-container">
        <!-- Sleek Minimal Header -->
        <div class="header-bar">
            <div class="header-title-wrap">
                <a href="{{ route('monitoring.index') }}" class="btn-back-circle" title="Kembali ke Monitoring">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 class="header-heading">Riwayat Aktivitas</h1>
                    <p class="header-sub">Rekam jejak kontrol perangkat & sistem IoT</p>
                </div>
            </div>
            <div class="live-tag">
                <span class="live-dot"></span>
                <span>Realtime Log</span>
            </div>
        </div>

        <!-- Compact Metrics Ribbon -->
        <div class="metrics-ribbon">
            <div class="metric-cell">
                <span class="metric-dot" style="background-color: #0284c7;"></span>
                <div class="metric-content">
                    <div class="metric-val">{{ number_format($stats['total']) }}</div>
                    <div class="metric-lbl">Total Log</div>
                </div>
            </div>
            <div class="metric-cell">
                <span class="metric-dot" style="background-color: #10b981;"></span>
                <div class="metric-content">
                    <div class="metric-val">{{ number_format($stats['today']) }}</div>
                    <div class="metric-lbl">Hari Ini</div>
                </div>
            </div>
            <div class="metric-cell">
                <span class="metric-dot" style="background-color: #06b6d4;"></span>
                <div class="metric-content">
                    <div class="metric-val">{{ number_format($stats['control']) }}</div>
                    <div class="metric-lbl">Kontrol Output</div>
                </div>
            </div>
            <div class="metric-cell">
                <span class="metric-dot" style="background-color: #8b5cf6;"></span>
                <div class="metric-content">
                    <div class="metric-val">{{ number_format($stats['pump_dosing']) }}</div>
                    <div class="metric-lbl">Pompa & Dosing</div>
                </div>
            </div>
        </div>

        <!-- Quick Category Tabs (Horizontal Scrollable) -->
        @php
            $activeCat = request('category', 'all');
            $baseParams = request()->except(['category', 'page']);
        @endphp
        <div class="category-tabs-bar">
            <a href="{{ route('riwayat.index', array_merge($baseParams, ['category' => 'all'])) }}"
               class="category-tab-btn {{ $activeCat === 'all' ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> Semua
            </a>
            <a href="{{ route('riwayat.index', array_merge($baseParams, ['category' => 'control'])) }}"
               class="category-tab-btn {{ $activeCat === 'control' ? 'active' : '' }}">
                <i class="bi bi-toggle2-on"></i> Kontrol Output
            </a>
            <a href="{{ route('riwayat.index', array_merge($baseParams, ['category' => 'pump'])) }}"
               class="category-tab-btn {{ $activeCat === 'pump' ? 'active' : '' }}">
                <i class="bi bi-droplet-fill"></i> Pompa & Irigasi
            </a>
            <a href="{{ route('riwayat.index', array_merge($baseParams, ['category' => 'dosing'])) }}"
               class="category-tab-btn {{ $activeCat === 'dosing' ? 'active' : '' }}">
                <i class="bi bi-funnel-fill"></i> Dosing
            </a>
            <a href="{{ route('riwayat.index', array_merge($baseParams, ['category' => 'device'])) }}"
               class="category-tab-btn {{ $activeCat === 'device' ? 'active' : '' }}">
                <i class="bi bi-cpu-fill"></i> Perangkat
            </a>
            <a href="{{ route('riwayat.index', array_merge($baseParams, ['category' => 'account'])) }}"
               class="category-tab-btn {{ $activeCat === 'account' ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i> Sistem & Akun
            </a>
        </div>

        <!-- Compact Search & Filter Toolbar -->
        <div class="filter-bar">
            <form action="{{ route('riwayat.index') }}" method="GET">
                <input type="hidden" name="category" value="{{ request('category', 'all') }}">

                <div class="row g-2 align-items-center">
                    <!-- Search Input -->
                    <div class="col-12 col-md-5">
                        <div class="search-field-wrap">
                            <i class="bi bi-search"></i>
                            <input type="text" name="q" value="{{ request('q') }}"
                                   class="form-control form-control-compact"
                                   placeholder="Cari aktivitas, nama output, target...">
                        </div>
                    </div>

                    <!-- Device Selector -->
                    <div class="col-6 col-md-3">
                        <select name="device_id" class="form-select form-select-compact">
                            <option value="">Semua Alat</option>
                            @foreach($userDevices as $ud)
                                @php
                                    $dev = $ud->device;
                                    $devName = $ud->custom_name ?: ($dev ? $dev->name : 'Perangkat #'.$ud->device_id);
                                @endphp
                                <option value="{{ $ud->device_id }}" {{ request('device_id') == $ud->device_id ? 'selected' : '' }}>
                                    {{ Str::limit($devName, 20) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="col-6 col-md-2">
                        <select name="date_range" class="form-select form-select-compact">
                            <option value="all" {{ request('date_range') == 'all' ? 'selected' : '' }}>Semua Waktu</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="7days" {{ request('date_range') == '7days' ? 'selected' : '' }}>7 Hari</option>
                            <option value="30days" {{ request('date_range') == '30days' ? 'selected' : '' }}>30 Hari</option>
                        </select>
                    </div>

                    <!-- Submit & Reset Buttons -->
                    <div class="col-12 col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-compact-filter btn-compact-primary flex-grow-1">
                            <i class="bi bi-funnel-fill"></i> Filter
                        </button>
                        @if(request()->hasAny(['q', 'device_id', 'date_range', 'category']))
                            <a href="{{ route('riwayat.index') }}" class="btn btn-compact-filter btn-compact-reset" title="Reset filter">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Activity Feed List -->
        @if($logs->count() > 0)
            @php
                $groupedLogs = $logs->groupBy(function($item) {
                    return $item->created_at->format('Y-m-d');
                });
            @endphp

            @foreach($groupedLogs as $dateKey => $dayLogs)
                @php
                    $firstDate = $dayLogs->first()->created_at;
                    if ($firstDate->isToday()) {
                        $dateLabel = 'Hari Ini — ' . $firstDate->isoFormat('D MMMM Y');
                    } elseif ($firstDate->isYesterday()) {
                        $dateLabel = 'Kemarin — ' . $firstDate->isoFormat('D MMMM Y');
                    } else {
                        $dateLabel = $firstDate->isoFormat('dddd, D MMMM Y');
                    }
                @endphp

                <div class="date-section">
                    <div class="date-section-header">
                        <i class="bi bi-calendar2-week"></i>
                        <span>{{ $dateLabel }}</span>
                    </div>

                    <div class="activity-card">
                        @foreach($dayLogs as $log)
                            @php
                                $details = $log->details ?? [];
                                $action = $log->action;

                                // Deteksi Status ON / OFF
                                $isTurnOn = false;
                                $isTurnOff = false;
                                $statusLabel = null;

                                if (isset($details['new_value'])) {
                                    $val = $details['new_value'];
                                    if ($val == 1 || $val === '1' || $val === true || strtolower((string)$val) === 'on') {
                                        $isTurnOn = true;
                                        $statusLabel = 'ON';
                                    } else {
                                        $isTurnOff = true;
                                        $statusLabel = 'OFF';
                                    }
                                } elseif (isset($details['action_type'])) {
                                    if ($details['action_type'] === 'on') {
                                        $isTurnOn = true;
                                        $statusLabel = 'ON';
                                    } else {
                                        $isTurnOff = true;
                                        $statusLabel = 'OFF';
                                    }
                                } elseif (isset($details['turn_on'])) {
                                    if ($details['turn_on']) {
                                        $isTurnOn = true;
                                        $statusLabel = 'ON';
                                    } else {
                                        $isTurnOff = true;
                                        $statusLabel = 'OFF';
                                    }
                                }

                                // Visual Icon Styling
                                $iconClass = 'icon-system';
                                $biIcon = 'bi-activity';

                                if ($action === 'device_control') {
                                    if ($isTurnOn) {
                                        $iconClass = 'icon-on';
                                        $biIcon = 'bi-power';
                                    } else {
                                        $iconClass = 'icon-off';
                                        $biIcon = 'bi-power';
                                    }
                                } elseif ($action === 'pump_control') {
                                    $iconClass = 'icon-pump';
                                    $biIcon = 'bi-fan';
                                } elseif ($action === 'irrigation_control') {
                                    $iconClass = 'icon-irrigation';
                                    $biIcon = 'bi-droplet-half';
                                } elseif ($action === 'dosing_control') {
                                    $iconClass = 'icon-dosing';
                                    $biIcon = 'bi-funnel-fill';
                                } elseif (in_array($action, ['add_device', 'update_device', 'remove_device'])) {
                                    $iconClass = 'icon-device';
                                    $biIcon = 'bi-cpu';
                                } elseif (in_array($action, ['login', 'logout', 'profile_update', 'password_change'])) {
                                    $iconClass = 'icon-system';
                                    $biIcon = 'bi-shield-check';
                                }

                                $deviceId = $details['device_id'] ?? null;
                            @endphp

                            <div class="activity-item">
                                <!-- Mini Icon Indicator -->
                                <div class="action-icon-mini {{ $iconClass }}">
                                    <i class="bi {{ $biIcon }}"></i>
                                </div>

                                <!-- Activity Content -->
                                <div class="activity-body">
                                    <div class="activity-topline">
                                        <div class="activity-desc">
                                            {{ $log->description }}
                                        </div>
                                        <div class="activity-time-stamp" title="{{ $log->created_at->format('d M Y, H:i:s') }} WIB">
                                            {{ $log->created_at->format('H:i') }} • {{ $log->created_at->diffForHumans(null, true) }}
                                        </div>
                                    </div>

                                    <!-- Micro Metadata Badges -->
                                    <div class="activity-meta-line">
                                        {{-- Status ON/OFF --}}
                                        @if($statusLabel)
                                            <span class="micro-tag {{ $isTurnOn ? 'micro-tag-on' : 'micro-tag-off' }}">
                                                <i class="bi {{ $isTurnOn ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                {{ $statusLabel }}
                                            </span>
                                        @endif

                                        {{-- Target Output --}}
                                        @if(isset($details['output_name']) || isset($details['target']))
                                            <span class="micro-tag micro-tag-target">
                                                <i class="bi bi-tag-fill"></i>
                                                {{ strtoupper($details['output_name'] ?? $details['target']) }}
                                            </span>
                                        @endif

                                        {{-- Dosing Volume --}}
                                        @if(isset($details['volume']))
                                            <span class="micro-tag micro-tag-param">
                                                <i class="bi bi-cup-straw"></i> {{ $details['volume'] }} mL
                                            </span>
                                        @endif

                                        {{-- Pump Type --}}
                                        @if(isset($details['pump_type']))
                                            @php
                                                $pLabels = ['dosing' => 'Dosing AB', 'ph_up' => 'pH Up', 'ph_down' => 'pH Down'];
                                            @endphp
                                            <span class="micro-tag micro-tag-param">
                                                {{ $pLabels[$details['pump_type']] ?? ucfirst($details['pump_type']) }}
                                            </span>
                                        @endif

                                        {{-- Irrigation Zone --}}
                                        @if(isset($details['zone']) && $details['zone'])
                                            <span class="micro-tag micro-tag-neutral">
                                                Zona {{ $details['zone'] }}
                                            </span>
                                        @endif

                                        {{-- Water Type --}}
                                        @if(isset($details['water_type']) && $details['water_type'])
                                            <span class="micro-tag micro-tag-neutral">
                                                Air {{ ucfirst($details['water_type']) }}
                                            </span>
                                        @endif

                                        {{-- Direct Device Link --}}
                                        @if($deviceId)
                                            <a href="{{ route('monitoring.show', $deviceId) }}" class="btn-mini-goto">
                                                <span>Monitoring</span>
                                                <i class="bi bi-chevron-right"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Compact Pagination -->
            <div class="pagination-bar">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>

        @else
            <!-- Elegant Compact Empty State -->
            <div class="empty-compact">
                <i class="bi bi-clock-history"></i>
                <div class="empty-compact-title">Tidak Ada Aktivitas Ditemukan</div>
                <div class="empty-compact-sub">
                    @if(request()->hasAny(['q', 'device_id', 'date_range', 'category']))
                        Filter yang diterapkan tidak menghasilkan data riwayat apapun.
                    @else
                        Belum ada rekaman log aktivitas kontrol yang tersimpan.
                    @endif
                </div>
                @if(request()->hasAny(['q', 'device_id', 'date_range', 'category']))
                    <a href="{{ route('riwayat.index') }}" class="btn btn-compact-filter btn-compact-primary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset Semua Filter
                    </a>
                @else
                    <a href="{{ route('monitoring.index') }}" class="btn btn-compact-filter btn-compact-primary">
                        <i class="bi bi-speedometer2"></i> Menuju Monitoring
                    </a>
                @endif
            </div>
        @endif
    </div>

    @include('partials.pwa-scripts')
</body>

</html>
