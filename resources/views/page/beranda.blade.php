<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#dc2626">
    <meta name="description" content="Swaratani IoT - Sistem monitoring pertanian cerdas berbasis IoT">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Swaratani">
    <link rel="icon" href="{{ asset(env('APP_LOGO', 'images/logo.png')) }}">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="{{ asset(env('APP_LOGO', 'images/logo.png')) }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <title>{{ env('APP_NAME', 'Swaratani') }} IoT - Dashboard</title>

    @include('partials.theme')

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* Main Content */
        .main-wrapper {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            z-index: 1;
        }

        /* Header Section */
        .page-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
        }

        .brand-logo img {
            height: 200px;
            width: auto;
        }

        .page-subtitle {
            color: var(--text-secondary);
            font-size: 1rem;
            font-weight: 400;
        }

        /* Menu Grid */
        .menu-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
        }

        /* Menu Card */
        .menu-card {
            width: 100%;
            max-width: 250px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem 1rem;
            text-decoration: none;
            color: var(--text-main);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .menu-card:hover {
            transform: translateY(-6px);
            border-color: var(--primary);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            color: var(--text-main);
        }

        .menu-card:hover .card-icon {
            transform: scale(1.1);
        }

        .menu-card:hover .card-arrow {
            transform: translateX(4px);
            color: var(--primary);
        }

        /* Card Icon */
        .card-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
            transition: transform 0.3s ease;
            color: white;
        }

        .card-icon.monitoring {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.35);
        }

        .card-icon.admin {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            box-shadow: 0 8px 20px rgba(245, 158, 11, 0.35);
        }

        .card-icon.login {
            background: linear-gradient(135deg, #10b981, #059669);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.35);
        }

        .card-icon.register {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.35);
        }

        /* Card Title */
        .card-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        /* Card Arrow */
        .card-arrow {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        /* User Badge */
        .user-badge {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            border: 1px solid var(--glass-border);
            z-index: 100;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: white;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .user-role {
            font-size: 0.7rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .logout-btn {
            background: rgba(239, 68, 68, 0.1);
            border: none;
            color: #ef4444;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: #ef4444;
            color: white;
        }

        /* Footer */
        .page-footer {
            text-align: center;
            padding: 1.5rem;
            color: var(--text-secondary);
            font-size: 0.85rem;
            position: relative;
            z-index: 1;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .brand-logo img {
                height: 150px;
            }

            .menu-card {
                max-width: calc(50% - 0.5rem);
            }

            .user-badge {
                top: 1rem;
                right: 1rem;
                padding: 0.4rem 0.75rem;
            }

            .user-info {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    @auth
        <!-- User Badge -->
        <div class="user-badge">
            <div class="user-avatar">
                <i class="bi bi-person-fill text-white"></i>
            </div>
            <div class="user-info">
                <span class="user-name">{{ Auth::user()->name }}</span>
                <span class="user-role">{{ Auth::user()->role === 'admin' ? 'Administrator' : 'User' }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    @endauth

    <!-- Main Content -->
    <div class="main-wrapper">
        <div class="container">
            <!-- Header -->
            <div class="page-header">
                <div class="brand-logo">
                    <img src="{{ asset(env('APP_LOGO', 'images/logo.png')) }}" alt="{{ env('APP_NAME', 'Swaratani') }}">
                </div>
                <p class="page-subtitle">Pilih menu untuk memulai</p>
            </div>

            <!-- Menu Grid -->
            <div class="menu-grid">
                @auth
                    <a href="{{ route('monitoring.index') }}" class="menu-card">
                        <div class="card-icon monitoring">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h3 class="card-title">Monitoring</h3>
                        <div class="card-arrow">
                            <span>Buka</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>

                    @if(Auth::user()->role === 'admin')
                        <a href="{{ route('admin.devices.index') }}" class="menu-card">
                            <div class="card-icon admin">
                                <i class="bi bi-gear-fill"></i>
                            </div>
                            <h3 class="card-title">Admin Panel</h3>
                            <div class="card-arrow">
                                <span>Kelola</span>
                                <i class="bi bi-arrow-right"></i>
                            </div>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="menu-card">
                        <div class="card-icon login">
                            <i class="bi bi-box-arrow-in-right"></i>
                        </div>
                        <h3 class="card-title">Login</h3>
                        <div class="card-arrow">
                            <span>Masuk</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>

                    <a href="{{ route('register') }}" class="menu-card">
                        <div class="card-icon register">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <h3 class="card-title">Daftar Akun</h3>
                        <div class="card-arrow">
                            <span>Buat Akun</span>
                            <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="page-footer">
        <p>&copy; 2025 Swaratani IoT &bull; Tim Engineering Pertanian</p>
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