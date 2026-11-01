@php
    $isSiram = !empty($sfStatus['siram']);
    $siramBlok = $sfStatus['blok'] ?? 1;
    $siramPupuk = ($sfStatus['pupuk'] ?? '') === 'ON';
    // Format countdown sisa (MMDD: 2 digit menit, 2 digit detik)
    $sisaRaw = isset($sfStatus['sisa_raw']) ? (string)$sfStatus['sisa_raw'] : (isset($sfStatus['sisa']) && strlen((string)$sfStatus['sisa']) === 4 ? (string)$sfStatus['sisa'] : null);
    if ($sisaRaw) {
        $sisaMenit = (int) substr($sisaRaw, 0, 2);
        $sisaDetikMod = (int) substr($sisaRaw, 2, 2);
        $sisaDetik = ($sisaMenit * 60) + $sisaDetikMod;
    } else {
        $sisaDetik = (int) ($sfStatus['sisa'] ?? 0);
        $sisaMenit = floor($sisaDetik / 60);
        $sisaDetikMod = $sisaDetik % 60;
    }
    $sfError = !empty($sfStatus['error']);
    $sfJam = $sfStatus['jam'] ?? null;
    $sfTimezone = $sfStatus['timezone'] ?? \Cache::get("device_timezone_{$device->id}", 'WIB');
    $sfJamStr = $sfJam !== null ? str_pad((string)$sfJam, 4, '0', STR_PAD_LEFT) : null;
    $jamFormatted = $sfJamStr && strlen($sfJamStr) === 4 ? substr($sfJamStr, 0, 2) . ':' . substr($sfJamStr, 2, 2) : null;

    $sfPompa = $outputs->firstWhere('output_name', 'sf_pompa');
    $sfBlok1 = $outputs->firstWhere('output_name', 'sf_blok1');
    $sfBlok2 = $outputs->firstWhere('output_name', 'sf_blok2');
    $sfBlok3 = $outputs->firstWhere('output_name', 'sf_blok3');
    $sfPupuk = $outputs->firstWhere('output_name', 'sf_pupuk');

    $isPompaActive = (bool) (($sfPompa && (float)$sfPompa->current_value > 0) || $isSiram);
    $isPupukActive = (bool) (($sfPupuk && (float)$sfPupuk->current_value > 0) || $siramPupuk);
    $activeBlokCount = ($sfBlok1 && (float)$sfBlok1->current_value > 0 ? 1 : 0) + 
                       ($sfBlok2 && (float)$sfBlok2->current_value > 0 ? 1 : 0) + 
                       ($sfBlok3 && (float)$sfBlok3->current_value > 0 ? 1 : 0);

    $otherOutputs = $outputs->whereNotIn('output_name', ['sf_pompa', 'sf_blok1', 'sf_blok2', 'sf_blok3', 'sf_pupuk']);

    $sfMode = $sfStatus['mode'] ?? null;
    if (!$sfMode) {
        if ($isSiram && $sisaDetik > 0) {
            $sfMode = 'OTOMATIS';
        } elseif ($isSiram || $isPompaActive || $isPupukActive || $activeBlokCount > 0) {
            $sfMode = 'MANUAL';
        } else {
            $sfMode = 'STANDBY';
        }
    }
@endphp

<!-- LEVEL 1: HERO STATUS & OPERATIONAL COMMAND BAR (MODERN SLEEK) -->
<div class="sf-hero-card mb-3" id="sf-status-card">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2.5">
        <!-- Status Info Left -->
        <div class="d-flex align-items-center gap-2.5">
            <div class="sf-status-icon-box" id="sf-icon-wrapper" style="background: {{ $sfMode === 'OTOMATIS' ? '#10b981' : ($sfMode === 'MANUAL' ? '#0ea5e9' : '#64748b') }};">
                <i class="bi {{ ($sfMode === 'OTOMATIS' || $sfMode === 'MANUAL') ? 'bi-droplet-fill' : 'bi-water' }}" id="sf-icon-main"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-1.5 flex-wrap">
                    <span class="sf-mode-badge {{ $sfMode === 'OTOMATIS' ? 'mode-auto' : ($sfMode === 'MANUAL' ? 'mode-manual' : 'mode-standby') }}" id="sf-badge-siram">
                        <i class="bi {{ $sfMode === 'OTOMATIS' ? 'bi-clock-history' : ($sfMode === 'MANUAL' ? 'bi-play-circle-fill' : 'bi-pause-circle') }}" id="sf-icon-badge"></i>
                        <span id="sf-text-siram">
                            @if($sfMode === 'OTOMATIS')
                                OTOMATIS (BLOK {{ $siramBlok }})
                            @elseif($sfMode === 'MANUAL')
                                @if($siramBlok > 0)
                                    MANUAL (BLOK {{ $siramBlok }})
                                @elseif($isPompaActive)
                                    MANUAL (POMPA)
                                @elseif($isPupukActive)
                                    MANUAL (PUPUK)
                                @else
                                    MANUAL (AKTIF)
                                @endif
                            @else
                                SIAGA (STANDBY)
                            @endif
                        </span>
                    </span>
                    <span class="sf-rtc-pill" id="sf-badge-jam" title="Waktu RTC alat. Klik untuk sinkronkan!" onclick="openRtcSyncModal()">
                        <i class="bi bi-clock text-primary"></i> <span id="sf-text-jam">{{ $jamFormatted ? ($jamFormatted . ' ' . $sfTimezone) : '--:--' }}</span>
                        <i class="bi bi-arrow-repeat ms-0.5 text-muted" id="sf-icon-sync-rtc"></i>
                    </span>
                    @if($sfError)
                        <button type="button" class="badge bg-danger text-white border-0 py-1 px-2 d-inline-flex align-items-center gap-1 rounded" id="sf-badge-error" onclick="resetRelayErrorQuick()">
                            <i class="bi bi-exclamation-triangle-fill"></i> Reset Relay
                        </button>
                    @endif
                </div>
                <div class="sf-detail-text" id="sf-detail-siram">
                    @if($sfMode === 'OTOMATIS')
                        Siram Otomatis <strong>Blok {{ $siramBlok }}</strong>
                        @if(($sisaMenit ?? 0) > 0 || ($sisaDetikMod ?? 0) > 0)
                            &bull; Sisa: <strong class="text-success font-monospace" id="sf-sisa-waktu">{{ $sisaMenit }}m {{ $sisaDetikMod }}s</strong>
                        @endif
                        @if($siramPupuk)
                            &bull; <span class="text-warning fw-bold"><i class="bi bi-droplet-half me-1"></i>Pupuk ON</span>
                        @endif
                    @elseif($sfMode === 'MANUAL')
                        Siram Manual <strong>Blok {{ $siramBlok }}</strong> &bull; <span class="text-primary fw-bold">Aktif</span>
                        @if($siramPupuk)
                            &bull; <span class="text-warning fw-bold"><i class="bi bi-droplet-half me-1"></i>Pupuk ON</span>
                        @endif
                    @else
                        Sistem irigasi siaga &bull; Pompa & katup siap
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Buttons Right -->
        <div class="d-flex align-items-center gap-1.5 sf-actions-group" id="sf-actions-container">
            @if($isSiram || $isPompaActive || $isPupukActive || $sfMode === 'MANUAL' || $sfMode === 'OTOMATIS')
                <button type="button" id="sf-btn-stop" class="btn-op btn-op-danger" onclick="stopSiramQuick()">
                    <i class="bi bi-stop-circle-fill"></i> Stop Siram
                </button>
            @else
                <button type="button" id="sf-btn-start" class="btn-op btn-op-primary" onclick="openSmartFarmSiramModal()">
                    <i class="bi bi-play-circle-fill"></i> Siram Manual
                </button>
            @endif
            <a href="{{ ($isAdminView ?? false) ? route('schedule.index', $device->id) : route('schedule.index', $userDevice->id) }}" class="btn-op btn-op-outline">
                <i class="bi bi-calendar-check text-primary"></i> Kelola Jadwal
            </a>
            <button type="button" class="btn-op btn-op-outline" onclick="openRtcSyncModal()" title="Sinkronkan Waktu RTC">
                <i class="bi bi-clock-history text-muted"></i> Jam RTC
            </button>
        </div>
    </div>
</div>

<!-- LEVEL 2: LIVE IRRIGATION PIPELINE VISUALIZER (MODERN COMPACT TRACK) -->
<div class="sf-pipeline-card mb-3">
    <div class="d-flex align-items-center justify-content-between mb-2">
        <span class="sf-section-label">
            <i class="bi bi-diagram-3-fill text-primary me-1"></i> Alur Distribusi Irigasi & Fertigasi
        </span>
    </div>
    <div class="pipeline-scroll-wrapper">
        <div class="pipeline-track">
            <!-- Node 1: Pompa Utama -->
            <div class="pipeline-node node-pompa {{ $isPompaActive ? 'active' : '' }}" id="pipe-node-pompa">
                <div class="pipeline-node-icon">
                    <i class="bi bi-water"></i>
                </div>
                <div class="pipeline-node-label">Pompa Utama</div>
                <div class="pipeline-node-status" id="pipe-status-pompa">{{ $isPompaActive ? 'MEMOMPA' : 'OFF' }}</div>
            </div>

            <!-- Connector 1 -->
            <div class="pipeline-connector {{ $isPompaActive ? 'active' : '' }}" id="pipe-conn-1"></div>

            <!-- Node 2: Dosing Pupuk -->
            <div class="pipeline-node node-pupuk {{ $isPupukActive ? 'active' : '' }}" id="pipe-node-pupuk">
                <div class="pipeline-node-icon">
                    <svg class="sf-icon-pupuk" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 2v5L4.5 17.5A2.5 2.5 0 0 0 6.6 21h10.8a2.5 2.5 0 0 0 2.1-3.5L14 7V2"></path>
                        <line x1="8.5" y1="2" x2="15.5" y2="2"></line>
                        <path d="M7.5 15h9"></path>
                        <circle cx="12" cy="11.5" r="1" fill="currentColor"></circle>
                    </svg>
                </div>
                <div class="pipeline-node-label">Injeksi Pupuk</div>
                <div class="pipeline-node-status" id="pipe-status-pupuk">{{ $isPupukActive ? 'INJEKSI' : 'STANDBY' }}</div>
            </div>

            <!-- Connector 2 -->
            <div class="pipeline-connector {{ $isPompaActive ? 'active' : '' }}" id="pipe-conn-2"></div>

            <!-- Node 3: Blok 1 -->
            @php
                $isB1Flow = ($sfBlok1?->current_value && $isPompaActive) || ($isSiram && $siramBlok == 1);
                $isB2Flow = ($sfBlok2?->current_value && $isPompaActive) || ($isSiram && $siramBlok == 2);
                $isB3Flow = ($sfBlok3?->current_value && $isPompaActive) || ($isSiram && $siramBlok == 3);
            @endphp
            <div class="pipeline-node node-blok1 {{ $isB1Flow ? 'active' : '' }}" id="pipe-node-blok1">
                <div class="pipeline-node-icon">1</div>
                <div class="pipeline-node-label">Blok 1</div>
                <div class="pipeline-node-status" id="pipe-status-blok1">{{ $isB1Flow ? 'MENGALIR' : ($sfBlok1?->current_value ? 'BUKA' : 'TUTUP') }}</div>
            </div>

            <!-- Connector 3 -->
            <div class="pipeline-connector {{ $isB2Flow ? 'active' : '' }}" id="pipe-conn-3"></div>

            <!-- Node 4: Blok 2 -->
            <div class="pipeline-node node-blok2 {{ $isB2Flow ? 'active' : '' }}" id="pipe-node-blok2">
                <div class="pipeline-node-icon">2</div>
                <div class="pipeline-node-label">Blok 2</div>
                <div class="pipeline-node-status" id="pipe-status-blok2">{{ $isB2Flow ? 'MENGALIR' : ($sfBlok2?->current_value ? 'BUKA' : 'TUTUP') }}</div>
            </div>

            <!-- Connector 4 -->
            <div class="pipeline-connector {{ $isB3Flow ? 'active' : '' }}" id="pipe-conn-4"></div>

            <!-- Node 5: Blok 3 -->
            <div class="pipeline-node node-blok3 {{ $isB3Flow ? 'active' : '' }}" id="pipe-node-blok3">
                <div class="pipeline-node-icon">3</div>
                <div class="pipeline-node-label">Blok 3</div>
                <div class="pipeline-node-status" id="pipe-status-blok3">{{ $isB3Flow ? 'MENGALIR' : ($sfBlok3?->current_value ? 'BUKA' : 'TUTUP') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- LEVEL 3: SUPLAI AIR & FERTIGASI -->
<div class="row g-2.5 mb-3">
    <!-- 1.1 POMPA UTAMA (AIR) -->
    @if($sfPompa)
    <div class="col-12 col-md-6">
        <div class="sf-hub-card h-100 {{ $isPompaActive ? 'border-primary' : '' }}" id="output-card-{{ $sfPompa->id }}">
            <div class="sf-hub-header">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="sf-actuator-icon icon-pompa">
                        <i class="bi bi-water"></i>
                    </div>
                    <div>
                        <h6 class="sf-hub-title">Pompa Air Utama</h6>
                        <div class="sf-hub-subtitle">Suplai air bertekanan utama ke pipa irigasi</div>
                    </div>
                </div>
                <span id="output-status-{{ $sfPompa->id }}" class="output-status {{ $isPompaActive ? 'on' : 'off' }}" data-on-text="MEMOMPA" data-off-text="OFF">
                    {{ $isPompaActive ? 'MEMOMPA' : 'OFF' }}
                </span>
            </div>

            <div class="segmented-control mt-2">
                <button type="button" class="segmented-btn {{ $isPompaActive ? 'active-on' : '' }}" onclick="openSmartFarmSiramModal()" id="btn-on-{{ $sfPompa->id }}">
                    <i class="bi bi-power me-1"></i> ON (SIRAM)
                </button>
                <button type="button" class="segmented-btn {{ !$isPompaActive ? 'active-off' : '' }}" onclick="stopSmartFarmSiram()" id="btn-off-{{ $sfPompa->id }}">
                    OFF
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- 1.2 INJEKSI PUPUK CAIR -->
    @if($sfPupuk)
    @php
        $isPupukOn = (bool) ($sfPupuk->current_value ?? 0);
    @endphp
    <div class="col-12 col-md-6">
        <div class="sf-hub-card h-100 {{ $isPupukOn ? 'border-warning' : '' }}" id="output-card-{{ $sfPupuk->id }}">
            <div class="sf-hub-header">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="sf-actuator-icon icon-pupuk">
                        <svg class="sf-icon-pupuk" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M10 2v5L4.5 17.5A2.5 2.5 0 0 0 6.6 21h10.8a2.5 2.5 0 0 0 2.1-3.5L14 7V2"></path>
                            <line x1="8.5" y1="2" x2="15.5" y2="2"></line>
                            <path d="M7.5 15h9"></path>
                            <circle cx="12" cy="11.5" r="1" fill="currentColor"></circle>
                        </svg>
                    </div>
                    <div>
                        <h6 class="sf-hub-title">Injeksi Pupuk Cair</h6>
                        <div class="sf-hub-subtitle">Dosing nutrisi tanaman terlarut</div>
                    </div>
                </div>
                <span id="output-status-{{ $sfPupuk->id }}" class="output-status {{ $isPupukOn ? 'on' : 'off' }}" data-on-text="INJEKSI AKTIF" data-off-text="STANDBY">
                    {{ $isPupukOn ? 'INJEKSI AKTIF' : 'STANDBY' }}
                </span>
            </div>

            <div class="segmented-control mt-2">
                <button type="button" class="segmented-btn {{ $isPupukOn ? 'active-on' : '' }}" onclick="setOutput({{ $sfPupuk->id }}, true)" id="btn-on-{{ $sfPupuk->id }}">
                    <i class="bi bi-power me-1"></i> ON (PUPUK)
                </button>
                <button type="button" class="segmented-btn {{ !$isPupukOn ? 'active-off' : '' }}" onclick="setOutput({{ $sfPupuk->id }}, false)" id="btn-off-{{ $sfPupuk->id }}">
                    OFF
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- LEVEL 4: INDIKATOR BLOK -->
<div class="sf-hub-card mb-3">
    <div class="sf-hub-header mb-2.5">
        <div class="d-flex align-items-center gap-2">
            <div class="sf-section-icon">
                <i class="bi bi-grid-3x3-gap-fill text-success"></i>
            </div>
            <div>
                <h6 class="sf-hub-title">Indikator Solenoid Blok</h6>
                <div class="sf-hub-subtitle">Status katup dan aliran air per zona lahan</div>
            </div>
        </div>
        <span class="badge bg-light text-muted border px-2 py-0.5" style="font-size: 0.7rem;">3 Blok</span>
    </div>

    <div class="sf-blok-indicators-grid">
        @php
            $blocks = [
                ['num' => 1, 'name' => 'Blok 1', 'obj' => $sfBlok1, 'color' => '#10b981'],
                ['num' => 2, 'name' => 'Blok 2', 'obj' => $sfBlok2, 'color' => '#0ea5e9'],
                ['num' => 3, 'name' => 'Blok 3', 'obj' => $sfBlok3, 'color' => '#8b5cf6'],
            ];
        @endphp

        @foreach($blocks as $b)
            @php
                $blk = $b['obj'];
                $isBlkOn = (bool) ($blk?->current_value ?? 0);
                $isFlowing = ($isBlkOn && $isPompaActive) || ($isSiram && $siramBlok == $b['num']);
            @endphp
            <div class="sf-zone-simple {{ $isFlowing ? 'active-flow' : '' }}" 
                 id="output-card-{{ $blk?->id }}"
                 style="--zone-color: {{ $b['color'] }};">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center gap-1.5 min-w-0">
                        <div class="sf-zone-avatar-sm" style="border-color: {{ $b['color'] }}; color: {{ $b['color'] }};">
                            {{ $b['num'] }}
                        </div>
                        <div class="min-w-0">
                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.76rem; line-height: 1.1;">{{ $b['name'] }}</div>
                            <div class="d-flex align-items-center gap-1 text-muted" style="font-size: 0.6rem;">
                                <i class="bi {{ $isBlkOn ? 'bi-unlock-fill text-success' : 'bi-lock-fill text-muted' }}" id="sf-valve-icon-blok{{ $b['num'] }}"></i>
                                <span class="output-status {{ $isBlkOn ? 'on' : 'off' }}" 
                                      id="output-status-{{ $blk?->id }}"
                                      data-on-text="BUKA"
                                      data-off-text="TUTUP">
                                    {{ $isBlkOn ? 'BUKA' : 'TUTUP' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 flex-shrink-0">
                        <span class="badge {{ $isFlowing ? 'bg-success text-white' : 'bg-light text-muted border' }}"
                              id="sf-flow-badge-blok{{ $b['num'] }}"
                              style="font-size: 0.6rem; padding: 2px 4px; font-weight: 700;">
                            {{ $isFlowing ? 'ALIR' : 'DIAM' }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Output Tambahan Lainnya (Jika Ada) -->
@if($otherOutputs->count() > 0)
<div class="output-panel mb-3">
    <h5 class="sf-section-label mb-2.5">
        <i class="bi bi-sliders me-1 text-primary"></i> Output Tambahan Lainnya
    </h5>
    <div class="row g-2.5">
        @foreach($otherOutputs as $output)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="output-card" id="output-card-{{ $output->id }}">
                    <div class="card-header-flex">
                        <div class="output-icon" style="background: {{ $output->color }};">
                            <i class="bi {{ $output->icon }}"></i>
                        </div>
                        <div class="output-label">{{ $output->output_label }}</div>
                    </div>
                    <div class="output-status {{ $output->current_value ? 'on' : 'off' }}" id="output-status-{{ $output->id }}">
                        {{ $output->current_value ? 'ON' : 'OFF' }}
                    </div>
                    <div class="segmented-control mt-2">
                        <button type="button" class="segmented-btn {{ $output->current_value ? 'active-on' : '' }}" onclick="setOutput({{ $output->id }}, true)" id="btn-on-{{ $output->id }}">ON</button>
                        <button type="button" class="segmented-btn {{ !$output->current_value ? 'active-off' : '' }}" onclick="setOutput({{ $output->id }}, false)" id="btn-off-{{ $output->id }}">OFF</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
