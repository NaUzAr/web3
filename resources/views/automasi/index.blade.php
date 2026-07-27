<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Otomasi - {{ $device->name }}</title>
    @include('partials.pwa-head')
    @include('partials.theme')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        /* Page Specific Overrides */

        /* Navbar Glass */
        .navbar-glass {
            background: var(--navbar-bg) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary) !important;
        }

        .nav-link {
            color: var(--text-secondary) !important;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .page-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
        }

        .device-title {
            color: var(--text-main);
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
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

        .btn-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-icon:hover {
            transform: scale(1.1);
            background-color: var(--glass-bg) !important;
            color: var(--primary);
        }

        /* Premium Params */
        .param-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            height: 100%;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        }
        
        .param-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.06);
            border-color: var(--primary);
        }

        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }

        .limit-row span:first-child {
            font-size: 1rem;
            color: var(--text-secondary);
            font-weight: 600;
        }

        .limit-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px dashed var(--glass-border);
        }
        
        .limit-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .limit-badge {
            background: rgba(14, 95, 138, 0.1);
            color: var(--primary);
            padding: 6px 16px;
            border-radius: 20px;
            font-weight: 800;
            font-size: 1.1rem;
            border: 1px solid rgba(14, 95, 138, 0.2);
        }

        /* Modal specific matching schedule style */
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
            margin-bottom: 0;
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
            background: #f8fafc;
            border-radius: 18px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
        }

        .form-section-title {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #9ca3af;
            letter-spacing: 1px;
            margin-bottom: 0.85rem;
        }

        .form-control-dark, .form-select-dark {
            background-color: #ffffff !important;
            border: 2px solid #e5e7eb !important;
            color: #111827 !important;
            border-radius: 14px;
            padding: 0.9rem 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control-dark:focus, .form-select-dark:focus {
            background-color: #ffffff !important;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(14, 95, 138, 0.12) !important;
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
            border-radius: 16px;
            padding: 1rem;
            font-size: 1.15rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: transform 0.15s, box-shadow 0.15s;
            box-shadow: 0 6px 20px rgba(14, 95, 138, 0.3);
            width: 100%;
        }

        .btn-save-schedule:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(14, 95, 138, 0.4);
            color: #fff;
        }

        .btn-cancel-schedule {
            background: #f3f4f6;
            color: #6b7280;
            border: none;
            border-radius: 16px;
            padding: 0.85rem;
            font-size: 1.05rem;
            font-weight: 600;
            transition: background 0.2s;
            width: 100%;
        }

        .btn-cancel-schedule:hover {
            background: #e5e7eb;
        }

        /* ========= Mobile Responsive ========= */
        @media (max-width: 768px) {
            .limit-badge {
                font-size: 0.85rem;
                padding: 4px 12px;
            }
            .limit-row span:first-child {
                font-size: 0.9rem;
            }

            .container.py-4 {
                padding: 1rem 0.75rem !important;
            }

            .page-header {
                padding: 1rem 1.25rem;
                border-radius: 16px;
            }

            .device-title {
                font-size: 1.15rem;
            }

            .sensor-card .px-4 {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }

            .sensor-card .p-4 {
                padding: 1rem !important;
            }

            .sensor-card h4 {
                font-size: 1rem;
            }

            /* Modal touch-friendly */
            .form-glass {
                font-size: 16px;
                min-height: 48px;
            }

            .btn-icon {
                width: 40px;
                height: 40px;
            }
        }

        @media (max-width: 400px) {
            .container.py-4 {
                padding: 0.75rem 0.5rem !important;
            }

            .page-header {
                padding: 0.75rem 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <!-- Navbar -->
    @include('partials.navbar')

    <div class="container pt-3 pb-5 min-vh-100 d-flex flex-column">
        <div class="page-header d-flex justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="mb-0 fw-bold d-flex align-items-center" style="color: var(--text-main);">
                    <i class="bi bi-robot me-2"></i>Otomasi
                </h2>
                <p class="mb-0 mt-1" style="color: var(--text-secondary);">
                    Device: <strong>{{ $device->name }}</strong>
                </p>
            </div>
            <div>
                <a href="{{ ($isAdminView ?? false) ? route('admin.device.monitoring', $deviceId) : route('monitoring.show', $deviceId) }}" class="btn btn-glass d-inline-flex align-items-center gap-2">
                    <i class="bi bi-arrow-left me-md-1"></i> <span class="d-none d-md-inline">Kembali ke Device</span>
                </a>
            </div>
        </div>

        <div class="d-flex flex-column gap-4">

                @if($hasFertilizer ?? false)
                    <!-- PEMUPUKAN SECTION -->
                    <div class="glass-card mb-2">
                        <div class="d-flex align-items-center mb-4 pb-3" style="border-bottom: 1px dashed var(--glass-border);">
                            <h5 class="card-title mb-0 fw-bold" style="color: var(--text-main);">
                                <i class="bi bi-flower1 me-2 text-warning"></i>Pemupukan
                            </h5>
                        </div>
                        <div>
                            <div class="row g-4">
                                @if($hasTds ?? true)
                                <!-- TDS / Nutrisi -->
                                <div class="col-md-6">
                                    <div class="param-card d-flex flex-column justify-content-between">
                                        <div class="d-flex align-items-center justify-content-between mb-4 pb-3" style="border-bottom: 1px solid var(--glass-border);">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box" style="background: rgba(234, 179, 8, 0.1);">
                                                    <i class="bi bi-droplet-half text-warning fs-3"></i>
                                                </div>
                                                <span class="fw-bold fs-5" style="color: var(--text-main);">Pompa Mix (TDS)</span>
                                            </div>
                                            <button class="btn btn-sm btn-light btn-icon rounded-circle shadow-sm"
                                                onclick="openEditModal('tds', 'Pompa Mix (TDS)', 'ppm', {{ $settings['ats_tds'] ?? 0 }}, {{ $settings['bwh_tds'] ?? 0 }})" title="Edit Otomasi Pompa Mix">
                                                <i class="bi bi-pencil-square text-dark fs-5"></i>
                                            </button>
                                        </div>
                                        <div class="limit-row">
                                            <span style="color: var(--text-secondary); font-weight: 500;">Batas Atas</span>
                                            <span class="limit-badge">{{ $settings['ats_tds'] ?? '-' }} ppm</span>
                                        </div>
                                        <div class="limit-row">
                                            <span style="color: var(--text-secondary); font-weight: 500;">Batas Bawah</span>
                                            <span class="limit-badge">{{ $settings['bwh_tds'] ?? '-' }} ppm</span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($hasPh ?? true)
                                <!-- pH -->
                                <div class="col-md-6">
                                    <div class="param-card d-flex flex-column justify-content-between">
                                        <div class="d-flex align-items-center justify-content-between mb-4 pb-3" style="border-bottom: 1px solid var(--glass-border);">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box" style="background: rgba(20, 184, 166, 0.1);">
                                                    <i class="bi bi-speedometer2 text-teal fs-3" style="color: #14b8a6;"></i>
                                                </div>
                                                <span class="fw-bold fs-5" style="color: var(--text-main);">Pompa pH</span>
                                            </div>
                                            <button class="btn btn-sm btn-light btn-icon rounded-circle shadow-sm"
                                                onclick="openEditModal('ph', 'Pompa pH', '', {{ $settings['ats_ph'] ?? 0 }}, {{ $settings['bwh_ph'] ?? 0 }})" title="Edit Otomasi Pompa pH">
                                                <i class="bi bi-pencil-square text-dark fs-5"></i>
                                            </button>
                                        </div>
                                        <div class="limit-row">
                                            <span style="color: var(--text-secondary); font-weight: 500;">Batas Atas</span>
                                            <span class="limit-badge">{{ $settings['ats_ph'] ?? '-' }}</span>
                                        </div>
                                        <div class="limit-row">
                                            <span style="color: var(--text-secondary); font-weight: 500;">Batas Bawah</span>
                                            <span class="limit-badge">{{ $settings['bwh_ph'] ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if($hasClimate ?? false)
                    <!-- CLIMATE SECTION -->
                    <div class="glass-card mb-2 mt-2">
                        <div class="d-flex align-items-center mb-4 pb-3" style="border-bottom: 1px dashed var(--glass-border);">
                            <h5 class="card-title mb-0 fw-bold" style="color: var(--text-main);">
                                <i class="bi bi-thermometer-sun me-2 text-info"></i>Climate
                            </h5>
                        </div>
                        <div>
                            <!-- Items -->
                            <div class="row g-4">
                                @if($hasSuhu ?? true)
                                <!-- Suhu -->
                                <div class="col-md-6">
                                    <div class="param-card d-flex flex-column justify-content-between">
                                        <div class="d-flex align-items-center justify-content-between mb-4 pb-3" style="border-bottom: 1px solid var(--glass-border);">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box" style="background: rgba(239, 68, 68, 0.1);">
                                                    <i class="bi bi-thermometer-half text-danger fs-3"></i>
                                                </div>
                                                <span class="fw-bold fs-5" style="color: var(--text-main);">Suhu Ruangan</span>
                                            </div>
                                            <button class="btn btn-sm btn-light btn-icon rounded-circle shadow-sm"
                                                onclick="openEditModal('suhu', 'Suhu Ruangan', '°C', {{ $settings['ats_suhu'] ?? 0 }}, {{ $settings['bwh_suhu'] ?? 0 }})" title="Edit Otomasi Suhu Ruangan">
                                                <i class="bi bi-pencil-square text-dark fs-5"></i>
                                            </button>
                                        </div>
                                        <div class="limit-row">
                                            <span style="color: var(--text-secondary); font-weight: 500;">Batas Atas</span>
                                            <span class="limit-badge">{{ $settings['ats_suhu'] ?? '-' }} °C</span>
                                        </div>
                                        <div class="limit-row">
                                            <span style="color: var(--text-secondary); font-weight: 500;">Batas Bawah</span>
                                            <span class="limit-badge">{{ $settings['bwh_suhu'] ?? '-' }} °C</span>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($hasKelem ?? true)
                                <!-- Kelembaban -->
                                <div class="col-md-6">
                                    <div class="param-card d-flex flex-column justify-content-between">
                                        <div class="d-flex align-items-center justify-content-between mb-4 pb-3" style="border-bottom: 1px solid var(--glass-border);">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box" style="background: rgba(59, 130, 246, 0.1);">
                                                    <i class="bi bi-droplet text-primary fs-3"></i>
                                                </div>
                                                <span class="fw-bold fs-5" style="color: var(--text-main);">Kelembaban</span>
                                            </div>
                                            <button class="btn btn-sm btn-light btn-icon rounded-circle shadow-sm"
                                                onclick="openEditModal('kelem', 'Kelembaban', '%', {{ $settings['ats_kelem'] ?? 0 }}, {{ $settings['bwh_kelem'] ?? 0 }})" title="Edit Otomasi Kelembaban">
                                                <i class="bi bi-pencil-square text-dark fs-5"></i>
                                            </button>
                                        </div>
                                        <div class="limit-row">
                                            <span style="color: var(--text-secondary); font-weight: 500;">Batas Atas</span>
                                            <span class="limit-badge">{{ $settings['ats_kelem'] ?? '-' }} %</span>
                                        </div>
                                        <div class="limit-row">
                                            <span style="color: var(--text-secondary); font-weight: 500;">Batas Bawah</span>
                                            <span class="limit-badge">{{ $settings['bwh_kelem'] ?? '-' }} %</span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                @if(!($hasFertilizer ?? false) && !($hasClimate ?? false))
                    <div class="alert alert-warning bg-opacity-25 border-warning text-center rounded-4 p-4"
                        style="color: var(--text-main);">
                        <i class="bi bi-exclamation-triangle fs-1 mb-2 d-block"></i>
                        <h5 class="fw-bold">Belum Ada Fitur Otomasi</h5>
                        <p class="mb-0 small" style="color: var(--text-secondary);">Device ini tidak memiliki sensor yang
                            mendukung otomasi
                            (Suhu/Kelembaban atau pH/TDS).</p>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content modal-content-glass">
                <div class="modal-handle"></div>
                <div class="modal-header-custom">
                    <h5 id="editModalLabel">Edit Setting</h5>
                    <div class="subtitle">Sesuaikan batas atas dan bawah otomasi</div>
                </div>
                <!-- Route update single handling -->
                <form action="{{ route('automasi.update_single', ['id' => $deviceId]) }}" method="POST">
                    @csrf
                    <div class="modal-body-custom">
                        <input type="hidden" name="sensor_type" id="modalSensorType">

                        <div class="form-section">
                            <div class="form-section-title">📊 Parameter Otomasi</div>
                            <div class="mb-3">
                                <label for="atsVal" class="form-label fw-bold" style="color: #374151; font-size: 0.9rem;">Batas Atas
                                    <span id="modalUnit1"></span></label>
                                <input type="number" step="0.01" class="form-control form-control-dark" id="atsVal" name="ats_val"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="bwhVal" class="form-label fw-bold" style="color: #374151; font-size: 0.9rem;">Batas Bawah
                                    <span id="modalUnit2"></span></label>
                                <input type="number" step="0.01" class="form-control form-control-dark" id="bwhVal" name="bwh_val"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button type="submit" class="btn btn-save-schedule">
                            <i class="bi bi-check-circle-fill me-1"></i> Simpan
                        </button>
                        <button type="button" class="btn btn-cancel-schedule" data-bs-dismiss="modal">Batalkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openEditModal(sensorType, title, unit, currentTop, currentBottom) {
            document.getElementById('editModalLabel').textContent = 'Edit ' + title;
            document.getElementById('modalSensorType').value = sensorType;

            document.getElementById('atsVal').value = currentTop;
            document.getElementById('bwhVal').value = currentBottom;

            const unitText = unit ? '(' + unit + ')' : '';
            document.getElementById('modalUnit1').textContent = unitText;
            document.getElementById('modalUnit2').textContent = unitText;

            var myModal = new bootstrap.Modal(document.getElementById('editModal'));
            myModal.show();
        }
    </script>
</body>

</html>