<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    @include('partials.theme')

    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
            border-radius: 24px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        .login-icon {
            width: auto;
            height: 120px;
            background: none;
            border-radius: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }

        .login-title {
            color: var(--text-main, #333);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
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

        .text-muted-light {
            color: var(--text-secondary);
        }

        .divider {
            border-top: 1px solid var(--glass-border);
        }

        /* Mobile input touch-friendly */
        .login-card .form-control {
            font-size: 16px;
            min-height: 48px;
            padding: 0.75rem 1rem;
            border-radius: 12px;
        }

        .login-card .btn-gradient {
            min-height: 48px;
            font-size: 1rem;
            border-radius: 50px;
        }

        /* Mobile responsive adjustments */
        @media (max-width: 576px) {
            body {
                padding: 1rem;
                align-items: flex-start;
                padding-top: 2rem;
            }

            .login-card {
                padding: 1.75rem 1.5rem;
                border-radius: 20px;
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            }

            .login-icon {
                height: 80px;
                margin-bottom: 1rem;
            }

            .login-title {
                font-size: 1.25rem;
            }

            .login-subtitle {
                font-size: 0.85rem;
            }

            .login-card .form-label {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 400px) {
            body {
                padding: 0.75rem;
                padding-top: 1.5rem;
            }

            .login-card {
                padding: 1.5rem 1.25rem;
            }

            .login-icon {
                height: 65px;
                margin-bottom: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <div class="login-card">
        <div class="text-center mb-4">
            <div class="login-icon">
                <img src="{{ asset(env('APP_LOGO', 'images/logo.png')) }}" alt="{{ env('APP_NAME', 'Swaratani') }}" style="height: 100%; width: auto;">
            </div>
            <h4 class="login-title">Selamat Datang!</h4>
            <p class="login-subtitle">Login ke Swaratani</p>
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

        <form action="{{ route('login.perform') }}{{ request('pwa') ? '?pwa=1' : '' }}" method="POST">
            @csrf
            @if(request('pwa'))
                <input type="hidden" name="pwa" value="1">
            @endif

            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="bi bi-person me-1"></i>Username
                </label>
                <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}"
                    placeholder="Masukkan username" required autofocus>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="bi bi-lock me-1"></i>Password
                </label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password"
                        placeholder="Masukkan password" minlength="6" required style="border-radius: 12px 0 0 12px;">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-radius: 0 12px 12px 0; border: 1px solid #ced4da; background-color: white;">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4 text-end">
                <a href="{{ route('password.request') }}"
                    style="color: var(--primary-light, #38bdf8); font-size: 0.85rem; text-decoration: none;">
                    Lupa Password?
                </a>
            </div>

            <div class="d-grid gap-2 mb-4">
                <button type="submit" class="btn btn-gradient">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </div>
        </form>

        <div class="text-center">
            <p class="text-muted-light small mb-2">Belum punya akun?</p>
            <a href="{{ route('register') }}" class="link-green">Buat Akun Baru</a>


        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function (e) {
            const password = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        });
    </script>
    @include('partials.pwa-scripts')
</body>

</html>