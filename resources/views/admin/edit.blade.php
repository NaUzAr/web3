<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Device - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    @include('partials.theme')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- Leaflet CSS for Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
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

        .form-control {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            border-radius: 12px;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            background: var(--glass-bg);
            border-color: var(--primary);
            color: var(--text-main);
            box-shadow: 0 0 0 3px rgba(14, 95, 138, 0.2);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
        }

        .form-control-readonly {
            background: rgba(0, 0, 0, 0.03) !important;
            color: var(--text-secondary) !important;
        }

        .form-label {
            color: var(--primary);
            font-weight: 600;
        }

        .form-text {
            color: var(--text-secondary);
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

        .info-box {
            background: rgba(0, 0, 0, 0.03);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 1rem;
        }

        .info-label {
            color: var(--text-secondary);
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: var(--text-main);
            font-weight: 600;
        }

        .badge-sensor {
            background: rgba(14, 95, 138, 0.1);
            color: var(--primary);
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            border-radius: 10px;
            margin: 4px;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
        }

        .badge-sensor-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .badge-sensor-column {
            font-size: 0.7rem;
            color: var(--text-secondary);
            font-family: monospace;
        }

        .sensors-container {
            background: rgba(0, 0, 0, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 1rem;
        }

        .alert-info-custom {
            background: rgba(14, 165, 233, 0.05);
            border: 1px solid rgba(14, 165, 233, 0.2);
            color: var(--text-main);
            border-radius: 12px;
        }

        .badge-type {
            background: var(--primary-gradient);
            color: #fff;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
        }

        .badge-output {
            background: rgba(250, 204, 21, 0.1);
            color: #d97706;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
            border-radius: 10px;
            margin: 4px;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
        }

        .badge-output-name {
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-output-type {
            font-size: 0.7rem;
            color: var(--text-secondary);
        }

        /* ========= Mobile Responsive ========= */
        @media (max-width: 768px) {
            .container.py-5 {
                padding: 1.25rem 0.75rem !important;
            }

            .glass-card {
                border-radius: 16px;
            }

            .card-header-gradient {
                padding: 1rem 1.25rem;
                border-radius: 16px 16px 0 0 !important;
            }

            .card-header-gradient h4 {
                font-size: 1.1rem;
            }

            .card-body {
                padding: 1.25rem !important;
            }

            .form-control {
                font-size: 16px;
                min-height: 48px;
            }

            #mapPicker {
                height: 200px !important;
            }

            .info-box {
                padding: 0.75rem;
            }

            .d-flex.gap-3.mt-4 {
                flex-direction: column;
            }

            .d-flex.gap-3.mt-4 .btn {
                width: 100%;
                text-align: center;
            }
        }

        @media (max-width: 400px) {
            .container.py-5 {
                padding: 1rem 0.5rem !important;
            }

            .card-body {
                padding: 1rem !important;
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
            <div class="col-lg-8">
                <div class="glass-card shadow-lg">
                    <div class="card-header-gradient d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-white">
                            <i class="bi bi-pencil-square me-2"></i>Edit Device
                        </h4>
                        <a href="{{ route('admin.devices.index') }}" class="btn btn-sm btn-outline-light">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Devices
                        </a>
                    </div>
                    <div class="card-body p-4">

                        <form action="{{ route('admin.device.update', $device->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <!-- Info Read Only -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-label"><i class="bi bi-key me-1"></i>Token</div>
                                        <div class="info-value font-monospace">{{ $device->token }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box">
                                        <div class="info-label"><i class="bi bi-cpu me-1"></i>Tipe Alat</div>
                                        <div class="info-value">
                                            <span class="badge-type">
                                                <i
                                                    class="bi {{ $device->type === 'aws' ? 'bi-cloud-sun' : 'bi-flower1' }} me-1"></i>
                                                {{ $deviceTypes[$device->type] ?? $device->type }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 3: DAFTAR SENSOR -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="bi bi-thermometer-half me-1"></i> Konfigurasi Sensor
                                    <span class="badge-count ms-2" id="sensorCount">0 sensor</span>
                                </label>

                                <div class="alert alert-warning-custom py-2 mb-3">
                                    <small><i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        <b>PERHATIAN:</b> Menghapus sensor yang sudah ada akan <b>menghapus seluruh data riwayat/history</b> dari sensor tersebut selamanya.
                                    </small>
                                </div>

                                <!-- Quick Add Sensors -->
                                <div class="mb-3">
                                    <label class="small mb-2 d-block" style="color: var(--text-secondary);">Quick Add
                                        (Klik untuk
                                        menambahkan):</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($availableSensors as $key => $sensorInfo)
                                            <button type="button" class="btn btn-sm btn-outline-secondary bg-opacity-10"
                                                onclick="addSensorRow('{{ $key }}')"
                                                style="border-color: var(--glass-border); background: var(--glass-bg); color: var(--text-main);">
                                                <i class="bi {{ $sensorInfo['icon'] }} me-1"></i> {{ $sensorInfo['label'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="alert alert-info-custom py-2 mb-3">
                                    <small><i class="bi bi-info-circle me-1"></i>
                                        Sensor yang mendukung otomasi memiliki toggle
                                        <span class="badge rounded-pill" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); font-size: 0.6rem; padding: 0.15rem 0.4rem;">Iklim</span> /
                                        <span class="badge rounded-pill" style="background: linear-gradient(135deg, #22c55e, #16a34a); font-size: 0.6rem; padding: 0.15rem 0.4rem;">Pemupukan</span>.
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
                                        Output dapat dikontrol melalui MQTT topic:
                                        <code>{{ $device->mqtt_topic }}/control</code>
                                    </small>
                                </div>

                                <!-- Quick Add Outputs -->
                                <div class="mb-3">
                                    <label class="small mb-2 d-block" style="color: var(--text-secondary);">Quick Add
                                        (Klik untuk menambahkan):</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($availableOutputs as $key => $outputInfo)
                                            <button type="button" class="btn btn-sm btn-outline-secondary bg-opacity-10"
                                                onclick="addOutputRow('{{ $key }}')"
                                                style="border-color: var(--glass-border); background: var(--glass-bg); color: var(--text-main);">
                                                <i class="bi {{ $outputInfo['icon'] }} me-1"></i> {{ $outputInfo['label'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <div id="outputContainer"></div>

                                <button type="button" class="btn btn-outline-add w-100 mt-3" onclick="addOutputRow()">
                                    <i class="bi bi-plus-circle me-1"></i> Tambah Output
                                </button>
                            </div>

                            <hr class="border-secondary my-4">

                            <!-- Editable Fields -->
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-tag me-1"></i> Nama Device</label>
                                <input type="text" name="name" class="form-control" value="{{ $device->name }}"
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
                                    value="{{ $device->location }}"
                                    placeholder="Contoh: Greenhouse A, Kebun Teh Blok 3 (atau klik Auto Lokasi)">
                            </div>

                            <!-- Map Picker -->
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-map me-1"></i> Pilih Titik Lokasi di
                                    Map</label>
                                <div id="mapPicker"
                                    style="height: 250px; border-radius: 12px; border: 1px solid var(--glass-border);">
                                </div>
                                <div class="form-text">Klik pada map untuk mengubah koordinat lokasi device.</div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-geo me-1"></i> Latitude</label>
                                    <input type="number" step="any" name="latitude" id="latitudeInput"
                                        class="form-control" value="{{ $device->latitude }}" placeholder="-6.9175"
                                        readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-geo me-1"></i> Longitude</label>
                                    <input type="number" step="any" name="longitude" id="longitudeInput"
                                        class="form-control" value="{{ $device->longitude }}" placeholder="107.6191"
                                        readonly>
                                </div>
                            </div>

                            @php
                                $schedule = $device->schedules->first();
                                $irrigationOutput = $device->outputs->where('output_type', 'irrigation_pump')->first();
                                $currentSectors = $irrigationOutput ? $irrigationOutput->max_sectors : ($schedule ? $schedule->max_sectors : null);
                            @endphp

                            @if($currentSectors)
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-geo-alt me-1"></i> Jumlah Zona (Sektor)</label>
                                <input type="number" min="1" max="20" name="max_sectors" class="form-control" value="{{ $currentSectors }}">
                                <div class="form-text">Berapa banyak zona/sektor penyiraman yang aktif (1-20).</div>
                            </div>
                            @endif

                            @if($schedule)
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-clock me-1"></i> Slot Jadwal Maksimal</label>
                                <input type="number" min="1" max="20" name="max_slots" class="form-control" value="{{ $schedule->max_slots }}">
                                <div class="form-text">Berapa banyak maksimal jadwal yang bisa disimpan device.</div>
                            </div>
                            @endif

                            <div class="mb-4">
                                <label class="form-label"><i class="bi bi-broadcast me-1"></i> MQTT Topic</label>
                                <input type="text" name="mqtt_topic" class="form-control"
                                    value="{{ $device->mqtt_topic }}" required>
                            </div>

                            <div class="d-flex gap-3 mt-4">
                                <a href="{{ route('admin.devices.index') }}" class="btn btn-glass">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-gradient flex-grow-1">
                                    <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
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
        const automationPresets = @json($automationPresets);
        let sensorCounter = 0;
        let outputCounter = 0;

        function getSensorOptions(selectedKey = '') {
            let options = '<option value="">-- Pilih Jenis Sensor --</option>';
            for (const [key, info] of Object.entries(availableSensors)) {
                // Parse key without _N suffix if exists
                const baseKey = selectedKey.replace(/_\d+$/, ''); 
                const selected = key === baseKey ? 'selected' : '';
                options += `<option value="${key}" ${selected}>${info.label} ${info.unit ? '(' + info.unit + ')' : ''}</option>`;
            }
            return options;
        }

        function getOutputOptions(selectedKey = '') {
            let options = '<option value="">-- Pilih Jenis Output --</option>';
            for (const [key, info] of Object.entries(availableOutputs)) {
                const baseKey = selectedKey.replace(/_\d+$/, '');
                const selected = key === baseKey ? 'selected' : '';
                const typeLabel = info.type === 'boolean' ? 'ON/OFF' : (info.type === 'percentage' ? '0-100%' : 'Angka');
                options += `<option value="${key}" ${selected}>${info.label} (${typeLabel})</option>`;
            }
            return options;
        }

        function getAutomationInfo(sensorKey) {
            if (!sensorKey) return null;
            const baseKey = sensorKey.replace(/_\d+$/, '');
            for (const [type, preset] of Object.entries(automationPresets)) {
                if (baseKey in preset.sensors) {
                    return { type, label: preset.label, icon: preset.icon };
                }
            }
            return null;
        }

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
            const autoArea = document.getElementById('autoArea_' + index);
            const autoPanel = document.getElementById('autoPanel_' + index);
            if (!autoArea) return;

            const baseKey = sensorKey ? sensorKey.replace(/_\d+$/, '') : '';
            const info = getAutomationInfo(baseKey);
            
            if (info) {
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
                const mapping = autoSettingKeyMap[baseKey];
                const unitLabel = mapping ? mapping.unit : '';
                const atsLabel = document.getElementById('atsLabel_' + index);
                const bwhLabel = document.getElementById('bwhLabel_' + index);
                if (atsLabel) atsLabel.textContent = 'Batas Atas' + (unitLabel ? ` (${unitLabel})` : '');
                if (bwhLabel) bwhLabel.textContent = 'Batas Bawah' + (unitLabel ? ` (${unitLabel})` : '');
            } else {
                autoArea.innerHTML = '';
                if (autoPanel) {
                    autoPanel.style.maxHeight = '0';
                    autoPanel.style.opacity = '0';
                    autoPanel.style.marginTop = '0';
                }
            }
        }

        function addSensorRow(key = '', label = '', mqttKey = '', autoEnabled = false, atsVal = '', bwhVal = '') {
            const index = sensorCounter++;
            const baseKey = key.replace(/_\d+$/, '');
            const sensorData = baseKey ? availableSensors[baseKey] : null;
            const inputLabel = label || (sensorData ? sensorData.label : '');
            const autoInfo = getAutomationInfo(baseKey);
            const mapping = baseKey ? autoSettingKeyMap[baseKey] : null;
            const unitLabel = mapping ? mapping.unit : '';

            let autoAreaHtml = '';
            if (autoInfo) {
                const styles = {
                    'climate': { bg: 'linear-gradient(135deg, #0ea5e9, #0284c7)', short: 'Iklim', icon: 'bi-thermometer-sun' },
                    'fertilizer': { bg: 'linear-gradient(135deg, #22c55e, #16a34a)', short: 'Pemupukan', icon: 'bi-flower1' }
                };
                const s = styles[autoInfo.type] || { bg: '#6b7280', short: autoInfo.label, icon: autoInfo.icon };
                const checked = autoEnabled ? 'checked' : '';
                autoAreaHtml = `
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge rounded-pill shadow-sm" style="background: ${s.bg}; font-size: 0.65rem; padding: 0.25rem 0.55rem;">
                            <i class="bi ${s.icon} me-1"></i>${s.short}
                        </span>
                        <div class="form-check form-switch mb-0" style="min-height: auto;">
                            <input class="form-check-input" type="checkbox" role="switch" id="autoToggle_${index}"
                                   name="sensors[${index}][auto_enabled]" value="1" ${checked}
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
                           placeholder="Label custom" value="${inputLabel}">
                    <input type="hidden" name="sensors[${index}][mqtt_key]" value="${mqttKey}">
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
            <div id="autoPanel_${index}" style="max-height: ${autoEnabled ? '100px' : '0'}; opacity: ${autoEnabled ? '1' : '0'}; overflow: hidden; transition: all 0.3s ease; margin-top: ${autoEnabled ? '0.5rem' : '0'};">
                <div class="row g-2 ps-2" style="border-left: 3px solid var(--primary); margin-left: 0.25rem; padding-top: 0.25rem;">
                    <div class="col-6">
                        <label class="form-label small mb-1" style="color: var(--text-secondary);" id="atsLabel_${index}">Batas Atas${unitLabel ? ` (${unitLabel})` : ''}</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="sensors[${index}][ats_val]" value="${atsVal}" placeholder="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label small mb-1" style="color: var(--text-secondary);" id="bwhLabel_${index}">Batas Bawah${unitLabel ? ` (${unitLabel})` : ''}</label>
                        <input type="number" step="0.01" class="form-control form-control-sm" name="sensors[${index}][bwh_val]" value="${bwhVal}" placeholder="0">
                    </div>
                </div>
            </div>
            `;
            document.getElementById('sensorContainer').appendChild(row);
            updateSensorCount();
        }

        function removeSensorRow(index) {
            const row = document.getElementById(`sensorRow_${index}`);
            if (row) {
                row.remove();
                updateSensorCount();
            }
        }

        function updateSensorCount() {
            const count = document.querySelectorAll('#sensorContainer .sensor-row').length;
            document.getElementById('sensorCount').innerText = `${count} sensor`;
        }

        function addOutputRow(outputKey = '', customLabel = '', maxSectors = '') {
            const index = outputCounter++;
            const baseKey = outputKey.replace(/_\d+$/, '');
            const row = document.createElement('div');
            row.className = 'sensor-row output-row mb-3';
            row.id = `outputRow_${index}`;

            const isIrrigation = baseKey === 'irrigation_pump';

            row.innerHTML = `
            <div class="row align-items-center g-3">
                <div class="col-md-4">
                    <select class="form-select output-select" name="outputs[${index}][type]" 
                            onchange="toggleZoneInput(${index}, this.value)">
                        ${getOutputOptions(outputKey)}
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="outputs[${index}][label]" 
                           placeholder="Label opsional" value="${customLabel}">
                </div>
                <div class="col-md-3" id="zoneInputContainer_${index}" style="display: ${isIrrigation ? 'block' : 'none'};">
                    <input type="number" class="form-control" name="outputs[${index}][zones]" 
                           placeholder="Jml Zona (max 20)" min="1" max="20" value="${maxSectors}">
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeOutputRow(${index})" style="border-radius: 50%;">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            `;
            document.getElementById('outputContainer').appendChild(row);
            updateOutputCount();
        }

        function toggleZoneInput(index, value) {
            const container = document.getElementById(`zoneInputContainer_${index}`);
            if (container) {
                container.style.display = value === 'irrigation_pump' ? 'block' : 'none';
            }
        }

        function removeOutputRow(index) {
            const row = document.getElementById(`outputRow_${index}`);
            if (row) {
                row.remove();
                updateOutputCount();
            }
        }

        function updateOutputCount() {
            const count = document.querySelectorAll('#outputContainer .output-row').length;
            document.getElementById('outputCount').innerText = `${count} output`;
        }

        // Initialize Existing Data
        document.addEventListener('DOMContentLoaded', function () {
            // Prepopulate Sensors
            @foreach($device->sensors as $sensor)
                @php
                    $isAutoEnabled = false;
                    $atsVal = '';
                    $bwhVal = '';
                    $baseKey = preg_replace('/_\d+$/', '', $sensor->sensor_name);
                    
                    $autoKeyMap = [
                        'ni_SUHU' => 'suhu', 'ni_KELEM' => 'kelem',
                        'ni_PH' => 'ph', 'ni_TDS' => 'tds'
                    ];
                    
                    if (isset($autoKeyMap[$baseKey])) {
                        $settingKey = $autoKeyMap[$baseKey];
                        $atsSetting = $device->settings->where('key', "ats_{$settingKey}")->first();
                        $bwhSetting = $device->settings->where('key', "bwh_{$settingKey}")->first();
                        
                        if ($atsSetting && $bwhSetting) {
                            $isAutoEnabled = true;
                            $atsVal = $atsSetting->value;
                            $bwhVal = $bwhSetting->value;
                        }
                    }
                @endphp
                addSensorRow("{{ $sensor->sensor_name }}", "{{ $sensor->sensor_label }}", "{{ $sensor->mqtt_key }}", {{ $isAutoEnabled ? 'true' : 'false' }}, "{{ $atsVal }}", "{{ $bwhVal }}");
            @endforeach

            // Prepopulate Outputs
            @foreach($device->outputs as $output)
                addOutputRow("{{ $output->output_name }}", "{{ $output->output_label }}", "{{ $output->max_sectors ?? '' }}");
            @endforeach
        });
    </script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get existing coordinates or use default
            const existingLat = {{ $device->latitude ?? -6.9175 }};
            const existingLng = {{ $device->longitude ?? 107.6191 }};
            const hasExistingCoords = {{ ($device->latitude && $device->longitude) ? 'true' : 'false' }};

            const map = L.map('mapPicker').setView([existingLat, existingLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            let marker = null;

            // Add existing marker if coordinates exist
            if (hasExistingCoords) {
                marker = L.marker([existingLat, existingLng], { draggable: true }).addTo(map);
                marker.bindPopup('<b>📍 Lokasi Device</b><br>Lat: ' + existingLat + '<br>Lng: ' + existingLng);

                marker.on('dragend', function (event) {
                    const position = marker.getLatLng();
                    document.getElementById('latitudeInput').value = position.lat.toFixed(7);
                    document.getElementById('longitudeInput').value = position.lng.toFixed(7);
                });
            }

            map.on('click', function (e) {
                const lat = e.latlng.lat.toFixed(7);
                const lng = e.latlng.lng.toFixed(7);

                document.getElementById('latitudeInput').value = lat;
                document.getElementById('longitudeInput').value = lng;

                if (marker) {
                    marker.setLatLng(e.latlng);
                } else {
                    marker = L.marker(e.latlng, { draggable: true }).addTo(map);
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

            setTimeout(function () { map.invalidateSize(); }, 100);
        });
    </script>
</body>

</html>