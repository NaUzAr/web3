<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Pengguna - {{ env('APP_NAME', 'Swaratani') }} Admin</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.theme')

    <style>
        .page-title {
            color: var(--text-main, #0f172a);
            font-weight: 800;
            font-size: 1.75rem;
        }

        .stat-card {
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--glass-border, rgba(14, 165, 233, 0.15));
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            display: flex;
            align-items: center;
            gap: 1.25rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.primary {
            background: rgba(14, 165, 233, 0.15);
            color: #0284c7;
        }

        .stat-icon.danger {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
        }

        .stat-icon.success {
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
        }

        .glass-card {
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--glass-border, rgba(14, 165, 233, 0.15));
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .user-table th {
            background: rgba(14, 165, 233, 0.04);
            color: var(--text-main, #334155);
            font-weight: 600;
            font-size: 0.85rem;
            border-bottom: 1px solid var(--glass-border, #e2e8f0);
            padding: 1rem 1.25rem;
        }

        .user-table td {
            padding: 1rem 1.25rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--glass-border, #f1f5f9);
            color: var(--text-main, #1e293b);
            font-size: 0.9rem;
        }

        .user-avatar-sm {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--primary-gradient, linear-gradient(135deg, #0ea5e9, #0369a1));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .badge-role {
            padding: 0.35rem 0.7rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .badge-role-admin {
            background: rgba(239, 68, 68, 0.12);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        .badge-role-user {
            background: rgba(14, 165, 233, 0.12);
            color: #0284c7;
            border: 1px solid rgba(14, 165, 233, 0.25);
        }

        .device-count-badge {
            background: rgba(99, 102, 241, 0.1);
            color: #4f46e5;
            padding: 0.25rem 0.6rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .btn-action-del {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-action-del:hover {
            background: #ef4444;
            color: #fff;
        }
    </style>
</head>

<body>
    @include('partials.navbar')

    <div class="container py-4">
        {{-- Admin Navigation Tabs --}}
        @include('admin.partials.nav')

        {{-- Header bar --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h1 class="page-title mb-1"><i class="bi bi-people-fill text-primary me-2"></i>Kelola Pengguna</h1>
                <p class="text-muted mb-0 small">Daftar pengguna terdaftar, kepemilikan device IoT, dan kontrol hak akses.</p>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Stats Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon primary">
                        <i class="bi bi-people"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Total Pengguna</div>
                        <div class="fs-4 fw-bold text-dark">{{ $totalUsers ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon danger">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Administrator</div>
                        <div class="fs-4 fw-bold text-dark">{{ $totalAdmins ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(99, 102, 241, 0.15); color: #4f46e5;">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Pengguna Biasa</div>
                        <div class="fs-4 fw-bold text-dark">{{ $totalRegular ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon success">
                        <i class="bi bi-activity"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Online Sekarang</div>
                        <div class="fs-4 fw-bold text-success">{{ $totalOnline ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Search Card --}}
        <div class="glass-card mb-4 p-3">
            <form action="{{ route('admin.users.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 shadow-none" placeholder="Cari nama, email, username..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select shadow-none">
                        <option value="">Semua Role</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User Biasa</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 rounded-3">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if(request()->hasAny(['search', 'role']))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary rounded-3">
                            <i class="bi bi-x-circle me-1"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="glass-card">
            <div class="table-responsive">
                <table class="table user-table mb-0">
                    <thead>
                        <tr>
                            <th>Pengguna</th>
                            <th>Username</th>
                            <th>Terakhir Aktif</th>
                            <th>Device Terhubung</th>
                            <th>Role Akun</th>
                            <th>Terdaftar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="user-avatar-sm position-relative">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                            @if($u->isOnline())
                                                <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle" style="width: 10px; height: 10px;" title="Online"></span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $u->name }}</div>
                                            <div class="text-muted small">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-monospace small text-muted">@ {{ $u->username }}</span>
                                </td>
                                <td>
                                    @if($u->isOnline())
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill small fw-bold">
                                            <i class="bi bi-circle-fill text-success me-1" style="font-size: 0.45rem;"></i> Online
                                        </span>
                                    @else
                                        <span class="text-muted small">
                                            <i class="bi bi-clock me-1"></i>{{ $u->lastActiveText() }}
                                        </span>
                                        @if($u->last_active_at)
                                            <div class="text-muted" style="font-size: 0.72rem;">
                                                {{ $u->last_active_at->format('d M Y, H:i') }}
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <span class="device-count-badge">
                                        <i class="bi bi-cpu me-1"></i>{{ $u->user_devices_count }} Device
                                    </span>
                                </td>
                                <td>
                                    @if($u->id === auth()->id())
                                        <span class="badge-role badge-role-admin">
                                            <i class="bi bi-shield-fill"></i> Anda (Admin)
                                        </span>
                                    @else
                                        <form action="{{ route('admin.users.update-role', $u->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <select name="role" class="form-select form-select-sm d-inline-block w-auto shadow-none" onchange="this.form.submit()">
                                                <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>User</option>
                                                <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                            </select>
                                        </form>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted small">{{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}</span>
                                </td>
                                <td class="text-end">
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $u->name }}? Akses device user ini juga akan dihapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action-del" title="Hapus User">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small fst-italic">Akun Aktif</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada data pengguna yang sesuai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-3 border-top">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    @include('partials.pwa-scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
