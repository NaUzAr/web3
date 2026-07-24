<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Devices - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @include('partials.theme')

    <style>
        /* Page Title */
        .page-title {
            color: #fff;
            font-weight: 700;
        }

        .page-title i {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Table Styles */
        .table-dark-custom {
            background: var(--navbar-bg) !important;
        }

        .table-dark-custom th {
            color: var(--primary-light);
            font-weight: 600;
            border-bottom: 1px solid var(--glass-border) !important;
            padding: 1rem;
        }

        .table tbody tr {
            background: transparent;
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .table tbody td {
            color: #1f2937;
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem;
            vertical-align: middle;
        }

        /* Badges */
        .badge-type {
            background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
            color: #fff;
            font-weight: 600;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
        }

        .badge-output {
            background: #fbbf24;
            color: #1f2937;
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            margin: 2px;
            font-size: 0.75rem;
            display: inline-block;
        }

        .badge-token {
            background: rgba(100, 116, 139, 0.15);
            color: #475569;
            font-family: monospace;
            padding: 0.35rem 0.6rem;
            border-radius: 8px;
        }

        /* Action Buttons */
        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-action-edit {
            background: rgba(250, 204, 21, 0.2);
            color: #facc15;
        }

        .btn-action-edit:hover {
            background: rgba(250, 204, 21, 0.4);
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-action-qr {
            background: rgba(14, 165, 233, 0.2);
            color: #0ea5e9;
        }

        .btn-action-qr:hover {
            background: rgba(14, 165, 233, 0.4);
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-action-copy {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
        }

        .btn-action-copy:hover {
            background: rgba(16, 185, 129, 0.4);
            color: #fff;
            transform: translateY(-2px);
        }

        .btn-action-delete {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }

        .btn-action-delete:hover {
            background: rgba(239, 68, 68, 0.4);
            color: #fff;
            transform: translateY(-2px);
        }

        /* QR Modal & QRIS Card */
        .qr-modal .modal-content {
            background: var(--glass-bg, #fff);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border, #e2e8f0);
            border-radius: 24px;
            overflow: hidden;
        }

        .qris-card {
            background: #ffffff;
            width: 100%;
            max-width: 340px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
        }

        .qris-header {
            background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
            padding: 1.5rem 1rem;
            color: white;
            text-align: center;
        }

        .qris-logo-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .qris-header-logo {
            width: 48px;
            height: 48px;
            object-fit: contain;
            background: white;
            border-radius: 12px;
            padding: 4px;
        }

        .qris-header-text {
            text-align: left;
        }

        .qris-brand {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 1px;
            line-height: 1.2;
        }

        .qris-sub {
            font-size: 0.75rem;
            font-weight: 500;
            opacity: 0.9;
            letter-spacing: 0.5px;
        }

        .qris-device-name {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e293b;
            text-align: center;
            padding: 1.25rem 1rem 0.5rem;
        }

        .qris-qr-wrapper {
            padding: 1rem;
            display: flex;
            justify-content: center;
        }

        .qris-qr-border {
            background: white;
            padding: 0.5rem;
            border-radius: 20px;
            box-shadow: inset 0 0 0 2px rgba(14, 165, 233, 0.2);
            position: relative;
        }

        .qris-qr-border::before {
            content: '';
            position: absolute;
            top: -2px; left: -2px; right: -2px; bottom: -2px;
            border-radius: 22px;
            background: linear-gradient(135deg, #0ea5e9, #8b5cf6);
            z-index: -1;
        }

        .qris-token {
            font-family: monospace;
            font-size: 1.25rem;
            letter-spacing: 3px;
            color: #334155;
            text-align: center;
            padding: 0.5rem;
            font-weight: 600;
        }

        .qris-footer {
            background: #f8fafc;
            padding: 1rem;
            text-align: center;
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
            border-top: 1px dashed #e2e8f0;
        }

        .qr-modal .modal-body {
            padding: 2rem;
            text-align: center;
        }

        .qr-display {
            display: inline-block;
            padding: 1rem;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }

        .qr-token-text {
            font-family: monospace;
            font-size: 1.1rem;
            letter-spacing: 2px;
            color: #475569;
            background: rgba(100, 116, 139, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: inline-block;
            margin-top: 1rem;
        }

        .btn-download-qr {
            background: var(--primary-gradient, linear-gradient(135deg, #0ea5e9, #0369a1));
            border: none;
            color: #fff;
            padding: 0.65rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-download-qr:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(14, 165, 233, 0.3);
            color: #fff;
        }

        /* Empty State */
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
        }

        .empty-state i {
            font-size: 4rem;
            color: rgba(255, 255, 255, 0.3);
        }

        .empty-state p {
            color: rgba(255, 255, 255, 0.6);
            margin-top: 1rem;
        }

        .empty-state a {
            color: var(--primary-light);
        }

        /* ========= Mobile Responsive ========= */
        @media (max-width: 768px) {
            .container.py-5 {
                padding: 1.5rem 0.75rem !important;
            }

            .page-title {
                font-size: 1.25rem;
            }

            .d-flex.justify-content-between.align-items-center.mb-4 {
                flex-direction: column;
                gap: 0.75rem;
                align-items: stretch !important;
            }

            .glass-card {
                border-radius: 16px;
                padding: 0.5rem;
            }

            /* Table → Card Layout */
            .table thead {
                display: none;
            }

            .table tbody tr {
                display: block;
                background: var(--glass-bg);
                border: 1px solid var(--glass-border);
                border-radius: 12px;
                padding: 0.75rem;
                margin-bottom: 0.75rem;
            }

            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.35rem 0.5rem;
                border: none;
                font-size: 0.85rem;
            }

            .table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                font-size: 0.75rem;
                color: var(--text-secondary);
                margin-right: 0.75rem;
                flex-shrink: 0;
            }

            .table tbody td:last-child {
                justify-content: flex-end;
                padding-top: 0.5rem;
                border-top: 1px solid var(--glass-border);
                margin-top: 0.25rem;
            }

            /* Hide less important on small screens */
            .table tbody td.d-mobile-none {
                display: none;
            }

            .btn-action {
                width: 40px;
                height: 40px;
            }
        }

        @media (max-width: 400px) {
            .container.py-5 {
                padding: 1rem 0.5rem !important;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <!-- Navbar -->
    @include('partials.navbar')

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="page-title mb-0">
                <i class="bi bi-cpu-fill me-2"></i>Device Management
            </h2>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-inboxes me-1"></i> Tiket
                </a>
                <a href="{{ route('admin.activity-logs') }}" class="btn btn-outline-light">
                    <i class="bi bi-journal-text me-1"></i> Logs
                </a>

                <!-- Search Form -->
                <form action="{{ route('admin.devices.index') }}" method="GET" class="d-flex mb-0">
                    <div class="input-group shadow-sm" style="max-width: 250px; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e2e8f0;">
                        <input type="text" name="search" class="form-control border-0 shadow-none text-dark" placeholder="Cari device..." value="{{ request('search') }}" style="background: transparent;">
                        <button type="submit" class="btn btn-light border-0 shadow-none" style="color: #0ea5e9;">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>

                <a href="{{ route('admin.device.create') }}" class="btn btn-gradient">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Device
                </a>
            </div>
        </div>



        @if(session('success'))
            <div class="alert alert-success-custom mb-4">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <div class="glass-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-dark-custom">
                        <tr>
                            <th>#</th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'name', 'order' => request('sort') == 'name' && request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none" style="color: var(--primary-light);">
                                    Nama Device
                                    @if(request('sort') == 'name')
                                        <i class="bi bi-sort-alpha-{{ request('order') == 'asc' ? 'down' : 'up-alt' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up opacity-50" style="font-size: 0.8em;"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Tipe</th>
                            <th>Sensors</th>
                            <th>Outputs</th>
                            <th>MQTT Topic</th>

                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => request('sort', 'created_at') == 'created_at' && request('order', 'desc') == 'desc' ? 'asc' : 'desc']) }}" class="text-decoration-none" style="color: var(--primary-light);">
                                    Dibuat
                                    @if(request('sort', 'created_at') == 'created_at')
                                        <i class="bi bi-sort-numeric-{{ request('order', 'desc') == 'asc' ? 'up-alt' : 'down' }}"></i>
                                    @else
                                        <i class="bi bi-arrow-down-up opacity-50" style="font-size: 0.8em;"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $device)
                            <tr>
                                <td data-label="#" class="fw-semibold">{{ $loop->iteration }}</td>
                                <td data-label="Device">
                                    <a href="{{ route('admin.device.monitoring', $device->id) }}"
                                        class="text-decoration-none">
                                        <div class="fw-bold" style="color: #1f2937;">{{ $device->name }}</div>
                                        <small style="color: #64748b;">{{ $device->table_name }}</small>
                                    </a>
                                </td>
                                <td data-label="Tipe">
                                    <span style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9; padding: 4px 10px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; display: inline-flex; align-items: center;">
                                        <i class="bi {{ $device->type === 'aws' ? 'bi-cloud-sun' : 'bi-flower1' }} me-1"></i>
                                        {{ strtoupper($device->type ?? 'N/A') }}
                                    </span>
                                </td>
                                <td data-label="Sensors">
                                    @if($device->sensors->count() > 0)
                                        @foreach($device->sensors->take(4) as $sensor)
                                            <span class="badge-sensor" title="{{ $sensor->sensor_label }}">
                                                {{ $sensor->sensor_name }}
                                            </span>
                                        @endforeach
                                        @if($device->sensors->count() > 4)
                                            <span class="badge-sensor">+{{ $device->sensors->count() - 4 }}</span>
                                        @endif
                                    @else
                                        <span style="color: var(--text-secondary);">-</span>
                                    @endif
                                </td>
                                <td data-label="Outputs">
                                    @if($device->outputs->count() > 0)
                                        @foreach($device->outputs->take(3) as $output)
                                            <span class="badge-output" title="{{ $output->output_label }}">
                                                <i class="bi bi-toggle-on me-1"></i>{{ $output->output_name }}
                                            </span>
                                        @endforeach
                                        @if($device->outputs->count() > 3)
                                            <span class="badge-output">+{{ $device->outputs->count() - 3 }}</span>
                                        @endif
                                    @else
                                        <span style="color: var(--text-secondary);">-</span>
                                    @endif
                                </td>
                                <td data-label="MQTT" class="d-mobile-none">
                                    <code class="text-info">{{ $device->mqtt_topic }}</code>
                                </td>

                                <td data-label="Dibuat" class="d-mobile-none">
                                    <small style="color: #64748b;">{{ $device->created_at ? $device->created_at->format('d M Y, H:i') : '-' }}</small>
                                </td>
                                <td data-label="" class="text-center">
                                    <button type="button" class="btn-action btn-action-qr" title="QR Code"
                                        onclick="showQrModal('{{ $device->token }}', '{{ $device->name }}')">
                                        <i class="bi bi-qr-code"></i>
                                    </button>
                                    <div class="dropdown d-inline">
                                        <button class="btn-action btn-action-copy" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Copy Data">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius: 12px; font-size: 0.9rem;">
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center" href="#" onclick="copyToClipboard(event, '{{ $device->token }}', this)">
                                                    <i class="bi bi-key me-2 text-secondary"></i> Copy Token
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2 d-flex align-items-center" href="#" onclick="copyToClipboard(event, '{{ $device->mqtt_topic }}', this)">
                                                    <i class="bi bi-broadcast me-2 text-secondary"></i> Copy MQTT Topic
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <a href="{{ route('admin.device.edit', $device->id) }}"
                                        class="btn-action btn-action-edit" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.device.destroy', $device->id) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('⚠️ BAHAYA: Menghapus device akan MENGHAPUS TABEL {{ $device->table_name }} secara permanen!\n\nLanjutkan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-action-delete" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <p>Belum ada device. <a href="{{ route('admin.device.create') }}">Tambah device
                                                pertama!</a></p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- QR Code Modal - QRIS Style -->
    <div class="modal fade qr-modal" id="qrModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <!-- Close button -->
                    <button type="button" class="btn-close position-absolute" data-bs-dismiss="modal"
                        style="top: 12px; right: 12px; z-index: 10; filter: invert(1);"></button>

                    <!-- QRIS-style Card -->
                    <div class="qris-card" id="qrisCard">
                        <!-- Header gradient band -->
                        <div class="qris-header">
                            <div class="qris-logo-row">
                                <img src="{{ asset(env('APP_LOGO', 'images/logo.png')) }}" alt="Logo" class="qris-header-logo">
                                <div class="qris-header-text">
                                    <div class="qris-brand">SWARATANI</div>
                                    <div class="qris-sub">Smart Agriculture IoT</div>
                                </div>
                            </div>
                        </div>

                        <!-- Device name -->
                        <div class="qris-device-name" id="qrisDeviceName">Device Name</div>

                        <!-- QR Code area with gradient border -->
                        <div class="qris-qr-wrapper">
                            <div class="qris-qr-border">
                                <div id="qrCanvas" style="width: 260px; min-height: 260px; margin: 0 auto;"></div>
                            </div>
                        </div>
                        <div id="qrError" class="text-danger text-center mt-2" style="display: none; font-size: 0.9rem;"></div>

                        <!-- Token -->
                        <div class="qris-token" id="qrisToken">XXXXXXXXXXXXXXXX</div>

                        <!-- Footer -->
                        <div class="qris-footer">
                            <span><i class="bi bi-shield-check me-1"></i>Scan untuk tambah device</span>
                        </div>
                    </div>

                    <!-- Download button (outside card for clean export) -->
                    <div class="text-center p-3">
                        <button class="btn btn-download-qr" onclick="downloadQr()">
                            <i class="bi bi-download me-2"></i>Download QR Card
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- QRCode.js Local -->
    <script src="{{ asset('js/qrcode.min.js') }}"></script>
    <!-- html2canvas for card download -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

    <script>
        let currentDeviceName = '';
        const logoImg = new Image();
        logoImg.crossOrigin = 'anonymous';
        logoImg.src = '{{ asset("images/logo.png") }}';

        function showQrModal(token, deviceName) {
            currentDeviceName = deviceName;
            document.getElementById('qrisDeviceName').textContent = deviceName;
            document.getElementById('qrisToken').textContent = token;

            // Wait a tiny bit for modal to be visible then generate
            setTimeout(() => generateQrisQr(token), 100);

            const modal = new bootstrap.Modal(document.getElementById('qrModal'));
            modal.show();
        }

        function generateQrisQr(text) {
            const qrContainer = document.getElementById('qrCanvas');
            const qrError = document.getElementById('qrError');

            // Reset previous QR and error message
            qrContainer.innerHTML = '';
            qrError.style.display = 'none';
            qrError.textContent = '';

            if (typeof QRCode === 'undefined') {
                qrError.textContent = 'QR library tidak dimuat (QRCode.js tidak ditemukan). Silakan refresh halaman.';
                qrError.style.display = 'block';
                return;
            }

            try {
                // Generate QR code directly into container using QRCode.js
                new QRCode(qrContainer, {
                    text: text,
                    width: 260,
                    height: 260,
                    colorDark: '#0c4a6e',
                    colorLight: '#ffffff',
                    correctLevel: QRCode.CorrectLevel.H,
                });
            } catch (err) {
                console.error('QR generation error:', err);
                qrError.textContent = 'Gagal membuat QR code. Cek konsol untuk detail.';
                qrError.style.display = 'block';
                return;
            }

            // After QR is rendered, overlay the logo in center
            setTimeout(function() {
                const qrImg = qrContainer.querySelector('img');
                const qrCanvasEl = qrContainer.querySelector('canvas');

                if (!qrImg && !qrCanvasEl) {
                    qrError.textContent = 'QR tidak muncul: Elemen QR tidak dibuat.';
                    qrError.style.display = 'block';
                    return;
                }

                if (qrCanvasEl) {
                    // Style the canvas
                    qrCanvasEl.style.borderRadius = '12px';
                    qrCanvasEl.style.display = 'block';

                    // Draw logo overlay on top of the canvas
                    const ctx = qrCanvasEl.getContext('2d');
                    const size = qrCanvasEl.width;

                    if (logoImg.complete && logoImg.naturalWidth > 0) {
                        drawLogoOnCanvas(ctx, size);
                    } else {
                        logoImg.onload = function() {
                            drawLogoOnCanvas(ctx, size);
                        };
                    }
                }

                // Hide the img fallback if canvas exists
                if (qrImg && qrCanvasEl) {
                    qrImg.style.display = 'none';
                }
            }, 500);
        }

        function drawLogoOnCanvas(ctx, size) {
            const logoSize = size * 0.2;
            const cx = size / 2;
            const cy = size / 2;
            const padding = 8;
            const totalSize = logoSize + padding * 2;

            // White circle background
            ctx.fillStyle = '#ffffff';
            ctx.beginPath();
            ctx.arc(cx, cy, totalSize / 2 + 4, 0, Math.PI * 2);
            ctx.fill();

            // Blue border ring
            ctx.strokeStyle = '#0ea5e9';
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.arc(cx, cy, totalSize / 2 + 4, 0, Math.PI * 2);
            ctx.stroke();

            // Draw the logo image
            ctx.drawImage(logoImg, cx - logoSize / 2, cy - logoSize / 2, logoSize, logoSize);
        }

        function downloadQr() {
            const card = document.getElementById('qrisCard');
            html2canvas(card, {
                scale: 3,
                backgroundColor: '#ffffff',
                useCORS: true,
            }).then(function(canvas) {
                const link = document.createElement('a');
                const safeName = currentDeviceName.replace(/[^a-zA-Z0-9]/g, '_');
                link.download = 'QR_Swaratani_' + safeName + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }

        function copyToClipboard(e, text, el) {
            e.preventDefault();
            navigator.clipboard.writeText(text).then(() => {
                const icon = el.querySelector('i');
                const originalClass = icon.className;
                const originalText = el.innerHTML;
                
                // Ubah icon jadi centang hijau
                icon.className = 'bi bi-check2 text-success me-2';
                const textNode = el.lastChild;
                if(textNode && textNode.nodeType === 3) textNode.nodeValue = ' Copied!';
                
                // Kembalikan ke asal setelah 2 detik
                setTimeout(() => {
                    el.innerHTML = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy data: ', err);
                alert('Gagal menyalin data.');
            });
        }
    </script>

</body>

</html>