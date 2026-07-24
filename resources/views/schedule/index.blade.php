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
        <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h2 class="mb-0 fw-bold d-flex align-items-center" style="color: var(--text-main);">
                    <i class="bi bi-calendar-check me-2"></i>Jadwal Otomatis
                </h2>
                <p class="mb-0 mt-1" style="color: var(--text-secondary);">
                    Device: <strong>{{ $device->name }}</strong> | Target: <strong>{{ $scheduleConfig->output_key }}</strong>
                </p>
            </div>
            <div>
                <a href="{{ route('monitoring.show', $userDevice->id) }}" class="btn btn-glass d-inline-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left"></i> Kembali ke Device
                </a>
            </div>
        </div>

        <div class="glass-card">

            @php
                $mode = $scheduleConfig->schedule_mode;
                $isDuration = str_contains($mode, 'duration');
                $isDays = str_contains($mode, 'days');
                $isSector = str_contains($mode, 'sector');
                $isType = str_contains($mode, 'type');
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
                            @if($isSector) <th class="text-center">Zona Tujuan</th> @endif
                            @if($isType) <th class="text-center">Input</th> @endif
                            @if($isDays) <th>Hari</th> @endif
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 1; $i <= ($scheduleConfig->max_slots ?? 14); $i++)
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
                                    <td data-label="Zona Tujuan" class="text-center">
                                        @if($isActive)
                                            <span class="badge rounded-pill" style="background: rgba(14, 165, 233, 0.1); color: #0284c7; border: 1px solid rgba(14, 165, 233, 0.2);">
                                                <i class="bi bi-geo-alt-fill me-1" style="color: #0ea5e9;"></i> Zona {{ isset($sch['sector']) ? (int)$sch['sector'] : 1 }}
                                            </span>
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
                                        <button class="btn btn-sm" style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #60a5fa; border-radius: 50px; padding: 6px 14px; font-weight: 500;" onclick='openScheduleModal({{ $i }}, @json($sch))' title="Edit Jadwal">
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

                    @if($isSector || $isType)
                    <!-- Section: Sumber -->
                    <div class="form-section">
                        <div class="form-section-title">💧 Sumber & Output</div>
                        @if($isSector)
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #374151; font-size: 0.9rem;">Zona Tujuan</label>
                            <select id="sector" class="form-select form-select-dark">
                                @for($s = 1; $s <= ($scheduleConfig->max_sectors ?? 1); $s++)
                                    <option value="{{ $s }}">📍 Zona {{ $s }}</option>
                                @endfor
                            </select>
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
        const maxSlots = {{ $scheduleConfig->max_slots ?? 14 }};

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
                
                if(isSector) document.getElementById('sector').value = data.sector !== undefined ? data.sector : 1;
                if(isType) document.getElementById('schedule_type').value = data.name || 'BAKU';
                
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
                    location.reload(); 
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
                    alert(data.message);
                    modal.hide();
                    location.reload();
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
    </script>
</body>
</html>