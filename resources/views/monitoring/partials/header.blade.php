        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h1 class="device-title mb-0">
                        <i class="bi {{ $device->type === 'aws' ? 'bi-cloud-sun' : 'bi-flower1' }} me-2"></i>
                        @if($isAdminView ?? false)
                            {{ $device->name }}
                        @else
                            {{ $userDevice->custom_name }}
                        @endif
                    </h1>
                    <div class="d-flex gap-2 align-items-center">
                        @if($isOnline ?? false)
                            <span class="device-type-badge" id="conn-badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); font-size: 0.8rem; padding: 0.35rem 0.8rem; height: fit-content;">
                                <i class="bi bi-wifi me-1"></i> ONLINE
                            </span>
                        @else
                            <span class="device-type-badge" id="conn-badge" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); font-size: 0.8rem; padding: 0.35rem 0.8rem; height: fit-content;">
                                <i class="bi bi-wifi-off me-1"></i> OFFLINE
                            </span>
                        @endif
                        

                        <span class="device-type-badge" style="font-size: 0.8rem; padding: 0.35rem 0.8rem; height: fit-content;">
                            {{ strtoupper($device->type ?? 'DEVICE') }}
                        </span>
                    </div>
                </div>
                <p class="mb-0 mt-2" style="color: var(--text-secondary);" id="last-update-text">
                    <span class="live-dot me-2" id="live-dot" style="display: {{ ($isOnline ?? false) ? 'inline-block' : 'none' }};"></span>
                    @if($lastSeen)
                        Terakhir aktif: {{ \Carbon\Carbon::parse($lastSeen)->diffForHumans() }}
                    @elseif($latestData && $latestData->recorded_at)
                        Terakhir update: {{ \Carbon\Carbon::parse($latestData->recorded_at)->diffForHumans() }}
                    @else
                        Menunggu data...
                    @endif
                </p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap justify-content-md-end header-actions-group">
                @if($device->type !== 'smart_farm')
                    <a href="{{ isset($isAdminView) && $isAdminView ? route('admin.device.history', $device->id) : route('monitoring.history', $userDevice->id) }}" class="btn btn-history btn-action-custom">
                        <i class="bi bi-clock-history me-1"></i> <span>Riwayat Data</span>
                    </a>
                    @if($scheduleConfig ?? false)
                        <a href="{{ ($isAdminView ?? false) ? route('schedule.index', $device->id) : route('schedule.index', $userDevice->id) }}" class="btn btn-app btn-action-custom">
                            <i class="bi bi-calendar-check me-1"></i> <span>Jadwal</span>
                        </a>
                    @endif
                @endif
                @if($hasAutomation ?? false)
                    <a href="{{ ($isAdminView ?? false) ? route('automasi.index', $device->id) : route('automasi.index', $userDevice->id) }}" class="btn btn-automation btn-action-custom">
                        <i class="bi bi-cpu me-1"></i> <span>Otomasi</span>
                    </a>
                @endif
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; color: #fca5a5; backdrop-filter: blur(10px);">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success mb-4" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px; color: #6ee7b7; backdrop-filter: blur(10px);">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif
