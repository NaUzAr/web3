<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Activity Logs - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    @include('partials.theme')

    <style>
        .page-title {
            color: #0f172a;
            font-weight: 800;
        }

        .page-title i {
            color: #0e5f8a;
        }

        .table-dark-custom {
            background: #f1f5f9 !important;
        }

        .table-dark-custom th {
            color: #334155 !important;
            font-weight: 700;
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 2px solid #e2e8f0 !important;
            padding: 0.85rem 1rem;
        }

        .table tbody td {
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
            padding: 0.85rem 1rem;
            vertical-align: middle;
            font-size: 0.85rem;
            background: #ffffff;
        }

        .table tbody tr:hover td {
            background: #f8fafc;
        }

        .glass-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .badge-action {
            font-weight: 600;
            padding: 0.3rem 0.6rem;
            border-radius: 8px;
            font-size: 0.75rem;
        }

        .badge-login {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }

        .badge-logout {
            background: rgba(100, 116, 139, 0.15);
            color: #64748b;
        }

        .badge-control {
            background: rgba(59, 130, 246, 0.15);
            color: #3b82f6;
        }

        .badge-device {
            background: rgba(168, 85, 247, 0.15);
            color: #a855f7;
        }

        .badge-schedule {
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
        }

        .badge-default {
            background: rgba(100, 116, 139, 0.15);
            color: #64748b;
        }

        .filter-form .form-control,
        .filter-form .form-select {
            font-size: 0.85rem;
            border-radius: 10px;
            min-height: 40px;
        }

        .text-secondary-light {
            color: #94a3b8;
        }

        @media (max-width: 768px) {
            .container.py-5 {
                padding: 1.5rem 0.75rem !important;
            }

            .page-title {
                font-size: 1.25rem;
            }

            .table thead {
                display: none;
            }

            .table tbody tr {
                display: block;
                background: var(--glass-bg);
                border: 1px solid var(--glass-border);
                border-radius: 12px;
                padding: 0.75rem;
                margin-bottom: 0.75rem;
            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.35rem 0.5rem;
                border: none;
            }

            .table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                font-size: 0.75rem;
                color: var(--text-secondary);
                margin-right: 0.75rem;
                flex-shrink: 0;
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
                    <i class="bi bi-journal-text me-2 text-info"></i>Audit Log Aktivitas Sistem
                </h2>
                <p class="text-muted mb-0 small">Catatan riwayat aktivitas login, kontrol relay, dan perubahan sistem oleh admin/user.</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="glass-card mb-4" style="padding: 1rem 1.25rem;">
            <form class="filter-form row g-2 align-items-end" method="GET">
                <div class="col-md-3">
                    <label class="form-label small text-secondary-light">Aksi</label>
                    <select name="action" class="form-select">
                        <option value="">Semua</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-secondary-light">Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-gradient btn-sm" style="border-radius: 10px;">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.activity-logs') }}" class="btn btn-outline-secondary btn-sm"
                        style="border-radius: 10px;">Reset</a>
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
                                    <span class="text-secondary-light">{{ $log->created_at->format('d M Y') }}</span><br>
                                    <small>{{ $log->created_at->format('H:i:s') }}</small>
                                </td>
                                <td data-label="User">{{ $log->user->name ?? 'Guest' }}</td>
                                <td data-label="Aksi">
                                    @php
                                        $badgeClass = match ($log->action) {
                                            'login' => 'badge-login',
                                            'logout' => 'badge-logout',
                                            'control_output', 'control_pump' => 'badge-control',
                                            'add_device', 'remove_device' => 'badge-device',
                                            'schedule', 'automation' => 'badge-schedule',
                                            default => 'badge-default',
                                        };
                                    @endphp
                                    <span class="badge-action {{ $badgeClass }}">{{ $log->action }}</span>
                                </td>
                                <td data-label="Deskripsi">{{ \Illuminate\Support\Str::limit($log->description, 80) }}</td>
                                <td data-label="Detail">
                                    @if($log->details)
                                        <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#detail-{{ $log->id }}">
                                            Lihat
                                        </button>
                                        <div class="collapse mt-2" id="detail-{{ $log->id }}">
                                            <pre class="bg-dark text-light p-2 rounded" style="font-size: 0.75rem;">{{ json_encode($log->details, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td data-label="IP"><small class="text-secondary-light">{{ $log->ip_address }}</small></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5" style="color: rgba(255,255,255,0.5);">
                                    <i class="bi bi-journal-x" style="font-size: 2rem;"></i>
                                    <p class="mt-2">Belum ada activity log.</p>
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
</body>

</html>