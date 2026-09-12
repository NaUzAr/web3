<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    @include('partials.theme')

    <title>Daftar - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-card {
            width: 100%;
            max-width: 450px;
            padding: 2.5rem;
            border-radius: 24px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .register-title {
            color: var(--text-main, #333);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .register-subtitle {
            color: var(--text-secondary, #666);
            font-size: 0.9rem;
        }

        .alert-danger-custom {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            border-radius: 12px;
        }

        .link-green {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .link-green:hover {
            color: var(--primary);
        }

        .logo-icon {
            width: auto;
            height: 120px;
            background: none;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }

        .register-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }

        .text-muted-light {
            color: var(--text-secondary);
        }

        /* Mobile input touch-friendly */
        .register-card .form-control {
            font-size: 16px;
            min-height: 48px;
            padding: 0.75rem 1rem;
            border-radius: 12px;
        }

        .register-card .btn-gradient {
            min-height: 48px;
            font-size: 1rem;
            border-radius: 50px;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 576px) {
            body {
                padding: 1rem;
                align-items: flex-start;
                padding-top: 1.5rem;
            }

            .register-card {
                padding: 1.75rem 1.5rem;
                border-radius: 20px;
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            }

            .logo-icon {
                height: 70px;
                margin-bottom: 0.75rem;
            }

            .register-title {
                font-size: 1.25rem;
            }

            .register-subtitle {
                font-size: 0.85rem;
            }

            .register-card .form-label {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 400px) {
            body {
                padding: 0.75rem;
                padding-top: 1rem;
            }

            .register-card {
                padding: 1.5rem 1.25rem;
            }

            .logo-icon {
                height: 55px;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <div class="register-card">
        <div class="text-center mb-4">
            <div class="logo-icon">
                <img src="{{ asset(env('APP_LOGO', 'images/logo.png')) }}" alt="{{ env('APP_NAME', 'Swaratani') }}" style="height: 100%; width: auto;">
            </div>
            <h4 class="register-title">Buat Akun Baru</h4>
            <p class="register-subtitle">Daftar untuk mengakses Swaratani</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger-custom mb-4">
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.perform') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label"><i class="bi bi-person me-1"></i>Nama Lengkap</label>
                <input type="text" class="form-control" name="name" value="{{ old('name') }}"
                    placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="bi bi-at me-1"></i>Username</label>
                <input type="text" class="form-control" name="username" value="{{ old('username') }}"
                    placeholder="Masukkan username" required>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="bi bi-envelope me-1"></i>Email</label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                    placeholder="Masukkan email" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="bi bi-lock me-1"></i>Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Min. 6 karakter" minlength="6" required style="border-radius: 12px 0 0 12px;">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-radius: 0 12px 12px 0; border: 1px solid #ced4da; background-color: white;">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label"><i class="bi bi-lock-fill me-1"></i>Ulangi Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            placeholder="Ulangi password" minlength="6" required style="border-radius: 12px 0 0 12px;">
                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm" style="border-radius: 0 12px 12px 0; border: 1px solid #ced4da; background-color: white;">
                            <i class="bi bi-eye" id="toggleIconConfirm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 mb-4">
                <button type="submit" class="btn btn-gradient">
                    <i class="bi bi-person-check me-2"></i>Daftar Sekarang
                </button>
            </div>
        </form>

        <div class="text-center mt-3">
            <span style="color: rgba(100, 116, 139, 0.8); font-size: 0.9rem;">Sudah punya akun?</span>
            <a href="{{ route('login') }}"
                style="color: #0ea5e9; text-decoration: none; font-weight: 500; margin-left: 4px;">Login disini</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const password = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        document.getElementById('togglePassword').addEventListener('click', function () {
            togglePasswordVisibility('password', 'toggleIcon');
        });

        document.getElementById('togglePasswordConfirm').addEventListener('click', function () {
            togglePasswordVisibility('password_confirmation', 'toggleIconConfirm');
        });
    </script>
</body>

</html>