<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kirim Pengumuman - Admin {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    @include('partials.theme')
    <style>
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
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <!-- Navbar -->
    <!-- Navbar -->
    @include('partials.navbar')

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h2 class="page-title mb-0">
                <i class="bi bi-megaphone-fill me-2"></i>Kirim Pengumuman
            </h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.devices.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center mb-4">
                <i class="bi bi-check-circle-fill me-2 flex-shrink-0"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger d-flex align-items-center mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2 flex-shrink-0"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        <!-- Form Broadcast -->
        <div class="glass-card mb-5">
            <div class="card-body p-4">
                <h5 class="mb-4" style="color: #1f2937;"><i class="bi bi-send me-2 text-primary"></i>Kirim Notifikasi ke Semua Pengguna</h5>
                <form action="{{ route('admin.announcements.send') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold" style="color: #374151;">Judul Pengumuman</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Contoh: Info Update Fitur Terbaru" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="message" class="form-label fw-bold" style="color: #374151;">Isi Pesan</label>
                        <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="4" placeholder="Tuliskan isi pesan notifikasi di sini..." required>{{ old('message') }}</textarea>
                        @error('message')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-gradient w-100" onclick="return confirm('Pesan ini akan dikirim ke seluruh device yang terdaftar. Lanjutkan?');">
                        <i class="bi bi-send-fill me-2"></i> Broadcast Sekarang
                    </button>
                </form>
            </div>
        </div>

        <!-- History -->
        <h4 class="page-title mb-4"><i class="bi bi-clock-history me-2"></i>Riwayat Pengumuman</h4>
        <div class="glass-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-dark-custom">
                        <tr>
                            <th>No</th>
                            <th>Judul</th>
                            <th>Pesan</th>
                            <th class="text-center">Status Kirim</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($announcements as $announcement)
                            <tr>
                                <td class="fw-semibold">{{ $announcements->firstItem() + $loop->index }}</td>
                                <td class="fw-bold">{{ $announcement->title }}</td>
                                <td>{{ Str::limit($announcement->message, 50) }}</td>
                                <td class="text-center">
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>{{ $announcement->success_count }}</span>
                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>{{ $announcement->failure_count }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($announcement->sent_at)->format('d M Y, H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted"><i class="bi bi-inbox fs-4 d-block mb-2"></i>Belum ada riwayat pengumuman</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($announcements->hasPages())
                <div class="p-3 border-top pb-0">
                    {{ $announcements->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
