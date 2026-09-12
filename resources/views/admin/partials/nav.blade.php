@php
    $currentRoute = Route::currentRouteName() ?? '';
    
    // Hitung badge notifikasi jika ada
    $pendingTicketsCount = \App\Models\Ticket::whereIn('status', ['open', 'in_progress'])->count();
@endphp

<style>
    .admin-nav-tabs-container {
        position: relative;
        margin-bottom: 1.75rem;
    }

    .admin-nav-tabs-wrap {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 0.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        display: flex;
        align-items: center;
        gap: 0.35rem;
        overflow-x: auto;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scroll-behavior: smooth;
    }

    .admin-nav-tabs-wrap::-webkit-scrollbar {
        height: 4px;
    }

    .admin-nav-tabs-wrap::-webkit-scrollbar-thumb {
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

    @media (max-width: 576px) {
        .admin-nav-tabs-container {
            margin-bottom: 1.25rem;
        }

        .admin-nav-tabs-wrap {
            padding: 0.35rem;
            gap: 0.25rem;
            border-radius: 14px;
            scrollbar-width: none;
        }

        .admin-nav-tabs-wrap::-webkit-scrollbar {
            display: none;
        }

        .admin-nav-tab {
            padding: 0.45rem 0.8rem;
            font-size: 0.82rem;
            border-radius: 10px;
            gap: 0.4rem;
        }

        .admin-nav-tab i {
            font-size: 0.95rem;
        }
    }
</style>

<div class="admin-nav-tabs-container">
    <div class="admin-nav-tabs-wrap" id="adminNavTabsWrap">
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
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const wrap = document.getElementById('adminNavTabsWrap');
        if (!wrap) return;
        const activeTab = wrap.querySelector('.admin-nav-tab.active');
        if (activeTab) {
            // Smoothly scroll active tab into view if partially hidden
            const wrapRect = wrap.getBoundingClientRect();
            const tabRect = activeTab.getBoundingClientRect();
            if (tabRect.right > wrapRect.right || tabRect.left < wrapRect.left) {
                activeTab.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }
        }
    });
</script>
