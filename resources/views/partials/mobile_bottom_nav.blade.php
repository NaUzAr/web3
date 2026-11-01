@php
    $currentRoute = Route::currentRouteName() ?? '';
    $currentUdid = isset($userDevice) && $userDevice ? $userDevice->id : (request()->route('userDevice') ? (is_object(request()->route('userDevice')) ? request()->route('userDevice')->id : request()->route('userDevice')) : null);
    $currentDevId = isset($device) && $device ? $device->id : (request()->route('device') ? (is_object(request()->route('device')) ? request()->route('device')->id : request()->route('device')) : null);
    $isAdminMode = isset($isAdminView) && $isAdminView;
@endphp

<div class="mobile-bottom-bar d-flex d-md-none" id="mobileBottomNav">
    {{-- 1. Perangkat / Hub --}}
    <a href="{{ route('monitoring.index') }}" class="mobile-nav-item {{ ($currentRoute === 'monitoring.index' || $currentRoute === 'home') ? 'active' : '' }}" title="Daftar Perangkat">
        <div class="mobile-nav-icon">
            <i class="bi bi-grid-fill"></i>
        </div>
        <span class="mobile-nav-label">Perangkat</span>
        <span class="mobile-nav-dot"></span>
    </a>

    {{-- 2. Monitoring (Active Device / Live Telemetri) --}}
    @if($currentUdid || $currentDevId)
        <a href="{{ $isAdminMode ? route('admin.device.monitoring', $currentDevId) : route('monitoring.show', $currentUdid) }}" 
           class="mobile-nav-item {{ (str_starts_with($currentRoute, 'monitoring.show') || str_starts_with($currentRoute, 'admin.device.monitoring')) ? 'active' : '' }}" 
           title="Live Monitoring">
            <div class="mobile-nav-icon">
                <i class="bi bi-speedometer2"></i>
            </div>
            <span class="mobile-nav-label">Monitoring</span>
            <span class="mobile-nav-dot"></span>
        </a>
    @else
        <a href="{{ route('monitoring.index') }}" 
           class="mobile-nav-item {{ (str_starts_with($currentRoute, 'monitoring.show') || str_starts_with($currentRoute, 'admin.device.monitoring')) ? 'active' : '' }}" 
           title="Live Monitoring">
            <div class="mobile-nav-icon">
                <i class="bi bi-speedometer2"></i>
            </div>
            <span class="mobile-nav-label">Monitoring</span>
            <span class="mobile-nav-dot"></span>
        </a>
    @endif

    {{-- 3. Jadwal (Schedule) atau Riwayat --}}
    @if($currentUdid || $currentDevId)
        <a href="{{ $isAdminMode ? route('schedule.index', $currentDevId) : route('schedule.index', $currentUdid) }}" 
           class="mobile-nav-item {{ str_starts_with($currentRoute, 'schedule.') ? 'active' : '' }}" 
           title="Jadwal Otomasi">
            <div class="mobile-nav-icon">
                <i class="bi bi-calendar-check"></i>
            </div>
            <span class="mobile-nav-label">Jadwal</span>
            <span class="mobile-nav-dot"></span>
        </a>
    @else
        <a href="{{ route('riwayat.index') }}" 
           class="mobile-nav-item {{ str_starts_with($currentRoute, 'riwayat.') ? 'active' : '' }}" 
           title="Riwayat Monitoring">
            <div class="mobile-nav-icon">
                <i class="bi bi-clock-history"></i>
            </div>
            <span class="mobile-nav-label">Riwayat</span>
            <span class="mobile-nav-dot"></span>
        </a>
    @endif

    {{-- 4. Bantuan (Support Tickets) --}}
    <a href="{{ route('tickets.index') }}" 
       class="mobile-nav-item {{ str_starts_with($currentRoute, 'tickets.') ? 'active' : '' }}" 
       title="Bantuan & Dukungan">
        <div class="mobile-nav-icon">
            <i class="bi bi-headset"></i>
        </div>
        <span class="mobile-nav-label">Bantuan</span>
        <span class="mobile-nav-dot"></span>
    </a>

    {{-- 5. Profil / Akun --}}
    <a href="{{ route('profile.index') }}" 
       class="mobile-nav-item {{ str_starts_with($currentRoute, 'profile.') ? 'active' : '' }}" 
       title="Profil Saya">
        <div class="mobile-nav-icon">
            <i class="bi bi-person-circle"></i>
        </div>
        <span class="mobile-nav-label">Profil</span>
        <span class="mobile-nav-dot"></span>
    </a>
</div>

