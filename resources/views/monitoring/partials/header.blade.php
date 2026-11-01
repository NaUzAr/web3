<div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <div class="d-flex align-items-center gap-2">
            <h1 class="device-title mb-0">
                @if($isAdminView ?? false)
                    {{ $device->name }}
                @else
                    {{ $userDevice->custom_name }}
                @endif
            </h1>
            @if($isOnline ?? false)
                <span class="device-type-badge badge-online" id="conn-badge">
                    <span class="status-beacon"></span> ONLINE
                </span>
            @else
                <span class="device-type-badge badge-offline" id="conn-badge">
                    <i class="bi bi-wifi-off me-1"></i> OFFLINE
                </span>
            @endif
        </div>
        <div class="d-flex align-items-center gap-2 text-muted" style="font-size: 0.75rem;" id="last-update-text">
            <span>&bull;</span>
            <span class="live-dot" id="live-dot" style="display: {{ ($isOnline ?? false) ? 'inline-block' : 'none' }};"></span>
            @if($lastSeen)
                Aktif {{ \Carbon\Carbon::parse($lastSeen)->diffForHumans() }}
            @elseif($latestData && $latestData->recorded_at)
                Update {{ \Carbon\Carbon::parse($latestData->recorded_at)->diffForHumans() }}
            @else
                Menunggu data...
            @endif
        </div>
    </div>

    <div class="d-flex gap-1.5 align-items-center header-actions-group">
        @if($device->type !== 'smart_farm')
            <a href="{{ isset($isAdminView) && $isAdminView ? route('admin.device.history', $device->id) : route('monitoring.history', $userDevice->id) }}" class="btn-action-modern">
                <i class="bi bi-clock-history"></i> <span>Riwayat</span>
            </a>
            @if($scheduleConfig ?? false)
                <a href="{{ ($isAdminView ?? false) ? route('schedule.index', $device->id) : route('schedule.index', $userDevice->id) }}" class="btn-action-modern">
                    <i class="bi bi-calendar-check"></i> <span>Jadwal</span>
                </a>
            @endif
        @endif
        @if($hasAutomation ?? false)
            <a href="{{ ($isAdminView ?? false) ? route('automasi.index', $device->id) : route('automasi.index', $userDevice->id) }}" class="btn-action-modern">
                <i class="bi bi-cpu"></i> <span>Otomasi</span>
            </a>
        @endif
    </div>
</div>

@if(session('error'))
    <div class="alert alert-danger mb-3 py-2 px-3 d-flex align-items-center gap-2" style="border-radius: 8px; font-size: 0.85rem;">
        <i class="bi bi-exclamation-triangle-fill"></i>{{ session('error') }}
    </div>
@endif
@if(session('success'))
    <div class="alert alert-success mb-3 py-2 px-3 d-flex align-items-center gap-2" style="border-radius: 8px; font-size: 0.85rem;">
        <i class="bi bi-check-circle-fill"></i>{{ session('success') }}
    </div>
@endif
