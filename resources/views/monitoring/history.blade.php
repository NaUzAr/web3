<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Riwayat Data - {{ $device->name }}</title>
    @include('partials.pwa-head')
    @include('partials.theme')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .navbar-glass {
            background: var(--navbar-bg) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
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
            padding: 1rem;
            vertical-align: middle;
            white-space: nowrap; /* Force scroll on overflow */
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
            white-space: nowrap;
        }

        @media (max-width: 767.98px) {
            .table-glass th,
            .table-glass td {
                padding: 0.5rem 0.4rem;
                font-size: 0.85rem;
            }
            .table-glass thead th {
                font-size: 0.75rem;
            }
        }

        /* Pagination Glass */
        .pagination-glass {
            flex-wrap: wrap;
            justify-content: center;
        }
        .pagination-glass .page-link {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            margin: 0 2px;
            border-radius: 8px;
        }
        .pagination-glass .page-item.active .page-link {
            background: var(--primary-gradient);
            color: white;
            border-color: transparent;
        }
        .pagination-glass .page-item.disabled .page-link {
            background: rgba(255, 255, 255, 0.5);
            color: var(--text-muted);
        }

        @media (max-width: 767.98px) {
            .pagination-glass .page-item {
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
        }

        /* Fix Flatpickr Z-Index for PC */
        .flatpickr-calendar {
            z-index: 99999 !important;
        }

        /* Prevent Date Picker from collapsing */
        .date-pill {
            min-width: 180px;
        }
        .date-pill input {
            background: transparent !important;
            color: var(--text-main) !important;
            min-width: 140px;
            outline: none !important;
            box-shadow: none !important;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .btn-action-custom {
            padding: 0.6rem 1.25rem;
            font-weight: 600;
            border-radius: 50px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .btn-download {
            color: #0ea5e9;
            border: 1px solid rgba(14, 165, 233, 0.2);
        }
        .btn-download:hover {
            background: #f0f9ff;
            color: #0284c7;
            border-color: #0ea5e9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15);
        }

        .btn-back {
            color: #475569;
            border: 1px solid rgba(71, 85, 105, 0.2);
        }
        .btn-back:hover {
            background: #f8fafc;
            color: #334155;
            border-color: #475569;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(71, 85, 105, 0.15);
        }

        /* Modal Classes */
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

        .modal-actions {
            padding: 1rem 1.25rem 1.5rem;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }

        .form-control-dark,
        .form-select-dark {
            background-color: #ffffff !important;
            border: 2px solid #e5e7eb !important;
            color: #111827 !important;
            border-radius: 14px;
            padding: 0.9rem 1rem;
            font-size: 1.1rem;
            font-weight: 600;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control-dark:focus,
        .form-select-dark:focus {
            background-color: #ffffff !important;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 4px rgba(14, 95, 138, 0.12) !important;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    @include('partials.navbar')

    <div class="container pt-3 pb-5">
        <!-- Header Page -->
        <div class="page-header d-flex justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h2 class="mb-0 fw-bold d-flex align-items-center" style="color: var(--text-main);">
                    <i class="bi bi-clock-history me-2"></i>Riwayat Data
                </h2>
                <p class="mb-0 mt-1" style="color: var(--text-secondary);">
                    Device: <strong>{{ $device->name }}</strong>
                </p>
            </div>
            <div class="d-flex gap-2">
                @if(!($isAdminView ?? false))
                    <button type="button" class="btn btn-action-custom btn-download" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="bi bi-download me-md-1"></i> <span class="d-none d-md-inline">Download CSV</span>
                    </button>
                @endif
                <a href="{{ isset($isAdminView) && $isAdminView ? route('admin.device.monitoring', $device->id) : route('monitoring.show', $userDevice->id) }}" class="btn btn-action-custom btn-back">
                    <i class="bi bi-arrow-left me-md-1"></i> <span class="d-none d-md-inline">Kembali ke Device</span>
                </a>
            </div>
        </div>

        @if($logData->count() > 0 || request()->has('start_date'))
            
            <!-- Grafik Data -->
            <div class="glass-card mb-4">
                <!-- Header (Title & Filters) -->
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between mb-4 pb-3 gap-3" style="border-bottom: 1px dashed var(--glass-border);">
                    <h5 class="card-title mb-0 text-nowrap" style="color: var(--text-main);"><i class="bi bi-graph-up me-2"></i>Grafik Sensor</h5>
                    
                    <!-- Filters Container -->
                    <div class="d-flex flex-column flex-xl-row align-items-stretch align-items-xl-center gap-3 w-100 justify-content-lg-end" style="max-width: 100%;">
                        <!-- Sensor Filter -->
                        <div class="d-flex align-items-center w-100 w-xl-auto justify-content-lg-end">
                            <label class="me-2 text-nowrap" style="color: var(--text-main);"><i class="bi bi-filter me-1"></i>Pilih Sensor:</label>
                            <select id="globalSensorSelect" class="form-select form-select-sm w-100 w-xl-auto" style="background: var(--glass-bg); color: var(--text-main); border: 1px solid var(--glass-border); min-width: 200px;">
                                <option value="all" style="color: #333; background-color: #ffffff;">Semua Sensor</option>
                                @foreach($sensors as $index => $sensor)
                                    <option value="{{ $index }}" style="color: #333; background-color: #ffffff;">
                                        {{ $sensor->sensor_label }} ({{ $sensor->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Date Filter Form -->
                        <form action="{{ url()->current() }}" method="GET" class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2 m-0 w-100 w-xl-auto justify-content-lg-end">
                            <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center gap-2">
                                <div class="date-pill flatpickr-wrapper d-flex align-items-center px-3 py-2 rounded-pill shadow-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); cursor: pointer; flex: 1;">
                                    <i class="bi bi-calendar-event text-primary me-2" data-toggle></i>
                                    <input type="text" class="form-control border-0 p-0 w-100" name="start_date" value="{{ request('start_date') }}" placeholder="Dari Waktu..." data-input>
                                </div>
                                <div class="d-none d-sm-flex align-items-center justify-content-center" style="color: var(--text-secondary);">
                                    <i class="bi bi-arrow-right"></i>
                                </div>
                                <div class="date-pill flatpickr-wrapper d-flex align-items-center px-3 py-2 rounded-pill shadow-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border); cursor: pointer; flex: 1;">
                                    <i class="bi bi-calendar-check text-primary me-2" data-toggle></i>
                                    <input type="text" class="form-control border-0 p-0 w-100" name="end_date" value="{{ request('end_date') }}" placeholder="Sampai Waktu..." data-input>
                                </div>
                            </div>
                            <div class="d-flex gap-2 mt-2 mt-md-0">
                                <button type="submit" class="btn btn-primary flex-grow-1 flex-md-grow-0 px-4 rounded-pill shadow-sm d-flex align-items-center justify-content-center" style="height: 42px;" title="Terapkan Filter">
                                    <i class="bi bi-search me-2"></i> Filter
                                </button>
                                @if(request()->has('start_date') || request()->has('end_date'))
                                    <a href="{{ url()->current() }}" class="btn btn-secondary text-white rounded-pill shadow-sm d-flex align-items-center justify-content-center px-4" style="height: 42px;" title="Reset Filter">
                                        <i class="bi bi-x-lg me-2"></i> Reset
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                <div style="position: relative; height: 50vh; min-height: 300px; max-height: 500px; width: 100%;">
                    <canvas id="sensorChart"></canvas>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="glass-card">
                <h5 class="card-title mb-4" style="color: var(--text-main);"><i class="bi bi-table me-2"></i>Tabel Data ({{ $logData->total() }} records)</h5>
                
                <div class="table-responsive">
                    <table class="table table-glass mb-0" id="sensorDataTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Waktu</th>
                                @foreach($sensors as $sensorIndex => $sensor)
                                    <th class="sensor-col" data-sensor-index="{{ $sensorIndex }}">
                                        {{ $sensor->sensor_label }} <br><small>({{ $sensor->unit }})</small>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logData as $index => $row)
                                <tr>
                                    <td>{{ $logData->firstItem() + $index }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->recorded_at)->format('d/m/Y H:i:s') }}</td>
                                    @foreach($sensors as $sensorIndex => $sensor)
                                        <td class="sensor-col" data-sensor-index="{{ $sensorIndex }}">
                                            @if(isset($row->{$sensor->sensor_name}))
                                                {{ number_format($row->{$sensor->sensor_name}, 2) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($logData->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        <nav>
                            <ul class="pagination pagination-glass mb-0">
                                @if($logData->onFirstPage())
                                    <li class="page-item disabled"><span class="page-link">«</span></li>
                                @else
                                    <li class="page-item"><a class="page-link" href="{{ $logData->previousPageUrl() }}">«</a></li>
                                @endif

                                @foreach($logData->getUrlRange(max(1, $logData->currentPage() - 2), min($logData->lastPage(), $logData->currentPage() + 2)) as $page => $url)
                                    @if($page == $logData->currentPage())
                                        <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                                    @else
                                        <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                                    @endif
                                @endforeach

                                @if($logData->hasMorePages())
                                    <li class="page-item"><a class="page-link" href="{{ $logData->nextPageUrl() }}">»</a></li>
                                @else
                                    <li class="page-item disabled"><span class="page-link">»</span></li>
                                @endif
                            </ul>
                        </nav>
                    </div>
                    <p class="text-center mt-2 small" style="color: var(--text-secondary);">
                        Showing {{ $logData->firstItem() }} - {{ $logData->lastItem() }} of {{ $logData->total() }} records
                    </p>
                @endif
            </div>
        @else
            <!-- No Data -->
            <div class="glass-card text-center py-5">
                <i class="bi bi-inbox mb-3" style="font-size: 4rem; color: var(--text-secondary);"></i>
                <h4 class="mb-2" style="color: var(--text-main);">Belum Ada Riwayat Data</h4>
                <p style="color: var(--text-secondary);">Data sensor belum terekam atau tidak ditemukan pada rentang tanggal tersebut.</p>
                @if(request()->has('start_date') || request()->has('end_date'))
                    <a href="{{ url()->current() }}" class="btn btn-primary rounded-pill px-4 mt-3">Reset Filter</a>
                @endif
            </div>
        @endif
    </div>

    @if(!($isAdminView ?? false))
        <!-- Export Modal -->
        <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content modal-content-glass">
                    <div class="modal-handle"></div>
                    <div class="modal-header-custom">
                        <h5 id="exportModalLabel"><i class="bi bi-download me-2"></i>Download Data</h5>
                        <div class="subtitle">Pilih rentang tanggal untuk data CSV</div>
                    </div>
                    <form action="{{ route('monitoring.export', $userDevice->id) }}" method="GET">
                        <div class="modal-body-custom">
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #374151; font-size: 0.9rem;">
                                    <i class="bi bi-calendar-event me-1"></i> Tanggal Mulai
                                </label>
                                <input type="date" name="start_date" class="form-control form-control-dark"
                                    value="{{ date('Y-m-d', strtotime('-7 days')) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #374151; font-size: 0.9rem;">
                                    <i class="bi bi-calendar-check me-1"></i> Tanggal Akhir
                                </label>
                                <input type="date" name="end_date" class="form-control form-control-dark"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm"
                                    style="border: 1px solid var(--primary); color: var(--primary); border-radius: 12px; padding: 6px 12px;"
                                    onclick="setDateRange(7)">7 Hari</button>
                                <button type="button" class="btn btn-sm"
                                    style="border: 1px solid var(--primary); color: var(--primary); border-radius: 12px; padding: 6px 12px;"
                                    onclick="setDateRange(30)">30 Hari</button>
                                <button type="button" class="btn btn-sm"
                                    style="border: 1px solid var(--primary); color: var(--primary); border-radius: 12px; padding: 6px 12px;"
                                    onclick="setDateRange(90)">3 Bulan</button>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="btn" style="background: linear-gradient(135deg, #0e5f8a, #0d9488); border-radius: 12px; padding: 0.75rem; font-size: 1rem; font-weight: 700; width: 100%; color: #fff; box-shadow: 0 4px 14px rgba(13, 148, 136, 0.3);">
                                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Download CSV
                            </button>
                            <button type="button" class="btn"
                                style="background: #f3f4f6; color: #6b7280; border-radius: 12px; padding: 0.75rem; font-size: 1rem; font-weight: 600; width: 100%; border: none;"
                                data-bs-dismiss="modal">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        function setDateRange(days) {
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(startDate.getDate() - days);

            document.querySelector('input[name="start_date"]').value = startDate.toISOString().split('T')[0];
            document.querySelector('input[name="end_date"]').value = endDate.toISOString().split('T')[0];
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Flatpickr for datetime inputs using wrapper mode
            flatpickr(".flatpickr-wrapper", {
                wrap: true,
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                allowInput: false, // Prevent keyboard from showing on mobile
                altInput: true,
                altFormat: "d M Y H:i",
                disableMobile: true // Force flatpickr on mobile too for consistent UI
            });

            const canvas = document.getElementById('sensorChart');
            if(canvas) {
                const ctx = canvas.getContext('2d');
                const chartData = @json($chartData ?? []);
                const sensors = @json($sensors ?? []);

                const colors = [
                    { border: '#22c55e', bg: 'rgba(34, 197, 94, 0.3)' },
                    { border: '#0ea5e9', bg: 'rgba(14, 165, 233, 0.3)' },
                    { border: '#f59e0b', bg: 'rgba(245, 158, 11, 0.3)' },
                    { border: '#8b5cf6', bg: 'rgba(139, 92, 246, 0.3)' },
                    { border: '#ef4444', bg: 'rgba(239, 68, 68, 0.3)' },
                    { border: '#06b6d4', bg: 'rgba(6, 182, 212, 0.3)' },
                    { border: '#84cc16', bg: 'rgba(132, 204, 22, 0.3)' },
                    { border: '#ec4899', bg: 'rgba(236, 72, 153, 0.3)' },
                ];

                function getSingleFilteredData(sensorIndex) {
                    const sensor = sensors[sensorIndex];
                    if(!sensor) return null;
                    
                    const sensorName = sensor.sensor_name;
                    // For chart labels, we use all chartData time points so datasets align correctly on the X axis
                    const filteredLabels = chartData.map(row => {
                        const date = new Date(row.recorded_at);
                        return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    });
                    
                    const filteredData = chartData.map(row => row[sensorName]);
                    
                    const colorIndex = sensorIndex % colors.length;
                    return {
                        labels: filteredLabels,
                        dataset: {
                            label: sensor.sensor_label + (sensor.unit ? ` (${sensor.unit})` : ''),
                            data: filteredData,
                            borderColor: colors[colorIndex].border,
                            backgroundColor: colors[colorIndex].bg,
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true, // Only fill if it's a single line, we will change this dynamically
                            pointRadius: 4,
                            pointBackgroundColor: colors[colorIndex].border,
                            pointHoverRadius: 6,
                            spanGaps: true // Allow lines to connect over null values if any
                        }
                    };
                }

                function getCombinedFilteredData(selectedValue) {
                    if (selectedValue === 'all') {
                        let datasets = [];
                        let labels = [];
                        if (chartData.length > 0) {
                            labels = chartData.map(row => {
                                const date = new Date(row.recorded_at);
                                return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                            });
                        }
                        
                        sensors.forEach((s, idx) => {
                            const data = getSingleFilteredData(idx);
                            if (data) {
                                // Turn off fill when displaying multiple datasets so they don't overlap completely
                                data.dataset.fill = false; 
                                datasets.push(data.dataset);
                            }
                        });
                        return { labels, datasets };
                    } else {
                        const data = getSingleFilteredData(parseInt(selectedValue));
                        if (data) {
                            data.dataset.fill = true;
                            return { labels: data.labels, datasets: [data.dataset] };
                        }
                        return { labels: [], datasets: [] };
                    }
                }

                if(chartData.length > 0 && sensors.length > 0) {
                    const initialData = getCombinedFilteredData('all');
                    let sensorChart = new Chart(ctx, {
                        type: 'line',
                        data: { labels: initialData.labels, datasets: initialData.datasets },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { 
                                legend: { 
                                    display: true,
                                    labels: { color: getComputedStyle(document.body).getPropertyValue('--text-main').trim() }
                                } 
                            },
                            scales: {
                                x: { 
                                    ticks: { color: getComputedStyle(document.body).getPropertyValue('--text-secondary').trim() }, 
                                    grid: { color: getComputedStyle(document.body).getPropertyValue('--glass-border').trim() } 
                                },
                                y: { 
                                    ticks: { color: getComputedStyle(document.body).getPropertyValue('--text-secondary').trim() }, 
                                    grid: { color: getComputedStyle(document.body).getPropertyValue('--glass-border').trim() } 
                                }
                            }
                        }
                    });

                    // Global Filter Listener (syncs both Chart and Table)
                    document.getElementById('globalSensorSelect')?.addEventListener('change', function () {
                        const selectedValue = this.value;
                        
                        // Update Chart
                        const filteredData = getCombinedFilteredData(selectedValue);
                        sensorChart.data.labels = filteredData.labels;
                        sensorChart.data.datasets = filteredData.datasets;
                        // Hide legend if only 1 dataset is showing, show if multiple
                        sensorChart.options.plugins.legend.display = (selectedValue === 'all');
                        sensorChart.update();

                        // Update Table
                        const sensorCols = document.querySelectorAll('.sensor-col');
                        const tableRows = document.querySelectorAll('#sensorDataTable tbody tr');

                        sensorCols.forEach(col => {
                            if (selectedValue === 'all') {
                                col.style.display = '';
                            } else {
                                col.style.display = col.dataset.sensorIndex === selectedValue ? '' : 'none';
                            }
                        });

                        tableRows.forEach(row => {
                            if (selectedValue === 'all') {
                                row.style.display = '';
                            } else {
                                const sensorCell = row.querySelector(`.sensor-col[data-sensor-index="${selectedValue}"]`);
                                if (sensorCell) {
                                    row.style.display = sensorCell.textContent.trim() === '-' ? 'none' : '';
                                }
                            }
                        });
                    });
                }
            }
        });
    </script>
</body>
</html>
