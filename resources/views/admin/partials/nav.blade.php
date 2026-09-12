@php
    $currentRoute = Route::currentRouteName() ?? '';
    
    // Hitung badge notifikasi jika ada
    $pendingTicketsCount = \App\Models\Ticket::whereIn('status', ['open', 'in_progress'])->count();
@endphp

<style>
    /* =========================================================
       DESKTOP VIEW: Horizontal Nav Pills (>= 768px)
       ========================================================= */
    .admin-nav-desktop {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 0.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        margin-bottom: 1.75rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
        overflow-x: auto;
        white-space: nowrap;
        scrollbar-width: thin;
    }

    .admin-nav-desktop::-webkit-scrollbar {
        height: 4px;
    }

    .admin-nav-desktop::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .admin-nav-tab {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748b;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
        flex-shrink: 0;
    }

    .admin-nav-tab i {
        font-size: 1.05rem;
        transition: transform 0.2s ease;
    }

    .admin-nav-tab:hover {
        color: #0e5f8a;
        background: #f8fafc;
        border-color: #e2e8f0;
        transform: translateY(-1px);
    }

    .admin-nav-tab.active {
        background: linear-gradient(135deg, #0e5f8a 0%, #0284c7 100%);
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(14, 95, 138, 0.28);
        border-color: transparent;
    }

    .admin-nav-tab.active i {
        color: #ffffff;
    }

    .admin-nav-badge {
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 10px;
        font-weight: 700;
    }

    .admin-nav-badge-warn {
        background: #fef3c7;
        color: #d97706;
    }

    .admin-nav-tab.active .admin-nav-badge-warn {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* =========================================================
       MOBILE VIEW: 2-Column Grid Tiles (< 768px)
       Semua menu terlihat jelas tanpa terpotong
       ========================================================= */
    .admin-nav-mobile {
        margin-bottom: 1.25rem;
    }

    .admin-nav-tab-m {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.65rem;
        border-radius: 12px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #475569;
        text-decoration: none;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
        min-height: 42px;
    }

    .admin-nav-tab-m i {
        font-size: 1rem;
        flex-shrink: 0;
        color: #0e5f8a;
    }

    .admin-nav-tab-m span {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .admin-nav-tab-m:hover,
    .admin-nav-tab-m:active {
        background: #f8fafc;
        color: #0e5f8a;
        border-color: #cbd5e1;
    }

    .admin-nav-tab-m.active {
        background: linear-gradient(135deg, #0e5f8a 0%, #0284c7 100%);
        color: #ffffff !important;
        border-color: transparent;
        box-shadow: 0 3px 10px rgba(14, 95, 138, 0.25);
    }

    .admin-nav-tab-m.active i {
        color: #ffffff !important;
    }

    .admin-nav-tab-m .badge-count {
        margin-left: auto;
        font-size: 0.68rem;
        padding: 2px 5px;
        border-radius: 8px;
        background: #fef3c7;
        color: #d97706;
        font-weight: 700;
    }

    .admin-nav-tab-m.active .badge-count {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }
</style>

{{-- TAMPILAN DESKTOP (>= 768px): Horizontal Bar --}}
<div class="admin-nav-desktop d-none d-md-flex">
    {{-- 1. Perangkat IoT --}}
    <a href="{{ route('admin.devices.index') }}" 
       class="admin-nav-tab {{ str_starts_with($currentRoute, 'admin.device') || $currentRoute === 'admin.devices.index' ? 'active' : '' }}">
        <i class="bi bi-cpu-fill"></i>
        <span>Perangkat IoT</span>
    </a>

    {{-- 2. Kelola Pengguna --}}
    <a href="{{ route('admin.users.index') }}" 
       class="admin-nav-tab {{ str_starts_with($currentRoute, 'admin.users') ? 'active' : '' }}">
        <i class="bi bi-people-fill"></i>
        <span>Pengguna</span>
    </a>

    {{-- 3. Aturan Sensor & FCM --}}
    <a href="{{ route('admin.sensor-rules.index') }}" 
       class="admin-nav-tab {{ str_starts_with($currentRoute, 'admin.sensor-rules') ? 'active' : '' }}">
        <i class="bi bi-bell-fill"></i>
        <span>Aturan Sensor</span>
    </a>

    {{-- 4. Pengumuman Broadcast --}}
    <a href="{{ route('admin.announcements.index') }}" 
       class="admin-nav-tab {{ str_starts_with($currentRoute, 'admin.announcements') ? 'active' : '' }}">
        <i class="bi bi-megaphone-fill"></i>
        <span>Pengumuman</span>
    </a>

    {{-- 5. Tiket Bantuan --}}
    <a href="{{ route('admin.tickets.index') }}" 
       class="admin-nav-tab {{ str_starts_with($currentRoute, 'admin.tickets') ? 'active' : '' }}">
        <i class="bi bi-inboxes-fill"></i>
        <span>Tiket Dukungan</span>
        @if($pendingTicketsCount > 0)
            <span class="admin-nav-badge admin-nav-badge-warn">{{ $pendingTicketsCount }}</span>
        @endif
    </a>

    {{-- 6. Log Aktivitas --}}
    <a href="{{ route('admin.activity-logs') }}" 
       class="admin-nav-tab {{ $currentRoute === 'admin.activity-logs' ? 'active' : '' }}">
        <i class="bi bi-journal-text"></i>
        <span>Log Sistem</span>
    </a>
</div>

{{-- TAMPILAN MOBILE (< 768px): 2-Kolom Grid, Semua 6 Menu Terlihat Jelas --}}
<div class="admin-nav-mobile d-md-none">
    <div class="row g-2">
        {{-- 1. Perangkat IoT --}}
        <div class="col-6">
            <a href="{{ route('admin.devices.index') }}" 
               class="admin-nav-tab-m {{ str_starts_with($currentRoute, 'admin.device') || $currentRoute === 'admin.devices.index' ? 'active' : '' }}">
                <i class="bi bi-cpu-fill"></i>
                <span>Perangkat IoT</span>
            </a>
        </div>

        {{-- 2. Pengguna --}}
        <div class="col-6">
            <a href="{{ route('admin.users.index') }}" 
               class="admin-nav-tab-m {{ str_starts_with($currentRoute, 'admin.users') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i>
                <span>Pengguna</span>
            </a>
        </div>

        {{-- 3. Aturan Sensor --}}
        <div class="col-6">
            <a href="{{ route('admin.sensor-rules.index') }}" 
               class="admin-nav-tab-m {{ str_starts_with($currentRoute, 'admin.sensor-rules') ? 'active' : '' }}">
                <i class="bi bi-bell-fill"></i>
                <span>Aturan Sensor</span>
            </a>
        </div>

        {{-- 4. Pengumuman --}}
        <div class="col-6">
            <a href="{{ route('admin.announcements.index') }}" 
               class="admin-nav-tab-m {{ str_starts_with($currentRoute, 'admin.announcements') ? 'active' : '' }}">
                <i class="bi bi-megaphone-fill"></i>
                <span>Pengumuman</span>
            </a>
        </div>

        {{-- 5. Tiket Dukungan --}}
        <div class="col-6">
            <a href="{{ route('admin.tickets.index') }}" 
               class="admin-nav-tab-m {{ str_starts_with($currentRoute, 'admin.tickets') ? 'active' : '' }}">
                <i class="bi bi-inboxes-fill"></i>
                <span>Tiket Bantuan</span>
                @if($pendingTicketsCount > 0)
                    <span class="badge-count">{{ $pendingTicketsCount }}</span>
                @endif
            </a>
        </div>

        {{-- 6. Log Sistem --}}
        <div class="col-6">
            <a href="{{ route('admin.activity-logs') }}" 
               class="admin-nav-tab-m {{ $currentRoute === 'admin.activity-logs' ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i>
                <span>Log Sistem</span>
            </a>
        </div>
    </div>
</div>
