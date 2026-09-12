<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Device - {{ env('APP_NAME', 'Swaratani') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">


    @include('partials.theme')

    <style>
        body {
            min-height: 100vh;
            background: var(--nature-gradient, #f8fafc);
        }

        .add-card {
            width: 100%;
            max-width: 480px;
            padding: 2.5rem;
            border-radius: 24px;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        }

        .card-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-gradient);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }

        .card-title {
            color: #1f2937;
            font-weight: 700;
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .card-subtitle {
            color: #64748b;
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-label {
            color: var(--primary-dark);
            font-weight: 600;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid var(--glass-border);
            color: #1f2937;
            border-radius: 12px;
            padding: 0.85rem 1rem;
            transition: all 0.3s ease;
            font-family: monospace;
            font-size: 1.1rem;
            letter-spacing: 2px;
            text-align: center;
        }

        .form-control:focus {
            background: #fff;
            border-color: var(--primary-color);
            color: #1f2937;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
        }

        .form-control::placeholder {
            color: #94a3b8;
            letter-spacing: 1px;
        }

        .form-control-name {
            font-family: 'Inter', sans-serif;
            letter-spacing: normal;
            text-align: left;
        }

        .alert-danger-custom {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #dc2626;
            border-radius: 12px;
        }

        .alert-info-custom {
            background: rgba(14, 165, 233, 0.1);
            border: 1px solid rgba(14, 165, 233, 0.3);
            color: #0284c7;
            border-radius: 12px;
        }

        .form-text-light {
            color: #64748b;
        }

        .btn-glass {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: #475569;
            padding: 0.85rem;
            border-radius: 12px;
            font-weight: 600;
        }

        .btn-glass:hover {
            background: rgba(0, 0, 0, 0.05);
            color: #1f2937;
        }

        /* ========= QR Scanner ========= */
        .btn-outline-scan {
            background: transparent;
            border: 2px dashed var(--primary-color, #0ea5e9);
            color: var(--primary-color, #0ea5e9);
            border-radius: 12px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-scan:hover {
            background: rgba(14, 165, 233, 0.1);
            border-style: solid;
            color: var(--primary-dark, #0369a1);
        }

        .btn-outline-scan.scanning {
            background: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
            border-style: solid;
            color: #ef4444;
        }

        .btn-outline-scan-img {
            background: transparent;
            border: 2px dashed #8b5cf6;
            color: #8b5cf6;
            border-radius: 12px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-scan-img:hover {
            background: rgba(139, 92, 246, 0.1);
            border-style: solid;
            color: #7c3aed;
        }

        #qrReader {
            border-radius: 12px;
            overflow: hidden;
        }

        #qrReader video {
            border-radius: 12px;
        }

        /* ========= Mobile Responsive ========= */
        @media (max-width: 576px) {
            body {
                padding: 1rem;
                align-items: flex-start;
                padding-top: 2rem;
            }

            .add-card {
                padding: 1.75rem 1.5rem;
                border-radius: 20px;
            }

            .card-icon {
                width: 60px;
                height: 60px;
                font-size: 2rem;
                border-radius: 16px;
                margin-bottom: 1rem;
            }

            .card-title {
                font-size: 1.15rem;
            }

            .card-subtitle {
                font-size: 0.85rem;
                margin-bottom: 1.5rem;
            }

            .form-control {
                font-size: 16px;
                min-height: 48px;
            }
        }

        @media (max-width: 400px) {
            body {
                padding: 0.75rem;
                padding-top: 1.5rem;
            }

            .add-card {
                padding: 1.5rem 1.25rem;
            }
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    @include('partials.navbar')

    <div class="container py-4 my-2 d-flex justify-content-center">
        <div class="add-card">
        <div class="card-icon">
            <i class="bi bi-key-fill text-white"></i>
        </div>
        <h4 class="card-title">Tambah Device</h4>
        <p class="card-subtitle">Masukkan token device untuk mulai monitoring</p>

        @if ($errors->any())
            <div class="alert alert-danger-custom mb-4">
                <ul class="mb-0 small">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('monitoring.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label"><i class="bi bi-key me-1"></i>Token Device</label>
                <input type="text" class="form-control" name="token" id="tokenInput" value="{{ old('token') }}"
                    placeholder="XXXXXXXXXXXXXXXX" maxlength="16" required autofocus>
                <div class="form-text form-text-light">
                    Token terdiri dari 16 karakter. Dapatkan dari admin.
                </div>
            </div>

            <!-- QR Scanner Section -->
            <div class="mb-3">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-scan flex-grow-1" id="btnScanQR" onclick="toggleScanner()">
                        <i class="bi bi-camera me-2"></i>Scan Kamera
                    </button>
                    <button type="button" class="btn btn-outline-scan-img flex-grow-1" onclick="document.getElementById('qrImageInput').click()">
                        <i class="bi bi-image me-2"></i>Scan dari Gambar
                    </button>
                    <input type="file" id="qrImageInput" accept="image/*" style="display:none" onchange="scanFromImage(this)">
                </div>
                <div id="qrScannerContainer" style="display: none;">
                    <div id="qrReader" style="width: 100%; margin-top: 0.75rem; border-radius: 12px; overflow: hidden;"></div>
                </div>
                <div id="qrScanStatus" class="text-center mt-2" style="display: none;">
                    <small class="text-success"><i class="bi bi-check-circle me-1"></i>Token berhasil di-scan!</small>
                </div>
                <div id="qrScanError" class="text-center mt-2" style="display: none;">
                    <small class="text-danger"><i class="bi bi-x-circle me-1"></i>QR Code tidak ditemukan di gambar.</small>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label"><i class="bi bi-tag me-1"></i>Nama Custom (Opsional)</label>
                <input type="text" class="form-control form-control-name" name="custom_name"
                    value="{{ old('custom_name') }}" placeholder="Contoh: Sensor Kebun Saya">
                <div class="form-text form-text-light">
                    Beri nama untuk memudahkan identifikasi device.
                </div>
            </div>

            <div class="alert alert-info-custom mb-4">
                <small><i class="bi bi-info-circle me-1"></i>
                    Device akan tersimpan di akun Anda dan bisa dilihat kapan saja sampai Anda menghapusnya.
                </small>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-gradient">
                    <i class="bi bi-plus-circle me-2"></i>Tambahkan Device
                </button>
                <a href="{{ route('monitoring.index') }}" class="btn btn-glass text-center">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
        </div>
    </div>

    <!-- html5-qrcode CDN -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        let html5QrCode = null;
        let scannerRunning = false;

        function toggleScanner() {
            if (scannerRunning) {
                stopScanner();
            } else {
                startScanner();
            }
        }

        function startScanner() {
            const container = document.getElementById('qrScannerContainer');
            const btn = document.getElementById('btnScanQR');
            const statusEl = document.getElementById('qrScanStatus');
            container.style.display = 'block';
            statusEl.style.display = 'none';
            btn.innerHTML = '<i class="bi bi-x-circle me-2"></i>Tutup Scanner';
            btn.classList.add('scanning');

            html5QrCode = new Html5Qrcode("qrReader");

            const config = {
                fps: 10,
                qrbox: function(viewfinderWidth, viewfinderHeight) {
                    let minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                    let qrboxSize = Math.floor(minEdge * 0.75);
                    return { width: qrboxSize, height: qrboxSize };
                },
                aspectRatio: 1.0,
            };

            // Prefer rear camera on mobile devices
            html5QrCode.start(
                { facingMode: "environment" },
                config,
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                // Fallback: try any available camera
                html5QrCode.start(
                    { facingMode: "user" },
                    config,
                    onScanSuccess,
                    onScanFailure
                ).catch(err2 => {
                    console.error("Camera error:", err2);
                    alert("Tidak dapat mengakses kamera. Pastikan izin kamera diaktifkan dan halaman diakses via HTTPS.");
                    stopScanner();
                });
            });

            scannerRunning = true;
        }

        function stopScanner() {
            const container = document.getElementById('qrScannerContainer');
            const btn = document.getElementById('btnScanQR');
            btn.innerHTML = '<i class="bi bi-qr-code-scan me-2"></i>Scan QR Code';
            btn.classList.remove('scanning');

            if (html5QrCode && scannerRunning) {
                html5QrCode.stop().then(() => {
                    html5QrCode.clear();
                    container.style.display = 'none';
                }).catch(err => {
                    console.error("Stop error:", err);
                    container.style.display = 'none';
                });
            } else {
                container.style.display = 'none';
            }
            scannerRunning = false;
        }

        function onScanSuccess(decodedText, decodedResult) {
            // Extract token: take up to 16 characters, remove whitespace
            let token = decodedText.trim().replace(/\s/g, '');
            if (token.length > 16) {
                token = token.substring(0, 16);
            }

            // Fill the token field
            const tokenInput = document.getElementById('tokenInput');
            tokenInput.value = token;
            tokenInput.focus();

            // Show success status
            const statusEl = document.getElementById('qrScanStatus');
            statusEl.style.display = 'block';

            // Add success animation to token field
            tokenInput.style.borderColor = '#22c55e';
            tokenInput.style.boxShadow = '0 0 0 3px rgba(34, 197, 94, 0.3)';
            setTimeout(() => {
                tokenInput.style.borderColor = '';
                tokenInput.style.boxShadow = '';
            }, 2000);

            // Stop scanner after successful scan
            stopScanner();
            // Keep the success status visible
            document.getElementById('qrScannerContainer').style.display = 'block';
        }

        function onScanFailure(error) {
            // Ignore scan failures (continuous scanning)
        }

        function scanFromImage(input) {
            if (!input.files || !input.files[0]) return;

            const file = input.files[0];
            const statusEl = document.getElementById('qrScanStatus');
            const errorEl = document.getElementById('qrScanError');
            statusEl.style.display = 'none';
            errorEl.style.display = 'none';

            // Stop camera scanner if running
            if (scannerRunning) stopScanner();

            const scanner = new Html5Qrcode('qrReader');
            scanner.scanFileV2(file, true)
                .then(result => {
                    onScanSuccess(result.decodedText, result);
                })
                .catch(err => {
                    console.error('Image scan error:', err);
                    errorEl.style.display = 'block';
                    setTimeout(() => { errorEl.style.display = 'none'; }, 3000);
                });

            // Reset file input so same file can be re-selected
            input.value = '';
        }
    </script>

</body>

</html>