<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Tiket - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.theme')

    <style>
        .page-title {
            color: var(--text-main);
            font-weight: 800;
            font-size: 1.75rem;
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-weight: 600;
        }
        .status-open { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .status-in_progress { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .status-resolved { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .status-closed { background: rgba(100, 116, 139, 0.1); color: #64748b; }

        .chat-bubble {
            background: rgba(255,255,255,0.7);
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--glass-border);
        }
        .chat-bubble.admin-reply {
            background: rgba(14, 95, 138, 0.05);
            border-color: rgba(14, 95, 138, 0.2);
            border-left: 4px solid var(--primary);
        }
    </style>
</head>
<body>
    <div class="bg-animation"></div>

    @include('partials.navbar')

    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h2 class="page-title mb-1">#TKT-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</h2>
                        <span class="text-secondary">{{ $ticket->subject }} &bull; {{ $ticket->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="status-badge status-{{ $ticket->status }}">
                            {{ strtoupper(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                        <a href="{{ route('tickets.index') }}" class="btn btn-glass btn-sm d-inline-flex align-items-center gap-2">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="chat-bubble">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-person-circle fs-3 me-2 text-secondary"></i>
                        <div>
                            <strong>Anda</strong><br>
                            <small class="text-secondary">Kategori: {{ $ticket->category }}</small>
                        </div>
                    </div>
                    <p class="mb-0" style="white-space: pre-wrap;">{{ $ticket->description }}</p>
                    
                    @if($ticket->attachment_path)
                    <div class="mt-3">
                        <span class="text-secondary small fw-bold">Lampiran:</span><br>
                        <a href="{{ Storage::url($ticket->attachment_path) }}" target="_blank">
                            <img src="{{ Storage::url($ticket->attachment_path) }}" alt="Lampiran" class="img-fluid rounded border mt-1" style="max-height: 200px;">
                        </a>
                    </div>
                    @endif
                </div>

                @foreach($ticket->replies as $reply)
                    @if($reply->user_id === $ticket->user_id)
                        {{-- User Reply --}}
                        <div class="chat-bubble" style="border-right: 4px solid var(--primary); margin-left: 30px;">
                            <div class="d-flex align-items-center mb-3 justify-content-end">
                                <div class="text-end me-2">
                                    <strong>Anda</strong><br>
                                    <small class="text-secondary">{{ $reply->created_at->format('d M Y, H:i') }}</small>
                                </div>
                                <i class="bi bi-person-circle fs-3 text-secondary"></i>
                            </div>
                            <p class="mb-0 text-end" style="white-space: pre-wrap;">{{ $reply->message }}</p>
                            @if($reply->attachment_path)
                            <div class="mt-3 text-end">
                                <a href="{{ Storage::url($reply->attachment_path) }}" target="_blank">
                                    <img src="{{ Storage::url($reply->attachment_path) }}" alt="Lampiran" class="img-fluid rounded border mt-1" style="max-height: 200px;">
                                </a>
                            </div>
                            @endif
                        </div>
                    @else
                        {{-- Admin Reply --}}
                        <div class="chat-bubble admin-reply" style="margin-right: 30px;">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-headset fs-3 me-2 text-primary"></i>
                                <div>
                                    <strong>Admin Swaratani</strong><br>
                                    <small class="text-secondary">{{ $reply->created_at->format('d M Y, H:i') }}</small>
                                </div>
                            </div>
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $reply->message }}</p>
                        </div>
                    @endif
                @endforeach
                
                @if(!$hasAdminReplied && $ticket->status === 'open')
                    <div class="text-center text-secondary py-3">
                        <div class="spinner-grow spinner-grow-sm text-primary mb-2" role="status"></div>
                        <p class="small mb-0">Menunggu tanggapan dari tim Admin...</p>
                    </div>
                @elseif($ticket->status === 'closed' || $ticket->status === 'resolved')
                    <div class="alert alert-secondary text-center rounded-3 p-3">
                        <i class="bi bi-info-circle me-2"></i>Tiket ini telah ditandai sebagai <strong>{{ strtoupper($ticket->status) }}</strong>. Anda tidak dapat mengirim pesan lagi.
                    </div>
                @else
                    {{-- Reply Form --}}
                    <div class="mt-4 bg-white p-4 rounded-3 shadow-sm border">
                        <h6 class="mb-3 fw-bold"><i class="bi bi-reply-fill me-2"></i>Balas Pesan</h6>
                        <form action="{{ route('tickets.reply', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <textarea name="message" class="form-control" rows="3" placeholder="Ketik balasan Anda di sini..." required></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <label class="form-label mb-0 small text-secondary">Lampiran (Opsional, JPG/PNG max 2MB)</label>
                                    <input type="file" name="attachment" class="form-control form-control-sm" accept="image/jpeg,image/png,image/jpg">
                                </div>
                                <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-send me-1"></i> Kirim</button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('partials.pwa-scripts')
</body>
</html>
