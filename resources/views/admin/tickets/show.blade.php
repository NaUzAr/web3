<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selesaikan Tiket - Admin</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.theme')

    <style>
        .page-title { color: #fff; font-weight: 700; }
        .ticket-detail-box {
            background: rgba(255,255,255,0.8);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--glass-border);
        }
        .form-control, .form-select {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="bg-animation"></div>

    @include('partials.navbar')

    <div class="container py-5">
        <div class="row">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="page-title mb-0">#TKT-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}: {{ $ticket->subject }}</h2>
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-glass btn-sm d-inline-flex align-items-center gap-2">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="ticket-detail-box">
                    <div class="row mb-3">
                        <div class="col-6">
                            <span class="text-secondary small">Pelapor:</span><br>
                            <strong>{{ $ticket->user->name ?? 'Unknown' }}</strong>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-secondary small">Tanggal:</span><br>
                            <strong>{{ $ticket->created_at->format('d M Y, H:i') }}</strong>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <span class="text-secondary small">Kategori:</span> <span class="badge bg-dark">{{ $ticket->category }}</span>
                    </div>

                    <div class="bg-light p-3 rounded mb-3" style="border: 1px dashed #ccc; font-family: monospace;">
                        {!! nl2br(e($ticket->description)) !!}
                    </div>

                    @if($ticket->attachment_path)
                    <div>
                        <span class="text-secondary small mb-1 d-block">Lampiran:</span>
                        <a href="{{ Storage::url($ticket->attachment_path) }}" target="_blank">
                            <img src="{{ Storage::url($ticket->attachment_path) }}" alt="attachment" style="max-width: 300px; border-radius: 8px;">
                        </a>
                    </div>
                    @endif
                </div>

                @if($ticket->replies->count() > 0)
                    <div class="mb-4">
                        <h5 class="text-white mb-3">Histori Pesan</h5>
                        @foreach($ticket->replies as $reply)
                            @if($reply->user_id === $ticket->user_id)
                                {{-- User Reply --}}
                                <div class="ticket-detail-box mb-3" style="border-left: 4px solid var(--primary);">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong><i class="bi bi-person-circle me-1"></i>{{ $reply->user->name }}</strong>
                                        <small class="text-secondary">{{ $reply->created_at->format('d M Y, H:i') }}</small>
                                    </div>
                                    <div style="white-space: pre-wrap;">{{ $reply->message }}</div>
                                </div>
                            @else
                                {{-- Admin Reply --}}
                                <div class="ticket-detail-box mb-3" style="background: rgba(14, 95, 138, 0.1); border-right: 4px solid #f59e0b; margin-left: 50px;">
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-secondary">{{ $reply->created_at->format('d M Y, H:i') }}</small>
                                        <strong><i class="bi bi-headset me-1 text-primary"></i>Admin</strong>
                                    </div>
                                    <div style="white-space: pre-wrap;" class="text-end">{{ $reply->message }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif


                <div class="glass-card" style="padding: 1.5rem;">
                    <h5 class="mb-3"><i class="bi bi-pencil-square me-2"></i>Tanggapi Laporan</h5>
                    <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ubah Status</label>
                            <select name="status" class="form-select">
                                <option value="open" {{ $ticket->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $ticket->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="closed" {{ $ticket->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pesan Balasan Admin (Opsional)</label>
                            <textarea name="admin_reply" class="form-control" rows="4" placeholder="Jelaskan solusi atau sampaikan pesan ke pelapor..."></textarea>
                            <div class="form-text">Pesan ini akan bisa dibaca tertambah ke histori obrolan laporan.</div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" style="border-radius: 10px;">Update Laporan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
