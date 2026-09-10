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

        /* Smart Farm Blok Selection Cards */
        .sf-blok-card {
            background: #ffffff;
            border: 2px solid #e5e7eb !important;
            transition: all 0.2s ease;
        }
        .sf-blok-card:hover {
            border-color: #10b981 !important;
            background: rgba(16, 185, 129, 0.04);
        }
        .sf-blok-card:has(input:checked) {
            border-color: #10b981 !important;
            background: rgba(16, 185, 129, 0.08);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }

        @keyframes spin { 100% { transform: rotate(360deg); } }
        .spin-icon { animation: spin 1s linear infinite; display: inline-block; }

        /* Timezone Selection Cards */
        .sf-tz-card {
            background: #ffffff;
            border: 2px solid #e5e7eb !important;
            transition: all 0.2s ease;
        }
        .sf-tz-card:hover {
            border-color: #0ea5e9 !important;
            background: rgba(14, 165, 233, 0.04);
        }
        .sf-tz-card:has(input:checked) {
            border-color: #0ea5e9 !important;
            background: rgba(14, 165, 233, 0.08);
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.15);
        }

        /* Smart Farm Grouped UI & Architecture Styling */
        .sf-hero-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 22px;
            padding: 1.5rem 1.8rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04);
            backdrop-filter: blur(20px);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .sf-hero-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #0ea5e9, #8b5cf6);
        }

        /* Pipeline Visualizer Strip */
        .sf-pipeline-card {
            background: rgba(255, 255, 255, 0.85);
            border: 1px solid rgba(229, 231, 235, 0.8);
            border-radius: 18px;
            padding: 1.1rem 1.25rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            backdrop-filter: blur(10px);
        }

        .pipeline-scroll-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px;
        }

        .pipeline-scroll-wrapper::-webkit-scrollbar {
            height: 4px;
        }

        .pipeline-scroll-wrapper::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 4px;
        }

        .pipeline-track {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            gap: 6px;
        }

        @media (max-width: 768px) {
            .pipeline-track {
                min-width: 470px;
            }
        }

        .pipeline-node {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            z-index: 2;
            min-width: 76px;
        }

        .pipeline-node-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #6b7280;
            background: #f3f4f6;
            border: 1.5px solid #e5e7eb;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .pipeline-node.active .pipeline-node-icon {
            color: #ffffff;
            transform: scale(1.08);
        }

        .pipeline-node.active.node-pompa .pipeline-node-icon {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            border-color: #0284c7;
            box-shadow: 0 6px 18px rgba(14, 165, 233, 0.35);
        }

        .pipeline-node.active.node-pupuk .pipeline-node-icon {
            background: linear-gradient(135deg, #d97706, #fbbf24);
            border-color: #f59e0b;
            box-shadow: 0 6px 18px rgba(245, 158, 11, 0.35);
        }

        .pipeline-node.active.node-blok1 .pipeline-node-icon {
            background: linear-gradient(135deg, #059669, #34d399);
            border-color: #10b981;
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.35);
        }

        .pipeline-node.active.node-blok2 .pipeline-node-icon {
            background: linear-gradient(135deg, #0284c7, #38bdf8);
            border-color: #0ea5e9;
            box-shadow: 0 6px 18px rgba(14, 165, 233, 0.35);
        }

        .pipeline-node.active.node-blok3 .pipeline-node-icon {
            background: linear-gradient(135deg, #7c3aed, #a78bfa);
            border-color: #8b5cf6;
            box-shadow: 0 6px 18px rgba(139, 92, 246, 0.35);
        }

        .pipeline-node-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: #374151;
            margin-top: 5px;
            white-space: nowrap;
        }

        .pipeline-node-status {
            font-size: 0.68rem;
            font-weight: 600;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .pipeline-node.active .pipeline-node-status {
            color: #059669;
            font-weight: 700;
        }

        .pipeline-connector {
            flex: 1;
            height: 3px;
            background: #e5e7eb;
            position: relative;
            border-radius: 2px;
            margin-top: -20px;
            z-index: 1;
            overflow: hidden;
        }

        .pipeline-connector.active {
            background: #10b981;
        }

        .pipeline-connector.active::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.9), transparent);
            animation: waterFlow 1.2s infinite linear;
        }

        @keyframes waterFlow {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* Hub Containers */
        .sf-hub-card {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 22px;
            padding: 1.4rem;
            height: 100%;
            box-shadow: 0 4px 18px -2px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .sf-hub-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.15rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .sf-hub-title {
            font-size: 1.02rem;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        .sf-hub-subtitle {
            font-size: 0.77rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* Actuator Control Cards (for Pompa & Pupuk) */
        .sf-actuator-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 1.15rem;
            margin-bottom: 0.85rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .sf-actuator-card:last-child {
            margin-bottom: 0;
        }

        .sf-actuator-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }

        .sf-actuator-card.active-pump {
            border-color: #0ea5e9;
            background: linear-gradient(135deg, #ffffff 0%, rgba(14, 165, 233, 0.05) 100%);
            box-shadow: 0 6px 18px rgba(14, 165, 233, 0.12);
        }

        .sf-actuator-card.active-dosing {
            border-color: #f59e0b;
            background: linear-gradient(135deg, #ffffff 0%, rgba(245, 158, 11, 0.05) 100%);
            box-shadow: 0 6px 18px rgba(245, 158, 11, 0.12);
        }

        .sf-actuator-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }

        .sf-actuator-card .output-status,
        .sf-zone-card .output-status {
            margin: 0 !important;
            letter-spacing: 0.3px;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            padding: 0.3rem 0.65rem !important;
            border-radius: 50px !important;
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            line-height: 1.2;
        }

        .sf-actuator-card .output-status.on,
        .sf-zone-card .output-status.on {
            background: rgba(16, 185, 129, 0.12) !important;
            color: #059669 !important;
            border: 1px solid rgba(16, 185, 129, 0.25) !important;
        }

        .sf-actuator-card .output-status.off,
        .sf-zone-card .output-status.off {
            background: rgba(239, 68, 68, 0.08) !important;
            color: #dc2626 !important;
            border: 1px solid rgba(239, 68, 68, 0.2) !important;
        }

        /* Zone Card */
        .sf-zone-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 1.15rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .sf-zone-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -3px rgba(0, 0, 0, 0.07);
        }

        .sf-zone-card.active-flow {
            border-color: #10b981;
            box-shadow: 0 8px 22px -3px rgba(16, 185, 129, 0.22);
        }

        .sf-zone-badge-num {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.92rem;
            flex-shrink: 0;
        }

        .sf-flow-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 700;
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            animation: flowGlow 2s ease-in-out infinite;
        }

        @keyframes flowGlow {
            0%, 100% { opacity: 0.95; transform: scale(1); }
            50% { opacity: 0.55; transform: scale(0.96); }
        }

        /* Duration Pills for Siram Modal */
        .sf-dur-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 1rem;
        }

        .sf-dur-pill {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.65rem 0.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s ease;
            background: #ffffff;
        }

        .sf-dur-pill:hover {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.04);
        }

        .sf-dur-pill:has(input:checked) {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.1);
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.15);
        }

        .sf-dur-pill .dur-num {
            font-size: 1.15rem;
            font-weight: 800;
            color: #1f2937;
            line-height: 1;
        }

        .sf-dur-pill .dur-unit {
            font-size: 0.72rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .sf-dur-pill:has(input:checked) .dur-num {
            color: #059669;
        }

        .sf-dur-pill:has(input:checked) .dur-unit {
            color: #059669;
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
            .container {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }

            /* Page Header compact */
            .page-header {
                padding: 1.15rem 1rem !important;
                border-radius: 18px !important;
                margin-bottom: 1.25rem !important;
                gap: 0.85rem !important;
            }

            .device-title {
                font-size: 1.25rem;
            }

            .device-type-badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.75rem;
            }

            /* Header Action Buttons on Mobile */
            .header-actions-group {
                width: 100%;
                display: flex !important;
                gap: 0.4rem !important;
            }

            .header-actions-group .btn-action-custom {
                flex: 1;
                justify-content: center;
                padding: 0.65rem 0.35rem !important;
                font-size: 0.82rem !important;
                font-weight: 600;
                border-radius: 12px !important;
                text-align: center;
                white-space: nowrap;
                min-height: 42px;
            }

            .header-actions-group .btn-action-custom span {
                display: inline !important;
            }

            /* Smart Farm Status Card on Mobile */
            #sf-status-card {
                padding: 1.15rem 1rem !important;
                border-radius: 18px !important;
                margin-bottom: 1.25rem !important;
            }

            #sf-status-card .d-flex.flex-wrap.align-items-center.justify-content-between {
                flex-direction: column;
                align-items: stretch !important;
                gap: 0.85rem !important;
            }

            #sf-status-card .sf-actions-group {
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                width: 100%;
                gap: 0.5rem;
            }

            #sf-status-card .sf-actions-group #sf-btn-start,
            #sf-status-card .sf-actions-group #sf-btn-stop {
                grid-column: span 2;
                font-size: 0.92rem !important;
                min-height: 44px;
            }

            #sf-status-card .sf-actions-group .btn {
                justify-content: center;
                padding: 0.6rem 0.45rem !important;
                font-size: 0.82rem !important;
                font-weight: 600;
                border-radius: 12px !important;
                min-height: 40px;
            }

            .sf-hub-card {
                padding: 1.1rem !important;
                border-radius: 18px !important;
            }

            /* Prevent auto-zoom in iOS Safari */
            input, select, textarea {
                font-size: 16px !important;
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
            .container {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }

            .page-header {
                padding: 0.85rem 0.75rem !important;
            }

            .header-actions-group .btn-action-custom {
                font-size: 0.75rem !important;
                padding: 0.55rem 0.25rem !important;
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
                        

                        <span class="device-type-badge" style="font-size: 0.8rem; padding: 0.35rem 0.8rem; height: fit-content;">
                            {{ strtoupper($device->type ?? 'DEVICE') }}
                        </span>
                    </div>
                </div>
                <p class="mb-0 mt-2" style="color: var(--text-secondary);" id="last-update-text">
                    <span class="live-dot me-2" id="live-dot" style="display: {{ ($isOnline ?? false) ? 'inline-block' : 'none' }};"></span>
                    @if($lastSeen)
                        Terakhir aktif: {{ \Carbon\Carbon::parse($lastSeen)->diffForHumans() }}
                    @elseif($latestData && $latestData->recorded_at)
                        Terakhir update: {{ \Carbon\Carbon::parse($latestData->recorded_at)->diffForHumans() }}
                    @else
                        Menunggu data...
                    @endif
                </p>
            </div>
            <div class="d-flex gap-2 align-items-center flex-wrap justify-content-md-end header-actions-group">
                @if($device->type !== 'smart_farm')
                    <a href="{{ isset($isAdminView) && $isAdminView ? route('admin.device.history', $device->id) : route('monitoring.history', $userDevice->id) }}" class="btn btn-history btn-action-custom">
                        <i class="bi bi-clock-history me-1"></i> <span>Riwayat Data</span>
                    </a>
                    @if($scheduleConfig ?? false)
                        <a href="{{ ($isAdminView ?? false) ? route('schedule.index', $device->id) : route('schedule.index', $userDevice->id) }}" class="btn btn-app btn-action-custom">
                            <i class="bi bi-calendar-check me-1"></i> <span>Jadwal</span>
                        </a>
                    @endif
                @endif
                @if($hasAutomation ?? false)
                    <a href="{{ ($isAdminView ?? false) ? route('automasi.index', $device->id) : route('automasi.index', $userDevice->id) }}" class="btn btn-automation btn-action-custom">
                        <i class="bi bi-cpu me-1"></i> <span>Otomasi</span>
                    </a>
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

        @if($latestData && $sensors->count() > 0)
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

                        if (str_contains($labelLower, 'suhu') || str_contains($labelLower, 'temperature')) {
                            $icon = 'bi-thermometer-half';
                            $bgColor = '#FF5733';
                            $stdUnit = '°C';
                        } elseif (str_contains($labelLower, 'kelembapan') || str_contains($labelLower, 'kelembaban') || str_contains($labelLower, 'humidity')) {
                            $icon = 'bi-droplet';
                            $bgColor = '#3498DB';
                            $stdUnit = '%';
                        } elseif (str_contains($labelLower, 'curah hujan') || str_contains($labelLower, 'rain')) {
                            $icon = 'bi-cloud-rain';
                            $bgColor = '#1ABC9C';
                            $stdUnit = 'mm';
                        } elseif (str_contains($labelLower, 'kecepatan angin') || str_contains($labelLower, 'wind speed') || str_contains($labelLower, 'wind')) {
                            $icon = 'bi-wind';
                            $bgColor = '#9B59B6';
                            $stdUnit = 'm/s';
                        } elseif (str_contains($labelLower, 'arah angin') || str_contains($labelLower, 'wind dir')) {
                            $icon = 'bi-compass';
                            $bgColor = '#34495E';
                            $stdUnit = '°';
                        } elseif (str_contains($labelLower, 'radiasi matahari') || str_contains($labelLower, 'solar') || str_contains($labelLower, 'cahaya') || str_contains($labelLower, 'lux')) {
                            $icon = 'bi-sun';
                            $bgColor = '#F39C12';
                            $stdUnit = 'W/m²';
                        } elseif (str_contains($labelLower, 'tekanan') || str_contains($labelLower, 'pressure')) {
                            $icon = 'bi-speedometer2';
                            $bgColor = '#E67E22';
                            $stdUnit = 'hPa';
                        } elseif (str_contains($labelLower, 'ph')) {
                            $icon = 'bi-droplet-half';
                            $bgColor = '#16A085';
                            $stdUnit = 'pH';
                        } elseif (str_contains($labelLower, 'tds') || str_contains($labelLower, 'ppm')) {
                            $icon = 'bi-water';
                            $bgColor = '#2980B9';
                            $stdUnit = 'ppm';
                        } elseif (str_contains($labelLower, 'ec') || str_contains($labelLower, 'konduktivitas')) {
                            $icon = 'bi-lightning';
                            $bgColor = '#8E44AD';
                            $stdUnit = 'µS/cm';
                        } elseif (str_contains($labelLower, 'water level') || str_contains($labelLower, 'tinggi air') || str_contains($labelLower, 'level air')) {
                            $icon = 'bi-water';
                            $bgColor = '#0ea5e9';
                            $stdUnit = 'cm';
                        } elseif (str_contains($labelLower, 'suhu air') || str_contains($labelLower, 'water temp')) {
                            $icon = 'bi-thermometer';
                            $bgColor = '#0284c7';
                            $stdUnit = '°C';
                        } elseif (str_contains($labelLower, 'co2')) {
                            $icon = 'bi-cloud';
                            $bgColor = '#64748b';
                            $stdUnit = 'ppm';
                        } elseif (str_contains($labelLower, 'baterai') || str_contains($labelLower, 'battery')) {
                            $icon = 'bi-battery-charging';
                            $bgColor = '#27AE60';
                            $stdUnit = '%';
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
                                    {{ $value !== null ? (is_numeric($value) ? number_format($value, 1) : $value) : '-' }}
                                </div>
                                @if($stdUnit)
                                    <div class="sensor-unit">{{ $stdUnit }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>
            </div>
        @endif

        @if($device->type === 'smart_farm')
            @php
                $isSiram = !empty($sfStatus['siram']);
                $siramBlok = $sfStatus['blok'] ?? 1;
                $siramPupuk = ($sfStatus['pupuk'] ?? '') === 'ON';
                $sisaDetik = (int) ($sfStatus['sisa'] ?? 0);
                $sisaMenit = floor($sisaDetik / 60);
                $sisaDetikMod = $sisaDetik % 60;
                $sfError = !empty($sfStatus['error']);
                $sfJam = $sfStatus['jam'] ?? null;
                $sfTimezone = $sfStatus['timezone'] ?? \Cache::get("device_timezone_{$device->id}", 'WIB');
                $jamFormatted = $sfJam && strlen((string)$sfJam) === 4 ? substr((string)$sfJam, 0, 2) . ':' . substr((string)$sfJam, 2, 2) : null;

                $sfPompa = $outputs->firstWhere('output_name', 'sf_pompa');
                $sfBlok1 = $outputs->firstWhere('output_name', 'sf_blok1');
                $sfBlok2 = $outputs->firstWhere('output_name', 'sf_blok2');
                $sfBlok3 = $outputs->firstWhere('output_name', 'sf_blok3');
                $sfPupuk = $outputs->firstWhere('output_name', 'sf_pupuk');

                $isPompaActive = (bool) (($sfPompa && $sfPompa->current_value) || $isSiram);
                $otherOutputs = $outputs->whereNotIn('output_name', ['sf_pompa', 'sf_blok1', 'sf_blok2', 'sf_blok3', 'sf_pupuk']);
            @endphp

            <!-- ==================================================== -->
            <!-- 🌿 SMART FARM CONTROL CENTER: UNIFIED ARCHITECTURE   -->
            <!-- ==================================================== -->

            @php
                $isPupukActive = (bool) (($sfPupuk && $sfPupuk->current_value) || ($isSiram && $siramPupuk));
            @endphp

            <!-- LEVEL 1: HERO STATUS & OPERATIONAL COMMAND BAR -->
            <div class="sf-hero-card mb-4" id="sf-status-card">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center justify-content-center" id="sf-icon-wrapper" style="width: 58px; height: 58px; border-radius: 18px; background: {{ ($isSiram || $isPompaActive) ? 'linear-gradient(135deg, #059669, #10b981)' : 'linear-gradient(135deg, #0284c7, #38bdf8)' }}; color: white; font-size: 1.75rem; box-shadow: 0 8px 22px -4px rgba(0,0,0,0.18);">
                            <i class="bi {{ ($isSiram || $isPompaActive) ? 'bi-droplet-fill' : 'bi-water' }}" id="sf-icon-main"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge rounded-pill {{ ($isSiram || $isPompaActive) ? 'bg-success text-white' : 'bg-secondary text-white' }}" id="sf-badge-siram" style="padding: 6px 14px; font-weight: 700; letter-spacing: 0.5px;">
                                    <i class="bi {{ ($isSiram || $isPompaActive) ? 'bi-play-circle-fill' : 'bi-pause-circle' }} me-1" id="sf-icon-badge"></i>
                                    <span id="sf-text-siram">
                                        @if($isSiram)
                                            SEDANG MENYIRAM (BLOK {{ $siramBlok }})
                                        @elseif($isPompaActive)
                                            SEDANG MENYIRAM (MANUAL)
                                        @else
                                            SIAGA (STANDBY)
                                        @endif
                                    </span>
                                </span>
                                <span class="badge rounded-pill bg-light text-muted border small d-inline-flex align-items-center gap-1 shadow-sm" id="sf-badge-jam" title="Waktu RTC alat. Klik untuk sinkronkan zona waktu!" style="cursor: pointer; transition: all 0.2s;" onclick="openRtcSyncModal()">
                                    <i class="bi bi-clock text-primary"></i>RTC: <span id="sf-text-jam">{{ $jamFormatted ? ($jamFormatted . ' ' . $sfTimezone) : '--:--' }}</span>
                                    <i class="bi bi-arrow-repeat ms-1 text-muted" id="sf-icon-sync-rtc"></i>
                                </span>
                                @if($sfError)
                                    <button type="button" class="badge rounded-pill bg-danger text-white small border-0 d-inline-flex align-items-center gap-1 shadow-sm" id="sf-badge-error" title="Klik untuk Reset Error Relay pada alat" onclick="resetRelayErrorQuick()" style="cursor: pointer;">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Error Relay (Klik Reset)
                                    </button>
                                @endif
                            </div>
                            <div class="small text-muted" id="sf-detail-siram">
                                @if($isSiram)
                                    Menyiram <strong>Blok {{ $siramBlok }}</strong> &bull; Sisa Waktu: <strong><span id="sf-sisa-waktu">{{ $sisaMenit }}m {{ $sisaDetikMod }}s</span></strong>
                                    @if($siramPupuk)
                                        &bull; <span class="text-warning fw-bold"><i class="bi bi-droplet-half me-1"></i>Pupuk Aktif</span>
                                    @endif
                                @elseif($isPompaActive)
                                    Penyiraman manual sedang berjalan aktif.
                                @else
                                    Sistem irigasi multi-zona siap. Pompa dan katup solenoid dalam kondisi siaga.
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Operation Buttons -->
                    <div class="d-flex align-items-center gap-2 flex-wrap sf-actions-group" id="sf-actions-container">
                        @if($isSiram || $isPompaActive)
                            <button type="button" id="sf-btn-stop" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 50px; padding: 0.65rem 1.4rem; font-weight: 700;" onclick="stopSiramQuick()">
                                <i class="bi bi-stop-circle-fill"></i> Stop Siram
                            </button>
                        @else
                            <button type="button" id="sf-btn-start" class="btn btn-success btn-sm d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 50px; padding: 0.65rem 1.4rem; font-weight: 700; background: linear-gradient(135deg, #10b981, #059669);" onclick="openSmartFarmSiramModal()">
                                <i class="bi bi-play-circle-fill"></i> Siram Manual
                            </button>
                        @endif
                        <a href="{{ ($isAdminView ?? false) ? route('schedule.index', $device->id) : route('schedule.index', $userDevice->id) }}" class="btn btn-glass btn-sm d-inline-flex align-items-center gap-2 shadow-sm" style="border-radius: 50px; padding: 0.65rem 1.3rem; font-weight: 600;">
                            <i class="bi bi-calendar-check text-primary"></i> Kelola Jadwal
                        </a>
                        <button type="button" class="btn btn-light btn-sm d-inline-flex align-items-center gap-1 border shadow-sm" style="border-radius: 50px; padding: 0.65rem 1.1rem; font-weight: 600; color: #4b5563;" onclick="openRtcSyncModal()" title="Sinkronkan Waktu RTC Device">
                            <i class="bi bi-clock-history text-primary"></i> Jam RTC
                        </button>
                    </div>
                </div>
            </div>

            <!-- LEVEL 2: LIVE IRRIGATION PIPELINE VISUALIZER -->
            <div class="sf-pipeline-card mb-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="small fw-bold text-muted d-flex align-items-center gap-1" style="font-size: 0.76rem; letter-spacing: 0.5px; text-transform: uppercase;">
                        <i class="bi bi-diagram-3-fill text-primary"></i> Alur Distribusi Irigasi & Fertigasi
                    </span>
                    <span class="small text-muted" style="font-size: 0.72rem;">
                        <i class="bi bi-shield-check text-success"></i> Auto-Interlock RS485
                    </span>
                </div>
                <div class="pipeline-scroll-wrapper">
                    <div class="pipeline-track">
                        <!-- Node 1: Pompa Utama -->
                        <div class="pipeline-node node-pompa {{ $isPompaActive ? 'active' : '' }}" id="pipe-node-pompa">
                            <div class="pipeline-node-icon" style="color: #0284c7; background: rgba(2, 132, 199, 0.08); border-color: rgba(2, 132, 199, 0.2);">
                                <i class="bi bi-water"></i>
                            </div>
                            <div class="pipeline-node-label">Pompa Utama</div>
                            <div class="pipeline-node-status" id="pipe-status-pompa">{{ $isPompaActive ? 'MEMOMPA' : 'OFF' }}</div>
                        </div>

                        <!-- Connector 1 -->
                        <div class="pipeline-connector {{ $isPompaActive ? 'active' : '' }}" id="pipe-conn-1"></div>

                        <!-- Node 2: Dosing Pupuk -->
                        <div class="pipeline-node node-pupuk {{ $isPupukActive ? 'active' : '' }}" id="pipe-node-pupuk">
                            <div class="pipeline-node-icon" style="color: #d97706; background: rgba(217, 119, 6, 0.08); border-color: rgba(217, 119, 6, 0.2);">
                                <i class="bi bi-flask-fill"></i>
                            </div>
                            <div class="pipeline-node-label">Injeksi Pupuk</div>
                            <div class="pipeline-node-status" id="pipe-status-pupuk">{{ $isPupukActive ? 'INJEKSI' : 'STANDBY' }}</div>
                        </div>

                        <!-- Connector 2 -->
                        <div class="pipeline-connector {{ $isPompaActive ? 'active' : '' }}" id="pipe-conn-2"></div>

                        <!-- Node 3: Blok 1 -->
                        @php
                            $isB1Flow = ($sfBlok1?->current_value && $isPompaActive) || ($isSiram && $siramBlok == 1);
                            $isB2Flow = ($sfBlok2?->current_value && $isPompaActive) || ($isSiram && $siramBlok == 2);
                            $isB3Flow = ($sfBlok3?->current_value && $isPompaActive) || ($isSiram && $siramBlok == 3);
                        @endphp
                        <div class="pipeline-node node-blok1 {{ $isB1Flow ? 'active' : '' }}" id="pipe-node-blok1">
                            <div class="pipeline-node-icon" style="color: #059669; background: rgba(16, 185, 129, 0.08); border-color: rgba(16, 185, 129, 0.2);">
                                <i class="bi bi-grid-fill"></i>
                            </div>
                            <div class="pipeline-node-label">Blok 1</div>
                            <div class="pipeline-node-status" id="pipe-status-blok1">{{ $isB1Flow ? 'MENGALIR' : ($sfBlok1?->current_value ? 'BUKA' : 'TUTUP') }}</div>
                        </div>

                        <!-- Node 4: Blok 2 -->
                        <div class="pipeline-node node-blok2 {{ $isB2Flow ? 'active' : '' }}" id="pipe-node-blok2">
                            <div class="pipeline-node-icon" style="color: #0284c7; background: rgba(14, 165, 233, 0.08); border-color: rgba(14, 165, 233, 0.2);">
                                <i class="bi bi-grid-fill"></i>
                            </div>
                            <div class="pipeline-node-label">Blok 2</div>
                            <div class="pipeline-node-status" id="pipe-status-blok2">{{ $isB2Flow ? 'MENGALIR' : ($sfBlok2?->current_value ? 'BUKA' : 'TUTUP') }}</div>
                        </div>

                        <!-- Node 5: Blok 3 -->
                        <div class="pipeline-node node-blok3 {{ $isB3Flow ? 'active' : '' }}" id="pipe-node-blok3">
                            <div class="pipeline-node-icon" style="color: #7c3aed; background: rgba(139, 92, 246, 0.08); border-color: rgba(139, 92, 246, 0.2);">
                                <i class="bi bi-grid-fill"></i>
                            </div>
                            <div class="pipeline-node-label">Blok 3</div>
                            <div class="pipeline-node-status" id="pipe-status-blok3">{{ $isB3Flow ? 'MENGALIR' : ($sfBlok3?->current_value ? 'BUKA' : 'TUTUP') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LEVEL 3: DUA HUB KONTROL UTAMA (SIDE-BY-SIDE GRID) -->
            <div class="row g-4 mb-4">
                <!-- ============================================== -->
                <!-- HUB 1: SUPLAI AIR & FERTIGASI (KOLOM KIRI - 5)  -->
                <!-- ============================================== -->
                <div class="col-12 col-lg-5">
                    <div class="sf-hub-card h-100">
                        <div class="sf-hub-header">
                            <div>
                                <h6 class="sf-hub-title">
                                    <i class="bi bi-droplet-half text-primary"></i> Suplai Air & Fertigasi
                                </h6>
                                <div class="sf-hub-subtitle">
                                    Kontrol pompa air utama dan injeksi nutrisi cair
                                </div>
                            </div>
                            <span class="badge rounded-pill bg-light text-muted border px-2 py-1 small">2 Unit</span>
                        </div>

                        <!-- 1.1 POMPA UTAMA (AIR) -->
                        @if($sfPompa)
                        <div class="sf-actuator-card {{ $isPompaActive ? 'active-pump' : '' }}" id="output-card-{{ $sfPompa->id }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="sf-actuator-icon" style="background: {{ $isPompaActive ? 'linear-gradient(135deg, #0284c7, #38bdf8)' : 'rgba(2, 132, 199, 0.1)' }}; color: {{ $isPompaActive ? '#ffffff' : '#0284c7' }}; border: 1px solid {{ $isPompaActive ? '#0284c7' : 'rgba(2, 132, 199, 0.2)' }};">
                                        <i class="bi bi-water"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">Pompa Utama</div>
                                        <div class="text-muted" style="font-size: 0.76rem;">Suplai air utama ke pipa</div>
                                    </div>
                                </div>
                                <span id="output-status-{{ $sfPompa->id }}" class="output-status {{ $isPompaActive ? 'on' : 'off' }} badge rounded-pill px-2.5 py-1 small m-0" data-on-text="MEMOMPA" data-off-text="OFF">
                                    {{ $isPompaActive ? 'MEMOMPA' : 'OFF' }}
                                </span>
                            </div>

                            <div class="segmented-control mt-2">
                                <button type="button" class="segmented-btn {{ $isPompaActive ? 'active-on' : '' }}" onclick="openSmartFarmSiramModal()" id="btn-on-{{ $sfPompa->id }}">
                                    <i class="bi bi-power me-1"></i> ON
                                </button>
                                <button type="button" class="segmented-btn {{ !$isPompaActive ? 'active-off' : '' }}" onclick="stopSmartFarmSiram()" id="btn-off-{{ $sfPompa->id }}">
                                    OFF
                                </button>
                            </div>
                        </div>
                        @endif

                        <!-- 1.2 INJEKSI PUPUK CAIR -->
                        @if($sfPupuk)
                        @php
                            $isPupukOn = (bool) ($sfPupuk->current_value ?? 0);
                        @endphp
                        <div class="sf-actuator-card {{ $isPupukOn ? 'active-dosing' : '' }}" id="output-card-{{ $sfPupuk->id }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="sf-actuator-icon" style="background: {{ $isPupukOn ? 'linear-gradient(135deg, #d97706, #f59e0b)' : 'rgba(217, 119, 6, 0.1)' }}; color: {{ $isPupukOn ? '#ffffff' : '#d97706' }}; border: 1px solid {{ $isPupukOn ? '#f59e0b' : 'rgba(217, 119, 6, 0.2)' }};">
                                        <i class="bi bi-flask-fill"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">Injeksi Pupuk</div>
                                        <div class="text-muted" style="font-size: 0.76rem;"><i class="bi bi-shield-check text-warning me-1"></i>Dosing nutrisi ke pipa</div>
                                    </div>
                                </div>
                                <span id="output-status-{{ $sfPupuk->id }}" class="output-status {{ $isPupukOn ? 'on' : 'off' }} badge rounded-pill px-2.5 py-1 small m-0" data-on-text="INJEKSI AKTIF" data-off-text="STANDBY">
                                    {{ $isPupukOn ? 'INJEKSI AKTIF' : 'STANDBY' }}
                                </span>
                            </div>

                            <div class="segmented-control mt-2">
                                <button type="button" class="segmented-btn {{ $isPupukOn ? 'active-on' : '' }}" onclick="setOutput({{ $sfPupuk->id }}, true)" id="btn-on-{{ $sfPupuk->id }}">
                                    <i class="bi bi-power me-1"></i> ON
                                </button>
                                <button type="button" class="segmented-btn {{ !$isPupukOn ? 'active-off' : '' }}" onclick="setOutput({{ $sfPupuk->id }}, false)" id="btn-off-{{ $sfPupuk->id }}">
                                    OFF
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- ==================================================== -->
                <!-- HUB 2: DISTRIBUSI ZONA LAHAN (KOLOM KANAN - 7)       -->
                <!-- ==================================================== -->
                <div class="col-12 col-lg-7">
                    <div class="sf-hub-card h-100">
                        <div class="sf-hub-header">
                            <div>
                                <h6 class="sf-hub-title">
                                    <i class="bi bi-grid-3x3-gap-fill text-success"></i> Distribusi Zona Lahan (Katup Solenoid)
                                </h6>
                                <div class="sf-hub-subtitle">
                                    Buka katup untuk mengalirkan air ke blok kebun yang dituju
                                </div>
                            </div>
                            <span class="badge rounded-pill bg-light text-muted border px-2 py-1 small">3 Zona Lahan</span>
                        </div>

                        <div class="row g-3 h-100">
                            @php
                                $blocks = [
                                    ['num' => 1, 'obj' => $sfBlok1, 'color' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.12)', 'border' => '#10b981'],
                                    ['num' => 2, 'obj' => $sfBlok2, 'color' => '#0ea5e9', 'bg' => 'rgba(14, 165, 233, 0.12)', 'border' => '#0ea5e9'],
                                    ['num' => 3, 'obj' => $sfBlok3, 'color' => '#8b5cf6', 'bg' => 'rgba(139, 92, 246, 0.12)', 'border' => '#8b5cf6'],
                                ];
                            @endphp

                            @foreach($blocks as $b)
                                @php
                                    $blk = $b['obj'];
                                    $isBlkOn = (bool) ($blk?->current_value ?? 0);
                                    $isFlowing = ($isBlkOn && $isPompaActive) || ($isSiram && $siramBlok == $b['num']);
                                @endphp
                                <div class="col-12 col-sm-6 col-xl-4">
                                    <div class="sf-zone-card {{ $isFlowing ? 'active-flow' : '' }}" id="output-card-{{ $blk?->id }}">
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="sf-zone-badge-num" style="background: {{ $b['bg'] }}; color: {{ $b['color'] }};">
                                                        {{ $b['num'] }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold text-dark" style="font-size: 0.92rem;">Blok {{ $b['num'] }}</div>
                                                        <div class="text-muted" style="font-size: 0.72rem;">Zona {{ $b['num'] }}</div>
                                                    </div>
                                                </div>

                                                <span class="sf-flow-indicator" id="sf-flow-blok{{ $b['num'] }}" style="display: {{ $isFlowing ? 'inline-flex' : 'none' }};">
                                                    <i class="bi bi-droplet-fill"></i> Mengalir
                                                </span>
                                            </div>

                                            <div class="d-flex align-items-center justify-content-between p-2 px-2.5 rounded-3 mb-2.5" style="background: #f9fafb; border: 1px solid #f3f4f6;">
                                                <span class="text-muted fw-semibold" style="font-size: 0.74rem;">Status Katup:</span>
                                                <span class="output-status {{ $isBlkOn ? 'on' : 'off' }} fw-bold m-0"
                                                    id="output-status-{{ $blk?->id }}"
                                                    data-on-text="TERBUKA"
                                                    data-off-text="TERTUTUP"
                                                    style="letter-spacing: 0.3px; font-size: 0.74rem;">
                                                    {{ $isBlkOn ? 'TERBUKA' : 'TERTUTUP' }}
                                                </span>
                                            </div>
                                        </div>

                                        <div>
                                            @if($blk)
                                            <div class="segmented-control mb-2">
                                                <button type="button" class="segmented-btn {{ $isBlkOn ? 'active-on' : '' }}"
                                                    onclick="setOutput({{ $blk->id }}, true)" id="btn-on-{{ $blk->id }}">
                                                    BUKA
                                                </button>
                                                <button type="button" class="segmented-btn {{ !$isBlkOn ? 'active-off' : '' }}"
                                                    onclick="setOutput({{ $blk->id }}, false)" id="btn-off-{{ $blk->id }}">
                                                    TUTUP
                                                </button>
                                            </div>
                                            @endif

                                            <button type="button" class="btn btn-sm btn-outline-success w-100 rounded-pill py-1.5 d-flex align-items-center justify-content-center gap-1"
                                                style="font-size: 0.78rem; font-weight: 600;"
                                                onclick="openSmartFarmSiramModal({{ $b['num'] }})">
                                                <i class="bi bi-play-circle"></i> Siram Blok {{ $b['num'] }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Output Tambahan Lainnya (Jika Ada) -->
            @if($otherOutputs->count() > 0)
            <div class="output-panel mb-4">
                <h5 class="card-title mb-3" style="color: var(--text-main);">
                    <i class="bi bi-sliders me-2 text-primary"></i>Output Tambahan Lainnya
                </h5>
                <div class="row g-4">
                    @foreach($otherOutputs as $output)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="output-card" id="output-card-{{ $output->id }}">
                                <div class="card-header-flex">
                                    <div class="output-icon" style="background: {{ $output->color }};">
                                        <i class="bi {{ $output->icon }}"></i>
                                    </div>
                                    <div class="output-label">{{ $output->output_label }}</div>
                                </div>
                                <div class="output-status {{ $output->current_value ? 'on' : 'off' }}" id="output-status-{{ $output->id }}">
                                    {{ $output->current_value ? 'ON' : 'OFF' }}
                                </div>
                                <div class="segmented-control">
                                    <button type="button" class="segmented-btn {{ $output->current_value ? 'active-on' : '' }}" onclick="setOutput({{ $output->id }}, true)" id="btn-on-{{ $output->id }}">ON</button>
                                    <button type="button" class="segmented-btn {{ !$output->current_value ? 'active-off' : '' }}" onclick="setOutput({{ $output->id }}, false)" id="btn-off-{{ $output->id }}">OFF</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

        @else
            <!-- Standard Output Control Panel for Other Devices -->
            @if($outputs->count() > 0)
                <div class="output-panel">
                    <h5 class="card-title mb-4" style="color: var(--text-main);">
                        <i class="bi bi-sliders me-2 text-primary"></i>Kontrol Output
                    </h5>
                    <div class="row g-4">
                        @php
                            $sortedOutputs = $outputs->where('output_type', '!=', 'multi_zone')->sortBy(function ($output) {
                                $name = strtolower($output->output_name);
                                if (in_array($name, ['st_bak', 'st_ppk'])) return 100;
                                if (str_starts_with($name, 'st_')) return 95;
                                if (str_contains($name, 'pompa') || str_contains($name, 'pump') && !str_contains($name, 'ab') && !str_contains($name, 'ph')) return 10;
                                if (str_contains($name, 'pump_ab') || str_contains($name, 'dosing')) return 20;
                                if (str_contains($name, 'ph_up') || str_contains($name, 'ph1')) return 30;
                                if (str_contains($name, 'ph_down') || str_contains($name, 'ph2')) return 31;
                                if (str_contains($name, 'mist')) return 50;
                                if (str_contains($name, 'fan')) return 51;
                                if (str_contains($name, 'air')) return 52;
                                if (str_contains($name, 'lamp')) return 53;
                                if (str_contains($name, 'mix')) return 54;
                                return 99;
                            })->values();

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
                                            <div class="output-status {{ $output->current_value ? 'on' : 'off' }}" id="output-status-{{ $output->id }}">
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
                                                $isDosingPump = str_contains($outName, 'pump_ab') || str_contains($outName, 'dosing') || $outName === 'st_dos';
                                                $isPhUp = str_contains($outName, 'ph_up') || str_contains($outName, 'ph1') || $outName === 'st_ph_u';
                                                $isPhDown = str_contains($outName, 'ph_down') || str_contains($outName, 'ph2') || $outName === 'st_ph_d';
                                                $isModalPump = $isPhUp || $isPhDown || $isDosingPump;
                                                $pumpType = $isDosingPump ? 'dosing' : ($isPhUp ? 'ph_up' : ($isPhDown ? 'ph_down' : ''));
                                            @endphp
                                            <div class="output-status {{ $output->current_value ? 'on' : 'off' }}"
                                                id="output-status-{{ $output->id }}"
                                                data-on-text="{{ $onText }}"
                                                data-off-text="{{ $offText }}">
                                                {{ $output->current_value ? $onText : $offText }}
                                            </div>
                                            
                                            @if($isModalPump)
                                                <div class="segmented-control">
                                                    <button type="button" class="segmented-btn {{ $output->current_value ? 'active-on' : '' }}"
                                                        onclick="openPhControlModal({{ $output->id }}, '{{ $pumpType }}')" id="btn-on-{{ $output->id }}">
                                                        ON
                                                    </button>
                                                    <button type="button" class="segmented-btn {{ !$output->current_value ? 'active-off' : '' }}"
                                                        onclick="setOutput({{ $output->id }}, false)" id="btn-off-{{ $output->id }}">
                                                        OFF
                                                    </button>
                                                </div>
                                            @else
                                                <div class="segmented-control">
                                                    <button type="button" class="segmented-btn {{ $output->current_value ? 'active-on' : '' }}"
                                                        onclick="setOutput({{ $output->id }}, true)" id="btn-on-{{ $output->id }}">
                                                        {{ $onText }}
                                                    </button>
                                                    <button type="button" class="segmented-btn {{ !$output->current_value ? 'active-off' : '' }}"
                                                        onclick="setOutput({{ $output->id }}, false)" id="btn-off-{{ $output->id }}">
                                                        {{ $offText }}
                                                    </button>
                                                </div>
                                            @endif
                                        @endif
                                    @else
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
        @endif

        @if(!$latestData && $device->type !== 'smart_farm')
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




    @if(true)
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

        @if($device->type === 'smart_farm')
        <!-- Smart Farm: Siram Manual Modal (Bottom Sheet Style) -->
        <div class="modal fade" id="sfSiramModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content modal-content-glass">
                    <div class="modal-handle"></div>
                    <div class="modal-header-custom">
                        <h5 style="font-weight: 800; color: #1f2937;">
                            <i class="bi bi-droplet-half me-2" style="color: #10b981;"></i>Siram Manual (Irigasi)
                        </h5>
                        <div class="subtitle" style="font-size: 0.85rem; color: #6b7280;">Buka katup blok terlebih dahulu, lalu nyalakan pompa utama</div>
                    </div>
                    <div class="modal-body-custom">
                        <!-- VIEW 1: FORM PEMILIHAN BLOK -->
                        <div id="sf-form-view">
                            <label class="form-label fw-bold mb-2" style="color: #374151; font-size: 0.9rem;">
                                <i class="bi bi-geo-alt-fill me-1 text-success"></i> Pilih Blok yang Ingin Disiram:
                            </label>
                            
                            <div class="d-flex flex-column gap-2 mb-3">
                                <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between sf-blok-card" style="cursor: pointer;" for="sf-blok-1">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(16, 185, 129, 0.15); color: #059669; font-weight: 700;">1</div>
                                        <div>
                                            <div class="fw-bold text-dark">Blok 1 (Zona 1)</div>
                                            <div class="small text-muted">Buka katup solenoid blok 1</div>
                                        </div>
                                    </div>
                                    <input class="form-check-input fs-5 m-0" type="radio" name="sf_target_blok" id="sf-blok-1" value="1" checked>
                                </label>

                                <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between sf-blok-card" style="cursor: pointer;" for="sf-blok-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(14, 165, 233, 0.15); color: #0284c7; font-weight: 700;">2</div>
                                        <div>
                                            <div class="fw-bold text-dark">Blok 2 (Zona 2)</div>
                                            <div class="small text-muted">Buka katup solenoid blok 2</div>
                                        </div>
                                    </div>
                                    <input class="form-check-input fs-5 m-0" type="radio" name="sf_target_blok" id="sf-blok-2" value="2">
                                </label>

                                <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between sf-blok-card" style="cursor: pointer;" for="sf-blok-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: rgba(139, 92, 246, 0.15); color: #7c3aed; font-weight: 700;">3</div>
                                        <div>
                                            <div class="fw-bold text-dark">Blok 3 (Zona 3)</div>
                                            <div class="small text-muted">Buka katup solenoid blok 3</div>
                                        </div>
                                    </div>
                                    <input class="form-check-input fs-5 m-0" type="radio" name="sf_target_blok" id="sf-blok-3" value="3">
                                </label>
                            </div>

                            <!-- Pilihan Durasi -->
                            <div class="mb-3">
                                <label class="form-label fw-bold mb-2" style="color: #374151; font-size: 0.9rem;">
                                    <i class="bi bi-hourglass-split me-1 text-primary"></i> Durasi Penyiraman:
                                </label>
                                <div class="sf-dur-grid">
                                    <label class="sf-dur-pill" for="sf-dur-3">
                                        <input type="radio" name="sf_duration" id="sf-dur-3" value="3" class="d-none">
                                        <span class="dur-num">3</span>
                                        <span class="dur-unit">Menit</span>
                                    </label>
                                    <label class="sf-dur-pill" for="sf-dur-5">
                                        <input type="radio" name="sf_duration" id="sf-dur-5" value="5" class="d-none" checked>
                                        <span class="dur-num">5</span>
                                        <span class="dur-unit">Menit</span>
                                    </label>
                                    <label class="sf-dur-pill" for="sf-dur-10">
                                        <input type="radio" name="sf_duration" id="sf-dur-10" value="10" class="d-none">
                                        <span class="dur-num">10</span>
                                        <span class="dur-unit">Menit</span>
                                    </label>
                                    <label class="sf-dur-pill" for="sf-dur-15">
                                        <input type="radio" name="sf_duration" id="sf-dur-15" value="15" class="d-none">
                                        <span class="dur-num">15</span>
                                        <span class="dur-unit">Menit</span>
                                    </label>
                                    <label class="sf-dur-pill" for="sf-dur-30">
                                        <input type="radio" name="sf_duration" id="sf-dur-30" value="30" class="d-none">
                                        <span class="dur-num">30</span>
                                        <span class="dur-unit">Menit</span>
                                    </label>
                                    <label class="sf-dur-pill" for="sf-dur-0">
                                        <input type="radio" name="sf_duration" id="sf-dur-0" value="0" class="d-none">
                                        <span class="dur-num"><i class="bi bi-infinity"></i></span>
                                        <span class="dur-unit">Manual</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Opsi Pupuk -->
                            <div class="d-flex align-items-center justify-content-between p-3 mb-3" style="background: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.25); border-radius: 16px;">
                                <div>
                                    <div style="font-weight: 700; color: #92400e; font-size: 0.92rem;">
                                        <i class="bi bi-droplet-half me-1"></i> Sertakan Pompa Pupuk
                                    </div>
                                    <div style="font-size: 0.78rem; color: #78350f;">
                                        Nyalakan injeksi pupuk bersamaan dengan pompa utama
                                    </div>
                                </div>
                                <div class="form-check form-switch m-0 fs-4">
                                    <input class="form-check-input" type="checkbox" id="sf-siram-pupuk" style="cursor: pointer;">
                                </div>
                            </div>

                            <div class="modal-actions">
                                <button type="button" class="btn" onclick="executeSmartFarmSiram()"
                                    style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 14px; padding: 0.8rem; font-size: 1rem; font-weight: 700; width: 100%; color: #fff; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);">
                                    <i class="bi bi-play-circle-fill me-2"></i> Mulai Menyiram
                                </button>
                                <button type="button" class="btn mt-2"
                                    style="background: #f3f4f6; color: #6b7280; border-radius: 12px; padding: 0.65rem; font-size: 0.95rem; font-weight: 600; width: 100%; border: none;"
                                    data-bs-dismiss="modal">Batal</button>
                            </div>
                        </div>

                        <!-- VIEW 2: LOADING & SEQUENCE ANIMATION -->
                        <div id="sf-loading-view" style="display: none;" class="py-3">
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <div class="spinner-grow text-success" style="width: 64px; height: 64px;" role="status"></div>
                                    <i class="bi bi-water position-absolute top-50 start-50 translate-middle fs-3 text-success" id="sf-status-icon"></i>
                                </div>
                                <h6 class="fw-bold mt-3 mb-1" id="sf-status-headline" style="color: #1f2937;">Menyiapkan Siram Manual...</h6>
                                <p class="small text-muted mb-0" id="sf-status-subline">Urutan: Blok dibuka dulu &rarr; Pompa utama menyala</p>
                            </div>

                            <!-- Progress Bar -->
                            <div class="progress mb-4" style="height: 8px; border-radius: 10px; background: rgba(0,0,0,0.05);">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" id="sf-progress-bar" role="progressbar" style="width: 0%; transition: width 0.6s ease;"></div>
                            </div>

                            <!-- Step Items -->
                            <div class="d-flex flex-column gap-2">
                                <div id="sf-step-1" class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light border">
                                    <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                                    <div>
                                        <div class="fw-bold text-dark">Langkah 1: Membuka Katup Blok <span id="sf-target-blok-label">1</span></div>
                                        <div class="small text-muted">Mengaktifkan solenoid katup via MQTT...</div>
                                    </div>
                                </div>

                                <div id="sf-step-2" class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light border opacity-50">
                                    <i class="bi bi-circle text-muted fs-5"></i>
                                    <div>
                                        <div class="fw-bold text-dark">Langkah 2: Menyalakan Pompa Utama</div>
                                        <div class="small text-muted">Menunggu katup siap sebelum pompa menyala...</div>
                                    </div>
                                </div>

                                <div id="sf-step-3" class="d-flex align-items-center gap-3 p-3 rounded-3 bg-light border opacity-50" style="display: none;">
                                    <i class="bi bi-circle text-muted fs-5"></i>
                                    <div>
                                        <div class="fw-bold text-dark">Langkah 3: Menyalakan Pompa Pupuk</div>
                                        <div class="small text-muted">Injeksi nutrisi pupuk...</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Sinkronisasi Zona Waktu RTC (WIB / WITA / WIT) -->
        <div class="modal fade" id="sfRtcSyncModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content modal-content-glass">
                    <div class="modal-handle"></div>
                    <div class="modal-header-custom">
                        <h5 style="font-weight: 800; color: #1f2937;">
                            <i class="bi bi-clock-history me-2" style="color: #0ea5e9;"></i>Sinkronkan Waktu RTC Alat
                        </h5>
                        <div class="subtitle" style="font-size: 0.85rem; color: #6b7280;">Pilih zona waktu sesuai lokasi kebun/alat Anda</div>
                    </div>
                    <div class="modal-body-custom">
                        <label class="form-label fw-bold mb-2" style="color: #374151; font-size: 0.9rem;">
                            <i class="bi bi-geo-alt-fill me-1 text-primary"></i> Zona Waktu Lokasi Alat:
                        </label>

                        <div class="d-flex flex-column gap-2 mb-3">
                            <!-- WIB -->
                            <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between sf-tz-card" style="cursor: pointer;" for="sf-tz-wib">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(14, 165, 233, 0.15); color: #0284c7; font-weight: 800; font-size: 0.85rem;">WIB</div>
                                    <div>
                                        <div class="fw-bold text-dark">WIB (UTC+7)</div>
                                        <div class="small text-muted">Sumatera, Jawa, Kalbar, Kalteng</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark border px-2 py-1 fs-6 fw-bold" id="sf-tz-clock-wib">--:--:--</span>
                                    <input class="form-check-input fs-5 m-0" type="radio" name="sf_target_tz" id="sf-tz-wib" value="WIB" {{ ($sfTimezone ?? 'WIB') === 'WIB' ? 'checked' : '' }}>
                                </div>
                            </label>

                            <!-- WITA -->
                            <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between sf-tz-card" style="cursor: pointer;" for="sf-tz-wita">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(16, 185, 129, 0.15); color: #059669; font-weight: 800; font-size: 0.85rem;">WITA</div>
                                    <div>
                                        <div class="fw-bold text-dark">WITA (UTC+8)</div>
                                        <div class="small text-muted">Bali, NTB, NTT, Kalimantan, Sulawesi</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark border px-2 py-1 fs-6 fw-bold" id="sf-tz-clock-wita">--:--:--</span>
                                    <input class="form-check-input fs-5 m-0" type="radio" name="sf_target_tz" id="sf-tz-wita" value="WITA" {{ ($sfTimezone ?? '') === 'WITA' ? 'checked' : '' }}>
                                </div>
                            </label>

                            <!-- WIT -->
                            <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between sf-tz-card" style="cursor: pointer;" for="sf-tz-wit">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; background: rgba(139, 92, 246, 0.15); color: #7c3aed; font-weight: 800; font-size: 0.85rem;">WIT</div>
                                    <div>
                                        <div class="fw-bold text-dark">WIT (UTC+9)</div>
                                        <div class="small text-muted">Maluku, Papua</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-light text-dark border px-2 py-1 fs-6 fw-bold" id="sf-tz-clock-wit">--:--:--</span>
                                    <input class="form-check-input fs-5 m-0" type="radio" name="sf_target_tz" id="sf-tz-wit" value="WIT" {{ ($sfTimezone ?? '') === 'WIT' ? 'checked' : '' }}>
                                </div>
                            </label>
                        </div>

                        <div class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-3" style="border-radius: 12px; background: rgba(14, 165, 233, 0.08); border: 1px solid rgba(14, 165, 233, 0.2); color: #0369a1;">
                            <i class="bi bi-info-circle-fill fs-5"></i>
                            <div>Waktu RTC alat akan disesuaikan secara presisi dengan zona waktu yang Anda pilih di atas.</div>
                        </div>

                        <div class="modal-actions">
                            <button type="button" class="btn" id="btn-submit-rtc-sync" onclick="sendRtcSync()"
                                style="background: linear-gradient(135deg, #0ea5e9, #0284c7); border-radius: 14px; padding: 0.8rem; font-size: 1rem; font-weight: 700; width: 100%; color: #fff; box-shadow: 0 4px 14px rgba(14, 165, 233, 0.35);">
                                <i class="bi bi-send-check-fill me-2"></i> Kirim Waktu ke Alat
                            </button>
                            <button type="button" class="btn mt-2"
                                style="background: #f3f4f6; color: #6b7280; border-radius: 12px; padding: 0.65rem; font-size: 0.95rem; font-weight: 600; width: 100%; border: none;"
                                data-bs-dismiss="modal">Batal</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <script>
            // Setup CSRF token for AJAX requests
            const csrfToken = '{{ csrf_token() }}';
            const isAdminView = {{ ($isAdminView ?? false) ? 'true' : 'false' }};
            const deviceId = {{ $device->id ?? 'null' }};
            const userDeviceId = {{ $userDevice->id ?? 'null' }};
            
            function getBaseUrl() {
                return isAdminView ? `/admin/device/${deviceId}` : `/monitoring/device/${userDeviceId}`;
            }

            // Smart Farm Output IDs Mapping
            const sfOutputMap = {
                pompa: {{ $outputs->firstWhere('output_name', 'sf_pompa')?->id ?? 'null' }},
                blok1: {{ $outputs->firstWhere('output_name', 'sf_blok1')?->id ?? 'null' }},
                blok2: {{ $outputs->firstWhere('output_name', 'sf_blok2')?->id ?? 'null' }},
                blok3: {{ $outputs->firstWhere('output_name', 'sf_blok3')?->id ?? 'null' }},
                pupuk: {{ $outputs->firstWhere('output_name', 'sf_pupuk')?->id ?? 'null' }},
            };

            // Async setOutput for sequential automation
            async function setOutputAsync(outputId, isOn) {
                const url = getBaseUrl() + `/output/${outputId}/toggle`;
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ value: isOn })
                });
                const data = await response.json();
                if (data.success) {
                    setOptimisticUI(outputId, isOn);
                }
                return data;
            }

            let manualSiramTimer = null;
            let manualSiramInterval = null;

            // Open Smart Farm Siram Manual Modal
            function openSmartFarmSiramModal(targetBlok = null) {
                const formView = document.getElementById('sf-form-view');
                const loadingView = document.getElementById('sf-loading-view');
                const progressBar = document.getElementById('sf-progress-bar');
                if (formView) formView.style.display = 'block';
                if (loadingView) loadingView.style.display = 'none';
                if (progressBar) progressBar.style.width = '0%';

                if (targetBlok) {
                    const radio = document.getElementById(`sf-blok-${targetBlok}`);
                    if (radio) radio.checked = true;
                }

                const modal = new bootstrap.Modal(document.getElementById('sfSiramModal'));
                modal.show();
            }

            // Execute Smart Farm Siram Sequence: Blok ON -> Loading Delay -> Pompa ON
            async function executeSmartFarmSiram() {
                const selectedBlok = document.querySelector('input[name="sf_target_blok"]:checked')?.value || 1;
                const selectedDurasi = parseInt(document.querySelector('input[name="sf_duration"]:checked')?.value || 5);
                const includePupuk = document.getElementById('sf-siram-pupuk')?.checked || false;

                const blokOutputId = sfOutputMap[`blok${selectedBlok}`];
                const pompaOutputId = sfOutputMap['pompa'];
                const pupukOutputId = sfOutputMap['pupuk'];

                if (!pompaOutputId || !blokOutputId) {
                    alert('Data output Smart Farm belum lengkap di sistem.');
                    return;
                }

                // Switch view to loading animation
                document.getElementById('sf-form-view').style.display = 'none';
                document.getElementById('sf-loading-view').style.display = 'block';
                const blokLabel = document.getElementById('sf-target-blok-label');
                if (blokLabel) blokLabel.innerText = selectedBlok;

                const step1 = document.getElementById('sf-step-1');
                const step2 = document.getElementById('sf-step-2');
                const step3 = document.getElementById('sf-step-3');
                const progressBar = document.getElementById('sf-progress-bar');
                const statusIcon = document.getElementById('sf-status-icon');
                const statusHeadline = document.getElementById('sf-status-headline');
                const statusSubline = document.getElementById('sf-status-subline');

                try {
                    // --- STEP 1: Buka Katup Solenoid Blok ---
                    step1.classList.remove('opacity-50');
                    step1.innerHTML = `
                        <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                        <div>
                            <div class="fw-bold text-dark">Langkah 1: Membuka Katup Blok ${selectedBlok}...</div>
                            <div class="small text-muted">Mengaktifkan solenoid katup via MQTT</div>
                        </div>
                    `;
                    progressBar.style.width = '25%';

                    await setOutputAsync(blokOutputId, true);

                    step1.innerHTML = `
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div>
                            <div class="fw-bold text-success">Langkah 1: Katup Blok ${selectedBlok} Berhasil Dibuka</div>
                            <div class="small text-muted">Jalur pipa terbuka & siap dialiri</div>
                        </div>
                    `;
                    progressBar.style.width = '50%';

                    // Animasi jeda sebentar (1.2 detik)
                    statusHeadline.innerText = 'Menunggu aliran siap...';
                    statusSubline.innerText = 'Memberi jeda agar katup terbuka sempurna';
                    await new Promise(r => setTimeout(r, 1200));

                    // --- STEP 2: Nyalakan Pompa Utama ---
                    step2.classList.remove('opacity-50');
                    step2.innerHTML = `
                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        <div>
                            <div class="fw-bold text-dark">Langkah 2: Menyalakan Pompa Utama...</div>
                            <div class="small text-muted">Mengalirkan air ke Blok ${selectedBlok}</div>
                        </div>
                    `;
                    progressBar.style.width = '75%';

                    await setOutputAsync(pompaOutputId, true);

                    step2.innerHTML = `
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div>
                            <div class="fw-bold text-success">Langkah 2: Pompa Utama Menyala</div>
                            <div class="small text-muted">Air sedang mengalir ke Blok ${selectedBlok}</div>
                        </div>
                    `;

                    // --- STEP 3 (Opsional): Nyalakan Pompa Pupuk ---
                    if (includePupuk && pupukOutputId) {
                        step3.style.display = 'flex';
                        step3.classList.remove('opacity-50');
                        step3.innerHTML = `
                            <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                            <div>
                                <div class="fw-bold text-dark">Langkah 3: Menyalakan Pompa Pupuk...</div>
                                <div class="small text-muted">Mengaktifkan injeksi nutrisi</div>
                            </div>
                        `;
                        await new Promise(r => setTimeout(r, 800));
                        await setOutputAsync(pupukOutputId, true);
                        step3.innerHTML = `
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                            <div>
                                <div class="fw-bold text-success">Langkah 3: Pompa Pupuk Aktif</div>
                                <div class="small text-muted">Injeksi nutrisi berjalan</div>
                            </div>
                        `;
                    }

                    progressBar.style.width = '100%';
                    statusIcon.className = 'bi bi-check-circle-fill position-absolute top-50 start-50 translate-middle fs-3 text-success';
                    statusHeadline.innerText = `Penyiraman Blok ${selectedBlok} Berhasil Dijalankan! 🎉`;
                    statusSubline.innerText = selectedDurasi > 0 ? `Durasi: ${selectedDurasi} menit (otomatis stop)` : 'Penyiraman manual tanpa batas waktu.';

                    // Durasi dalam detik
                    const durasiDetik = selectedDurasi * 60;

                    // Update UI live status card
                    updateSmartFarmLiveStatus({
                        siram: 1,
                        blok: parseInt(selectedBlok),
                        pupuk: includePupuk ? 'ON' : 'NONE',
                        sisa: durasiDetik
                    });

                    // Update flow indicators pada kartu zona blok
                    [1, 2, 3].forEach(num => {
                        const flowBadge = document.getElementById(`sf-flow-blok${num}`);
                        if (flowBadge) flowBadge.style.display = (num == selectedBlok) ? 'inline-flex' : 'none';
                        const outId = sfOutputMap[`blok${num}`];
                        const cardEl = document.getElementById(`output-card-${outId}`);
                        if (cardEl) {
                            if (num == selectedBlok) cardEl.classList.add('active-flow');
                            else cardEl.classList.remove('active-flow');
                        }
                    });

                    // Countdown & Auto-Stop timer jika durasi > 0
                    if (manualSiramTimer) clearTimeout(manualSiramTimer);
                    if (manualSiramInterval) clearInterval(manualSiramInterval);

                    if (durasiDetik > 0) {
                        let remainingSec = durasiDetik;
                        manualSiramInterval = setInterval(() => {
                            remainingSec--;
                            if (remainingSec <= 0) {
                                clearInterval(manualSiramInterval);
                            } else {
                                const m = Math.floor(remainingSec / 60);
                                const s = remainingSec % 60;
                                const sisaEl = document.getElementById('sf-sisa-waktu');
                                if (sisaEl) sisaEl.innerText = `${m}m ${s}s`;
                            }
                        }, 1000);

                        manualSiramTimer = setTimeout(() => {
                            stopSmartFarmSiram(false);
                        }, durasiDetik * 1000);
                    }

                    await new Promise(r => setTimeout(r, 1200));
                    const modalEl = document.getElementById('sfSiramModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();

                } catch (err) {
                    console.error('Siram manual error:', err);
                    alert('Gagal memulai penyiraman: ' + err.message);
                    document.getElementById('sf-form-view').style.display = 'block';
                    document.getElementById('sf-loading-view').style.display = 'none';
                }
            }

            // Stop Smart Farm Siram (Matikan Pompa & Blok)
            async function stopSmartFarmSiram(askConfirm = true) {
                if (askConfirm && !confirm('Hentikan penyiraman dan matikan Pompa Utama?')) return;
                if (manualSiramTimer) clearTimeout(manualSiramTimer);
                if (manualSiramInterval) clearInterval(manualSiramInterval);

                [1, 2, 3].forEach(num => {
                    const flowBadge = document.getElementById(`sf-flow-blok${num}`);
                    if (flowBadge) flowBadge.style.display = 'none';
                    const outId = sfOutputMap[`blok${num}`];
                    const cardEl = document.getElementById(`output-card-${outId}`);
                    if (cardEl) cardEl.classList.remove('active-flow');
                });

                await stopSiramQuick(false);
            }

            // Set output ON/OFF (for buttons)
            function setOutput(outputId, isOn) {
                const url = getBaseUrl() + `/output/${outputId}/toggle`;

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
                const url = isAdminView 
                    ? `/admin/device/${deviceId}/output/${outputId}/irrigation-pump`
                    : `/monitoring/device/${userDeviceId}/output/${outputId}/irrigation-pump`;

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

                // Kirim lewat AJAX
                const url = getBaseUrl() + `/output/${outputId}/toggle`;

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

                const url = getBaseUrl() + `/dosing/volume`;

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
                const url = getBaseUrl() + `/output/${outputId}/toggle`;

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

            // Helper function to fetch latest status data
            function fetchLatestStatus() {
                const url = getBaseUrl() + `/status`;
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        // Logic to update UI with status
                        console.log('Status updated', data);
                    })
                    .catch(error => console.error('Error fetching status:', error));
            }



            // Special Pump Control Functions
            function sendPumpOn() {
                const zone = document.getElementById('pumpZone').value;
                const inputType = document.getElementById('pumpInputType').value;
                const url = getBaseUrl() + `/pump/control`;

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
                const url = getBaseUrl() + `/pump/control`;

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
                const statusEl = document.getElementById(`output-status-${outputId}`);
                if (isOn) {
                    btnOn.className = 'segmented-btn active-on';
                    btnOff.className = 'segmented-btn';
                    if (statusEl) {
                        statusEl.classList.remove('off');
                        statusEl.classList.add('on');
                        statusEl.innerText = statusEl.getAttribute('data-on-text') || 'ON';
                    }
                } else {
                    btnOn.className = 'segmented-btn';
                    btnOff.className = 'segmented-btn active-off';
                    if (statusEl) {
                        statusEl.classList.remove('on');
                        statusEl.classList.add('off');
                        statusEl.innerText = statusEl.getAttribute('data-off-text') || 'OFF';
                    }
                }
            }
        }
        
        // Auto-reload status every 30 seconds (as fallback and periodic heartbeat verification)
        setInterval(fetchStatus, 30000);

        async function fetchStatus() {
            try {
                @if($isAdminView ?? false)
                    const response = await fetch('{{ route("admin.device.status", $device->id) }}');
                @else
                    const response = await fetch('{{ route("monitoring.status", $userDevice->id) }}');
                @endif
                const data = await response.json();

                if (data.success) {
                    if (data.is_online) {
                        setDeviceOnline(data.last_seen_text);
                    } else if (data.is_online === false) {
                        setDeviceOffline();
                    }
                    if (data.outputs) {
                        updateOutputs(data.outputs);
                    }
                    if (data.sensors) {
                        updateSensors(data.sensors);
                    }
                    if (data.sf_status) {
                        updateSmartFarmLiveStatus(data.sf_status);
                    }
                }
            } catch (error) {
                console.error('Polling error:', error);
            }
        }
        
        // Timeout timer for offline detection (3 minutes without updates)
        let offlineTimer = null;
        
        function resetOfflineTimer() {
            if (offlineTimer) clearTimeout(offlineTimer);
            offlineTimer = setTimeout(() => {
                setDeviceOffline();
            }, 180000); // 3 minutes without updates = Offline
        }
        
        function setDeviceOnline(lastSeenText = null) {
            const badge = document.getElementById('conn-badge');
            if (badge) {
                badge.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                badge.innerHTML = '<i class="bi bi-wifi me-1"></i> ONLINE';
            }
            const dot = document.getElementById('live-dot');
            if (dot) dot.style.display = 'inline-block';

            const lastUpdateEl = document.getElementById('last-update-text');
            if (lastUpdateEl) {
                const text = lastSeenText || 'Baru saja';
                lastUpdateEl.innerHTML = `<span class="live-dot me-2" id="live-dot" style="display: inline-block;"></span> Terakhir aktif: ${text}`;
            }
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
                        
                        setDeviceOnline('Baru saja');

                        if (e.sensors && Object.keys(e.sensors).length > 0) {
                            updateSensors(e.sensors);
                        }
                        
                        if (e.outputs && Array.isArray(e.outputs) && e.outputs.length > 0) {
                            updateOutputs(e.outputs);
                        }

                        if (e.smartFarmStatus) {
                            updateSmartFarmLiveStatus(e.smartFarmStatus);
                        }
                    });
            } else {
                console.warn("Laravel Echo is not initialized. WebSockets will not work.");
            }
        });

        function updateSmartFarmLiveStatus(sf) {
            const card = document.getElementById('sf-status-card');
            if (!card) return;

            const isSiram = sf.siram === 1 || sf.siram === '1' || sf.siram === true;
            const badgeSiram = document.getElementById('sf-badge-siram');
            const textSiram = document.getElementById('sf-text-siram');
            const iconBadge = document.getElementById('sf-icon-badge');
            const iconWrapper = document.getElementById('sf-icon-wrapper');
            const iconMain = document.getElementById('sf-icon-main');
            const detailSiram = document.getElementById('sf-detail-siram');
            const badgeJam = document.getElementById('sf-badge-jam');
            const actionsContainer = document.getElementById('sf-actions-container');
            let btnStop = document.getElementById('sf-btn-stop');
            let btnStart = document.getElementById('sf-btn-start');

            // Pipeline Elements
            const pNodePompa = document.getElementById('pipe-node-pompa');
            const pNodePupuk = document.getElementById('pipe-node-pupuk');
            const pConn1 = document.getElementById('pipe-conn-1');
            const pConn2 = document.getElementById('pipe-conn-2');
            const pStatusPompa = document.getElementById('pipe-status-pompa');
            const pStatusPupuk = document.getElementById('pipe-status-pupuk');

            if (isSiram) {
                if (badgeSiram) badgeSiram.className = 'badge rounded-pill bg-success text-white';
                if (textSiram) textSiram.innerText = `SEDANG MENYIRAM (BLOK ${sf.blok || 1})`;
                if (iconBadge) iconBadge.className = 'bi bi-play-circle-fill me-1';
                if (iconWrapper) iconWrapper.style.background = 'linear-gradient(135deg, #059669, #10b981)';
                if (iconMain) iconMain.className = 'bi bi-droplet-fill';

                if (detailSiram) {
                    const sisa = parseInt(sf.sisa) || 0;
                    const m = Math.floor(sisa / 60);
                    const s = sisa % 60;
                    let pupukHtml = (sf.pupuk === 'ON') ? ' &bull; <span class="text-warning fw-bold"><i class="bi bi-droplet-half me-1"></i>Pupuk Aktif</span>' : '';
                    detailSiram.innerHTML = `Menyiram <strong>Blok ${sf.blok || 1}</strong> &bull; Sisa Waktu: <strong><span id="sf-sisa-waktu">${m}m ${s}s</span></strong>${pupukHtml}`;
                }

                if (btnStart) btnStart.style.display = 'none';
                if (!btnStop && actionsContainer) {
                    btnStop = document.createElement('button');
                    btnStop.type = 'button';
                    btnStop.id = 'sf-btn-stop';
                    btnStop.className = 'btn btn-danger btn-sm d-inline-flex align-items-center gap-2 shadow-sm';
                    btnStop.style.borderRadius = '50px';
                    btnStop.style.padding = '0.65rem 1.4rem';
                    btnStop.style.fontWeight = '700';
                    btnStop.onclick = () => stopSiramQuick(true);
                    btnStop.innerHTML = '<i class="bi bi-stop-circle-fill"></i> Stop Siram';
                    actionsContainer.prepend(btnStop);
                } else if (btnStop) {
                    btnStop.style.display = 'inline-flex';
                }

                // Pipeline Nodes
                if (pNodePompa) pNodePompa.classList.add('active');
                if (pStatusPompa) pStatusPompa.innerText = 'MEMOMPA';
                if (pConn1) pConn1.classList.add('active');
                if (pConn2) pConn2.classList.add('active');

                const isPupuk = (sf.pupuk === 'ON');
                if (pNodePupuk) {
                    if (isPupuk) pNodePupuk.classList.add('active');
                    else pNodePupuk.classList.remove('active');
                }
                if (pStatusPupuk) pStatusPupuk.innerText = isPupuk ? 'INJEKSI' : 'STANDBY';

                [1, 2, 3].forEach(b => {
                    const node = document.getElementById(`pipe-node-blok${b}`);
                    const status = document.getElementById(`pipe-status-blok${b}`);
                    const flow = document.getElementById(`sf-flow-blok${b}`);
                    const isTarget = (sf.blok == b);
                    if (node) {
                        if (isTarget) node.classList.add('active');
                        else node.classList.remove('active');
                    }
                    if (status) status.innerText = isTarget ? 'MENGALIR' : 'TUTUP';
                    if (flow) flow.style.display = isTarget ? 'inline-flex' : 'none';
                });
            } else {
                if (badgeSiram) badgeSiram.className = 'badge rounded-pill bg-secondary text-white';
                if (textSiram) textSiram.innerText = 'SIAGA (STANDBY)';
                if (iconBadge) iconBadge.className = 'bi bi-pause-circle me-1';
                if (iconWrapper) iconWrapper.style.background = 'linear-gradient(135deg, #0284c7, #38bdf8)';
                if (iconMain) iconMain.className = 'bi bi-water';
                if (detailSiram) {
                    detailSiram.innerHTML = 'Sistem irigasi multi-zona siap. Pompa dan katup solenoid dalam kondisi siaga.';
                }
                if (btnStop) {
                    btnStop.remove();
                }
                if (btnStart) {
                    btnStart.style.display = 'inline-flex';
                }

                // If not in automated siram, sync with manual outputs
                const pompaBtn = sfOutputMap.pompa ? document.getElementById(`btn-on-${sfOutputMap.pompa}`) : null;
                const isPumpManualOn = pompaBtn && pompaBtn.classList.contains('active-on');
                if (pNodePompa) {
                    if (isPumpManualOn) pNodePompa.classList.add('active');
                    else pNodePompa.classList.remove('active');
                }
                if (pStatusPompa) pStatusPompa.innerText = isPumpManualOn ? 'MEMOMPA' : 'OFF';
                if (pConn1) {
                    if (isPumpManualOn) pConn1.classList.add('active');
                    else pConn1.classList.remove('active');
                }
            }

            if (sf.jam) {
                const jamStr = String(sf.jam);
                const textJam = document.getElementById('sf-text-jam');
                if (jamStr.length === 4) {
                    const formatted = `${jamStr.substring(0, 2)}:${jamStr.substring(2, 4)}`;
                    const tz = sf.timezone || '{{ $sfTimezone ?? "WIB" }}';
                    if (textJam) textJam.innerText = `${formatted} ${tz}`;
                }
            }

            let badgeError = document.getElementById('sf-badge-error');
            const hasError = sf.error === 1 || sf.error === '1' || sf.error === true;
            if (hasError) {
                if (!badgeError) {
                    const badgeContainer = document.querySelector('#sf-status-card .d-flex.align-items-center.gap-2.mb-1');
                    if (badgeContainer) {
                        badgeError = document.createElement('button');
                        badgeError.type = 'button';
                        badgeError.id = 'sf-badge-error';
                        badgeError.className = 'badge rounded-pill bg-danger text-white small border-0 d-inline-flex align-items-center gap-1 shadow-sm';
                        badgeError.title = 'Klik untuk Reset Error Relay pada alat';
                        badgeError.onclick = resetRelayErrorQuick;
                        badgeError.style.cursor = 'pointer';
                        badgeError.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Error Relay (Klik Reset)';
                        badgeContainer.appendChild(badgeError);
                    }
                }
            } else {
                if (badgeError) badgeError.remove();
            }
        }

        let rtcClockTimer = null;

        function updateRtcModalClocks() {
            const now = new Date();
            const utcTime = now.getTime() + (now.getTimezoneOffset() * 60000);

            const wib = new Date(utcTime + (3600000 * 7));
            const wita = new Date(utcTime + (3600000 * 8));
            const wit = new Date(utcTime + (3600000 * 9));

            const pad = n => String(n).padStart(2, '0');
            const formatTime = d => `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;

            const elWib = document.getElementById('sf-tz-clock-wib');
            const elWita = document.getElementById('sf-tz-clock-wita');
            const elWit = document.getElementById('sf-tz-clock-wit');

            if (elWib) elWib.innerText = formatTime(wib);
            if (elWita) elWita.innerText = formatTime(wita);
            if (elWit) elWit.innerText = formatTime(wit);
        }

        function openRtcSyncModal() {
            updateRtcModalClocks();
            if (rtcClockTimer) clearInterval(rtcClockTimer);
            rtcClockTimer = setInterval(updateRtcModalClocks, 1000);

            const modal = new bootstrap.Modal(document.getElementById('sfRtcSyncModal'));
            modal.show();

            const modalEl = document.getElementById('sfRtcSyncModal');
            modalEl.addEventListener('hidden.bs.modal', function onHidden() {
                if (rtcClockTimer) clearInterval(rtcClockTimer);
                modalEl.removeEventListener('hidden.bs.modal', onHidden);
            });
        }

        async function sendRtcSync() {
            const selectedTz = document.querySelector('input[name="sf_target_tz"]:checked')?.value || 'WIB';
            const btn = document.getElementById('btn-submit-rtc-sync');
            const originalHtml = btn.innerHTML;

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengirim ke alat...';

            try {
                const targetId = '{{ ($isAdminView ?? false) ? $device->id : ($userDevice->id ?? $device->id) }}';
                const res = await fetch(`/device/${targetId}/schedule/set-rtc`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ timezone: selectedTz })
                });
                const data = await res.json();
                if (data.success) {
                    const textJam = document.getElementById('sf-text-jam');
                    if (textJam && data.jam) textJam.innerText = `${data.jam} ${data.timezone || selectedTz}`;
                    alert(data.message || 'Waktu RTC alat berhasil disinkronkan!');
                    const modalEl = document.getElementById('sfRtcSyncModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (e) {
                alert('Gagal mengirim perintah sinkronisasi waktu: ' + e.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        async function resetRelayErrorQuick() {
            if (!confirm('Reset error relay pada alat sekarang?')) return;
            try {
                const targetId = '{{ ($isAdminView ?? false) ? $device->id : ($userDevice->id ?? $device->id) }}';
                const res = await fetch(`/device/${targetId}/schedule/reset-error`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    alert(data.message || 'Perintah reset error relay dikirim!');
                    const badgeError = document.getElementById('sf-badge-error');
                    if (badgeError) badgeError.remove();
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (e) {
                alert('Gagal mengirim reset error: ' + e.message);
            }
        }

        async function stopSiramQuick(askConfirm = true) {
            if (askConfirm && !confirm('Hentikan penyiraman irigasi sekarang?')) return;
            try {
                const targetId = '{{ ($isAdminView ?? false) ? $device->id : ($userDevice->id ?? $device->id) }}';
                const res = await fetch(`/device/${targetId}/schedule/siram-stop`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();
                if (data.success) {
                    if (askConfirm) alert(data.message || 'Perintah stop penyiraman dikirim!');
                    updateSmartFarmLiveStatus({ siram: 0, blok: 0, pupuk: 'NONE', sisa: 0 });
                } else {
                    if (askConfirm) alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
                }
            } catch (err) {
                if (askConfirm) alert('Gagal mengirim perintah: ' + err.message);
            }
        }
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
                        statusEl.classList.remove('off');
                        statusEl.classList.add('on');
                        statusEl.innerText = statusEl.getAttribute('data-on-text') || 'ON';
                    } else {
                        btnOn.className = 'segmented-btn';
                        btnOff.className = 'segmented-btn active-off';
                        statusEl.classList.remove('on');
                        statusEl.classList.add('off');
                        statusEl.innerText = statusEl.getAttribute('data-off-text') || 'OFF';
                    }

                    // Smart Farm specific visual sync
                    if (typeof sfOutputMap !== 'undefined') {
                        if (output.id === sfOutputMap.pompa) {
                            const card = document.getElementById(`output-card-${output.id}`);
                            const pNode = document.getElementById('pipe-node-pompa');
                            const pStatus = document.getElementById('pipe-status-pompa');
                            const pConn1 = document.getElementById('pipe-conn-1');
                            if (card) {
                                if (isOn) card.classList.add('active-pump');
                                else card.classList.remove('active-pump');
                            }
                            if (pNode) {
                                if (isOn) pNode.classList.add('active');
                                else pNode.classList.remove('active');
                            }
                            if (pStatus) pStatus.innerText = isOn ? 'MEMOMPA' : 'OFF';
                            if (pConn1) {
                                if (isOn) pConn1.classList.add('active');
                                else pConn1.classList.remove('active');
                            }
                        }
                        if (output.id === sfOutputMap.pupuk) {
                            const card = document.getElementById(`output-card-${output.id}`);
                            const pNode = document.getElementById('pipe-node-pupuk');
                            const pStatus = document.getElementById('pipe-status-pupuk');
                            if (card) {
                                if (isOn) card.classList.add('active-dosing');
                                else card.classList.remove('active-dosing');
                            }
                            if (pNode) {
                                if (isOn) pNode.classList.add('active');
                                else pNode.classList.remove('active');
                            }
                            if (pStatus) pStatus.innerText = isOn ? 'INJEKSI' : 'STANDBY';
                        }
                        [1, 2, 3].forEach(b => {
                            if (output.id === sfOutputMap[`blok${b}`]) {
                                const node = document.getElementById(`pipe-node-blok${b}`);
                                const status = document.getElementById(`pipe-status-blok${b}`);
                                const flow = document.getElementById(`sf-flow-blok${b}`);
                                const isPumpOn = document.querySelector('#btn-on-' + sfOutputMap.pompa)?.classList.contains('active-on');
                                if (node) {
                                    if (isOn && isPumpOn) node.classList.add('active');
                                    else if (!isOn) node.classList.remove('active');
                                }
                                if (status) {
                                    status.innerText = (isOn && isPumpOn) ? 'MENGALIR' : (isOn ? 'BUKA' : 'TUTUP');
                                }
                                if (flow) {
                                    flow.style.display = (isOn && isPumpOn) ? 'inline-flex' : 'none';
                                }
                            }
                        });
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
