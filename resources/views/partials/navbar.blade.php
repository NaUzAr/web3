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

    /* Modern User Profile Dropdown */
    .nav-user-dropdown {
        position: relative;
        margin-left: 0.4rem;
        display: inline-block;
    }

    .nav-user-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.28rem 0.75rem 0.28rem 0.35rem;
        border-radius: 50px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(203, 213, 225, 0.8);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        color: #1e293b !important;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
    }

    .nav-user-btn:hover,
    .nav-user-btn:focus,
    .nav-user-btn[aria-expanded="true"] {
        background: #ffffff;
        border-color: rgba(14, 95, 138, 0.4);
        box-shadow: 0 4px 14px rgba(14, 95, 138, 0.12);
        transform: translateY(-1px);
    }

    .nav-user-btn::after {
        display: none !important;
    }

    .nav-user-avatar-wrap {
        position: relative;
        width: 32px;
        height: 32px;
        flex-shrink: 0;
    }

    .nav-user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0e5f8a 0%, #0284c7 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 0.85rem;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(14, 95, 138, 0.25);
    }

    .user-status-dot {
        position: absolute;
        bottom: -1px;
        right: -1px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #22c55e;
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.2);
    }

    .nav-user-name {
        font-size: 0.84rem;
        font-weight: 600;
        color: #1e293b;
        max-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .nav-user-chevron {
        font-size: 0.68rem;
        color: #64748b;
        margin-left: 0.1rem;
        transition: transform 0.25s ease;
    }

    .nav-user-dropdown.show .nav-user-chevron,
    .nav-user-btn[aria-expanded="true"] .nav-user-chevron {
        transform: rotate(180deg);
        color: #0e5f8a;
    }

    /* Modern Dropdown Card (Compact & Ringkas) */
    .nav-user-menu {
        min-width: 235px;
        max-width: 280px;
        border-radius: 14px !important;
        padding: 0.45rem !important;
        margin-top: 0.55rem !important;
        border: 1px solid rgba(226, 232, 240, 0.9) !important;
        background: #ffffff !important;
        box-shadow: 0 14px 34px -4px rgba(15, 23, 42, 0.14), 0 4px 10px rgba(15, 23, 42, 0.04) !important;
        animation: userDropdownSlide 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        z-index: 1050;
    }

    @keyframes userDropdownSlide {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .user-menu-header {
        padding: 0.75rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 12px;
        margin-bottom: 0.4rem;
        border: 1px solid rgba(226, 232, 240, 0.6);
    }

    .user-menu-header-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0e5f8a 0%, #0284c7 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 1.1rem;
        font-weight: 700;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(14, 95, 138, 0.25);
    }

    .user-menu-item {
        display: flex !important;
        align-items: center;
        gap: 0.75rem;
        padding: 0.55rem 0.75rem !important;
        border-radius: 10px !important;
        color: #334155 !important;
        text-decoration: none;
        transition: all 0.18s ease;
        cursor: pointer;
    }

    .user-menu-item:hover {
        background: #f1f5f9 !important;
        color: #0e5f8a !important;
        transform: translateX(2px);
    }

    .menu-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .bg-primary-light { background: rgba(14, 95, 138, 0.1); }
    .bg-info-light { background: rgba(56, 189, 248, 0.15); }
    .bg-warning-light { background: rgba(245, 158, 11, 0.12); }
    .bg-success-light { background: rgba(34, 197, 94, 0.12); }
    .bg-danger-light { background: rgba(239, 68, 68, 0.1); }

    .menu-item-title {
        font-size: 0.84rem;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.2;
    }

    .menu-item-desc {
        font-size: 0.72rem;
        color: #64748b;
        line-height: 1.2;
    }

    .logout-item:hover {
        background: rgba(239, 68, 68, 0.08) !important;
    }
    .logout-item:hover .menu-item-title {
        color: #dc2626 !important;
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
        .nav-user-btn {
            padding: 0.25rem;
            border-radius: 50%;
        }
        .nav-user-chevron {
            display: none;
        }
        .nav-user-menu {
            position: fixed !important;
            top: 60px !important;
            right: 12px !important;
            left: 12px !important;
            max-width: none !important;
            width: auto !important;
        }
    }
</style>

<nav class="navbar-main">
    <div class="container d-flex align-items-center justify-content-between flex-wrap gap-2">
        <a class="navbar-brand" href="{{ $isPwa ? route('monitoring.index') : route('home') }}">
            <img src="{{ asset(env('APP_LOGO', 'images/logo.png')) }}" alt="{{ env('APP_NAME', 'Swaratani') }}">
            <span>{{ env('APP_NAME', 'Swaratani') }}</span>
        </a>

        <div class="nav-links-wrap ms-auto">
            @auth
                @if(!$isPwa)
                    <a href="{{ route('home') }}" class="{{ $currentRoute === 'home' ? 'active' : '' }}" title="Beranda">
                        <i class="bi bi-house"></i> <span class="nav-text">Beranda</span>
                    </a>
                @endif
                <a href="{{ route('monitoring.index') }}" class="{{ ($currentRoute === 'monitoring.index' || str_starts_with($currentRoute, 'monitoring.show') || str_starts_with($currentRoute, 'monitoring.history')) ? 'active' : '' }}" title="Monitoring">
                    <i class="bi bi-graph-up-arrow"></i> <span class="nav-text">Monitoring</span>
                </a>
                <a href="{{ route('monitoring.create') }}" class="{{ $currentRoute === 'monitoring.create' ? 'active' : '' }}" title="Perangkat Baru">
                    <i class="bi bi-plus-circle"></i> <span class="nav-text">Perangkat Baru</span>
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

                {{-- Modern User Profile Menu Dropdown (Ringkas Khusus Akun) --}}
                <div class="dropdown nav-user-dropdown">
                    <button class="nav-user-btn dropdown-toggle" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Menu Akun">
                        <div class="nav-user-avatar-wrap">
                            <div class="nav-user-avatar">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="user-status-dot" title="Online"></span>
                        </div>
                        <span class="nav-user-name">{{ Auth::user()->name }}</span>
                        <i class="bi bi-chevron-down nav-user-chevron"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end nav-user-menu" aria-labelledby="userMenuDropdown">
                        <!-- User Info Header -->
                        <div class="user-menu-header">
                            <div class="d-flex align-items-center gap-2">
                                <div class="user-menu-header-avatar">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="overflow-hidden">
                                    <h6 class="mb-0 fw-bold text-truncate" style="font-size: 0.9rem; color: #0f172a;">
                                        {{ Auth::user()->name }}
                                    </h6>
                                    <span class="d-block text-muted text-truncate" style="font-size: 0.74rem;">
                                        {{ Auth::user()->email }}
                                    </span>
                                    <span class="badge {{ $isAdmin ? 'bg-primary text-white' : 'bg-success text-white' }} mt-1" style="font-size: 0.63rem; font-weight: 600; padding: 2px 7px;">
                                        <i class="bi {{ $isAdmin ? 'bi-shield-check' : 'bi-person-check' }} me-1"></i>{{ $isAdmin ? 'Administrator' : 'Pengguna' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Menu Item: Tambah Perangkat Baru -->
                        <a class="dropdown-item user-menu-item" href="{{ route('monitoring.create') }}">
                            <div class="menu-icon-box text-info bg-info-light">
                                <i class="bi bi-plus-circle-dotted"></i>
                            </div>
                            <div>
                                <div class="menu-item-title">Perangkat Baru</div>
                                <div class="menu-item-desc">Scan QR / masukkan token baru</div>
                            </div>
                        </a>

                        <!-- Menu Item: Profil Saya -->
                        <a class="dropdown-item user-menu-item" href="{{ route('profile.index') }}">
                            <div class="menu-icon-box text-primary bg-primary-light">
                                <i class="bi bi-person-gear"></i>
                            </div>
                            <div>
                                <div class="menu-item-title">Profil Saya</div>
                                <div class="menu-item-desc">Kelola profil & ganti kata sandi</div>
                            </div>
                        </a>

                        <div class="dropdown-divider my-1"></div>

                        <!-- Logout Item -->
                        <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                            @csrf
                            <button type="submit" class="dropdown-item user-menu-item logout-item w-100 text-start border-0 bg-transparent">
                                <div class="menu-icon-box text-danger bg-danger-light">
                                    <i class="bi bi-box-arrow-right"></i>
                                </div>
                                <div>
                                    <div class="menu-item-title text-danger">Keluar (Logout)</div>
                                    <div class="menu-item-desc">Akhiri sesi di perangkat ini</div>
                                </div>
                            </button>
                        </form>
                    </div>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userBtn = document.getElementById('userMenuDropdown');
        if (!userBtn) return;
        
        const userDropdown = userBtn.closest('.nav-user-dropdown');
        const userMenu = userDropdown ? userDropdown.querySelector('.nav-user-menu') : null;

        if (userBtn && userDropdown && userMenu) {
            userBtn.addEventListener('click', function(e) {
                // If bootstrap bundle is not loaded or doesn't toggle
                if (typeof bootstrap === 'undefined' || !bootstrap.Dropdown) {
                    e.preventDefault();
                    e.stopPropagation();
                    const isShown = userMenu.classList.contains('show');
                    if (isShown) {
                        userMenu.classList.remove('show');
                        userDropdown.classList.remove('show');
                        userBtn.setAttribute('aria-expanded', 'false');
                    } else {
                        userMenu.classList.add('show');
                        userDropdown.classList.add('show');
                        userBtn.setAttribute('aria-expanded', 'true');
                    }
                }
            });

            document.addEventListener('click', function(e) {
                if (!userDropdown.contains(e.target)) {
                    userMenu.classList.remove('show');
                    userDropdown.classList.remove('show');
                    userBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
</script>

