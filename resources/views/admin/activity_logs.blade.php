<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activity Logs - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.theme')

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .page-title {
            color: var(--text-main);
            font-weight: 800;
        }

        .glass-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 18px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            overflow: hidden;
        }

        .table-dark-custom {
            background: rgba(14, 95, 138, 0.05) !important;
        }

        .table-dark-custom th {
            color: var(--text-main) !important;
            font-weight: 700;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--glass-border) !important;
            padding: 0.95rem 1rem;
            white-space: nowrap;
        }

        .table tbody td {
            color: var(--text-main);
            border-bottom: 1px solid var(--glass-border);
            padding: 0.9rem 1rem;
            vertical-align: middle;
            font-size: 0.86rem;
            background: transparent;
        }

        .table tbody tr:hover td {
            background: rgba(14, 165, 233, 0.04);
        }

        .badge-action {
            font-weight: 700;
            padding: 0.35rem 0.65rem;
            border-radius: 8px;
            font-size: 0.74rem;
            letter-spacing: 0.3px;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            text-transform: uppercase;
        }

        .badge-login {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        .badge-logout {
            background: rgba(100, 116, 139, 0.15);
            color: #64748b;
            border: 1px solid rgba(100, 116, 139, 0.25);
        }

        .badge-control {
            background: rgba(14, 165, 233, 0.15);
            color: #0284c7;
            border: 1px solid rgba(14, 165, 233, 0.25);
        }

        .badge-pump {
            background: rgba(6, 182, 212, 0.15);
            color: #0891b2;
            border: 1px solid rgba(6, 182, 212, 0.25);
        }

        .badge-dosing {
            background: rgba(168, 85, 247, 0.15);
            color: #9333ea;
            border: 1px solid rgba(168, 85, 247, 0.25);
        }

        .badge-device {
            background: rgba(245, 158, 11, 0.15);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .badge-default {
            background: rgba(100, 116, 139, 0.12);
            color: var(--text-secondary);
            border: 1px solid var(--glass-border);
        }

        .filter-form .form-control,
        .filter-form .form-select {
            font-size: 0.88rem;
            border-radius: 12px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            min-height: 42px;
            backdrop-filter: blur(10px);
        }

        .filter-form .form-control:focus,
        .filter-form .form-select:focus {
            background: var(--glass-bg);
            color: var(--text-main);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--glow-1);
        }

        .btn-gradient {
            background: var(--primary-gradient);
            color: #ffffff;
            border: none;
            box-shadow: 0 4px 12px var(--glow-1);
            font-weight: 600;
        }

        .btn-gradient:hover {
            color: #ffffff;
            opacity: 0.95;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .table thead {
                display: none;
            }

            .table tbody tr {
                display: block;
                background: var(--glass-bg);
                border: 1px solid var(--glass-border);
                border-radius: 14px;
                padding: 0.85rem;
                margin-bottom: 0.85rem;
            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.45rem 0.5rem;
                border: none;
            }

            .table tbody td::before {
                content: attr(data-label);
                font-weight: 700;
                font-size: 0.76rem;
                color: var(--text-secondary);
                margin-right: 0.75rem;
                flex-shrink: 0;
                text-transform: uppercase;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    @include('partials.navbar')

    <div class="container py-4">
        @include('admin.partials.nav')

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="page-title mb-1">
                    <i class="bi bi-journal-text me-2 text-primary"></i>Audit Log Aktivitas Sistem
                </h2>
                <p class="text-secondary mb-0 small">Catatan riwayat kontrol saklar, irigasi, dosing, login, dan aktivitas sistem dari seluruh pengguna.</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="glass-card mb-4" style="padding: 1.25rem;">
            <form class="filter-form row g-2 align-items-end" method="GET">
                <div class="col-md-3">
                    <label class="form-label small text-secondary">Aksi</label>
                    <select name="action" class="form-select">
                        <option value="">Semua Aksi</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $action)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-secondary">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-gradient btn-sm px-3" style="border-radius: 10px; min-height: 42px;">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.activity-logs') }}" class="btn btn-outline-secondary btn-sm px-3 d-inline-flex align-items-center"
                        style="border-radius: 10px; min-height: 42px;">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="glass-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-dark-custom">
                        <tr>
                            <th>#</th>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Deskripsi</th>
                            <th>Detail</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td data-label="#">{{ $logs->firstItem() + $loop->index }}</td>
                                <td data-label="Waktu">
                                    <span class="fw-bold">{{ $log->created_at->format('d M Y') }}</span><br>
                                    <small class="text-secondary">{{ $log->created_at->format('H:i:s') }} ({{ $log->created_at->diffForHumans() }})</small>
                                </td>
                                <td data-label="User">
                                    <span class="fw-semibold">{{ $log->user->name ?? 'Guest' }}</span>
                                </td>
                                <td data-label="Aksi">
                                    @php
                                        $badgeClass = match ($log->action) {
                                            'login' => 'badge-login',
                                            'logout' => 'badge-logout',
                                            'device_control', 'control_output' => 'badge-control',
                                            'pump_control', 'control_pump', 'irrigation_control' => 'badge-pump',
                                            'dosing_control' => 'badge-dosing',
                                            'add_device', 'update_device', 'remove_device' => 'badge-device',
                                            default => 'badge-default',
                                        };
                                    @endphp
                                    <span class="badge-action {{ $badgeClass }}">{{ str_replace('_', ' ', $log->action) }}</span>
                                </td>
                                <td data-label="Deskripsi">
                                    <span class="text-main">{{ \Illuminate\Support\Str::limit($log->description, 80) }}</span>
                                </td>
                                <td data-label="Detail">
                                    @if($log->details)
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#detail-{{ $log->id }}" style="border-radius: 8px;">
                                            <i class="bi bi-code-square me-1"></i> Data
                                        </button>
                                        <div class="collapse mt-2" id="detail-{{ $log->id }}">
                                            <pre class="p-2 rounded" style="background: rgba(0,0,0,0.4); color: #38bdf8; font-size: 0.75rem; border: 1px solid var(--glass-border);">{{ json_encode($log->details, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td data-label="IP"><small class="text-secondary">{{ $log->ip_address }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5" style="color: var(--text-secondary);">
                                    <i class="bi bi-journal-x" style="font-size: 2.5rem; opacity: 0.5;"></i>
                                    <p class="mt-2 mb-0 fw-semibold">Belum ada activity log yang tercatat.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="d-flex justify-content-center mt-3 pb-3">
                    {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    @include('partials.pwa-scripts')
</body>

</html>