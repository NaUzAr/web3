<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Saya - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.theme')

    <style>
        .page-title {
            color: var(--text-main);
            font-weight: 800;
            font-size: 1.85rem;
            margin-bottom: 0.25rem;
        }

        .profile-header-card {
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--glass-border, rgba(14, 165, 233, 0.15));
            border-radius: 20px;
            padding: 1.75rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .profile-avatar-lg {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: var(--primary-gradient, linear-gradient(135deg, #0ea5e9, #0369a1));
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.25);
        }

        .form-card {
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--glass-border, rgba(14, 165, 233, 0.15));
            border-radius: 20px;
            padding: 1.75rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            height: 100%;
        }

        .form-card-title {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--glass-border, #f1f5f9);
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--text-main);
            margin-bottom: 0.35rem;
        }

        .form-control {
            border-radius: 12px;
            padding: 0.65rem 1rem;
            border: 1px solid var(--glass-border, #cbd5e1);
            background: var(--input-bg, #ffffff);
            color: var(--text-main);
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--primary, #0ea5e9);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }

        .form-control:disabled, .form-control[readonly] {
            background-color: rgba(100, 116, 139, 0.08);
            color: #64748b;
        }

        .btn-submit {
            background: var(--primary-gradient, linear-gradient(135deg, #0ea5e9, #0369a1));
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 0.7rem 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.25);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.35);
        }

        .badge-role {
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .badge-admin {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .badge-user {
            background: rgba(14, 165, 233, 0.15);
            color: #0284c7;
            border: 1px solid rgba(14, 165, 233, 0.3);
        }
    </style>
</head>
<body>
    @include('partials.navbar')

    <div class="container py-4">
        {{-- Breadcrumb / Header --}}
        <div class="mb-4">
            <h1 class="page-title"><i class="bi bi-person-circle text-primary me-2"></i>Profil Pengguna</h1>
            <p class="text-muted mb-0">Kelola identitas akun dan keamanan kata sandi Anda.</p>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('password_success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                <i class="bi bi-shield-check me-2"></i>{{ session('password_success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- User Header Card --}}
        <div class="profile-header-card">
            <div class="profile-avatar-lg">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h3 class="fw-bold mb-0 text-dark">{{ $user->name }}</h3>
                    <span class="badge-role {{ $user->is_admin ? 'badge-admin' : 'badge-user' }}">
                        <i class="bi {{ $user->is_admin ? 'bi-shield-fill' : 'bi-person-fill' }} me-1"></i>{{ $user->role ?? 'User' }}
                    </span>
                </div>
                <div class="text-muted small">
                    <span class="me-3"><i class="bi bi-at me-1"></i>{{ $user->username }}</span>
                    <span class="me-3"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</span>
                    <span><i class="bi bi-calendar-check me-1"></i>Bergabung {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- Edit Profile Form --}}
            <div class="col-lg-6">
                <div class="form-card">
                    <div class="form-card-title">
                        <i class="bi bi-person-lines-fill text-primary"></i> Perbarui Data Akun
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" value="{{ $user->username }}" readonly disabled>
                            <small class="text-muted">Username digunakan untuk login dan tidak dapat diubah.</small>
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="phone" class="form-label">Nomor WhatsApp / HP</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;"><i class="bi bi-telephone"></i></span>
                                <input type="tel" name="phone" id="phone" class="form-control border-start-0 @error('phone') is-invalid @enderror" style="border-radius: 0 12px 12px 0;" placeholder="08xxxxxxxxxx" value="{{ old('phone', $user->phone ?? '') }}">
                            </div>
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            {{-- Change Password Form --}}
            <div class="col-lg-6">
                <div class="form-card">
                    <div class="form-card-title">
                        <i class="bi bi-key-fill text-warning"></i> Ubah Kata Sandi
                    </div>

                    <form action="{{ route('profile.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" required autocomplete="current-password">
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required minlength="6" autocomplete="new-password">
                            <small class="text-muted">Minimal 6 karakter.</small>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required minlength="6" autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <i class="bi bi-shield-lock"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('partials.pwa-scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
