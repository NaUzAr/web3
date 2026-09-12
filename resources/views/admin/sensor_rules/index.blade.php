<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Global Sensor Rules - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.theme')
    <style>
        .page-title { color: #0f172a; font-weight: 800; }
        .page-title i { color: #0e5f8a; }
        .table-dark-custom { background: #f1f5f9 !important; }
        .table-dark-custom th { color: #334155 !important; font-weight: 700; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.4px; border-bottom: 2px solid #e2e8f0 !important; padding: 0.85rem 1rem; }
        .table tbody td { color: #1e293b; border-bottom: 1px solid #f1f5f9; padding: 0.85rem 1rem; vertical-align: middle; background: #ffffff; }
        .table tbody tr:hover td { background: #f8fafc; }
        .glass-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); overflow: hidden; }
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
                    <i class="bi bi-bell-fill me-2 text-warning"></i>Aturan Peringatan Dini Sensor (FCM)
                </h2>
                <p class="text-muted mb-0 small">Konfigurasi ambang batas sensor untuk otomatis mengirim notifikasi ke HP pengguna.</p>
            </div>
            <div class="d-flex gap-2">
                <form action="{{ route('admin.sensor-rules.test') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning shadow-sm" style="border-radius: 10px; font-weight: 600;">
                        <i class="bi bi-phone-vibrate me-1"></i> Uji Coba Ping ke HP Saya
                    </button>
                </form>
                <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal" style="border-radius: 10px; font-weight: 600;">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Aturan
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(isset($errors) && $errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="glass-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-dark-custom">
                        <tr>
                            <th>Sensor</th>
                            <th>Batas Min</th>
                            <th>Batas Max</th>
                            <th>Status Limit</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rules as $rule)
                            <tr>
                                <td><span class="fw-bold">{{ strtoupper(str_replace('ni_', '', $rule->sensor_key)) }}</span></td>
                                <td>{{ $rule->min_value ?? '-' }}</td>
                                <td>{{ $rule->max_value ?? '-' }}</td>
                                <td>
                                    @if($rule->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Mati</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.sensor-rules.destroy', $rule->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus aturan ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada pengaturan batas peringatan sensor dini (FCM).</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Add -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.sensor-rules.store') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Batas Sensor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Jenis Sensor</label>
                            <select name="sensor_key" class="form-select" required>
                                @foreach($availableSensors as $key => $sData)
                                    <option value="{{ $key }}">{{ $sData['label'] }} ({{ $sData['unit'] }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Batas Minimum (Abaikan jika tak butuh)</label>
                            <input type="number" step="0.01" name="min_value" class="form-control" placeholder="Contoh: 10">
                        </div>
                        <div class="mb-3">
                            <label>Batas Maksimum (Abaikan jika tak butuh)</label>
                            <input type="number" step="0.01" name="max_value" class="form-control" placeholder="Contoh: 80">
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                            <label class="form-check-label">Jadikan Aktif Peringatan FCM</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
