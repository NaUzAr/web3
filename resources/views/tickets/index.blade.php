<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Masalah - {{ env('APP_NAME', 'Swaratani') }}</title>
    @include('partials.pwa-head')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.theme')

    <style>
        .page-title {
            color: var(--text-main);
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .ticket-card {
            background: #ffffff;
            border: 1px solid rgba(14, 165, 233, 0.15);
            border-radius: 16px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .ticket-card:hover {
            border-color: rgba(14, 165, 233, 0.4);
            transform: translateY(-2px);
            color: inherit;
            box-shadow: 0 12px 30px rgba(14, 165, 233, 0.1);
        }

        .btn-back {
            color: #0ea5e9;
            border: 1px solid rgba(14, 165, 233, 0.2);
            border-radius: 50px;
            padding: 0.5rem 1.2rem;
            font-weight: 600;
            text-decoration: none;
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
        }
        .btn-back:hover {
            background: #f0f9ff;
            color: #0284c7;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15);
        }

        .status-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.65rem;
            border-radius: 20px;
            font-weight: 600;
        }
        .status-open { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
        .status-in_progress { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .status-resolved { background: rgba(16, 185, 129, 0.1); color: #10b981; }
        .status-closed { background: rgba(100, 116, 139, 0.1); color: #64748b; }

        .ticket-subject {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.25rem;
            color: var(--text-main);
        }

        .ticket-meta {
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .btn-create {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            color: #fff;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
            transition: all 0.3s ease;
        }

        .btn-create:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(14, 165, 233, 0.3);
        }
    </style>
</head>
<body>
    <div class="bg-animation"></div>

    @include('partials.navbar')

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="page-title mb-0">Tiket Bantuan</h2>
            <a href="{{ route('tickets.create') }}" class="btn btn-create">
                <i class="bi bi-pencil-square me-1"></i> Buat Tiket
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($tickets->count() > 0)
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    @foreach($tickets as $ticket)
                        <a href="{{ route('tickets.show', $ticket->id) }}" class="ticket-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="status-badge status-{{ $ticket->status }}">
                                    {{ strtoupper(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                                <span class="ticket-meta">{{ $ticket->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="ticket-subject">#TKT-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }} {{ $ticket->subject }}</div>
                            <div class="ticket-meta"><i class="bi bi-tag"></i> {{ $ticket->category }}</div>
                        </a>
                    @endforeach
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $tickets->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5 glass-card" style="border-radius: 20px;">
                <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                <h5 class="mt-3">Aman Terkendali</h5>
                <p class="text-secondary">Anda belum melaporkan masalah apapun.</p>
            </div>
        @endif
    </div>

    @include('partials.pwa-scripts')
</body>
</html>
