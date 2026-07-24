<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ ($isAdminView ?? false) ? $device->name : $userDevice->custom_name }} - Monitoring</title>
    @include('partials.pwa-head')
    @include('partials.theme')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Page Specific Overrides */


        * {
            font-family: 'Inter', sans-serif;
        }

        .navbar-glass {
            background: var(--navbar-bg) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary) !important;
        }

        .nav-link {
            color: var(--text-secondary) !important;
        }

        .nav-link:hover {
            color: var(--primary) !important;
        }

        .page-header {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
        }

        .device-title {
            color: var(--text-main);
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
        }

        .device-type-badge {
            background: var(--primary-gradient);
            color: #fff;
            padding: 0.35rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .sensor-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 1rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .sensor-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }

        .sensor-card:hover {
            transform: translateY(-5px) scale(1.02);
            border-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .sensor-card:hover::before {
            opacity: 1;
        }

        .card-header-flex {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 1rem;
        }

        .sensor-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            box-shadow: inset 0 2px 4px rgba(255,255,255,0.3), 0 4px 8px rgba(0,0,0,0.1);
            flex-shrink: 0;
            transition: transform 0.4s ease;
        }

        .sensor-card:hover .sensor-icon {
            transform: scale(1.1) rotate(-10deg);
        }

        .sensor-label {
            color: var(--text-secondary);
            font-size: 0.75rem;
            font-weight: 700;
            line-height: 1.2;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .sensor-value-container {
            display: flex;
            align-items: baseline;
            justify-content: center;
            gap: 4px;
            margin-top: auto;
            margin-bottom: 0.5rem;
        }

        .sensor-value {
            color: var(--text-main);
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.5px;
        }

        .sensor-unit {
            color: var(--text-muted);
            font-size: 1rem;
            font-weight: 700;
        }

        .sensor-panel, .output-panel {
            background: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.5);
            border-radius: 24px;
            padding: 1.5rem;
            margin-top: 1.5rem;
            box-shadow: inset 0 2px 15px rgba(255,255,255,0.5);
        }

        .output-card, .output-card-special {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 1rem;
            text-align: center;
            height: 100%;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .output-card::before, .output-card-special::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 100%);
            opacity: 0;
            transition: opacity 0.4s ease;
            pointer-events: none;
        }
        
        .output-card:hover, .output-card-special:hover {
            transform: translateY(-5px) scale(1.02);
            border-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .output-card:hover::before, .output-card-special:hover::before {
            opacity: 1;
        }

        .output-icon, .output-icon-special {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            font-size: 1.2rem;
            color: white;
            box-shadow: inset 0 2px 4px rgba(255,255,255,0.3), 0 4px 8px rgba(0,0,0,0.1);
            flex-shrink: 0;
            transition: transform 0.4s ease;
        }

        .output-card:hover .output-icon, .output-card-special:hover .output-icon-special {
            transform: scale(1.1) rotate(-10deg);
        }

        .output-label {
            color: var(--text-secondary);
            font-size: 0.75rem;
            font-weight: 700;
            line-height: 1.2;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .output-status {
            font-size: 0.9rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 0.75rem 0;
        }
        .output-status.on { color: #10B981; }
        .output-status.off { color: #EF4444; }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 1.5rem;
            margin-top: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }

        .card-title {
            color: var(--text-main);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .last-update {
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        /* Modal Classes (From Schedule - Bottom Sheet) */
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

        /* Handle bar (iOS style) */
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

        .btn-glass {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            padding: 0.6rem 1.25rem;
            border-radius: 50px;
            text-decoration: none;
        }

        /* Segmented Control for Outputs */
        .segmented-control {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 50px;
            padding: 3px;
            display: flex;
            gap: 4px;
            width: 100%;
            margin-top: 0.5rem;
        }
        
        .segmented-btn {
            flex: 1;
            border: none;
            background: transparent;
            border-radius: 50px;
            padding: 0.4rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--text-secondary);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-align: center;
        }

        .segmented-btn:hover {
            background: rgba(0,0,0,0.05);
            color: var(--text-main);
        }

        .segmented-btn.active-on {
            background: #10B981;
            color: white;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }

        .segmented-btn.active-off {
            background: #EF4444;
            color: white;
            box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);
        }

        .segmented-btn.active-on:hover, .segmented-btn.active-off:hover {
            transform: translateY(-2px);
            color: white;
        }

        .date-pill {
            background: rgba(255, 255, 255, 0.7);
            border: 1px solid var(--glass-border);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        /* Custom Toast Notification */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .custom-toast {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-left: 4px solid #10B981;
            border-radius: 12px;
            padding: 1rem 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(120%);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .custom-toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .custom-toast.error {
            border-left-color: #EF4444;
        }

        .custom-toast.error i {
            color: #EF4444;
        }

        .custom-toast i {
            color: #10B981;
            font-size: 1.5rem;
        }
        
        .date-pill:hover, .date-pill:focus-within {
            background: #ffffff;
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
        }
        
        .date-pill input {
            color: var(--text-main);
            font-size: 0.9rem;
            font-weight: 500;
            min-width: 140px !important;
            flex-grow: 1;
            width: 100%;
        }
        
        .date-pill input::placeholder {
            color: #9ca3af;
            font-weight: 400;
        }

        .btn-glass:hover {
            background: var(--glass-bg);
            border-color: var(--primary);
            color: var(--primary);
        }

        .no-data {
            color: var(--text-secondary);
            text-align: center;
            padding: 3rem;
        }

        .btn-glass {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            padding: 0.6rem 1.25rem;
            border-radius: 50px;
            font-weight: 500;
            text-decoration: none;
        }

        .btn-glass:hover {
            background: var(--glass-bg);
            border-color: var(--primary);
            color: var(--primary);
        }

        .live-dot {
            width: 10px;
            height: 10px;
            background: var(--primary);
            border-radius: 50%;
            display: inline-block;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        /* Table Styles */
        .table-glass {
            color: var(--text-main);
        }

        .table-glass thead th {
            background: rgba(var(--primary), 0.1);
            color: var(--primary);
            font-weight: 600;
            border-bottom: 1px solid var(--glass-border);
            padding: 1rem;
        }

        .table-glass tbody td {
            border-bottom: 1px solid var(--glass-border);
            padding: 0.75rem 1rem;
            color: var(--text-main);
        }

        .table-glass tbody tr:hover {
            background: rgba(var(--primary), 0.05);
        }

        /* Pagination */
        .pagination-glass .page-link {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
        }

        .pagination-glass .page-link:hover {
            background: var(--primary-light);
            color: #fff;
        }

        .pagination-glass .page-item.active .page-link {
            background: var(--primary-gradient);
            border-color: transparent;
            color: #fff;
        }

        .pagination-glass .page-item.disabled .page-link {
            background: rgba(0, 0, 0, 0.05);
            color: var(--text-secondary);
        }

        /* Tabs */
        .nav-tabs-glass {
            border-bottom: 1px solid var(--glass-border);
        }

        .nav-tabs-glass .nav-link {
            color: var(--text-secondary);
            border: none;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }

        .nav-tabs-glass .nav-link:hover {
            color: var(--primary);
            border: none;
        }

        .nav-tabs-glass .nav-link.active {
            background: transparent;
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
        }



        /* ========= Mobile Responsive ========= */
        @media (max-width: 768px) {
            .container.py-4 {
                padding: 0.75rem 0.5rem !important;
            }

            /* Page Header compact */
            .page-header {
                padding: 1rem 1.25rem;
                border-radius: 14px;
                margin-bottom: 1.25rem;
            }

            .device-title {
                font-size: 1.1rem;
            }

            .device-type-badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.75rem;
            }

            /* Sensor Cards compact */
            .sensor-card {
                border-radius: 14px;
                padding: 1rem;
            }

            .sensor-icon, .output-icon, .output-icon-special {
                width: 32px;
                height: 32px;
                font-size: 1rem;
            }

            .sensor-label, .output-label {
                font-size: 0.7rem;
            }

            .sensor-value {
                font-size: 1.5rem;
            }

            .sensor-unit {
                font-size: 0.8rem;
            }

            /* Glass cards compact */
            .glass-card {
                border-radius: 14px;
                padding: 1rem;
                margin-top: 1.25rem;
            }

            .card-title {
                font-size: 1rem;
                margin-bottom: 0.75rem;
            }

            /* Chart smaller */
            .glass-card canvas {
                max-height: 200px !important;
            }

            /* Output cards compact */
            .output-card-special {
                padding: 0.85rem;
                border-radius: 14px;
            }

            .output-card-special .badge {
                font-size: 0.7rem;
                padding: 0.35rem 0.6rem !important;
            }

            .output-card-special .btn-sm {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
                min-width: 45px !important;
            }

            .output-card {
                padding: 0.85rem;
                border-radius: 14px;
            }

            .output-card .btn-sm {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
                min-width: 45px !important;
            }

            .output-icon,
            .output-icon-special {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                font-size: 1rem;
                margin-bottom: 0.5rem;
            }

            /* Button glass compact */
            .btn-glass {
                font-size: 0.8rem;
                padding: 0.45rem 1rem;
            }

            .btn-action-custom, .btn-gradient {
                padding: 0.45rem 1rem;
                font-size: 0.8rem;
            }

            /* Pump button compact */
            .btn-pump-special {
                padding: 0.4rem 0.75rem;
                font-size: 0.8rem;
            }

            /* Last update */
            .last-update {
                font-size: 0.75rem;
            }

            /* Navbar compact */
            .navbar .navbar-brand img {
                height: 30px !important;
            }

            .navbar .navbar-brand span {
                font-size: 0.9rem;
            }

            /* Row gaps smaller */
            .row.g-3 {
                --bs-gutter-x: 0.5rem;
                --bs-gutter-y: 0.5rem;
            }

            .row.g-4 {
                --bs-gutter-x: 0.5rem;
                --bs-gutter-y: 0.5rem;
            }
        }

        @media (max-width: 400px) {
            .container.py-4 {
                padding: 0.5rem 0.35rem !important;
            }

            .page-header {
                padding: 0.75rem 1rem;
            }

            .sensor-card {
                padding: 0.75rem;
            }

            .sensor-icon {
                width: 30px;
                height: 30px;
                font-size: 0.85rem;
                margin-bottom: 0.35rem;
            }

            .sensor-value {
                font-size: 1.3rem;
            }

            .glass-card {
                padding: 0.75rem;
            }
        }

        .output-card-special:hover {
            border-color: #0ea5e9;
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(14, 165, 233, 0.2);
        }

        .output-icon-special {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
            font-size: 1.2rem;
            color: white;
        }

        .btn-pump-special {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-pump-special:hover {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: white;
            transform: scale(1.05);
        }

        /* Modal styles for pump */
        .modal-content-pump {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(14, 165, 233, 0.3);
            color: var(--text-main);
        }

        .form-control-pump,
        .form-select-pump {
            background-color: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
        }

        .form-control-pump:focus,
        .form-select-pump:focus {
            background-color: var(--glass-bg);
            border-color: #0ea5e9;
            color: var(--text-main);
            box-shadow: 0 0 0 0.25rem rgba(14, 165, 233, 0.25);
        }

        .btn-pump-send {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border: none;
            color: white;
            font-weight: 600;
        }

        .btn-pump-send:hover {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
        }

        .btn-pump-off {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            border: none;
            color: white;
            font-weight: 600;
        }

        .btn-pump-off:hover {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: white;
        }

        /* Action Buttons */
        .btn-action-custom {
            font-weight: 600;
            padding: 0.6rem 1.25rem;
            transition: all 0.3s ease;
            text-decoration: none;
            border-radius: 50px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: inline-flex;
            align-items: center;
        }

        .btn-app {
            color: #0ea5e9;
            border: 1px solid rgba(14, 165, 233, 0.2);
        }
        .btn-app:hover {
            background: #f0f9ff;
            color: #0284c7;
            border-color: #0ea5e9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15);
        }

        .btn-history {
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        .btn-history:hover {
            background: #eef2ff;
            color: #4f46e5;
            border-color: #6366f1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
        }

        .btn-report {
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .btn-report:hover {
            background: #fef2f2;
            color: #dc2626;
            border-color: #ef4444;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
        }

        .btn-automation {
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .btn-automation:hover {
            background: #ecfdf5;
            color: #059669;
            border-color: #10b981;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--primary) 0%, #8b5cf6 100%);
            color: #fff;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            border-radius: 50px;
            border: none;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            display: inline-flex;
            align-items: center;
        }

        .btn-gradient:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }
    </style>
</head>

<body>
    <div class="bg-animation"></div>

    <!-- Navbar -->
    @include('partials.navbar')

    <div class="container pt-3 pb-5">
        <!-- Header -->
        <div class="page-header d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <h1 class="device-title mb-0">
                        <i class="bi {{ $device->type === 'aws' ? 'bi-cloud-sun' : 'bi-flower1' }} me-2"></i>
                        @if($isAdminView ?? false)
                            {{ $device->name }}
                        @else
                            {{ $userDevice->custom_name }}
                        @endif
                    </h1>
                    <div class="d-flex gap-2 align-items-center">
                        @if($isOnline ?? false)
                            <span class="device-type-badge" id="conn-badge" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); font-size: 0.8rem; padding: 0.35rem 0.8rem; height: fit-content;">
                                <i class="bi bi-wifi me-1"></i> ONLINE
                            </span>
                        @else
                            <span class="device-type-badge" id="conn-badge" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); font-size: 0.8rem; padding: 0.35rem 0.8rem; height: fit-content;">
                                <i class="bi bi-wifi-off me-1"></i> OFFLINE
                            </span>
                        @endif
                        
                        @if($isAdminView ?? false)
                            <span class="device-type-badge" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); font-size: 0.8rem; padding: 0.35rem 0.8rem; height: fit-content;">
                                <i class="bi bi-shield-check me-1"></i> Admin View
                            </span>
                        @endif
                        <span class="device-type-badge" style="font-size: 0.8rem; padding: 0.35rem 0.8rem; height: fit-content;">
                            {{ strtoupper($device->type ?? 'DEVICE') }}
                        </span>
                    </div>
                </div>
                <p class="mb-0 mt-2" style="color: var(--text-secondary);" id="last-update-text">
                    <span class="live-dot me-2" id="live-dot" style="display: {{ ($isOnline ?? false) ? 'inline-block' : 'none' }};"></span>
                    @if($latestData)
                        Terakhir update: {{ \Carbon\Carbon::parse($latestData->recorded_at)->diffForHumans() }}
                    @else
                        Menunggu data...
                    @endif
                </p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap justify-content-md-end">
                <a href="{{ isset($isAdminView) && $isAdminView ? route('admin.device.history', $device->id) : route('monitoring.history', $userDevice->id) }}" class="btn btn-history btn-action-custom">
                    <i class="bi bi-clock-history me-sm-1"></i> <span class="d-none d-sm-inline">Riwayat Data</span>
                </a>
                @if(!($isAdminView ?? false))
                    @if($scheduleConfig ?? false)
                        <a href="{{ route('schedule.index', $userDevice->id) }}" class="btn btn-app btn-action-custom">
                            <i class="bi bi-calendar-check me-sm-1"></i> <span class="d-none d-sm-inline">Jadwal</span>
                        </a>
                    @endif
                    @if($hasAutomation ?? false)
                        <a href="{{ route('automasi.index', $userDevice->id) }}" class="btn btn-automation btn-action-custom">
                            <i class="bi bi-cpu me-sm-1"></i> <span class="d-none d-sm-inline">Otomasi</span>
                        </a>
                    @endif
                    <button type="button" class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="bi bi-download me-sm-1"></i> <span class="d-none d-sm-inline">Download CSV</span>
                    </button>
                @endif
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger mb-4" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; color: #fca5a5; backdrop-filter: blur(10px);">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="alert alert-success mb-4" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px; color: #6ee7b7; backdrop-filter: blur(10px);">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if($latestData)
            <!-- Sensor Panel -->
            <div class="sensor-panel mb-4">
                <h5 class="card-title mb-4" style="color: var(--text-main);">
                    <i class="bi bi-activity me-2 text-primary"></i>Data Sensor
                </h5>
                <div class="row g-4">
                @foreach($sensors as $sensor)
                    @php
                        $value = $latestData->{$sensor->sensor_name} ?? null;
                        $labelLower = strtolower($sensor->sensor_label);
                        
                        $icon = 'bi-activity';
                        $bgColor = 'var(--primary-gradient)';
                        $stdUnit = $sensor->unit;

                        if (str_contains($labelLower, 'suhu udara') || str_contains($labelLower, 'suhu ruangan') || $labelLower === 'suhu' || str_contains($labelLower, 'temperature')) {
                            $icon = 'bi-thermometer-half';
                            $bgColor = '#FF5733';
                            $stdUnit = '°C';
                        } elseif (str_contains($labelLower, 'kelembapan udara') || str_contains($labelLower, 'kelembaban udara') || $labelLower === 'kelembaban' || $labelLower === 'kelembapan' || str_contains($labelLower, 'humidity')) {
                            $icon = 'bi-droplet';
                            $bgColor = '#3498DB';
                            $stdUnit = '%';
                        } elseif (str_contains($labelLower, 'curah hujan') || str_contains($labelLower, 'rain')) {
                            $icon = 'bi-cloud-rain';
                            $bgColor = '#5DADE2';
                            $stdUnit = 'mm';
                        } elseif (str_contains($labelLower, 'kecepatan angin') || str_contains($labelLower, 'wind speed')) {
                            $icon = 'bi-wind';
                            $bgColor = '#85C1E9';
                            $stdUnit = 'km/h';
                        } elseif (str_contains($labelLower, 'arah angin') || str_contains($labelLower, 'wind dir')) {
                            $icon = 'bi-compass';
                            $bgColor = '#7FB3D5';
                            $stdUnit = '°';
                        } elseif (str_contains($labelLower, 'tekanan udara') || str_contains($labelLower, 'pressure')) {
                            $icon = 'bi-speedometer';
                            $bgColor = '#2E86C1';
                            $stdUnit = 'hPa';
                        } elseif (str_contains($labelLower, 'indeks uv') || str_contains($labelLower, ' uv')) {
                            $icon = 'bi-brightness-high';
                            $bgColor = '#F1C40F';
                            $stdUnit = '';
                        } elseif (str_contains($labelLower, 'intensitas cahaya') || str_contains($labelLower, 'cahaya') || str_contains($labelLower, 'light')) {
                            $icon = 'bi-sun';
                            $bgColor = '#F7DC6F';
                            $stdUnit = 'lux';
                        } elseif (str_contains($labelLower, 'kelembaban tanah') || str_contains($labelLower, 'kelembapan tanah') || str_contains($labelLower, 'soil moisture')) {
                            $icon = 'bi-moisture';
                            $bgColor = '#27AE60';
                            $stdUnit = '%';
                        } elseif (str_contains($labelLower, 'suhu tanah') || str_contains($labelLower, 'soil temp')) {
                            $icon = 'bi-thermometer';
                            $bgColor = '#D35400';
                            $stdUnit = '°C';
                        } elseif (str_contains($labelLower, 'level air') || str_contains($labelLower, 'water level') || str_contains($labelLower, 'ketinggian air') || str_contains($labelLower, 'jarak')) {
                            $icon = 'bi-water';
                            $bgColor = '#1ABC9C';
                            $stdUnit = 'cm';
                        } elseif (str_contains($labelLower, 'co2') || str_contains($labelLower, 'karbon')) {
                            $icon = 'bi-cloud';
                            $bgColor = '#7D3C98';
                            $stdUnit = 'ppm';
                        } elseif (str_contains($labelLower, 'ec ') || $labelLower === 'ec' || str_contains($labelLower, 'electrical conductivity')) {
                            $icon = 'bi-lightning';
                            $bgColor = '#16A085';
                            $stdUnit = 'mS/cm';
                        } elseif (str_contains($labelLower, 'tds') || str_contains($labelLower, 'salinitas') || str_contains($labelLower, 'nutrisi')) {
                            $icon = 'bi-droplet-fill';
                            $bgColor = '#48C9B0';
                            $stdUnit = 'ppm';
                        } elseif (str_contains($labelLower, 'ph')) {
                            $icon = 'bi-speedometer2';
                            $bgColor = '#8E44AD';
                            $stdUnit = '';
                        }
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="sensor-card">
                            <div class="card-header-flex">
                                <div class="sensor-icon" style="background: {{ $bgColor }};">
                                    <i class="bi {{ $icon }}"></i>
                                </div>
                                <div class="sensor-label">{{ $sensor->sensor_label }}</div>
                            </div>
                            <div class="sensor-value-container">
                                <div class="sensor-value" id="sensor-val-{{ $sensor->id }}">
                                    @if($value !== null)
                                        {{ number_format($value, 1) }}
                                    @else
                                        --
                                    @endif
                                </div>
                                <div class="sensor-unit">{{ $stdUnit }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        @endif

        @if($outputs->count() > 0)
            <!-- Output Control Panel -->
            <div class="output-panel">
                <h5 class="card-title mb-4" style="color: var(--text-main);">
                    <i class="bi bi-sliders me-2 text-primary"></i>Kontrol Output
                </h5>
                <div class="row g-4">
                    @php
                        // Sort outputs by priority then name (excluding multi_zone)
                        $sortedOutputs = $outputs->where('output_type', '!=', 'multi_zone')->sortBy(function ($output) {
                            $name = strtolower($output->output_name);

                            // Paling bawah khusus Air Baku Valve dan Air Pupuk Valve
                            if (in_array($name, ['st_bak', 'st_ppk']))
                                return 100;

                            // Display-only status outputs
                            if (str_starts_with($name, 'st_'))
                                return 95;

                            // Priority Mapping
                            if (str_contains($name, 'pompa') || str_contains($name, 'pump') && !str_contains($name, 'ab') && !str_contains($name, 'ph'))
                                return 10;
                            if (str_contains($name, 'pump_ab') || str_contains($name, 'dosing'))
                                return 20;
                            if (str_contains($name, 'ph_up') || str_contains($name, 'ph1'))
                                return 30; // pH Up (pmpPH)
                            if (str_contains($name, 'ph_down') || str_contains($name, 'ph2'))
                                return 31; // pH Down (pmpPH2)

                            // Environment controls
                            if (str_contains($name, 'mist'))
                                return 50;
                            if (str_contains($name, 'fan'))
                                return 51;
                            if (str_contains($name, 'air'))
                                return 52;
                            if (str_contains($name, 'lamp'))
                                return 53;
                            if (str_contains($name, 'mix'))
                                return 54;

                            return 99; // Default priority
                        })->values();
                    @endphp

                    {{-- Dynamic Irrigation Pump Cards (from database) --}}
                    @php
                        $irrigationPumps = $outputs->where('output_type', 'multi_zone');
                    @endphp

                    @foreach($irrigationPumps as $pump)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="output-card-special" id="output-card-irrigation-{{ $pump->id }}">
                                <div class="card-header-flex">
                                    <div class="output-icon-special" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                                        <i class="bi bi-droplet-fill"></i>
                                    </div>
                                    <div class="output-label">{{ $pump->output_label }}</div>
                                </div>
                                
                                <div class="output-status {{ $pump->current_value ? 'on' : 'off' }}" id="pump-status-{{ $pump->id }}">
                                    {{ $pump->current_value ? 'ON' : 'OFF' }}
                                </div>

                                <div class="segmented-control">
                                    <button type="button" class="segmented-btn {{ $pump->current_value ? 'active-on' : '' }}"
                                        onclick="openIrrigationModal({{ $pump->id }}, {{ $pump->max_sectors ?? 1 }})"
                                        id="btn-on-{{ $pump->id }}">
                                        ON
                                    </button>
                                    <button type="button" class="segmented-btn {{ !$pump->current_value ? 'active-off' : '' }}"
                                        onclick="sendIrrigationPumpOff({{ $pump->id }})" id="btn-off-{{ $pump->id }}">
                                        OFF
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @foreach($sortedOutputs as $output)
                        @php
                            $outIcon = $output->icon;
                            $outBgColor = $output->color;
                            
                            $isStatusOnly = str_starts_with($output->output_name, 'sts_') || in_array($output->output_name, ['st_bak', 'st_ppk']);
                            $bgColorHex = (strpos($outBgColor, '#') === 0) ? $outBgColor : '#0ea5e9';
                        @endphp
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="output-card" id="output-card-{{ $output->id }}">
                                <div class="card-header-flex">
                                    <div class="output-icon" style="background: {{ $outBgColor }};">
                                        <i class="bi {{ $outIcon }}"></i>
                                    </div>
                                    <div class="output-label">{{ $output->output_label }}</div>
                                </div>

                                @if($output->output_type === 'boolean')
                                    @if(in_array($output->output_name, ['st_bak', 'st_ppk']))
                                        {{-- Display-only status for Air Baku & Air Pupuk Valve --}}
                                        <div class="output-status {{ $output->current_value ? 'on' : 'off' }}"
                                            id="output-status-{{ $output->id }}">
                                            Otomatis
                                        </div>
                                        <div class="d-flex justify-content-center mt-auto">
                                            <span class="badge rounded-pill px-3 py-1 {{ $output->current_value ? 'bg-success' : 'bg-secondary' }}" id="badge-status-{{ $output->id }}">
                                                <i class="bi {{ $output->current_value ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                                                {{ $output->current_value ? 'ON' : 'OFF' }}
                                            </span>
                                        </div>
                                    @else
                                        @php
                                            $outName = strtolower($output->output_name);
                                            $isShading = str_contains($outName, 'shading') || str_contains($outName, 'net');
                                            $onText = $isShading ? 'OPEN' : 'ON';
                                            $offText = $isShading ? 'CLOSE' : 'OFF';
                                        @endphp
                                        <div class="output-status {{ $output->current_value ? 'on' : 'off' }}"
                                            id="output-status-{{ $output->id }}"
                                            data-on-text="{{ $onText }}"
                                            data-off-text="{{ $offText }}">
                                            {{ $output->current_value ? $onText : $offText }}
                                        </div>
                                        
                                        {{-- ON/OFF Buttons for Boolean --}}
                                        @php
                                            $isDosingPump = str_contains($outName, 'pump_ab') || str_contains($outName, 'dosing') || $outName === 'st_dos';
                                            $isPhUp = str_contains($outName, 'ph_up') || str_contains($outName, 'ph1') || $outName === 'st_ph_u';
                                            $isPhDown = str_contains($outName, 'ph_down') || str_contains($outName, 'ph2') || $outName === 'st_ph_d';
                                            
                                            $isModalPump = $isPhUp || $isPhDown || $isDosingPump;
                                            $pumpType = $isDosingPump ? 'dosing' : ($isPhUp ? 'ph_up' : ($isPhDown ? 'ph_down' : ''));
                                        @endphp

                                        @if($isModalPump)
                                            {{-- Dosing/pH: ON opens popup with Manual & mL options --}}
                                            <div class="segmented-control">
                                                <button type="button"
                                                    class="segmented-btn {{ $output->current_value ? 'active-on' : '' }}"
                                                    onclick="openPhControlModal({{ $output->id }}, '{{ $pumpType }}')" id="btn-on-{{ $output->id }}">
                                                    ON
                                                </button>
                                                <button type="button"
                                                    class="segmented-btn {{ !$output->current_value ? 'active-off' : '' }}"
                                                    onclick="setOutput({{ $output->id }}, false)" id="btn-off-{{ $output->id }}">
                                                    OFF
                                                </button>
                                            </div>
                                        @else
                                            {{-- Normal Boolean ON/OFF --}}
                                            <div class="segmented-control">
                                                <button type="button"
                                                    class="segmented-btn {{ $output->current_value ? 'active-on' : '' }}"
                                                    onclick="setOutput({{ $output->id }}, true)" id="btn-on-{{ $output->id }}">
                                                    {{ $onText }}
                                                </button>
                                                <button type="button"
                                                    class="segmented-btn {{ !$output->current_value ? 'active-off' : '' }}"
                                                    onclick="setOutput({{ $output->id }}, false)" id="btn-off-{{ $output->id }}">
                                                    {{ $offText }}
                                                </button>
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <!-- Range Slider for Number/Percentage -->
                                    <div class="output-status on">
                                        {{ $output->output_type === 'percentage' ? '0-100%' : '0-180°' }}
                                    </div>
                                    <div class="range-value text-center" id="output-value-{{ $output->id }}">
                                        {{ (int) $output->current_value }}{{ $output->unit }}
                                    </div>
                                    <input type="range" class="range-slider mt-auto" id="output-{{ $output->id }}"
                                        data-output-id="{{ $output->id }}" data-output-type="{{ $output->output_type }}" min="0"
                                        max="{{ $output->output_type === 'percentage' ? 100 : 180 }}"
                                        value="{{ (int) $output->current_value }}"
                                        oninput="updateRangeValue({{ $output->id }}, this.value, '{{ $output->unit }}')"
                                        onchange="toggleOutput({{ $output->id }}, this.value)">
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($latestData)

        @else
            <!-- No Data -->
            <div class="glass-card">
                <div class="no-data">
                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-white">Belum Ada Data</h5>
                    <p>Device ini belum mengirimkan data sensor.<br>Data akan muncul setelah device terhubung dan mengirim
                        data.</p>
                </div>
            </div>

            <div class="glass-card mt-4">
                <h5 class="card-title"><i class="bi bi-list-check me-2"></i>Sensor yang Dikonfigurasi</h5>
                <div class="row g-3 mt-2">
                    @foreach($sensors as $sensor)
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3"
                                style="background: rgba(255,255,255,0.05); border-radius: 12px;">
                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                <div>
                                    <div class="text-white fw-semibold">{{ $sensor->sensor_label }}</div>
                                    <small class="text-white-50">{{ $sensor->sensor_name }}
                                        {{ $sensor->unit ? '(' . $sensor->unit . ')' : '' }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
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

        <!-- Irrigation Pump Control Modal (Bottom Sheet Style) -->
        <div class="modal fade" id="irrigationPumpModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content modal-content-glass">
                    <div class="modal-handle"></div>
                    <div class="modal-header-custom">
                        <h5 id="irrigationPumpModalLabel">
                            <i class="bi bi-droplet-fill me-2" style="color: #0ea5e9;"></i>Kontrol Pompa Irigasi
                        </h5>
                        <div class="subtitle">Pilih jenis air dan zona output yang dituju</div>
                    </div>
                    <div class="modal-body-custom">
                        <!-- Hidden field for output ID -->
                        <input type="hidden" id="irrigationOutputId" value="">

                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #374151; font-size: 0.9rem;">
                                <i class="bi bi-water me-1" style="color: #0ea5e9;"></i> Jenis Air
                            </label>
                            <select id="irrigationWaterType" class="form-select form-select-dark">
                                <option value="2">Air Baku</option>
                                <option value="1">Air Pupuk</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #374151; font-size: 0.9rem;">
                                <i class="bi bi-geo-alt me-1" style="color: #0ea5e9;"></i> Zona / Blok
                            </label>
                            <select id="irrigationZone" class="form-select form-select-dark">
                                <!-- Dynamically populated by JavaScript -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn" onclick="sendIrrigationPumpOn()"
                            style="background: linear-gradient(135deg, #0ea5e9, #0284c7); border-radius: 12px; padding: 0.75rem; font-size: 1rem; font-weight: 700; width: 100%; color: #fff; box-shadow: 0 4px 14px rgba(14, 165, 233, 0.3);">
                            <i class="bi bi-play-fill me-1"></i> Nyalakan Pompa
                        </button>
                        <button type="button" class="btn"
                            style="background: #f3f4f6; color: #6b7280; border-radius: 12px; padding: 0.75rem; font-size: 1rem; font-weight: 600; width: 100%; border: none;"
                            data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- pH Control Modal (Bottom Sheet Style) -->
        <div class="modal fade" id="phControlModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content modal-content-glass">
                    <div class="modal-handle"></div>
                    <div class="modal-header-custom">
                        <h5 id="phControlModalLabel">
                            <i class="bi bi-droplet-half me-2" style="color: #8b5cf6;"></i>pH Control
                        </h5>
                        <div class="subtitle" id="phControlSubtitle">Pilih mode kontrol pH</div>
                    </div>
                    <div class="modal-body-custom">
                        <input type="hidden" id="phControlOutputId" value="">
                        <input type="hidden" id="phControlType" value="">

                        <!-- Option 1: Manual ON -->
                        <div class="mb-3 p-3" style="background: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.25); border-radius: 16px;">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div style="font-weight: 700; color: #166534; font-size: 0.95rem;">
                                        <i class="bi bi-power me-1"></i> ON Manual
                                    </div>
                                    <div style="font-size: 0.78rem; color: #6b7280; margin-top: 2px;">
                                        Nyalakan pompa secara manual (ON terus sampai dimatikan)
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm px-3" onclick="sendPhManualOn()"
                                    id="btnPhManualOn"
                                    style="background: linear-gradient(135deg, #22c55e, #16a34a); border: none; border-radius: 12px; font-weight: 600; min-width: 70px; color: white;">
                                    <i class="bi bi-power me-1"></i> ON
                                </button>
                            </div>
                        </div>

                        <!-- Option 2: By Volume (mL) -->
                        <div class="p-3" style="background: rgba(139, 92, 246, 0.08); border: 1px solid rgba(139, 92, 246, 0.25); border-radius: 16px;">
                            <div style="font-weight: 700; color: #5b21b6; font-size: 0.95rem; margin-bottom: 8px;">
                                <i class="bi bi-eyedropper me-1"></i> ON by Volume (mL)
                            </div>
                            <div style="font-size: 0.78rem; color: #6b7280; margin-bottom: 10px;">
                                Pompa berjalan sesuai volume lalu berhenti otomatis
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-bold" style="color: #374151; font-size: 0.85rem;">
                                    Volume (mL)
                                </label>
                                <input type="number" id="phDosingVolume" class="form-control form-control-dark"
                                    min="1" max="9999" value="100" placeholder="Masukkan volume dalam mL">
                            </div>

                            <div class="d-flex gap-2 flex-wrap mb-3">
                                <button type="button" class="btn btn-sm" style="border: 1px solid #8b5cf6; color: #8b5cf6; border-radius: 12px; padding: 4px 10px; font-size: 0.8rem;" onclick="document.getElementById('phDosingVolume').value=100">100 mL</button>
                                <button type="button" class="btn btn-sm" style="border: 1px solid #8b5cf6; color: #8b5cf6; border-radius: 12px; padding: 4px 10px; font-size: 0.8rem;" onclick="document.getElementById('phDosingVolume').value=200">200 mL</button>
                                <button type="button" class="btn btn-sm" style="border: 1px solid #8b5cf6; color: #8b5cf6; border-radius: 12px; padding: 4px 10px; font-size: 0.8rem;" onclick="document.getElementById('phDosingVolume').value=500">500 mL</button>
                                <button type="button" class="btn btn-sm" style="border: 1px solid #8b5cf6; color: #8b5cf6; border-radius: 12px; padding: 4px 10px; font-size: 0.8rem;" onclick="document.getElementById('phDosingVolume').value=1000">1000 mL</button>
                                <button type="button" class="btn btn-sm" style="border: 1px solid #8b5cf6; color: #8b5cf6; border-radius: 12px; padding: 4px 10px; font-size: 0.8rem;" onclick="document.getElementById('phDosingVolume').value=2000">2000 mL</button>
                            </div>

                            <button type="button" class="btn w-100" onclick="sendPhByVolume()"
                                id="btnPhByVolume"
                                style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 12px; padding: 0.75rem; font-size: 1rem; font-weight: 700; color: #fff; box-shadow: 0 4px 14px rgba(139, 92, 246, 0.3);">
                                <i class="bi bi-send-fill me-1"></i> Kirim Volume
                            </button>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn"
                            style="background: #f3f4f6; color: #6b7280; border-radius: 12px; padding: 0.75rem; font-size: 1rem; font-weight: 600; width: 100%; border: none;"
                            data-bs-dismiss="modal">Batal</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Setup CSRF token for AJAX requests
            const csrfToken = '{{ csrf_token() }}';
            const userDeviceId = {{ $userDevice->id ?? 'null' }};



            // Set output ON/OFF (for buttons)
            function setOutput(outputId, isOn) {
                const url = `/monitoring/device/${userDeviceId}/output/${outputId}/toggle`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ value: isOn })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            setOptimisticUI(outputId, isOn);

                            // Flash card border
                            const card = document.getElementById(`output-card-${outputId}`);
                            if (card) {
                                card.style.borderColor = isOn ? '#22c55e' : '#ef4444';
                                setTimeout(() => {
                                    card.style.borderColor = 'rgba(250, 204, 21, 0.3)';
                                }, 500);
                            }

                            console.log('Output toggled:', data.message);
                        } else {
                            console.error('Failed to update output');
                            showToast('Gagal mengubah status: ' + (data.message || 'Silakan coba lagi.'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat mengupdate output.');
                    });
            }

            // ============= IRRIGATION PUMP MODAL FUNCTIONS =============

            // Open irrigation modal with dynamic zone selection
            function openIrrigationModal(outputId, maxZones) {
                // Set output ID in hidden field
                document.getElementById('irrigationOutputId').value = outputId;

                // Populate zone dropdown dynamically
                const zoneSelect = document.getElementById('irrigationZone');
                zoneSelect.innerHTML = '';
                for (let z = 1; z <= maxZones; z++) {
                    const option = document.createElement('option');
                    option.value = z;
                    option.textContent = `Zona ${z}`;
                    zoneSelect.appendChild(option);
                }

                // Reset to default values
                document.getElementById('irrigationWaterType').value = '2'; // Default Air Baku (2)

                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('irrigationPumpModal'));
                modal.show();
            }

            // Send pump ON command from modal
            function sendIrrigationPumpOn() {
                const outputId = document.getElementById('irrigationOutputId').value;
                const zone = document.getElementById('irrigationZone').value;
                const waterType = document.getElementById('irrigationWaterType').value;

                // Send via AJAX
                const url = `/monitoring/device/${userDeviceId}/output/${outputId}/irrigation-pump`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        zone: zone,
                        turnOn: true,
                        waterType: waterType
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('irrigationPumpModal'));
                            modal.hide();

                            setOptimisticUI(outputId, true);

                            // Flash card border for feedback
                            const card = document.getElementById(`output-card-irrigation-${outputId}`);
                            if (card) {
                                card.style.borderColor = '#22c55e';
                                setTimeout(() => {
                                    card.style.borderColor = 'rgba(14, 165, 233, 0.3)';
                                }, 1000);
                            }

                            console.log('Irrigation pump ON:', data.message);
                        } else {
                            showToast('Gagal mengirim perintah pompa: ' + (data.message || 'Silakan coba lagi.'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat mengirim perintah.');
                    });
            }

            // Send pump OFF command (direct, no modal needed)
            function sendIrrigationPumpOff(outputId) {
                const url = `/monitoring/device/${userDeviceId}/output/${outputId}/irrigation-pump`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        zone: '0', // 0 = all zones
                        turnOn: false,
                        waterType: '1'
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            setOptimisticUI(outputId, false);

                            // Flash card border for feedback
                            const card = document.getElementById(`output-card-irrigation-${outputId}`);
                            if (card) {
                                card.style.borderColor = '#ef4444';
                                setTimeout(() => {
                                    card.style.borderColor = 'rgba(14, 165, 233, 0.3)';
                                }, 1000);
                            }

                            console.log('Irrigation pump OFF:', data.message);
                        } else {
                            showToast('Gagal mematikan pompa: ' + (data.message || 'Silakan coba lagi.'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat mengirim perintah.');
                    });
            }

            // ============= pH CONTROL MODAL FUNCTIONS =============

            function openPhControlModal(outputId, pumpType) {
                document.getElementById('phControlOutputId').value = outputId;
                document.getElementById('phControlType').value = pumpType;

                let title = 'Control';
                let icon = 'bi-droplet-half';
                if (pumpType === 'dosing') {
                    title = 'Dosing AB';
                    icon = 'bi-eyedropper';
                } else if (pumpType === 'ph_up') {
                    title = 'pH Up';
                    icon = 'bi-arrow-up-circle';
                } else if (pumpType === 'ph_down') {
                    title = 'pH Down';
                    icon = 'bi-arrow-down-circle';
                }

                document.getElementById('phControlModalLabel').innerHTML =
                    `<i class="bi ${icon} me-2" style="color: #8b5cf6;"></i>${title} Control`;
                document.getElementById('phControlSubtitle').textContent =
                    `Pilih mode kontrol ${title}`;

                document.getElementById('phDosingVolume').value = 10;

                const modal = new bootstrap.Modal(document.getElementById('phControlModal'));
                modal.show();
            }

            // pH Manual ON - sends <pmpPH#1#> or <pmpPH2#1#>
            function sendPhManualOn() {
                const outputId = document.getElementById('phControlOutputId').value;
                const btn = document.getElementById('btnPhManualOn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';
                btn.disabled = true;

                // Use the existing toggle endpoint to send manual ON
                const url = `/monitoring/device/${userDeviceId}/output/${outputId}/toggle`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ value: true })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('phControlModal'));
                            modal.hide();

                            setOptimisticUI(outputId, true);

                            // Flash card border
                            const card = document.getElementById(`output-card-${outputId}`);
                            if (card) {
                                card.style.borderColor = '#22c55e';
                                setTimeout(() => { card.style.borderColor = 'rgba(250, 204, 21, 0.3)'; }, 500);
                            }
                        } else {
                            showToast('Gagal: ' + (data.message || 'Silakan coba lagi.'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat mengirim perintah.');
                    })
                    .finally(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
            }

            // pH by Volume - sends <pmpph#10#> or <pmpph2#10#>
            function sendPhByVolume() {
                const phType = document.getElementById('phControlType').value;
                const volume = parseInt(document.getElementById('phDosingVolume').value);

                if (!volume || volume < 1) {
                    showToast('Masukkan volume yang valid (minimal 1 mL).');
                    return;
                }

                const btn = document.getElementById('btnPhByVolume');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';
                btn.disabled = true;

                const url = `/monitoring/device/${userDeviceId}/dosing/volume`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        pump_type: phType,
                        volume: volume
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('phControlModal'));
                            modal.hide();
                            showToast(data.message);
                        } else {
                            showToast('Gagal: ' + (data.message || 'Silakan coba lagi.'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat mengirim perintah.');
                    })
                    .finally(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
            }

            // Toggle output (AJAX) - kept for range sliders
            function toggleOutput(outputId, value) {
                const url = `/monitoring/device/${userDeviceId}/output/${outputId}/toggle`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ value: value })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            setOptimisticUI(outputId, data.new_value == 1 || data.new_value === true);

                            // Show success feedback
                            const card = document.getElementById(`output-card-${outputId}`);
                            if (card) {
                                card.style.borderColor = '#22c55e';
                                setTimeout(() => {
                                    card.style.borderColor = 'rgba(250, 204, 21, 0.3)';
                                }, 500);
                            }

                            console.log('Output updated:', data.message);
                        } else {
                            console.error('Failed to update output');
                            showToast('Gagal mengupdate output. Silakan coba lagi.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat mengupdate output.');
                    });
            }

            // Update range value display
            function updateRangeValue(outputId, value, unit) {
                const valueEl = document.getElementById(`output-value-${outputId}`);
                if (valueEl) {
                    valueEl.textContent = value + unit;
                }
            }

            function setDateRange(days) {
                const endDate = new Date();
                const startDate = new Date();
                startDate.setDate(startDate.getDate() - days);

                document.querySelector('input[name="start_date"]').value = startDate.toISOString().split('T')[0];
                document.querySelector('input[name="end_date"]').value = endDate.toISOString().split('T')[0];
            }

            // Special Pump Control Functions
            function sendPumpOn() {
                const zone = document.getElementById('pumpZone').value;
                const inputType = document.getElementById('pumpInputType').value;
                const url = `/monitoring/device/${userDeviceId}/pump/control`;

                // Show loading state
                const btn = document.querySelector('#pumpModal .btn-pump-send');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';
                btn.disabled = true;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        action: 'on',
                        zone: zone,
                        input_type: inputType
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update status display
                            const statusEl = document.getElementById('pump-special-status');
                            const typeName = inputType == '0' ? 'Air Baku' : 'Air Pupuk';
                            if (statusEl) {
                                statusEl.textContent = `ON - Zona ${zone} (${typeName})`;
                                statusEl.style.color = '#22c55e';
                            }

                            // Visual feedback
                            const card = document.getElementById('output-card-special-pump');
                            if (card) {
                                card.style.borderColor = '#22c55e';
                                setTimeout(() => {
                                    card.style.borderColor = 'rgba(14, 165, 233, 0.3)';
                                }, 1000);
                            }

                            // Close modal
                            const modal = bootstrap.Modal.getInstance(document.getElementById('pumpModal'));
                            modal.hide();

                            console.log('Pump ON sent:', data.message);
                        } else {
                            showToast('Gagal mengirim perintah pompa: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat mengirim perintah pompa.');
                    })
                    .finally(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
            }

            function sendPumpOff() {
                const url = `/monitoring/device/${userDeviceId}/pump/control`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ action: 'off' })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update status display
                            const statusEl = document.getElementById('pump-special-status');
                            if (statusEl) {
                                statusEl.textContent = 'OFF';
                                statusEl.style.color = 'var(--text-secondary)';
                            }

                            // Visual feedback
                            const card = document.getElementById('output-card-special-pump');
                            if (card) {
                                card.style.borderColor = '#ef4444';
                                setTimeout(() => {
                                    card.style.borderColor = 'rgba(14, 165, 233, 0.3)';
                                }, 1000);
                            }

                            console.log('Pump OFF sent:', data.message);
                        } else {
                            showToast('Gagal mengirim perintah pompa: ' + (data.message || 'Unknown error'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat mengirim perintah pompa.');
                    });
            }


        </script>
    @endif

    @if($isAdminView ?? false)
        <script>
            // Admin Output Control JavaScript
            const csrfToken = '{{ csrf_token() }}';
            const deviceId = {{ $device->id }};

            // Set output ON/OFF (for buttons)
            function setOutput(outputId, isOn) {
                const url = `/admin/device/${deviceId}/output/${outputId}/toggle`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ value: isOn })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            setOptimisticUI(outputId, isOn);

                            const card = document.getElementById(`output-card-${outputId}`);
                            if (card) {
                                card.style.borderColor = '#22c55e';
                                setTimeout(() => {
                                    card.style.borderColor = 'rgba(250, 204, 21, 0.3)';
                                }, 500);
                            }
                        } else {
                            showToast('Gagal mengupdate output.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat mengupdate output.');
                    });
            }

            function toggleOutput(outputId, value) {
                const url = `/admin/device/${deviceId}/output/${outputId}/toggle`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ value: value })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const card = document.getElementById(`output-card-${outputId}`);
                            if (card) {
                                card.style.borderColor = '#22c55e';
                                setTimeout(() => {
                                    card.style.borderColor = 'rgba(250, 204, 21, 0.3)';
                                }, 500);
                            }
                        } else {
                            showToast('Gagal mengupdate output.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat mengupdate output.');
                    });
            }

            function updateRangeValue(outputId, value, unit) {
                const valueEl = document.getElementById(`output-value-${outputId}`);
                if (valueEl) {
                    valueEl.textContent = value + unit;
                }
            }

            // ============= pH CONTROL MODAL FUNCTIONS (Admin) =============

            function openPhControlModal(outputId, pumpType) {
                document.getElementById('phControlOutputId').value = outputId;
                document.getElementById('phControlType').value = pumpType;

                let title = 'Control';
                let icon = 'bi-droplet-half';
                if (pumpType === 'dosing') {
                    title = 'Dosing AB';
                    icon = 'bi-eyedropper';
                } else if (pumpType === 'ph_up') {
                    title = 'pH Up';
                    icon = 'bi-arrow-up-circle';
                } else if (pumpType === 'ph_down') {
                    title = 'pH Down';
                    icon = 'bi-arrow-down-circle';
                }

                document.getElementById('phControlModalLabel').innerHTML =
                    `<i class="bi ${icon} me-2" style="color: #8b5cf6;"></i>${title} Control`;
                document.getElementById('phControlSubtitle').textContent =
                    `Pilih mode kontrol ${title}`;

                document.getElementById('phDosingVolume').value = 10;

                const modal = new bootstrap.Modal(document.getElementById('phControlModal'));
                modal.show();
            }

            // pH Manual ON (Admin) - sends <pmpPH#1#> or <pmpPH2#1#>
            function sendPhManualOn() {
                const outputId = document.getElementById('phControlOutputId').value;
                const btn = document.getElementById('btnPhManualOn');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';
                btn.disabled = true;

                const url = `/admin/device/${deviceId}/output/${outputId}/toggle`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ value: true })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('phControlModal'));
                            modal.hide();

                            setOptimisticUI(outputId, true);

                            const card = document.getElementById(`output-card-${outputId}`);
                            if (card) {
                                card.style.borderColor = '#22c55e';
                                setTimeout(() => { card.style.borderColor = 'rgba(250, 204, 21, 0.3)'; }, 500);
                            }
                        } else {
                            showToast('Gagal: ' + (data.message || 'Silakan coba lagi.'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat mengirim perintah.');
                    })
                    .finally(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
            }

            // pH by Volume (Admin) - sends <pmpph#10#> or <pmpph2#10#>
            function sendPhByVolume() {
                const phType = document.getElementById('phControlType').value;
                const volume = parseInt(document.getElementById('phDosingVolume').value);

                if (!volume || volume < 1) {
                    showToast('Masukkan volume yang valid (minimal 1 mL).');
                    return;
                }

                const btn = document.getElementById('btnPhByVolume');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengirim...';
                btn.disabled = true;

                const url = `/admin/device/${deviceId}/dosing/volume`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        pump_type: phType,
                        volume: volume
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('phControlModal'));
                            modal.hide();
                            showToast(data.message);
                        } else {
                            showToast('Gagal: ' + (data.message || 'Silakan coba lagi.'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showToast('Terjadi kesalahan saat mengirim perintah.');
                    })
                    .finally(() => {
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    });
            }

            // Auto-reload status every 2 seconds
            setInterval(fetchStatus, 2000);

            async function fetchStatus() {
                try {
                    @if($isAdminView ?? false)
                        const response = await fetch('{{ route("admin.device.status", $device->id) }}');
                    @else
                        const response = await fetch('{{ route("monitoring.status", $userDevice->id) }}');
                    @endif
                                                                                                                                                                                                    const data = await response.json();

                    if (data.success) {
                        if (data.outputs) {
                            updateOutputs(data.outputs);
                        }
                        if (data.sensors) {
                            updateSensors(data.sensors);
                        }
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }

            // Map sensor name to ID using PHP array
            const sensorMap = @json($sensors->pluck('id', 'sensor_name'));

            function updateSensors(sensorData) {
                // key is sensor_name (e.g. ni_PH), value is the reading
                for (const [key, value] of Object.entries(sensorData)) {
                    if (sensorMap[key]) {
                        const sensorId = sensorMap[key];
                        const el = document.getElementById(`sensor-val-${sensorId}`);
                        if (el) {
                            // Format number to 1 decimal place if it's a number
                            const num = parseFloat(value);
                            el.innerText = !isNaN(num) ? num.toFixed(1) : value;
                        }
                    }
                }
            }


            // Custom Toast Notification
            function showToast(message, type = 'success') {
                // Determine type based on message content if not explicitly passed
                if (message.toString().toLowerCase().includes('gagal') || 
                    message.toString().toLowerCase().includes('kesalahan') ||
                    message.toString().toLowerCase().includes('masukkan')) {
                    type = 'error';
                }

                const container = document.getElementById('toastContainer');
                const toast = document.createElement('div');
                toast.className = `custom-toast ${type}`;
                
                const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
                
                toast.innerHTML = `
                    <i class="bi ${icon}"></i>
                    <div style="font-weight: 600; color: #1f2937;">${message}</div>
                `;
                
                container.appendChild(toast);
                
                // Animate in
                setTimeout(() => toast.classList.add('show'), 10);
                
                // Animate out and remove
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 400);
                }, 3000);
            }
        </script>
    @endif

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    {{-- Auto-Reload Script - Always runs regardless of initial data --}}
    <script>
        // Map sensor name to ID using PHP array
        const sensorMap = @json($sensors->pluck('id', 'sensor_name'));
        
        // Map output name to ID using PHP array
        const outputMap = @json($outputs->pluck('id', 'output_name'));
        
        const pendingOutputs = {}; // Lock buttons for 20s after click

        // Optimistically update UI and lock for 20 seconds
        function setOptimisticUI(outputId, isOn) {
            pendingOutputs[outputId] = { expectedValue: isOn, timestamp: Date.now() };

            const btnOn = document.getElementById(`btn-on-${outputId}`);
            const btnOff = document.getElementById(`btn-off-${outputId}`);

            if (btnOn && btnOff) {
                if (isOn) {
                    btnOn.className = 'segmented-btn active-on';
                    btnOff.className = 'segmented-btn';
                } else {
                    btnOn.className = 'segmented-btn';
                    btnOff.className = 'segmented-btn active-off';
                }
            }
        }
        
        // Auto-reload status every 60 seconds (as a fallback, since WebSockets handle real-time)
        setInterval(fetchStatus, 60000);

        async function fetchStatus() {
            try {
                @if($isAdminView ?? false)
                    const response = await fetch('{{ route("admin.device.status", $device->id) }}');
                @else
                    const response = await fetch('{{ route("monitoring.status", $userDevice->id) }}');
                @endif
                const data = await response.json();

                if (data.success) {
                    if (data.outputs) {
                        updateOutputs(data.outputs);
                    }
                    if (data.sensors) {
                        updateSensors(data.sensors);
                    }
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        }
        
        // Timeout timer for offline detection
        let offlineTimer = null;
        
        function resetOfflineTimer() {
            if (offlineTimer) clearTimeout(offlineTimer);
            offlineTimer = setTimeout(() => {
                setDeviceOffline();
            }, 60000); // 1 minute without updates = Offline
        }
        
        function setDeviceOnline() {
            const badge = document.getElementById('conn-badge');
            if (badge) {
                badge.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                badge.innerHTML = '<i class="bi bi-wifi me-1"></i> ONLINE';
            }
            const dot = document.getElementById('live-dot');
            if (dot) dot.style.display = 'inline-block';
            resetOfflineTimer();
        }
        
        function setDeviceOffline() {
            const badge = document.getElementById('conn-badge');
            if (badge) {
                badge.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
                badge.innerHTML = '<i class="bi bi-wifi-off me-1"></i> OFFLINE';
            }
            const dot = document.getElementById('live-dot');
            if (dot) dot.style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function () {
            @if($isOnline ?? false)
                resetOfflineTimer();
            @endif

            if (window.Echo) {
                window.Echo.private(`device.{{ $device->id }}`)
                    .listen('DeviceStatusUpdated', (e) => {
                        console.log('Realtime Update:', e);
                        
                        setDeviceOnline();
                        
                        // Update last update text
                        const lastUpdateEl = document.getElementById('last-update-text');
                        if (lastUpdateEl) {
                            lastUpdateEl.innerHTML = `<span class="live-dot me-2" id="live-dot" style="display: inline-block;"></span> Terakhir update: Baru saja`;
                        }

                        if (e.outputs && Array.isArray(e.outputs) && e.outputs.length > 0) {
                            updateOutputs(e.outputs);
                        }
                        
                        if (e.sensors) {
                            updateSensors(e.sensors);
                        }
                    });
            } else {
                console.warn("Laravel Echo is not initialized. WebSockets will not work.");
            }
        });
        function updateSensors(sensorData) {
            for (const [key, value] of Object.entries(sensorData)) {
                if (sensorMap[key]) {
                    const sensorId = sensorMap[key];
                    const el = document.getElementById(`sensor-val-${sensorId}`);
                    if (el) {
                        const num = parseFloat(value);
                        el.innerText = !isNaN(num) ? num.toFixed(1) : value;
                    }
                }
            }
        }

        function updateOutputs(outputs) {
            outputs.forEach(output => {
                // Skip update if button is locked (within 20s of user click)
                if (pendingOutputs[output.id]) {
                    const pending = pendingOutputs[output.id];
                    const isOn = parseFloat(output.value) > 0;
                    if (Date.now() - pending.timestamp < 20000) {
                        if (isOn === pending.expectedValue) {
                            // Device confirmed the change, unlock
                            delete pendingOutputs[output.id];
                        } else {
                            // Device hasn't changed yet, keep button locked
                            return;
                        }
                    } else {
                        // 20s expired, unlock and let real state through
                        delete pendingOutputs[output.id];
                    }
                }

                // Update Boolean Outputs (Buttons)
                const btnOn = document.getElementById(`btn-on-${output.id}`);
                const btnOff = document.getElementById(`btn-off-${output.id}`);
                let statusEl = document.getElementById(`output-status-${output.id}`);
                
                // Fallback for irrigation pump which uses a different ID prefix
                if (!statusEl) {
                    statusEl = document.getElementById(`pump-status-${output.id}`);
                }

                if (btnOn && btnOff && statusEl) {
                    const isOn = parseFloat(output.value) > 0;

                    if (isOn) {
                        btnOn.className = 'segmented-btn active-on';
                        btnOff.className = 'segmented-btn';
                        statusEl.className = 'output-status on';
                        statusEl.innerText = statusEl.getAttribute('data-on-text') || 'ON';
                    } else {
                        btnOn.className = 'segmented-btn';
                        btnOff.className = 'segmented-btn active-off';
                        statusEl.className = 'output-status off';
                        statusEl.innerText = statusEl.getAttribute('data-off-text') || 'OFF';
                    }
                }

                // Update Range/Slider Outputs
                const slider = document.getElementById(`output-${output.id}`);
                const valueDisplay = document.getElementById(`output-value-${output.id}`);

                if (slider && document.activeElement !== slider) {
                    slider.value = output.value;
                    if (valueDisplay) {
                        const currentText = valueDisplay.innerText;
                        const unit = currentText.replace(/[0-9\.]/g, '');
                        valueDisplay.innerText = parseInt(output.value) + unit;
                    }
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr(".flatpickr-datetime", {
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                time_24hr: true,
                altInput: true,
                altFormat: "Y-m-d H:i",
                disableMobile: false
            });
        });
    </script>
    @include('partials.pwa-scripts')
</body>

</html>
