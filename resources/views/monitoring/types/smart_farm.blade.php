            @php
                $isSiram = !empty($sfStatus['siram']);
                $siramBlok = $sfStatus['blok'] ?? 1;
                $siramPupuk = ($sfStatus['pupuk'] ?? '') === 'ON';
                $sisaDetik = (int) ($sfStatus['sisa'] ?? 0);
                $sisaMenit = floor($sisaDetik / 60);
                $sisaDetikMod = $sisaDetik % 60;
                $sfError = !empty($sfStatus['error']);
                $sfJam = $sfStatus['jam'] ?? null;
                $sfTimezone = $sfStatus['timezone'] ?? \Cache::get("device_timezone_{$device->id}", 'WIB');
                $jamFormatted = $sfJam && strlen((string)$sfJam) === 4 ? substr((string)$sfJam, 0, 2) . ':' . substr((string)$sfJam, 2, 2) : null;

                $sfPompa = $outputs->firstWhere('output_name', 'sf_pompa');
                $sfBlok1 = $outputs->firstWhere('output_name', 'sf_blok1');
                $sfBlok2 = $outputs->firstWhere('output_name', 'sf_blok2');
                $sfBlok3 = $outputs->firstWhere('output_name', 'sf_blok3');
                $sfPupuk = $outputs->firstWhere('output_name', 'sf_pupuk');

                $isPompaActive = (bool) (($sfPompa && $sfPompa->current_value) || $isSiram);
                $otherOutputs = $outputs->whereNotIn('output_name', ['sf_pompa', 'sf_blok1', 'sf_blok2', 'sf_blok3', 'sf_pupuk']);
            @endphp

            <!-- ==================================================== -->
            <!-- 🌿 SMART FARM CONTROL CENTER: UNIFIED ARCHITECTURE   -->
            <!-- ==================================================== -->

            @php
                $isPupukActive = (bool) (($sfPupuk && $sfPupuk->current_value) || ($isSiram && $siramPupuk));
            @endphp

            <!-- LEVEL 1: HERO STATUS & OPERATIONAL COMMAND BAR -->
            <div class="sf-hero-card mb-4" id="sf-status-card">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center" id="sf-icon-wrapper" style="width: 58px; height: 58px; border-radius: 18px; background: {{ ($isSiram || $isPompaActive) ? 'linear-gradient(135deg, #059669, #10b981)' : 'linear-gradient(135deg, #0284c7, #38bdf8)' }}; color: white; font-size: 1.75rem; box-shadow: 0 8px 22px -4px rgba(0,0,0,0.18);">
                            <i class="bi {{ ($isSiram || $isPompaActive) ? 'bi-droplet-fill' : 'bi-water' }}" id="sf-icon-main"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge rounded-pill {{ ($isSiram || $isPompaActive) ? 'bg-success text-white' : 'bg-secondary text-white' }}" id="sf-badge-siram" style="padding: 6px 14px; font-weight: 700; letter-spacing: 0.5px;">
                                    <i class="bi {{ ($isSiram || $isPompaActive) ? 'bi-play-circle-fill' : 'bi-pause-circle' }} me-1" id="sf-icon-badge"></i>
                                    <span id="sf-text-siram">
                                        @if($isSiram)
                                            SEDANG MENYIRAM (BLOK {{ $siramBlok }})
                                        @elseif($isPompaActive)
                                            SEDANG MENYIRAM (MANUAL)
                                        @else
                                            SIAGA (STANDBY)
                                        @endif
                                    </span>
                                </span>
                                <span class="badge rounded-pill bg-light text-muted border small d-inline-flex align-items-center gap-1 shadow-sm" id="sf-badge-jam" title="Waktu RTC alat. Klik untuk sinkronkan zona waktu!" style="cursor: pointer; transition: all 0.2s;" onclick="openRtcSyncModal()">
                                    <i class="bi bi-clock text-primary"></i>RTC: <span id="sf-text-jam">{{ $jamFormatted ? ($jamFormatted . ' ' . $sfTimezone) : '--:--' }}</span>
                                    <i class="bi bi-arrow-repeat ms-1 text-muted" id="sf-icon-sync-rtc"></i>
                                </span>
                                @if($sfError)
                                    <button type="button" class="badge rounded-pill bg-danger text-white small border-0 d-inline-flex align-items-center gap-1 shadow-sm" id="sf-badge-error" title="Klik untuk Reset Error Relay pada alat" onclick="resetRelayErrorQuick()" style="cursor: pointer;">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Error Relay (Klik Reset)
                                    </button>
                                @endif
                            </div>
                            <div class="small text-muted" id="sf-detail-siram">
                                @if($isSiram)
                                    Menyiram <strong>Blok {{ $siramBlok }}</strong>
                                    @if(($sisaMenit ?? 0) > 0 || ($sisaDetikMod ?? 0) > 0)
                                        &bull; Sisa Waktu: <strong><span id="sf-sisa-waktu">{{ $sisaMenit }}m {{ $sisaDetikMod }}s</span></strong>
                                    @else
                                        &bull; <span class="text-success fw-bold"><i class="bi bi-play-circle-fill me-1"></i>Manual Aktif</span>
                                    @endif
                                    @if($siramPupuk)
                                        &bull; <span class="text-warning fw-bold d-inline-flex align-items-center gap-1">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M10 2v5L4.5 17.5A2.5 2.5 0 0 0 6.6 21h10.8a2.5 2.5 0 0 0 2.1-3.5L14 7V2"></path>
                                                <line x1="8.5" y1="2" x2="15.5" y2="2"></line>
                                                <path d="M7.5 15h9"></path>
                                                <circle cx="12" cy="11.5" r="1" fill="currentColor"></circle>
                                            </svg>
                                            Pupuk Aktif
                                        </span>
                                    @endif
                                @elseif($isPompaActive)
                                    Penyiraman manual sedang berjalan aktif.
                                @else
                                    Sistem irigasi multi-zona siap. Pompa dan katup solenoid dalam kondisi siaga.
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Operation Buttons -->
                    <div class="d-flex align-items-center gap-2 flex-wrap sf-actions-group" id="sf-actions-container">
                        @if($isSiram || $isPompaActive)
                            <button type="button" id="sf-btn-stop" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 50px; padding: 0.65rem 1.4rem; font-weight: 700;" onclick="stopSiramQuick()">
                                <i class="bi bi-stop-circle-fill"></i> Stop Siram
                            </button>
                        @else
                            <button type="button" id="sf-btn-start" class="btn btn-success btn-sm d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 50px; padding: 0.65rem 1.4rem; font-weight: 700; background: linear-gradient(135deg, #10b981, #059669);" onclick="openSmartFarmSiramModal()">
                                <i class="bi bi-play-circle-fill"></i> Siram Manual
                            </button>
                        @endif
                        <a href="{{ ($isAdminView ?? false) ? route('schedule.index', $device->id) : route('schedule.index', $userDevice->id) }}" class="btn btn-glass btn-sm d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 50px; padding: 0.65rem 1.3rem; font-weight: 600;">
                            <i class="bi bi-calendar-check text-primary"></i> Kelola Jadwal
                        </a>
                        <button type="button" class="btn btn-light btn-sm d-inline-flex align-items-center gap-1 border shadow-sm" style="border-radius: 50px; padding: 0.65rem 1.1rem; font-weight: 600; color: #4b5563;" onclick="openRtcSyncModal()" title="Sinkronkan Waktu RTC Device">
                            <i class="bi bi-clock-history text-primary"></i> Jam RTC
                        </button>
                    </div>
                </div>
            </div>

            <!-- LEVEL 2: LIVE IRRIGATION PIPELINE VISUALIZER -->
            <div class="sf-pipeline-card mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-muted d-flex align-items-center gap-1" style="font-size: 0.76rem; letter-spacing: 0.5px; text-transform: uppercase;">
                        <i class="bi bi-diagram-3-fill text-primary"></i> Alur Distribusi Irigasi & Fertigasi
                    </span>
                    <span class="small text-muted" style="font-size: 0.72rem;">
                        <i class="bi bi-shield-check text-success"></i> Auto-Interlock RS485
                    </span>
                </div>
                <div class="pipeline-scroll-wrapper">
                    <div class="pipeline-track">
                        <!-- Node 1: Pompa Utama -->
                        <div class="pipeline-node node-pompa {{ $isPompaActive ? 'active' : '' }}" id="pipe-node-pompa">
                            <div class="pipeline-node-icon" style="color: #0284c7; background: rgba(2, 132, 199, 0.08); border-color: rgba(2, 132, 199, 0.2);">
                                <i class="bi bi-water"></i>
                            </div>
                            <div class="pipeline-node-label">Pompa Utama</div>
                            <div class="pipeline-node-status" id="pipe-status-pompa">{{ $isPompaActive ? 'MEMOMPA' : 'OFF' }}</div>
                        </div>

                        <!-- Connector 1 -->
                        <div class="pipeline-connector {{ $isPompaActive ? 'active' : '' }}" id="pipe-conn-1"></div>

                        <!-- Node 2: Dosing Pupuk -->
                        <div class="pipeline-node node-pupuk {{ $isPupukActive ? 'active' : '' }}" id="pipe-node-pupuk">
                            <div class="pipeline-node-icon" style="color: #d97706; background: rgba(217, 119, 6, 0.08); border-color: rgba(217, 119, 6, 0.2);">
                                <svg class="sf-icon-pupuk" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                            <div class="pipeline-node-icon" style="color: #059669; background: rgba(16, 185, 129, 0.08); border-color: rgba(16, 185, 129, 0.2); font-weight: 800; font-size: 1.15rem;">
                                1
                            </div>
                            <div class="pipeline-node-label">Blok 1</div>
                            <div class="pipeline-node-status" id="pipe-status-blok1">{{ $isB1Flow ? 'MENGALIR' : ($sfBlok1?->current_value ? 'BUKA' : 'TUTUP') }}</div>
                        </div>

                        <!-- Node 4: Blok 2 -->
                        <div class="pipeline-node node-blok2 {{ $isB2Flow ? 'active' : '' }}" id="pipe-node-blok2">
                            <div class="pipeline-node-icon" style="color: #0284c7; background: rgba(14, 165, 233, 0.08); border-color: rgba(14, 165, 233, 0.2); font-weight: 800; font-size: 1.15rem;">
                                2
                            </div>
                            <div class="pipeline-node-label">Blok 2</div>
                            <div class="pipeline-node-status" id="pipe-status-blok2">{{ $isB2Flow ? 'MENGALIR' : ($sfBlok2?->current_value ? 'BUKA' : 'TUTUP') }}</div>
                        </div>

                        <!-- Node 5: Blok 3 -->
                        <div class="pipeline-node node-blok3 {{ $isB3Flow ? 'active' : '' }}" id="pipe-node-blok3">
                            <div class="pipeline-node-icon" style="color: #7c3aed; background: rgba(139, 92, 246, 0.08); border-color: rgba(139, 92, 246, 0.2); font-weight: 800; font-size: 1.15rem;">
                                3
                            </div>
                            <div class="pipeline-node-label">Blok 3</div>
                            <div class="pipeline-node-status" id="pipe-status-blok3">{{ $isB3Flow ? 'MENGALIR' : ($sfBlok3?->current_value ? 'BUKA' : 'TUTUP') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LEVEL 3: DUA HUB KONTROL UTAMA (SIDE-BY-SIDE GRID) -->
            <div class="row g-4 mb-4">
                <!-- ============================================== -->
                <!-- HUB 1: SUPLAI AIR & FERTIGASI (KOLOM KIRI - 5)  -->
                <!-- ============================================== -->
                <div class="col-12 col-lg-5">
                    <div class="sf-hub-card h-100">
                        <div class="sf-hub-header">
                            <div>
                                <h6 class="sf-hub-title">
                                    <i class="bi bi-droplet-half text-primary"></i> Suplai Air & Fertigasi
                                </h6>
                                <div class="sf-hub-subtitle">
                                    Kontrol pompa air utama dan injeksi nutrisi cair
                                </div>
                            </div>
                            <span class="badge rounded-pill bg-light text-muted border px-2 py-1 small">2 Unit</span>
                        </div>

                        <!-- 1.1 POMPA UTAMA (AIR) -->
                        @if($sfPompa)
                        <div class="sf-actuator-card {{ $isPompaActive ? 'active-pump' : '' }}" id="output-card-{{ $sfPompa->id }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="sf-actuator-icon" style="background: {{ $isPompaActive ? 'linear-gradient(135deg, #0284c7, #38bdf8)' : 'rgba(2, 132, 199, 0.1)' }}; color: {{ $isPompaActive ? '#ffffff' : '#0284c7' }}; border: 1px solid {{ $isPompaActive ? '#0284c7' : 'rgba(2, 132, 199, 0.2)' }};">
                                        <i class="bi bi-water"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">Pompa Utama</div>
                                        <div class="text-muted" style="font-size: 0.76rem;">Suplai air utama ke pipa</div>
                                    </div>
                                </div>
                                <span id="output-status-{{ $sfPompa->id }}" class="output-status {{ $isPompaActive ? 'on' : 'off' }} badge rounded-pill px-2.5 py-1 small m-0" data-on-text="MEMOMPA" data-off-text="OFF">
                                    {{ $isPompaActive ? 'MEMOMPA' : 'OFF' }}
                                </span>
                            </div>

                            <div class="segmented-control mt-2">
                                <button type="button" class="segmented-btn {{ $isPompaActive ? 'active-on' : '' }}" onclick="openSmartFarmSiramModal()" id="btn-on-{{ $sfPompa->id }}">
                                    <i class="bi bi-power me-1"></i> ON
                                </button>
                                <button type="button" class="segmented-btn {{ !$isPompaActive ? 'active-off' : '' }}" onclick="stopSmartFarmSiram()" id="btn-off-{{ $sfPompa->id }}">
                                    OFF
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- 1.2 INJEKSI PUPUK CAIR -->
                        @if($sfPupuk)
                        @php
                            $isPupukOn = (bool) ($sfPupuk->current_value ?? 0);
                        @endphp
                        <div class="sf-actuator-card {{ $isPupukOn ? 'active-dosing' : '' }}" id="output-card-{{ $sfPupuk->id }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="sf-actuator-icon" style="background: {{ $isPupukOn ? 'linear-gradient(135deg, #d97706, #f59e0b)' : 'rgba(217, 119, 6, 0.1)' }}; color: {{ $isPupukOn ? '#ffffff' : '#d97706' }}; border: 1px solid {{ $isPupukOn ? '#f59e0b' : 'rgba(217, 119, 6, 0.2)' }};">
                                        <svg class="sf-icon-pupuk" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M10 2v5L4.5 17.5A2.5 2.5 0 0 0 6.6 21h10.8a2.5 2.5 0 0 0 2.1-3.5L14 7V2"></path>
                                            <line x1="8.5" y1="2" x2="15.5" y2="2"></line>
                                            <path d="M7.5 15h9"></path>
                                            <circle cx="12" cy="11.5" r="1" fill="currentColor"></circle>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">Injeksi Pupuk</div>
                                        <div class="text-muted" style="font-size: 0.76rem;"><i class="bi bi-shield-check text-warning me-1"></i>Dosing nutrisi ke pipa</div>
                                    </div>
                                </div>
                                <span id="output-status-{{ $sfPupuk->id }}" class="output-status {{ $isPupukOn ? 'on' : 'off' }} badge rounded-pill px-2.5 py-1 small m-0" data-on-text="INJEKSI AKTIF" data-off-text="STANDBY">
                                    {{ $isPupukOn ? 'INJEKSI AKTIF' : 'STANDBY' }}
                                </span>
                            </div>

                            <div class="segmented-control mt-2">
                                <button type="button" class="segmented-btn {{ $isPupukOn ? 'active-on' : '' }}" onclick="setOutput({{ $sfPupuk->id }}, true)" id="btn-on-{{ $sfPupuk->id }}">
                                    <i class="bi bi-power me-1"></i> ON
                                </button>
                                <button type="button" class="segmented-btn {{ !$isPupukOn ? 'active-off' : '' }}" onclick="setOutput({{ $sfPupuk->id }}, false)" id="btn-off-{{ $sfPupuk->id }}">
                                    OFF
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- ==================================================== -->
                <!-- HUB 2: DISTRIBUSI ZONA LAHAN (KOLOM KANAN - 7)       -->
                <!-- ==================================================== -->
                <div class="col-12 col-lg-7">
                    <div class="sf-hub-card h-100">
                        <div class="sf-hub-header">
                            <div>
                                <h6 class="sf-hub-title">
                                    <i class="bi bi-grid-3x3-gap-fill text-success"></i> Distribusi Zona Lahan (Katup Solenoid)
                                </h6>
                                <div class="sf-hub-subtitle">
                                    Buka katup untuk mengalirkan air ke blok kebun yang dituju
                                </div>
                            </div>
                            <span class="badge rounded-pill bg-light text-muted border px-2 py-1 small">3 Zona Lahan</span>
                        </div>

                        <div class="row g-3 h-100">
                            @php
                                $blocks = [
                                    [
                                        'num' => 1,
                                        'name' => 'Blok 1',
                                        'zone' => 'Zona 1',
                                        'crop' => 'Tanaman Utama',
                                        'valve' => 'Katup Solenoid #1',
                                        'obj' => $sfBlok1,
                                        'icon' => 'bi-tree-fill',
                                        'color' => '#10b981',
                                        'color_dark' => '#059669',
                                        'gradient' => 'linear-gradient(135deg, #10b981, #059669)',
                                        'bg' => 'rgba(16, 185, 129, 0.12)',
                                        'border_light' => 'rgba(16, 185, 129, 0.25)',
                                        'glow' => 'rgba(16, 185, 129, 0.28)'
                                    ],
                                    [
                                        'num' => 2,
                                        'name' => 'Blok 2',
                                        'zone' => 'Zona 2',
                                        'crop' => 'Hortikultura / Sayur',
                                        'valve' => 'Katup Solenoid #2',
                                        'obj' => $sfBlok2,
                                        'icon' => 'bi-flower2',
                                        'color' => '#0ea5e9',
                                        'color_dark' => '#0284c7',
                                        'gradient' => 'linear-gradient(135deg, #0ea5e9, #0284c7)',
                                        'bg' => 'rgba(14, 165, 233, 0.12)',
                                        'border_light' => 'rgba(14, 165, 233, 0.25)',
                                        'glow' => 'rgba(14, 165, 233, 0.28)'
                                    ],
                                    [
                                        'num' => 3,
                                        'name' => 'Blok 3',
                                        'zone' => 'Zona 3',
                                        'crop' => 'Bibit & Pembesaran',
                                        'valve' => 'Katup Solenoid #3',
                                        'obj' => $sfBlok3,
                                        'icon' => 'bi-flower1',
                                        'color' => '#8b5cf6',
                                        'color_dark' => '#7c3aed',
                                        'gradient' => 'linear-gradient(135deg, #8b5cf6, #7c3aed)',
                                        'bg' => 'rgba(139, 92, 246, 0.12)',
                                        'border_light' => 'rgba(139, 92, 246, 0.25)',
                                        'glow' => 'rgba(139, 92, 246, 0.28)'
                                    ],
                                ];
                            @endphp

                            @foreach($blocks as $b)
                                @php
                                    $blk = $b['obj'];
                                    $isBlkOn = (bool) ($blk?->current_value ?? 0);
                                    $isFlowing = ($isBlkOn && $isPompaActive) || ($isSiram && $siramBlok == $b['num']);
                                @endphp
                                <div class="col-12 col-sm-6 col-xl-4">
                                    <div class="sf-zone-card {{ $isFlowing ? 'active-flow' : '' }}" 
                                         id="output-card-{{ $blk?->id }}"
                                         style="--zone-color: {{ $b['color'] }}; --zone-glow: {{ $b['glow'] }};">
                                        
                                        <!-- Top Accent Line -->
                                        <div class="sf-zone-accent" style="background: {{ $b['gradient'] }};"></div>

                                        <div>
                                            <!-- Zone Header: Avatar, Name, Zone Tag, Flow Status -->
                                            <div class="d-flex align-items-center justify-content-between mb-2.5">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="sf-zone-avatar" style="background: {{ $b['bg'] }}; color: {{ $b['color_dark'] }}; border: 1.5px solid {{ $b['border_light'] }}; font-weight: 800; font-size: 1.15rem;">
                                                        {{ $b['num'] }}
                                                    </div>
                                                    <div>
                                                        <div class="d-flex align-items-center gap-1.5">
                                                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $b['name'] }}</span>
                                                            <span class="sf-zone-pill-tag" style="background: {{ $b['bg'] }}; color: {{ $b['color_dark'] }}; border: 1px solid {{ $b['border_light'] }};">
                                                                {{ $b['zone'] }}
                                                            </span>
                                                        </div>
                                                        <div class="text-muted" style="font-size: 0.72rem; margin-top: 1px;">
                                                            <i class="bi bi-cpu text-muted me-1"></i>{{ $b['valve'] }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <span class="sf-flow-indicator" id="sf-flow-blok{{ $b['num'] }}" style="display: {{ $isFlowing ? 'inline-flex' : 'none' }};">
                                                    <span class="sf-flow-dot"></span>
                                                    <i class="bi bi-droplet-fill"></i> Mengalir
                                                </span>
                                            </div>

                                            <!-- Solenoid Valve Telemetry Box -->
                                            <div class="sf-zone-telemetry mb-2.5">
                                                <div class="d-flex align-items-center gap-1.5 text-muted" style="font-size: 0.74rem;">
                                                    <i class="bi {{ $isBlkOn ? 'bi-unlock-fill text-success' : 'bi-lock-fill text-muted' }}" style="font-size: 0.82rem;"></i>
                                                    <span class="fw-semibold">Katup Fisik:</span>
                                                </div>
                                                <span class="output-status {{ $isBlkOn ? 'on' : 'off' }} m-0"
                                                    id="output-status-{{ $blk?->id }}"
                                                    data-on-text="TERBUKA"
                                                    data-off-text="TERTUTUP"
                                                    style="letter-spacing: 0.3px; font-size: 0.72rem;">
                                                    {{ $isBlkOn ? 'TERBUKA' : 'TERTUTUP' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div>
                                            @if($blk)
                                            <div class="segmented-control mb-2">
                                                <button type="button" class="segmented-btn {{ $isBlkOn ? 'active-on' : '' }}"
                                                    onclick="setOutput({{ $blk->id }}, true)" id="btn-on-{{ $blk->id }}">
                                                    <i class="bi bi-unlock me-1"></i> BUKA
                                                </button>
                                                <button type="button" class="segmented-btn {{ !$isBlkOn ? 'active-off' : '' }}"
                                                    onclick="setOutput({{ $blk->id }}, false)" id="btn-off-{{ $blk->id }}">
                                                    TUTUP
                                                </button>
                                            </div>
                                            @endif

                                            <button type="button" 
                                                class="sf-zone-action-btn w-100 {{ $isFlowing ? 'active-action' : '' }}"
                                                onclick="openSmartFarmSiramModal({{ $b['num'] }})">
                                                <i class="bi {{ $isFlowing ? 'bi-water' : 'bi-play-circle-fill' }} me-1.5" style="color: {{ $b['color_dark'] }}; font-size: 0.95rem;"></i>
                                                <span>{{ $isFlowing ? 'Menyiram Blok ' . $b['num'] : 'Siram ' . $b['name'] }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Output Tambahan Lainnya (Jika Ada) -->
            @if($otherOutputs->count() > 0)
            <div class="output-panel mb-4">
                <h5 class="card-title mb-3" style="color: var(--text-main);">
                    <i class="bi bi-sliders me-2 text-primary"></i>Output Tambahan Lainnya
                </h5>
                <div class="row g-4">
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
                                <div class="segmented-control">
                                    <button type="button" class="segmented-btn {{ $output->current_value ? 'active-on' : '' }}" onclick="setOutput({{ $output->id }}, true)" id="btn-on-{{ $output->id }}">ON</button>
                                    <button type="button" class="segmented-btn {{ !$output->current_value ? 'active-off' : '' }}" onclick="setOutput({{ $output->id }}, false)" id="btn-off-{{ $output->id }}">OFF</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
