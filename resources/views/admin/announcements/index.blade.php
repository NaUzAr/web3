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
        .page-title { color: #0f172a; font-weight: 800; }
        .page-title i { color: #0e5f8a; }
        .table-dark-custom { background: #f1f5f9 !important; }
        .table-dark-custom th { color: #334155 !important; font-weight: 700; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.4px; border-bottom: 2px solid #e2e8f0 !important; padding: 0.85rem 1rem; }
        .table tbody td { color: #1e293b; border-bottom: 1px solid #f1f5f9; padding: 0.85rem 1rem; vertical-align: middle; background: #ffffff; }
        .table tbody tr:hover td { background: #f8fafc; }
        .glass-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    @include('partials.navbar')

    <div class="container py-4">
        @include('admin.partials.nav')

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="page-title mb-1">
                    <i class="bi bi-megaphone-fill me-2 text-danger"></i>Kirim Pengumuman Broadcast
                </h2>
                <p class="text-muted mb-0 small">Kirim pengumuman massal atau notifikasi peringatan ke semua pengguna sistem.</p>
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
