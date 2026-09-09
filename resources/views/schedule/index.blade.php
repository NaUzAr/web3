<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Jadwal Otomatis - {{ $device->name }}</title>
    @include('partials.pwa-head')
    @include('partials.theme')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .page-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
        }

        .btn-glass {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            padding: 0.6rem 1.25rem;
            border-radius: 50px;
            text-decoration: none;
        }

        .btn-glass:hover {
            background: var(--glass-bg);
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            color: #fff;
        }

        .table-glass {
            color: var(--text-main);
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        
        .table-glass th,
        .table-glass td {
            border-bottom: 1px dashed var(--glass-border);
            padding: 1.25rem 1rem;
            vertical-align: middle;
        }
        
        .table-glass tbody tr {
            transition: all 0.2s ease;
        }

        .table-glass tbody tr:hover {
            background: rgba(14, 95, 138, 0.03);
        }
        
        .table-glass thead th {
            border-bottom: 2px solid var(--glass-border);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
        }

        .badge-sector {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
        }

        .modal-content-glass {
            background: #ffffff;
            border: none;
            border-radius: 24px 24px 0 0;
            color: #1f2937;
            box-shadow: 0 -10px 60px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-height: 90vh;
        }

        .modal-dialog {
            margin: 0;
            max-width: 100%;
            display: flex;
            align-items: flex-end;
            min-height: 100%;
        }

        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 480px;
                margin: auto;
                align-items: center;
            }
            .modal-content-glass {
                border-radius: 24px;
            }
        }

        /* Handle bar (iOS style) */
        .modal-handle {
            width: 40px;
            height: 5px;
            background: #d1d5db;
            border-radius: 3px;
            margin: 12px auto 0;
        }

        .modal-header-custom {
            padding: 0.75rem 1.5rem 1rem;
            text-align: center;
            border: none;
        }

        .modal-header-custom h5 {
            font-size: 1.3rem;
            font-weight: 800;
            color: #111827;
        }

        .modal-header-custom .subtitle {
            font-size: 0.85rem;
            color: #9ca3af;
            margin-top: 2px;
        }

        .modal-body-custom {
            padding: 0 1.25rem 1rem;
            overflow-y: auto;
        }

        /* Section Card */
        .form-section {
            background: #f9fafb;
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(0,0,0,0.03);
        }

        .form-section-title {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: 0.5px;
            margin-bottom: 0.85rem;
        }

        .form-control-dark, .form-select-dark {
            background-color: #ffffff !important;
            border: 1px solid #d1d5db !important;
            color: #1f2937 !important;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            font-weight: 500;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control-dark:focus, .form-select-dark:focus {
            background-color: #ffffff !important;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(14, 95, 138, 0.12) !important;
        }

        /* ===== Time & Duration Inputs ===== */
        input[type="time"].form-control-dark,
        input[type="number"].form-control-dark {
            text-align: center;
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 0.6rem 0.5rem;
            min-height: 50px;
        }

        /* Duration Quick Buttons */
        .duration-quick {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .duration-chip {
            border: 2px solid #e5e7eb;
            background: #fff;
            color: #374151;
            border-radius: 12px;
            padding: 10px 0;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
            flex: 1;
            text-align: center;
            min-width: 55px;
        }

        .duration-chip:hover { border-color: var(--primary); color: var(--primary); }
        .duration-chip.active {
            background: linear-gradient(135deg, #0e5f8a, #0d9488);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 4px 12px rgba(14, 95, 138, 0.3);
        }

        /* Day Selector */
        .schedule-day-check { display: none; }

        .schedule-day-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 12px;
            border: 1px solid #d1d5db;
            background: #ffffff;
            color: #6b7280;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            user-select: none;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        .schedule-day-check:checked + .schedule-day-label {
            background: linear-gradient(135deg, #0e5f8a, #0d9488);
            border-color: transparent;
            color: #fff;
            transform: scale(1.08);
            box-shadow: 0 4px 14px rgba(14, 95, 138, 0.35);
        }

        /* Footer action bar */
        .modal-actions {
            padding: 1rem 1.25rem 1.5rem;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }

        .btn-save-schedule {
            background: linear-gradient(135deg, #0e5f8a, #0d9488);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 4px 14px rgba(13, 148, 136, 0.3);
        }

        .btn-save-schedule:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(14, 95, 138, 0.4);
            color: #fff;
        }

        .btn-save-schedule:active {
            transform: scale(0.98);
        }

        .btn-cancel-schedule {
            background: #f3f4f6;
            color: #6b7280;
            border: none;
            border-radius: 12px;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            transition: background 0.2s;
        }

        .btn-cancel-schedule:hover {
            background: #e5e7eb;
        }

        .btn-delete-schedule {
            background: #fef2f2;
            color: #ef4444;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 0.75rem;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            margin-top: 0.25rem;
            transition: background 0.2s;
        }

        .btn-delete-schedule:hover {
            background: rgba(239, 68, 68, 0.08);
        }

        /* ========= Mobile Responsive ========= */
        @media (max-width: 768px) {
            .glass-card {
                padding: 1.25rem;
                border-radius: 16px;
            }

            .glass-card h4 {
                font-size: 1.15rem;
            }

            /* Table → Card Layout */
            .table-glass thead {
                display: none;
            }

            .table-glass tbody tr {
                display: block;
                background: var(--glass-bg);
                border: 1px solid var(--glass-border);
                border-radius: 16px;
                padding: 1rem;
                margin-bottom: 1rem;
                box-shadow: 0 4px 10px rgba(0,0,0,0.02);
            }

            .table-glass tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.4rem 0.5rem;
                border: none;
                font-size: 0.9rem;
            }

            .table-glass tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                font-size: 0.8rem;
                color: var(--text-secondary);
                margin-right: 1rem;
                flex-shrink: 0;
            }

            .table-glass tbody td:last-child {
                justify-content: flex-end;
                padding-top: 0.5rem;
                border-top: 1px solid var(--glass-border);
                margin-top: 0.25rem;
            }

            /* Day selector */
            .schedule-day-label {
                width: 44px;
                height: 44px;
                font-size: 0.85rem;
            }

            /* Modal form touch-friendly */
            .form-control-dark, .form-select-dark {
                font-size: 1.1rem;
                min-height: 52px;
            }

            /* Alert compact */
            .alert {
                font-size: 0.85rem;
            }
        }

        @media (max-width: 400px) {
            .glass-card {
                padding: 1rem;
            }

            .schedule-day-label {
                width: 38px;
                height: 38px;
                line-height: 36px;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <!-- Navbar -->
    @include('partials.navbar')

    <div class="container pt-3 pb-5 min-vh-100 d-flex flex-column">
        <!-- Header Page -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="mb-0 fw-bold d-flex align-items-center" style="color: var(--text-main);">
                    <i class="bi bi-calendar-check me-2"></i>Jadwal Otomatis
                </h2>
                <p class="mb-0 mt-1" style="color: var(--text-secondary);">
                    Device: <strong>{{ $device->name }}</strong> | Target: <strong>{{ $scheduleConfig->output_key }}</strong>
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if($device->type === 'smart_farm')
                    <span id="sync-status-indicator" class="badge rounded-pill d-inline-flex align-items-center gap-1" style="display: none !important; font-size: 0.78rem; padding: 0.55rem 0.9rem; background: rgba(14, 165, 233, 0.12); color: #0284c7; border: 1px solid rgba(14, 165, 233, 0.25);">
                        <i class="bi bi-arrow-repeat spin-icon" id="indicator-spin-icon"></i> <span id="sync-status-text">Sinkron ke alat...</span>
                    </span>
                    <button type="button" id="btn-sync-jadwal" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 50px; padding: 0.6rem 1.25rem; font-weight: 600;" onclick="syncJadwalDevice()" title="Tarik seluruh jadwal dari memori alat (EEPROM)">
                        <i class="bi bi-arrow-repeat" id="icon-sync-jadwal"></i> <span>Tarik dari Alat</span>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 50px; padding: 0.6rem 1.25rem;" onclick="stopSiram()">
                        <i class="bi bi-stop-circle-fill"></i> <span>Stop Siram</span>
                    </button>
                @endif
                <a href="{{ ($isAdminView ?? false) ? route('admin.device.monitoring', $device->id) : route('monitoring.show', $userDevice->id) }}" class="btn btn-glass d-inline-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left me-md-1"></i> <span class="d-none d-md-inline">Kembali ke Device</span>
                </a>
            </div>
        </div>

        <div class="glass-card">

            @php
                $mode = $scheduleConfig->schedule_mode;
                $isSmartFarm = ($device->type === 'smart_farm') || ($mode === 'irigasi_jadwal');
                $isDuration = str_contains($mode, 'duration') || $isSmartFarm;
                $isDays = str_contains($mode, 'days') || $isSmartFarm;
                $isSector = str_contains($mode, 'sector') || $isSmartFarm;
                $isType = str_contains($mode, 'type') && !$isSmartFarm;
                $maxSlots = $isSmartFarm ? 10 : ($scheduleConfig->max_slots ?? 14);
                $maxSectors = $isSmartFarm ? 3 : ($scheduleConfig->max_sectors ?? 1);
            @endphp
            
            {{-- Remove Add Button, use Fixed Slots --}}
            
            <div class="table-responsive">
                <table class="table table-glass">
                    <thead>
                        <tr>
                            <th>Jadwal</th>
                            <th class="text-center">Waktu Mulai</th>
                            @if($isDuration) 
                                <th class="text-center">Durasi</th> 
                            @else
                                <th class="text-center">Waktu Selesai</th>
                            @endif
                            @if($isSector) <th class="text-center">{{ $isSmartFarm ? 'Blok Irigasi' : 'Zona Tujuan' }}</th> @endif
                            @if($isSmartFarm) <th class="text-center">Pupuk (L)</th> @endif
                            @if($isType) <th class="text-center">Input</th> @endif
                            @if($isDays) <th>Hari</th> @endif
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 1; $i <= $maxSlots; $i++)
                            @php 
                                $key = "sch{$i}";
                                $sch = $cachedSchedules[$key] ?? null;
                                $isActive = $sch && ($sch['is_active'] ?? false);
                                
                                // Format days if exists
                                $days = '-';
                                if ($isActive && !empty($sch['days'])) {
                                    $days = is_array($sch['days']) ? implode(', ', $sch['days']) : $sch['days'];
                                }
                            @endphp
                            <tr id="row-slot-{{ $i }}">
                                <td data-label="Jadwal">
                                    <span class="badge rounded-pill" style="background: rgba(14, 95, 138, 0.1); color: var(--primary); border: 1px solid rgba(14, 95, 138, 0.2); padding: 6px 12px; font-weight: 700;">
                                        Jadwal {{ $i }}
                                    </span>
                                </td>
                                
                                <td data-label="Waktu Mulai" class="fw-bold text-center">{{ $isActive ? substr($sch['on_time'], 0, 5) : '-' }}</td>
                                
                                @if($isDuration) 
                                    <td data-label="Durasi" class="text-center">{{ $isActive ? $sch['duration'] . ' Menit' : '-' }}</td> 
                                @else
                                    <td data-label="Waktu Selesai" class="fw-bold text-center">{{ $isActive ? ($sch['off_time'] ?? '-') : '-' }}</td>
                                @endif
                                
                                @if($isSector) 
                                    <td data-label="{{ $isSmartFarm ? 'Blok Irigasi' : 'Zona Tujuan' }}" class="text-center">
                                        @if($isActive)
                                            @php
                                                $blokVal = $sch['blok'] ?? $sch['sector'] ?? 1;
                                            @endphp
                                            <span class="badge rounded-pill" style="background: rgba(14, 165, 233, 0.1); color: #0284c7; border: 1px solid rgba(14, 165, 233, 0.2);">
                                                <i class="bi bi-geo-alt-fill me-1" style="color: #0ea5e9;"></i> {{ $isSmartFarm ? 'Blok ' . $blokVal : 'Zona ' . $blokVal }}
                                            </span>
                                        @else
                                            <span style="color: var(--text-secondary);">-</span>
                                        @endif
                                    </td> 
                                @endif

                                @if($isSmartFarm)
                                    <td data-label="Pupuk (L)" class="text-center">
                                        @if($isActive)
                                            @php
                                                $pupukVal = $sch['liter_pupuk'] ?? (isset($sch['liter_pupuk_10']) ? ($sch['liter_pupuk_10']/10) : 0);
                                            @endphp
                                            @if($pupukVal > 0)
                                                <span class="badge rounded-pill" style="background: rgba(234, 179, 8, 0.1); color: #ca8a04; border: 1px solid rgba(234, 179, 8, 0.2);">
                                                    <i class="bi bi-droplet-half me-1"></i>{{ $pupukVal }} L
                                                </span>
                                            @else
                                                <span class="text-muted small">Tanpa Pupuk</span>
                                            @endif
                                        @else
                                            <span style="color: var(--text-secondary);">-</span>
                                        @endif
                                    </td>
                                @endif

                                @if($isType)
                                    <td data-label="Input" class="text-center">
                                        @if($isActive)
                                            @if(($sch['name'] ?? '') == 'PUPUK')
                                                <span class="badge rounded-pill" style="background: rgba(234, 179, 8, 0.1); color: #ca8a04; border: 1px solid rgba(234, 179, 8, 0.2);"><i class="bi bi-droplet-half me-1"></i>Air Pupuk</span>
                                            @elseif(($sch['name'] ?? '') == 'BAKU')
                                                <span class="badge rounded-pill" style="background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34, 197, 94, 0.2);"><i class="bi bi-water me-1"></i>Air Baku</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary">{{ $sch['name'] ?? '-' }}</span>
                                            @endif
                                        @else
                                            <span style="color: var(--text-secondary);">-</span>
                                        @endif
                                    </td>
                                @endif
                                
                                @if($isDays) 
                                    <td data-label="Hari" style="color: var(--text-secondary);">{{ $isActive ? ($days ?: 'Setiap Hari') : '-' }}</td> 
                                @endif
                                
                                <td data-label="Status" class="text-center">
                                    @if($isActive)
                                        <span class="badge rounded-pill shadow-sm" style="background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3); padding: 6px 12px;">
                                            <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge rounded-pill" style="background: rgba(107, 114, 128, 0.1); color: var(--text-secondary); border: 1px solid rgba(107, 114, 128, 0.2); padding: 6px 12px;">
                                            Kosong
                                        </span>
                                    @endif
                                </td>
                                
                                <td data-label="">
                                    <div class="d-flex justify-content-end gap-2">
                                        @if($isSmartFarm && $isActive)
                                            <button class="btn btn-sm btn-success text-white" style="border-radius: 50px; padding: 6px 14px; font-weight: 500;" onclick="siramManual({{ $i - 1 }})" title="Jalankan Siram Sekarang">
                                                <i class="bi bi-play-fill me-1"></i>Siram
                                            </button>
                                        @endif
                                        <button class="btn btn-sm" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #60a5fa; border-radius: 50px; padding: 6px 14px; font-weight: 500;" onclick='openScheduleModal({{ $i }}, (window.cachedSchedules && window.cachedSchedules["sch{{ $i }}"]) ? window.cachedSchedules["sch{{ $i }}"] : @json($sch))' title="Edit Jadwal">
                                            <i class="bi bi-pencil-square me-1"></i> Edit
                                        </button>
                                        @if($isActive)
                                            <button class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 50px; padding: 6px 12px;" onclick="confirmDeleteSchedule({{ $i }})" title="Hapus Jadwal">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-info mt-3 d-flex align-items-center">
                <i class="bi bi-info-circle-fill me-2 fs-4"></i>
                <div>
                    Data di atas adalah sinkronisasi terakhir dari device. 
                    <br>Jika Anda mengirim jadwal baru atau menghapus, data akan terupdate setelah device merespons.
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal (Bottom Sheet Style) -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-content-glass">
                <!-- iOS Handle Bar -->
                <div class="modal-handle"></div>

                <!-- Header -->
                <div class="modal-header-custom">
                    <h5 id="modalTitle">Atur Jadwal</h5>
                    <div class="subtitle">Sesuaikan waktu sesuai kebutuhan Anda</div>
                </div>

                <!-- Body -->
                <div class="modal-body-custom">
                    <input type="hidden" id="slot_id">

                    <!-- Section: Waktu -->
                    <div class="form-section">
                        <div class="form-section-title">⏰ Pengaturan Waktu</div>
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label fw-bold text-center d-block" style="color: #374151; font-size: 0.9rem;">Waktu Mulai</label>
                                <input type="time" id="on_time" class="form-control form-control-dark">
                            </div>
                            <div class="col-6">
                                @if($isDuration)
                                    <label class="form-label fw-bold text-center d-block" style="color: #374151; font-size: 0.9rem;">Durasi (menit)</label>
                                    <input type="number" id="duration" class="form-control form-control-dark" min="1" value="5">
                                @else
                                    <label class="form-label fw-bold text-center d-block" style="color: #374151; font-size: 0.9rem;">Waktu Selesai</label>
                                    <input type="time" id="off_time" class="form-control form-control-dark">
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($isSector || $isType || $isSmartFarm)
                    <!-- Section: Sumber & Output -->
                    <div class="form-section">
                        <div class="form-section-title">💧 {{ $isSmartFarm ? 'Pengaturan Irigasi' : 'Sumber & Output' }}</div>
                        @if($isSector)
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #374151; font-size: 0.9rem;">{{ $isSmartFarm ? 'Pilih Blok Irigasi' : 'Zona Tujuan' }}</label>
                            <select id="sector" class="form-select form-select-dark">
                                @for($s = 1; $s <= $maxSectors; $s++)
                                    <option value="{{ $s }}">📍 {{ $isSmartFarm ? 'Blok ' . $s : 'Zona ' . $s }}</option>
                                @endfor
                            </select>
                        </div>
                        @endif
                        @if($isSmartFarm)
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #374151; font-size: 0.9rem;">Volume Pupuk (Liter)</label>
                            <input type="number" step="0.1" min="0" max="50" id="liter_pupuk" class="form-control form-control-dark" placeholder="0 = tanpa pupuk" value="0">
                            <div class="form-text text-muted" style="font-size: 0.75rem;">Isi 0 jika hanya menyiram air biasa tanpa pupuk.</div>
                        </div>
                        @endif
                        @if($isType)
                        <div>
                            <label class="form-label fw-bold" style="color: #374151; font-size: 0.9rem;">Jenis Air</label>
                            <select id="schedule_type" class="form-select form-select-dark">
                                <option value="BAKU">🚿 Air Baku</option>
                                <option value="PUPUK">🧪 Air Pupuk</option>
                            </select>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($isDays)
                    <!-- Section: Hari -->
                    <div class="form-section">
                        <div class="form-section-title">📅 Pilih Hari Aktif</div>
                        <div class="d-flex flex-wrap justify-content-center gap-2">
                            @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $key => $day)
                                @php 
                                    $val = ($day == 'Min') ? 7 : ($key);
                                    if($day == 'Sen') $val = 1;
                                    if($day == 'Sel') $val = 2;
                                    if($day == 'Rab') $val = 3;
                                    if($day == 'Kam') $val = 4;
                                    if($day == 'Jum') $val = 5;
                                    if($day == 'Sab') $val = 6;
                                @endphp
                                <div>
                                    <input type="checkbox" id="day_{{ $key }}" class="schedule-day-check" value="{{ $key + 1 }}">
                                    <label for="day_{{ $key }}" class="schedule-day-label">{{ $day }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Actions -->
                <div class="modal-actions">
                    <button type="button" class="btn btn-save-schedule" onclick="sendSchedule()">
                        <i class="bi bi-save me-1"></i>
                        <span id="btnText">Simpan Jadwal</span>
                        <div id="btnLoading" class="spinner-border spinner-border-sm d-none"></div>
                    </button>
                    <button type="button" class="btn btn-cancel-schedule" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-delete-schedule d-none" id="btnDeleteModal" onclick="deleteScheduleFromModal()">
                        <i class="bi bi-trash3 me-1"></i> Hapus Jadwal
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-2" style="color: var(--text-main); font-weight: 700;">Hapus Jadwal?</h5>
                    <p class="mb-4" style="color: var(--text-secondary); font-size: 0.95rem;">
                        Yakin ingin menghapus <strong id="deleteSlotName">Jadwal X</strong>? Data tidak dapat dikembalikan.
                    </p>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn" style="background: rgba(100,116,139,0.1); color: #64748b; border-radius: 50px; font-weight: 600; padding: 0.6rem 1.5rem;" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn" style="background: #ef4444; color: white; border-radius: 50px; font-weight: 600; padding: 0.6rem 1.5rem; border: none; box-shadow: 0 4px 12px rgba(239,68,68,0.3);" id="confirmDeleteBtn">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const storeUrl = '{{ route("schedule.time.store", [$userDevice->id], false) }}';
        const deleteUrlBase = '/device/{{ $userDevice->id }}/schedule';
        const csrfToken = '{{ csrf_token() }}';
        
        const isDuration = {{ $isDuration ? 'true' : 'false' }};
        const isDays = {{ $isDays ? 'true' : 'false' }};
        const isSector = {{ $isSector ? 'true' : 'false' }};
        const isType = {{ $isType ? 'true' : 'false' }};
        const isSmartFarm = {{ $isSmartFarm ? 'true' : 'false' }};
        const maxSlots = {{ $maxSlots }};

        const modal = new bootstrap.Modal(document.getElementById('scheduleModal'));

        // Duration Quick Select
        function setDuration(val) {
            document.getElementById('duration').value = val;
            document.querySelectorAll('.duration-chip').forEach(c => c.classList.remove('active'));
            event.target.classList.add('active');
        }

        function highlightDuration(val) {
            document.querySelectorAll('.duration-chip').forEach(c => {
                const chipVal = c.textContent.trim();
                c.classList.remove('active');
                if ((val == 1 && chipVal === '1m') || (val == 5 && chipVal === '5m') ||
                    (val == 10 && chipVal === '10m') || (val == 15 && chipVal === '15m') ||
                    (val == 30 && chipVal === '30m') || (val == 60 && chipVal === '1j'))
                    c.classList.add('active');
            });
        }

        function openScheduleModal(slotId, data = null) {
            document.getElementById('slot_id').value = slotId;
            document.getElementById('modalTitle').innerText = `Atur Jadwal ${slotId}`;
            
            const btnDelete = document.getElementById('btnDeleteModal');
            
            // Default
            document.getElementById('on_time').value = '';
            if(isDuration) {
                document.getElementById('duration').value = 5;
                highlightDuration(5);
            } else {
                document.getElementById('off_time').value = '';
            }
            
            if(isSector) document.getElementById('sector').value = 1;
            if(isType) document.getElementById('schedule_type').value = 'BAKU';
            if(document.getElementById('liter_pupuk')) document.getElementById('liter_pupuk').value = 0;
            if(isDays) document.querySelectorAll('.schedule-day-check').forEach(el => el.checked = false);
            
            if (data && data.is_active) {
                btnDelete.classList.remove('d-none');
                document.getElementById('on_time').value = data.on_time ? data.on_time.substring(0, 5) : '';
                
                if(isDuration) {
                    const dur = data.duration || 5;
                    document.getElementById('duration').value = dur;
                    highlightDuration(dur);
                } else {
                    document.getElementById('off_time').value = data.off_time ? data.off_time.substring(0, 5) : '';
                }
                
                if(isSector) document.getElementById('sector').value = data.blok !== undefined ? data.blok : (data.sector !== undefined ? data.sector : 1);
                if(isType) document.getElementById('schedule_type').value = data.name || 'BAKU';
                if(document.getElementById('liter_pupuk')) {
                    const pupuk = data.liter_pupuk !== undefined ? data.liter_pupuk : (data.liter_pupuk_10 ? (data.liter_pupuk_10 / 10) : 0);
                    document.getElementById('liter_pupuk').value = pupuk;
                }
                
                if(isDays && data.days) {
                    let daysArr = Array.isArray(data.days) ? data.days : (data.days ? data.days.split(',') : []);
                    let map = {'Sen':1, 'Sel':2, 'Rab':3, 'Kam':4, 'Jum':5, 'Sab':6, 'Min':7};
                    daysArr.forEach(d => {
                        let dt = d.trim();
                        if(map[dt]) {
                            let el = document.querySelector(`.schedule-day-check[value="${map[dt]}"]`);
                            if(el) el.checked = true;
                        }
                    });
                }
            } else {
                btnDelete.classList.add('d-none');
            }
            
            modal.show();
        }

        let slotToDelete = null;
        let deleteConfirmModalEl = document.getElementById('deleteConfirmModal');
        let deleteConfirmModal = new bootstrap.Modal(deleteConfirmModalEl);

        function confirmDeleteSchedule(slotId) {
            slotToDelete = slotId;
            document.getElementById('deleteSlotName').innerText = `Jadwal ${slotId}`;
            deleteConfirmModal.show();
        }
        
        document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
            if(slotToDelete === null) return;
            const slotId = slotToDelete;
            slotToDelete = null;
            
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...';
            this.disabled = true;

            try {
                const res = await fetch(`${deleteUrlBase}/${slotId}`, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if(data.success) { 
                    deleteConfirmModal.hide();
                    setTimeout(() => {
                        if (typeof checkScheduleUpdates === 'function') {
                            checkScheduleUpdates(true);
                        } else {
                            location.reload();
                        }
                    }, 1500);
                } else { 
                    alert('Gagal: ' + data.message); 
                    this.innerHTML = 'Hapus';
                    this.disabled = false;
                    deleteConfirmModal.hide();
                }
            } catch (e) { 
                alert('Error: ' + e.message); 
                this.innerHTML = 'Hapus';
                this.disabled = false;
                deleteConfirmModal.hide();
            }
        });

        function deleteScheduleFromModal() {
            const slotId = document.getElementById('slot_id').value;
            modal.hide();
            confirmDeleteSchedule(slotId);
        }

        async function sendSchedule() {
            const slotId = parseInt(document.getElementById('slot_id').value);
            const onTime = document.getElementById('on_time').value;
            
            if(!onTime) { alert('Waktu Mulai harus diisi'); return; }

            let payload = { slot_id: slotId, on_time: onTime, _token: csrfToken };
            
            if(isDuration) payload.duration = document.getElementById('duration').value;
            else payload.off_time = document.getElementById('off_time').value;

            if(isSector) payload.sector = document.getElementById('sector').value;
            if(isType) payload.schedule_type = document.getElementById('schedule_type').value;

            if(isSmartFarm) {
                payload.blok = document.getElementById('sector').value;
                const pupukEl = document.getElementById('liter_pupuk');
                if(pupukEl) payload.liter_pupuk = parseFloat(pupukEl.value) || 0;
            }

            if(isDays) {
                let days = [];
                document.querySelectorAll('.schedule-day-check:checked').forEach(el => days.push(el.value));
                if(days.length === 0) { alert('Pilih minimal 1 hari'); return; }
                payload.days = days.join('');
            }

            const btn = document.querySelector('.btn-save-schedule');
            const btnText = document.getElementById('btnText');
            const loader = document.getElementById('btnLoading');
            
            btn.disabled = true;
            btnText.innerText = 'Mengirim...';
            loader.classList.remove('d-none');

            try {
                const res = await fetch(storeUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if(data.success) {
                    btnText.innerText = 'Tersimpan!';
                    modal.hide();
                    setTimeout(() => {
                        if (typeof checkScheduleUpdates === 'function') {
                            checkScheduleUpdates(true);
                        } else {
                            location.reload();
                        }
                    }, 1500);
                } else {
                    alert('Gagal: ' + data.message);
                }
            } catch (e) {
                alert('Error: ' + e.message);
            } finally {
                btn.disabled = false;
                btnText.innerText = 'Simpan Jadwal';
                loader.classList.add('d-none');
            }
        }

        // Smart Farm: Manual Siram
        async function siramManual(idx) {
            if(!confirm(`Mulai penyiraman sekarang menggunakan pengaturan Jadwal #${idx + 1}?`)) return;
            try {
                const res = await fetch('{{ route("schedule.siram.start", [$userDevice->id], false) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ jadwal_index: idx })
                });
                const data = await res.json();
                alert(data.message || (data.success ? 'Perintah berhasil dikirim' : 'Gagal'));
            } catch(e) {
                alert('Error: ' + e.message);
            }
        }

        // Smart Farm: Stop Siram
        async function stopSiram() {
            if(!confirm('Hentikan semua proses penyiraman yang sedang berjalan?')) return;
            try {
                const targetId = '{{ ($isAdminView ?? false) ? $device->id : ($userDevice->id ?? $device->id) }}';
                const res = await fetch(`/device/${targetId}/schedule/siram-stop`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                alert(data.message || (data.success ? 'Penyiraman dihentikan' : 'Gagal'));
            } catch(e) {
                alert('Error: ' + e.message);
            }
        }

        // Smart Farm: Tarik jadwal dari perangkat via MQTT
        async function syncJadwalDevice() {
            const btn = document.getElementById('btn-sync-jadwal');
            const icon = document.getElementById('icon-sync-jadwal');
            if (btn) btn.disabled = true;
            if (icon) icon.classList.add('spin-icon');

            try {
                const targetId = '{{ ($isAdminView ?? false) ? $device->id : ($userDevice->id ?? $device->id) }}';
                const res = await fetch(`/device/${targetId}/schedule/sync`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });
                const data = await res.json();
                if (data.success) {
                    setTimeout(() => {
                        checkScheduleUpdates(true);
                        if (btn) btn.disabled = false;
                        if (icon) icon.classList.remove('spin-icon');
                    }, 3500);
                } else {
                    alert('Gagal: ' + data.message);
                    if (btn) btn.disabled = false;
                    if (icon) icon.classList.remove('spin-icon');
                }
            } catch(e) {
                alert('Error: ' + e.message);
                if (btn) btn.disabled = false;
                if (icon) icon.classList.remove('spin-icon');
            }
        }

        @if($device->type === 'smart_farm')
        window.cachedSchedules = @json($cachedSchedules);
        const targetId = '{{ ($isAdminView ?? false) ? $device->id : ($userDevice->id ?? $device->id) }}';

        // Render baris jadwal langsung ke DOM tanpa reload
        function renderScheduleRow(slotNum, sch) {
            if (!window.cachedSchedules) window.cachedSchedules = {};
            window.cachedSchedules[`sch${slotNum}`] = sch;
            const row = document.getElementById(`row-slot-${slotNum}`);
            if (!row) return;

            const isActive = sch && (sch.is_active == 1 || sch.is_active === true);
            const onTime = (isActive && sch.on_time) ? sch.on_time.substring(0, 5) : '-';
            const duration = (isActive && sch.duration) ? `${sch.duration} Menit` : '-';
            const offTime = (isActive && sch.off_time) ? sch.off_time : '-';
            const blok = (isActive && (sch.blok !== undefined ? sch.blok : sch.sector)) ? (sch.blok !== undefined ? sch.blok : sch.sector) : 1;
            const literPupuk = (isActive && (sch.liter_pupuk !== undefined ? sch.liter_pupuk : (sch.liter_pupuk_10 ? sch.liter_pupuk_10 / 10 : 0))) || 0;

            let daysStr = '-';
            if (isActive && sch.days) {
                daysStr = Array.isArray(sch.days) ? sch.days.join(', ') : sch.days;
                if (!daysStr) daysStr = 'Setiap Hari';
            }

            // Waktu Mulai
            const cellWaktu = row.querySelector('[data-label="Waktu Mulai"]');
            if (cellWaktu) cellWaktu.textContent = onTime;

            // Durasi / Waktu Selesai
            const cellDurasi = row.querySelector('[data-label="Durasi"]');
            if (cellDurasi) cellDurasi.textContent = duration;
            const cellSelesai = row.querySelector('[data-label="Waktu Selesai"]');
            if (cellSelesai) cellSelesai.textContent = offTime;

            // Blok Irigasi
            const cellBlok = row.querySelector('[data-label="Blok Irigasi"]') || row.querySelector('[data-label="Zona Tujuan"]');
            if (cellBlok) {
                if (isActive) {
                    cellBlok.innerHTML = `
                        <span class="badge rounded-pill" style="background: rgba(14, 165, 233, 0.1); color: #0284c7; border: 1px solid rgba(14, 165, 233, 0.2);">
                            <i class="bi bi-geo-alt-fill me-1" style="color: #0ea5e9;"></i> ${isSmartFarm ? 'Blok ' : 'Zona '} ${blok}
                        </span>
                    `;
                } else {
                    cellBlok.innerHTML = `<span style="color: var(--text-secondary);">-</span>`;
                }
            }

            // Pupuk (L)
            const cellPupuk = row.querySelector('[data-label="Pupuk (L)"]');
            if (cellPupuk) {
                if (isActive) {
                    if (literPupuk > 0) {
                        cellPupuk.innerHTML = `
                            <span class="badge rounded-pill" style="background: rgba(234, 179, 8, 0.1); color: #ca8a04; border: 1px solid rgba(234, 179, 8, 0.2);">
                                <i class="bi bi-droplet-half me-1"></i>${literPupuk} L
                            </span>
                        `;
                    } else {
                        cellPupuk.innerHTML = `<span class="text-muted small">Tanpa Pupuk</span>`;
                    }
                } else {
                    cellPupuk.innerHTML = `<span style="color: var(--text-secondary);">-</span>`;
                }
            }

            // Hari
            const cellHari = row.querySelector('[data-label="Hari"]');
            if (cellHari) {
                cellHari.textContent = isActive ? daysStr : '-';
            }

            // Status
            const cellStatus = row.querySelector('[data-label="Status"]');
            if (cellStatus) {
                if (isActive) {
                    cellStatus.innerHTML = `
                        <span class="badge rounded-pill shadow-sm" style="background: rgba(16, 185, 129, 0.15); color: #059669; border: 1px solid rgba(16, 185, 129, 0.3); padding: 6px 12px;">
                            <i class="bi bi-check-circle-fill me-1"></i> Aktif
                        </span>
                    `;
                } else {
                    cellStatus.innerHTML = `
                        <span class="badge rounded-pill" style="background: rgba(107, 114, 128, 0.1); color: var(--text-secondary); border: 1px solid rgba(107, 114, 128, 0.2); padding: 6px 12px;">
                            Kosong
                        </span>
                    `;
                }
            }

            // Tombol Aksi
            const cellAksi = row.querySelector('td:last-child');
            if (cellAksi) {
                let btns = `<div class="d-flex justify-content-end gap-2">`;
                if (isSmartFarm && isActive) {
                    btns += `
                        <button class="btn btn-sm btn-success text-white" style="border-radius: 50px; padding: 6px 14px; font-weight: 500;" onclick="siramManual(${slotNum - 1})" title="Jalankan Siram Sekarang">
                            <i class="bi bi-play-fill me-1"></i>Siram
                        </button>
                    `;
                }
                btns += `
                    <button class="btn btn-sm" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #60a5fa; border-radius: 50px; padding: 6px 14px; font-weight: 500;" onclick="openScheduleModal(${slotNum}, window.cachedSchedules['sch${slotNum}'])" title="Edit Jadwal">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </button>
                `;
                if (isActive) {
                    btns += `
                        <button class="btn btn-sm" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; border-radius: 50px; padding: 6px 12px;" onclick="confirmDeleteSchedule(${slotNum})" title="Hapus Jadwal">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
                btns += `</div>`;
                cellAksi.innerHTML = btns;
            }
        }

        // Fungsi cek data jadwal terbaru dari server / device (tanpa reload halaman)
        async function checkScheduleUpdates(showIndicator = true) {
            const indicator = document.getElementById('sync-status-indicator');
            const statusText = document.getElementById('sync-status-text');
            const spinIcon = document.getElementById('indicator-spin-icon');
            const iconSync = document.getElementById('icon-sync-jadwal');

            if (showIndicator) {
                if (indicator) indicator.style.setProperty('display', 'inline-flex', 'important');
                if (iconSync) iconSync.classList.add('spin-icon');
                if (spinIcon) spinIcon.className = 'bi bi-arrow-repeat spin-icon';
                if (statusText) statusText.innerText = 'Sinkron ke alat...';
            }

            try {
                const res = await fetch(`/device/${targetId}/schedule/data`);
                const data = await res.json();

                if (iconSync) iconSync.classList.remove('spin-icon');

                if (data.success && data.schedules) {
                    // Update seluruh baris secara langsung di DOM (tanpa reload layar)
                    for (let i = 1; i <= maxSlots; i++) {
                        const sch = data.schedules[`sch${i}`] || null;
                        renderScheduleRow(i, sch);
                    }

                    if (indicator) {
                        indicator.style.background = 'rgba(16, 185, 129, 0.15)';
                        indicator.style.color = '#059669';
                        indicator.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                        if (spinIcon) spinIcon.className = 'bi bi-check2';
                        if (statusText) statusText.innerText = 'Jadwal tersinkron';
                        setTimeout(() => {
                            indicator.style.setProperty('display', 'none', 'important');
                        }, 2500);
                    }
                } else {
                    if (indicator) indicator.style.setProperty('display', 'none', 'important');
                }
            } catch (e) {
                console.warn('Sync check failed:', e);
                if (iconSync) iconSync.classList.remove('spin-icon');
                if (indicator) indicator.style.setProperty('display', 'none', 'important');
            }
        }

        // 1. Saat pertama kali halaman di-load:
        // Controller index() sudah mengirim CMD:JADWAL_GET ke alat.
        // Beri jeda 3.5 detik agar respon 10 slot dari STM32 selesai dikirim, lalu update tabel.
        window.addEventListener('DOMContentLoaded', () => {
            const indicator = document.getElementById('sync-status-indicator');
            const iconSync = document.getElementById('icon-sync-jadwal');
            if (indicator) indicator.style.setProperty('display', 'inline-flex', 'important');
            if (iconSync) iconSync.classList.add('spin-icon');

            setTimeout(() => {
                checkScheduleUpdates(true);
            }, 3500);

            // Cek sekali lagi di 7 detik untuk memastikan respon akhir (JADWAL_END) tertangkap
            setTimeout(() => {
                checkScheduleUpdates(false);
            }, 7000);
        });

        // 2. Autoload berkala setiap 1 menit (60 detik) tanpa reload halaman
        setInterval(async () => {
            // Jangan jalankan jika user sedang membuka modal edit
            const modalOpen = document.querySelector('.modal.show');
            if (modalOpen) return;

            try {
                // Kirim request sinkronisasi ke alat via MQTT
                await fetch(`/device/${targetId}/schedule/sync`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                });

                // Beri jeda 3.5 detik untuk respon alat, lalu perbarui tabel langsung di DOM
                setTimeout(() => {
                    checkScheduleUpdates(true);
                }, 3500);
            } catch (err) {
                console.warn('Autoload 1 menit gagal:', err);
            }
        }, 60000); // 60.000 ms = 1 menit
        @endif
    </script>
    <style>
        @keyframes spin { 100% { transform: rotate(360deg); } }
        .spin-icon { animation: spin 1s linear infinite; display: inline-block; }
    </style>
</body>
</html>