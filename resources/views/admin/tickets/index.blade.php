<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Tiket Bantuan - {{ env('APP_NAME', 'Swaratani') }} Admin</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.theme')

    <style>
        .page-title { color: #fff; font-weight: 700; }
        .table-dark-custom { background: var(--navbar-bg) !important; }
        .table-dark-custom th { color: var(--primary-light); font-weight: 600; border-bottom: 1px solid var(--glass-border) !important; padding: 0.75rem 1rem; }
        .table tbody td { color: #1f2937; border-bottom: 1px solid var(--glass-border); padding: 0.75rem 1rem; vertical-align: middle; }
        .table tbody tr:hover { background: rgba(255, 255, 255, 0.05); }
        .badge-status { font-weight: 600; padding: 0.3rem 0.6rem; border-radius: 8px; font-size: 0.75rem; }
        .status-open { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
        .status-in_progress { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
        .status-resolved { background: rgba(16, 185, 129, 0.15); color: #10b981; }
        .status-closed { background: rgba(100, 116, 139, 0.15); color: #64748b; }
    </style>
</head>
<body>
    <div class="bg-animation"></div>

    @include('partials.navbar')

    <div class="container py-5">
        <h2 class="page-title mb-4"><i class="bi bi-inboxes me-2"></i>Tiket Masuk</h2>

        <div class="glass-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-dark-custom">
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Pelapor</th>
                            <th>Subjek</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>#TKT-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $ticket->user->name ?? 'Guest' }}</td>
                                <td>{{ $ticket->subject }}</td>
                                <td><span class="badge bg-secondary">{{ $ticket->category }}</span></td>
                                <td><span class="badge-status status-{{ $ticket->status }}">{{ strtoupper(str_replace('_', ' ', $ticket->status)) }}</span></td>
                                <td>
                                    <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn btn-sm btn-primary" style="border-radius:8px;">Buka</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">Belum ada tiket masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tickets->hasPages())
                <div class="d-flex justify-content-center mt-3 pb-3">
                    {{ $tickets->links() }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
