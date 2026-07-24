<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Device - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    @include('partials.theme')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Leaflet CSS for Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        /* Page Specific Overrides */
        * {
            font-family: 'Inter', sans-serif;
        }

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

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
        }

        .card-header-gradient {
            background: var(--primary-gradient);
            border-radius: 24px 24px 0 0 !important;
            padding: 1.5rem 2rem;
        }

        .form-control,
        .form-select {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            border-radius: 12px;
            padding: 0.75rem 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            background: var(--glass-bg);
            border-color: var(--primary);
            color: var(--text-main);
            box-shadow: 0 0 0 3px rgba(var(--primary), 0.2);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
        }

        .form-select option {
            background: #ffffff;
            color: #333;
        }

        .form-label {
            color: var(--primary);
            font-weight: 600;
        }

        .form-text {
            color: var(--text-secondary);
        }

        .type-card {
            cursor: pointer;
            background: var(--glass-bg);
            border: 2px solid var(--glass-border);
            border-radius: 16px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            color: var(--text-main);
        }

        .type-card:hover,
        .type-card.selected {
            border-color: var(--primary);
            background: rgba(var(--primary), 0.1);
            transform: translateY(-5px);
        }

        .type-card i {
            font-size: 2.5rem;
            color: var(--primary);
        }

        .type-card h6 {
            margin-top: 0.5rem;
            margin-bottom: 0;
            color: var(--text-main);
        }

        .sensor-row {
            background: var(--glass-bg);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            border: 1px solid var(--glass-border);
            transition: all 0.2s ease;
        }

        .sensor-row:hover {
            background: var(--glass-bg);
            border-color: var(--primary);
        }

        .btn-gradient {
            background: var(--primary-gradient);
            border: none;
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            color: #fff;
        }

        .btn-glass {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
        }

        .btn-glass:hover {
            background: var(--glass-bg);
            border-color: var(--primary);
            color: var(--primary);
        }

        .btn-outline-add {
            background: transparent;
            border: 2px dashed var(--primary);
            color: var(--primary);
            border-radius: 12px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-add:hover {
            background: rgba(14, 95, 138, 0.1);
            color: var(--primary-dark);
            border-style: solid;
        }

        .alert-info-custom {
            background: rgba(14, 165, 233, 0.05);
            border: 1px solid rgba(14, 165, 233, 0.2);
            color: var(--text-main);
            border-radius: 12px;
        }

        .alert-warning-custom {
            background: rgba(250, 204, 21, 0.05);
            border: 1px solid rgba(250, 204, 21, 0.2);
            color: var(--text-main);
            border-radius: 12px;
        }

        .badge-count {
            background: var(--secondary-gradient);
            color: var(--primary-dark);
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }

        /* ========= Mobile Responsive ========= */
        @media (max-width: 768px) {
            .container.py-4 {
                padding: 1rem 0.75rem !important;
            }

            .glass-card {
                padding: 1.25rem;
                border-radius: 16px;
            }

            .glass-card h4 {
                font-size: 1.1rem;
            }

            .form-control,
            .form-select {
                font-size: 16px;
                min-height: 46px;
            }

            /* Type cards 1 column */
            .row.g-3.mb-4 .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }

            /* Map smaller */
            #mapPicker {
                height: 200px !important;
            }

            /* Sensor/output rows compact */
            .sensor-row,
            .output-row {
                flex-wrap: wrap;
            }

            /* Quick add buttons wrap better */
            .d-flex.flex-wrap.gap-2 {
                gap: 0.35rem !important;
            }

            .d-flex.flex-wrap.gap-2 .btn {
                font-size: 0.75rem;
                padding: 0.3rem 0.6rem;
            }
        }

        @media (max-width: 400px) {
            .container.py-4 {
                padding: 0.75rem 0.5rem !important;
            }

            .glass-card {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <!-- Navbar -->
    @include('partials.navbar')

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="glass-card shadow-lg">
                    <div class="card-header-gradient d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-white">
                            <i class="bi bi-plus-circle-fill me-2"></i>Tambah Device Baru
                        </h4>
                        <a href="{{ route('admin.devices.index') }}" class="btn btn-sm btn-outline-light">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Devices
                        </a>
                    </div>
                    <div class="card-body p-4">
                        @if ($errors->any())
                            <div class="alert alert-danger bg-danger bg-opacity-25 border-danger text-white">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger bg-danger bg-opacity-25 border-danger"
                                style="color: var(--text-main);">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('admin.device.store') }}" method="POST" id="deviceForm">
                            @csrf

                            <!-- STEP 1: TIPE ALAT -->
                            <div class="mb-4">
                                <label class="form-label"><i class="bi bi-cpu me-1"></i> Pilih Tipe Alat</label>
                                <div class="row g-3">
                                    @foreach($deviceTypes as $typeKey => $typeLabel)
                                        <div class="col-md-6">
                                            <div class="type-card" data-type="{{ $typeKey }}"
                                                onclick="selectDeviceType('{{ $typeKey }}')">
                                                <i
                                                    class="bi {{ $typeKey === 'aws' ? 'bi-cloud-sun-fill' : 'bi-flower1' }}"></i>
                                                <h6>{{ $typeLabel }}</h6>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="type" id="deviceType" value="" required>
                            </div>

                            <!-- STEP 2: INFO DEVICE -->
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-tag me-1"></i> Nama Device</label>
                                <input type="text" name="name" class="form-control" placeholder="Contoh: Sensor GH-01"
                                    required>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label mb-0"><i class="bi bi-geo-alt me-1"></i> Lokasi Alat</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnAutoDetect" style="border-radius: 12px;">
                                        <i class="bi bi-crosshair me-1"></i> Auto Lokasi
                                    </button>
                                </div>
                                <input type="text" name="location" id="locationInput" class="form-control"
                                    placeholder="Contoh: Greenhouse A, Kebun Teh Blok 3 (atau klik Auto Lokasi)">
                            </div>

                            <!-- Map Picker -->
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-map me-1"></i> Pilih Titik Lokasi di
                                    Map</label>
                                <div id="mapPicker"
                                    style="height: 250px; border-radius: 12px; border: 1px solid var(--glass-border);">
                                </div>
                                <div class="form-text">Klik pada map untuk menentukan koordinat lokasi device.</div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-geo me-1"></i> Latitude</label>
                                    <input type="number" step="any" name="latitude" id="latitudeInput"
                                        class="form-control" placeholder="-6.9175" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-geo me-1"></i> Longitude</label>
                                    <input type="number" step="any" name="longitude" id="longitudeInput"
                                        class="form-control" placeholder="107.6191" readonly>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label"><i class="bi bi-broadcast me-1"></i> Alamat Topik MQTT</label>
                                <input type="text" name="mqtt_topic" class="form-control"
                                    placeholder="Contoh: sensor/kebun/data" required>
                                <div class="form-text">Device akan mengirim data ke topik ini.</div>
                            </div>

                            <!-- STEP 3: DAFTAR SENSOR -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-thermometer-half me-1"></i> Konfigurasi Sensor
                                    <span class="badge-count ms-2" id="sensorCount">0 sensor</span>
                                </label>

                                <!-- Quick Add Sensors -->
                                <div class="mb-3">
                                    <label class="small mb-2 d-block" style="color: var(--text-secondary);">Quick Add
                                        (Klik untuk
                                        menambahkan):</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($availableSensors as $key => $sensor)
                                            <button type="button" class="btn btn-sm btn-outline-secondary bg-opacity-10"
                                                onclick="addSensorRow('{{ $key }}')"
                                                style="border-color: var(--glass-border); background: var(--glass-bg); color: var(--text-main);">
                                                <i class="bi {{ $sensor['icon'] }} me-1"></i> {{ $sensor['label'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="alert alert-info-custom py-2 mb-3">
                                    <small><i class="bi bi-info-circle me-1"></i>
                                        Sensor yang mendukung otomasi memiliki toggle
                                        <span class="badge rounded-pill" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); font-size: 0.6rem; padding: 0.15rem 0.4rem;">Iklim</span> /
                                        <span class="badge rounded-pill" style="background: linear-gradient(135deg, #22c55e, #16a34a); font-size: 0.6rem; padding: 0.15rem 0.4rem;">Pemupukan</span>.
                                        Aktifkan toggle untuk mengisi batas atas &amp; bawah awal.
                                    </small>
                                </div>

                                <div id="sensorContainer"></div>

                                <button type="button" class="btn btn-outline-add w-100" onclick="addSensorRow()">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Sensor Manual
                                </button>
                            </div>

                            <!-- STEP 4: DAFTAR OUTPUT -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-toggle-on me-1"></i> Konfigurasi Output (Opsional)
                                    <span class="badge-count ms-2" id="outputCount">0 output</span>
                                </label>


                                <div class="alert alert-info-custom py-2 mb-3">
                                    <small><i class="bi bi-info-circle me-1"></i>
                                        Output adalah aktuator yang dapat dikontrol via MQTT (relay, pompa, kipas, dll).
                                    </small>
                                </div>

                                <!-- Quick Add Outputs -->
                                <div class="mb-3">
                                    <label class="small mb-2 d-block" style="color: var(--text-secondary);">Quick Add
                                        (Klik untuk
                                        menambahkan):</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($availableOutputs as $key => $output)
                                            <button type="button" class="btn btn-sm btn-outline-secondary bg-opacity-10"
                                                onclick="addOutputRow('{{ $key }}')"
                                                style="border-color: var(--glass-border); background: var(--glass-bg); color: var(--text-main);">
                                                <i class="bi {{ $output['icon'] }} me-1"></i> {{ $output['label'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div id="outputContainer"></div>

                                <button type="button" class="btn btn-outline-add w-100 mt-3" onclick="addOutputRow()">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Output
                                </button>

                            </div>

                            <!-- STEP 5: TIPE PENJADWALAN (OPTIONAL) -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-clock me-1"></i> Tipe Penjadwalan (Opsional)
                                </label>
                                <div class="alert alert-info-custom py-2 mb-3">
                                    <small><i class="bi bi-info-circle me-1"></i>
                                        Pilih tipe penjadwalan jika device ini membutuhkan fitur jadwal otomatis.
                                        Kosongkan jika tidak perlu.
                                    </small>
                                </div>

                                <select class="form-select mb-3" name="schedule_type" id="scheduleType"
                                    onchange="toggleScheduleOptions(this.value)">
                                    <option value="">-- Tidak Ada Penjadwalan --</option>
                                    @foreach($scheduleTypes as $key => $info)
                                        <option value="{{ $key }}">{{ $info['label'] }} - {{ $info['description'] }}
                                        </option>
                                    @endforeach
                                </select>

                                <div id="scheduleOptions" style="display: none;">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label small" style="color: var(--text-secondary);">Jumlah
                                                Slot Jadwal</label>
                                            <input type="number" class="form-control" name="max_slots" value="8" min="1"
                                                max="20">
                                            <div class="form-text">Berapa banyak jadwal yang bisa disimpan</div>
                                        </div>
                                        <div class="col-md-6" id="sectorField" style="display: none;">
                                            <label class="form-label small" style="color: var(--text-secondary);">Jumlah
                                                Sektor</label>
                                            <input type="number" class="form-control" name="max_sectors" value="1"
                                                min="1" max="10">
                                            <div class="form-text">Untuk mode multi-sektor/zona</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning-custom">
                                <strong><i class="bi bi-exclamation-triangle me-1"></i> Perhatian:</strong>
                                Sistem akan otomatis membuatkan <b>Token Unik</b> dan <b>Tabel Database Baru</b>.
                            </div>

                            <div class="d-flex gap-3 mt-4">
                                <a href="{{ route('admin.devices.index') }}" class="btn btn-glass">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-gradient flex-grow-1" id="submitBtn" disabled>
                                    <i class="bi bi-plus-circle me-1"></i> Generate Device & Tabel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const availableSensors = @json($availableSensors);
        const availableOutputs = @json($availableOutputs);
        const defaultSensors = @json($defaultSensors);
        const defaultOutputs = @json($defaultOutputs);
        const automationPresets = @json($automationPresets);
        let sensorCounter = 0;
        let outputCounter = 0;

        // Toggle schedule options visibility
        function toggleScheduleOptions(value) {
            const scheduleOptions = document.getElementById('scheduleOptions');
            const sectorField = document.getElementById('sectorField');

            if (value) {
                scheduleOptions.style.display = 'block';
                // Show sector field for any mode containing 'sector'
                if (value.includes('sector')) {
                    sectorField.style.display = 'block';
                } else {
                    sectorField.style.display = 'none';
                }
            } else {
                scheduleOptions.style.display = 'none';
            }
        }

        function getSensorOptions(selectedKey = '') {
            let options = '<option value="">-- Pilih Jenis Sensor --</option>';
            for (const [key, info] of Object.entries(availableSensors)) {
                const selected = key === selectedKey ? 'selected' : '';
                options += `<option value="${key}" ${selected}>${info.label} ${info.unit ? '(' + info.unit + ')' : ''}</option>`;
            }
            return options;
        }

        function getOutputOptions(selectedKey = '') {
            let options = '<option value="">-- Pilih Jenis Output --</option>';
            for (const [key, info] of Object.entries(availableOutputs)) {
                const selected = key === selectedKey ? 'selected' : '';
                const typeLabel = info.type === 'boolean' ? 'ON/OFF' : (info.type === 'percentage' ? '0-100%' : 'Angka');
                options += `<option value="${key}" ${selected}>${info.label} (${typeLabel})</option>`;
            }
            return options;
        }

        // === Helper: Cek apakah sensor termasuk preset otomasi ===
        function getAutomationInfo(sensorKey) {
            if (!sensorKey) return null;
            for (const [type, preset] of Object.entries(automationPresets)) {
                if (sensorKey in preset.sensors) {
                    return { type, label: preset.label, icon: preset.icon };
                }
            }
            return null;
        }

        // Mapping sensor key → automation setting key (untuk batas atas/bawah)
        const autoSettingKeyMap = {
            'ni_SUHU': { key: 'suhu', unit: '°C' },
            'ni_KELEM': { key: 'kelem', unit: '%' },
            'ni_TDS': { key: 'tds', unit: 'ppm' },
            'ni_PH': { key: 'ph', unit: '' },
            'ni_LUX': { key: 'lux', unit: 'lux' },
            'co2': { key: 'co2', unit: 'ppm' },
            'water_level': { key: 'wlevel', unit: 'cm' }
        };

        function toggleAutoPanel(index) {
            const panel = document.getElementById('autoPanel_' + index);
            const toggle = document.getElementById('autoToggle_' + index);
            if (!panel || !toggle) return;
            if (toggle.checked) {
                panel.style.maxHeight = panel.scrollHeight + 'px';
                panel.style.opacity = '1';
                panel.style.marginTop = '0.5rem';
            } else {
                panel.style.maxHeight = '0';
                panel.style.opacity = '0';
                panel.style.marginTop = '0';
            }
        }

        function onSensorTypeChange(index, sensorKey) {
            updateSubmitButton();
            // Update automation toggle visibility
            const autoArea = document.getElementById('autoArea_' + index);
            const autoToggle = document.getElementById('autoToggle_' + index);
            const autoPanel = document.getElementById('autoPanel_' + index);
            if (!autoArea) return;

            const info = getAutomationInfo(sensorKey);
            if (info) {
                // Show toggle area with badge
                const styles = {
                    'climate': { bg: 'linear-gradient(135deg, #0ea5e9, #0284c7)', short: 'Iklim', icon: 'bi-thermometer-sun' },
                    'fertilizer': { bg: 'linear-gradient(135deg, #22c55e, #16a34a)', short: 'Pemupukan', icon: 'bi-flower1' }
                };
                const s = styles[info.type] || { bg: '#6b7280', short: info.label, icon: info.icon };
                autoArea.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill shadow-sm" style="background: ${s.bg}; font-size: 0.65rem; padding: 0.25rem 0.55rem;">
                            <i class="bi ${s.icon} me-1"></i>${s.short}
                        </span>
                        <div class="form-check form-switch mb-0" style="min-height: auto;">
                            <input class="form-check-input" type="checkbox" role="switch" id="autoToggle_${index}"
                                   name="sensors[${index}][auto_enabled]" value="1"
                                   onchange="toggleAutoPanel(${index})" style="cursor: pointer;">
                            <label class="form-check-label small" for="autoToggle_${index}" style="color: var(--text-secondary); cursor: pointer; font-size: 0.75rem;">
                                Otomasi
                            </label>
                        </div>
                    </div>
                `;
                // Update unit in batas inputs
                const mapping = autoSettingKeyMap[sensorKey];
                const unitLabel = mapping ? mapping.unit : '';
                const atsLabel = document.getElementById('atsLabel_' + index);
                const bwhLabel = document.getElementById('bwhLabel_' + index);
                if (atsLabel) atsLabel.textContent = 'Batas Atas' + (unitLabel ? ` (${unitLabel})` : '');
                if (bwhLabel) bwhLabel.textContent = 'Batas Bawah' + (unitLabel ? ` (${unitLabel})` : '');
            } else {
                // Hide toggle + collapse panel for non-automation sensors
                autoArea.innerHTML = '';
                if (autoPanel) {
                    autoPanel.style.maxHeight = '0';
                    autoPanel.style.opacity = '0';
                    autoPanel.style.marginTop = '0';
                }
            }
        }

        function addSensorRow(key = '', label = '') {
            const index = sensorCounter++;

            // Auto-select based on key
            const sensorData = key ? availableSensors[key] : null;
            const inputLabel = label || (sensorData ? sensorData.label : '');
            const autoInfo = getAutomationInfo(key);
            const mapping = key ? autoSettingKeyMap[key] : null;
            const unitLabel = mapping ? mapping.unit : '';

            // Build automation toggle area HTML (only if sensor is in a preset)
            let autoAreaHtml = '';
            if (autoInfo) {
                const styles = {
                    'climate': { bg: 'linear-gradient(135deg, #0ea5e9, #0284c7)', short: 'Iklim', icon: 'bi-thermometer-sun' },
                    'fertilizer': { bg: 'linear-gradient(135deg, #22c55e, #16a34a)', short: 'Pemupukan', icon: 'bi-flower1' }
                };
                const s = styles[autoInfo.type] || { bg: '#6b7280', short: autoInfo.label, icon: autoInfo.icon };
                autoAreaHtml = `
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill shadow-sm" style="background: ${s.bg}; font-size: 0.65rem; padding: 0.25rem 0.55rem;">
                            <i class="bi ${s.icon} me-1"></i>${s.short}
                        </span>
                        <div class="form-check form-switch mb-0" style="min-height: auto;">
                            <input class="form-check-input" type="checkbox" role="switch" id="autoToggle_${index}"
                                   name="sensors[${index}][auto_enabled]" value="1"
                                   onchange="toggleAutoPanel(${index})" style="cursor: pointer;">
                            <label class="form-check-label small" for="autoToggle_${index}" style="color: var(--text-secondary); cursor: pointer; font-size: 0.75rem;">
                                Otomasi
                            </label>
                        </div>
                    </div>
                `;
            }

            const row = document.createElement('div');
            row.className = 'sensor-row mb-3';
            row.id = `sensorRow_${index}`;
            row.innerHTML = `
            <div class="row align-items-center g-2">
                <div class="col-6 col-md-4">
                    <select class="form-select sensor-select" name="sensors[${index}][type]" required onchange="onSensorTypeChange(${index}, this.value)">
                        ${getSensorOptions(key)}
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <input type="text" class="form-control sensor-label-input" name="sensors[${index}][label]"
                           placeholder="Label custom (opsional)" value="${inputLabel}">
                </div>
                <div class="col-10 col-md-4" id="autoArea_${index}">
                    ${autoAreaHtml}
                </div>
                <div class="col-2 col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSensorRow(${index})" style="border-radius: 50%;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <!-- Batas Atas/Bawah (tersembunyi, muncul saat toggle ON) -->
            <div id="autoPanel_${index}" style="max-height: 0; opacity: 0; overflow: hidden; transition: all 0.3s ease; margin-top: 0;">
                <div class="row g-2 ps-2" style="border-left: 3px solid var(--primary); margin-left: 0.25rem; padding-top: 0.25rem;">
                    <div class="col-6">
                        <label class="form-label small mb-1" style="color: var(--text-secondary);" id="atsLabel_${index}">Batas Atas${unitLabel ? ` (${unitLabel})` : ''}</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="sensors[${index}][ats_val]" placeholder="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small mb-1" style="color: var(--text-secondary);" id="bwhLabel_${index}">Batas Bawah${unitLabel ? ` (${unitLabel})` : ''}</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="sensors[${index}][bwh_val]" placeholder="0">
                    </div>
                </div>
            </div>
        `;
            document.getElementById('sensorContainer').appendChild(row);

            if (key) {
                const select = row.querySelector('.sensor-select');
                select.value = key;
            }

            updateSensorCount();
            updateSubmitButton();
        }

        function addOutputRow(outputKey = '', customLabel = '', zoneCount = 4) {
            outputCounter++;
            const container = document.getElementById('outputContainer');
            const row = document.createElement('div');
            row.className = 'sensor-row output-row';
            row.id = `outputRow_${outputCounter}`;

            // Check if this is irrigation_pump to show zone input
            const showZones = outputKey === 'irrigation_pump';

            row.innerHTML = `
            <div class="row align-items-center g-3">
                <div class="col-md-4">
                    <label class="form-label small" style="color: var(--text-secondary);">Output Type</label>
                    <select class="form-select output-select" name="outputs[${outputCounter}][type]" 
                            onchange="updateSubmitButton(); toggleZoneInput(${outputCounter}, this.value)">
                        ${getOutputOptions(outputKey)}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small" style="color: var(--text-secondary);">Label (opsional)</label>
                    <input type="text" class="form-control output-label-input" name="outputs[${outputCounter}][label]"
                           placeholder="Label custom" value="${customLabel}">
                </div>
                <div class="col-md-3 zone-input-wrapper" id="zoneWrapper_${outputCounter}" style="display: ${showZones ? 'block' : 'none'};">
                    <label class="form-label small" style="color: var(--text-secondary);">Jumlah Zona</label>
                    <input type="number" class="form-control" name="outputs[${outputCounter}][zones]"
                           value="${zoneCount}" min="1" max="20" placeholder="1-20">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-outline-danger"
                            onclick="removeOutputRow(${outputCounter})" style="border-radius: 12px;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `;
            container.appendChild(row);
            updateOutputCount();
        }

        // Toggle zone input visibility based on output type selection
        function toggleZoneInput(outputId, outputType) {
            const zoneWrapper = document.getElementById(`zoneWrapper_${outputId}`);
            if (zoneWrapper) {
                zoneWrapper.style.display = outputType === 'irrigation_pump' ? 'block' : 'none';
            }
        }

        function removeSensorRow(id) {
            const row = document.getElementById(`sensorRow_${id}`);
            if (row) {
                row.remove();
                updateSensorCount();
                updateSubmitButton();
            }
        }

        function removeOutputRow(id) {
            const row = document.getElementById(`outputRow_${id}`);
            if (row) { row.remove(); updateOutputCount(); }
        }

        function updateSensorCount() {
            const count = document.querySelectorAll('.sensor-row:not(.output-row):not(.schedule-row)').length;
            document.getElementById('sensorCount').textContent = count + ' sensor';
        }

        function updateOutputCount() {
            const count = document.querySelectorAll('.output-row').length;
            document.getElementById('outputCount').textContent = count + ' output';
        }

        function updateScheduleCount() {
            // Deprecated - kept for safety if referenced but simplified not to fail
        }

        // Schedule Functions - Removed legacy code

        function updateSubmitButton() {
            const typeSelected = document.getElementById('deviceType').value !== '';
            const sensorCount = document.querySelectorAll('.sensor-row:not(.output-row)').length;
            const allSensorsSelected = Array.from(document.querySelectorAll('.sensor-select')).every(s => s.value !== '');
            document.getElementById('submitBtn').disabled = !(typeSelected && sensorCount > 0 && allSensorsSelected);
        }

        function selectDeviceType(type) {
            document.querySelectorAll('.type-card').forEach(card => card.classList.remove('selected'));
            document.querySelector(`[data-type="${type}"]`).classList.add('selected');
            document.getElementById('deviceType').value = type;

            // Only add default sensors if no sensors exist yet
            const sensorContainer = document.getElementById('sensorContainer');
            if (sensorContainer.children.length === 0 && defaultSensors[type]) {
                for (const [sensorKey, count] of Object.entries(defaultSensors[type])) {
                    for (let i = 0; i < count; i++) {
                        const label = count > 1 ? `${availableSensors[sensorKey].label} ${i + 1}` : '';
                        addSensorRow(sensorKey, label);
                    }
                }
            }

            // Only add default outputs if no outputs exist yet
            const outputContainer = document.getElementById('outputContainer');
            if (outputContainer.children.length === 0 && defaultOutputs[type]) {
                for (const [outputKey, count] of Object.entries(defaultOutputs[type])) {
                    for (let i = 0; i < count; i++) {
                        const label = count > 1 ? `${availableOutputs[outputKey].label} ${i + 1}` : '';
                        addOutputRow(outputKey, label);
                    }
                }
            }

            updateSubmitButton();
        }

        document.getElementById('deviceForm').addEventListener('submit', function (e) {
            const type = document.getElementById('deviceType').value;
            const sensors = document.querySelectorAll('.sensor-row:not(.output-row)').length;
            if (!type) { e.preventDefault(); alert('Pilih tipe alat terlebih dahulu!'); return false; }
            if (sensors === 0) { e.preventDefault(); alert('Tambahkan minimal 1 sensor!'); return false; }
        });
    </script>


    <!-- Leaflet JS for Map Picker -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize Map Picker
        document.addEventListener('DOMContentLoaded', function () {
            // Default center: Indonesia
            const defaultLat = -6.9175;
            const defaultLng = 107.6191;

            const map = L.map('mapPicker').setView([defaultLat, defaultLng], 13);

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            let marker = null;

            // Click event to place marker
            map.on('click', function (e) {
                const lat = e.latlng.lat.toFixed(7);
                const lng = e.latlng.lng.toFixed(7);

                // Update input fields
                document.getElementById('latitudeInput').value = lat;
                document.getElementById('longitudeInput').value = lng;

                // Add or move marker
                if (marker) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = L.marker(e.latlng, {
                        draggable: true
                    }).addTo(map);

                    // Drag event for marker
                    marker.on('dragend', function (event) {
                        const position = marker.getLatLng();
                        document.getElementById('latitudeInput').value = position.lat.toFixed(7);
                        document.getElementById('longitudeInput').value = position.lng.toFixed(7);
                    });
                }

                marker.bindPopup('<b>📍 Lokasi Device</b><br>Lat: ' + lat + '<br>Lng: ' + lng).openPopup();
            });

            // Auto Detect Location
            document.getElementById('btnAutoDetect').addEventListener('click', function() {
                const btn = this;
                const originalText = btn.innerHTML;
                
                if (!navigator.geolocation) {
                    alert('Geolocation tidak didukung oleh browser ini.');
                    return;
                }
                
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mencari...';
                btn.disabled = true;
                
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    document.getElementById('latitudeInput').value = lat.toFixed(7);
                    document.getElementById('longitudeInput').value = lng.toFixed(7);
                    
                    const newLatLng = new L.LatLng(lat, lng);
                    map.setView(newLatLng, 15);
                    
                    if (marker) {
                        marker.setLatLng(newLatLng);
                    } else {
                        marker = L.marker(newLatLng, { draggable: true }).addTo(map);
                        marker.on('dragend', function (event) {
                            const pos = marker.getLatLng();
                            document.getElementById('latitudeInput').value = pos.lat.toFixed(7);
                            document.getElementById('longitudeInput').value = pos.lng.toFixed(7);
                        });
                    }
                    marker.bindPopup('<b>📍 Lokasi Anda (Auto)</b><br>Lat: ' + lat.toFixed(5) + '<br>Lng: ' + lng.toFixed(5)).openPopup();
                    
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.display_name) {
                                document.getElementById('locationInput').value = data.display_name;
                            }
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                        });
                        
                }, function(error) {
                    alert('Gagal mendapatkan lokasi. Pastikan izin lokasi diizinkan di browser Anda.');
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }, { enableHighAccuracy: true });
            });

            // Fix map display issue when in tabs/hidden containers
            setTimeout(function () {
                map.invalidateSize();
            }, 100);
        });
    </script>
</body>

</html>