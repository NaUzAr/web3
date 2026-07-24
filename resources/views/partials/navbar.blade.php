{{-- Navbar Utama - Seragam untuk semua halaman --}}
@php
    $currentRoute = Route::currentRouteName() ?? '';
    $isAdmin = auth()->check() && auth()->user()->role === 'admin';
    $isPwa = session('is_pwa');
@endphp

<style>
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

    .nav-links-wrap {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .nav-links-wrap a,
    .nav-links-wrap button {
        color: var(--navbar-text, #333);
        text-decoration: none;
        padding: 0.45rem 0.75rem;
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
        white-space: nowrap;
    }

    .nav-links-wrap a:hover,
    .nav-links-wrap button:hover {
        background: rgba(0, 0, 0, 0.05);
        color: var(--primary);
    }

    .nav-links-wrap a.active {
        background: var(--primary);
        color: #fff !important;
    }

    .nav-user-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.25rem 0.65rem 0.25rem 0.3rem;
        border-radius: 50px;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        margin-left: 0.3rem;
    }

    .nav-user-avatar {
        width: 28px;
        height: 28px;
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

    @media (max-width: 576px) {
        .nav-links-wrap a span.nav-text {
            display: none;
        }
        .nav-links-wrap a {
            padding: 0.4rem 0.5rem;
            font-size: 1rem;
        }
        .nav-user-name {
            display: none;
        }
    }
</style>

<nav class="navbar-main">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
        <a class="navbar-brand" href="{{ $isPwa ? route('monitoring.index') : route('home') }}">
            <img src="{{ asset(env('APP_LOGO', 'images/logo.png')) }}" alt="{{ env('APP_NAME', 'Swaratani') }}">
            <span>{{ env('APP_NAME', 'Swaratani') }} IoT</span>
        </a>

        <div class="nav-links-wrap ms-auto">
            @auth
                @if(!$isPwa)
                    <a href="{{ route('home') }}" class="{{ $currentRoute === 'home' ? 'active' : '' }}" title="Beranda">
                        <i class="bi bi-house"></i> <span class="nav-text">Beranda</span>
                    </a>
                @endif
                <a href="{{ route('monitoring.index') }}" class="{{ str_starts_with($currentRoute, 'monitoring.') ? 'active' : '' }}" title="Monitoring">
                    <i class="bi bi-graph-up-arrow"></i> <span class="nav-text">Monitoring</span>
                </a>
                <a href="{{ route('riwayat.index') }}" class="{{ $currentRoute === 'riwayat.index' ? 'active' : '' }}" title="Riwayat">
                    <i class="bi bi-clock-history"></i> <span class="nav-text">Riwayat</span>
                </a>
                <a href="{{ route('tickets.index') }}" class="{{ str_starts_with($currentRoute, 'tickets.') ? 'active' : '' }}" title="Support">
                    <i class="bi bi-headset"></i> <span class="nav-text">Support</span>
                </a>
                @if($isAdmin)
                    <a href="{{ route('admin.devices.index') }}" class="{{ str_starts_with($currentRoute, 'admin.') ? 'active' : '' }}" title="Admin">
                        <i class="bi bi-gear"></i> <span class="nav-text">Admin</span>
                    </a>
                @endif

                <div class="nav-user-pill">
                    <div class="nav-user-avatar">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="nav-user-name">{{ Auth::user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline m-0">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 text-danger ms-1" title="Logout" style="font-size: 0.9rem;">
                            <i class="bi bi-box-arrow-right"></i>
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="{{ $currentRoute === 'login' ? 'active' : '' }}">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
                <a href="{{ route('register') }}" class="{{ $currentRoute === 'register' ? 'active' : '' }}">
                    <i class="bi bi-person-plus"></i> Daftar
                </a>
            @endauth
        </div>
    </div>
</nav>
