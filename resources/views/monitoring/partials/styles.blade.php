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
            border-radius: 14px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            cursor: pointer;
        }
        .sf-blok-card:hover {
            border-color: #10b981 !important;
            background: rgba(16, 185, 129, 0.03);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }
        .sf-blok-card:has(input:checked) {
            border-color: #10b981 !important;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(16, 185, 129, 0.02));
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.18);
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

        /* Zone Card - Modern Agricultural Grid */
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .sf-zone-accent {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .sf-zone-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -4px rgba(0, 0, 0, 0.08);
            border-color: #cbd5e1;
        }

        .sf-zone-card.active-flow {
            border-color: var(--zone-color, #10b981) !important;
            box-shadow: 0 10px 25px -4px var(--zone-glow, rgba(16, 185, 129, 0.3)) !important;
            background: linear-gradient(180deg, #ffffff 0%, rgba(240, 253, 244, 0.6) 100%);
        }

        .sf-zone-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
            transition: transform 0.25s ease;
        }

        .sf-zone-card:hover .sf-zone-avatar {
            transform: scale(1.08);
        }

        .sf-zone-pill-tag {
            font-size: 0.64rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 2px 7px;
            border-radius: 20px;
            display: inline-block;
            line-height: 1.3;
        }

        .sf-zone-telemetry {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.45rem 0.75rem;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
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
            border: 1px solid rgba(16, 185, 129, 0.25);
            animation: flowGlow 2s ease-in-out infinite;
        }

        .sf-flow-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #059669;
            animation: pulseDot 1.2s infinite;
        }

        @keyframes pulseDot {
            0% { transform: scale(0.9); opacity: 0.6; }
            50% { transform: scale(1.4); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.6; }
        }

        .sf-zone-action-btn {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.55rem 0.75rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
            cursor: pointer;
            width: 100%;
        }

        .sf-zone-action-btn:hover {
            background: #ffffff;
            border-color: #10b981;
            color: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
        }

        .sf-zone-action-btn.active-action {
            background: rgba(16, 185, 129, 0.1);
            border-color: #10b981;
            color: #059669;
        }

        .sf-icon-pupuk {
            display: inline-block;
            vertical-align: middle;
            transition: transform 0.25s ease;
        }

        .sf-actuator-card:hover .sf-icon-pupuk {
            transform: scale(1.1) rotate(5deg);
        }

        .sf-modal-blok-avatar {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }

        @keyframes flowGlow {
            0%, 100% { opacity: 0.95; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(0.97); }
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

        /* ==================================================== */
        /* 🌿 SWEETALERT2 POPUP MODERN MOBILE-FRIENDLY THEME     */
        /* ==================================================== */
        div:where(.swal2-container) {
            z-index: 10000 !important;
        }

        div:where(.swal2-container).swal2-backdrop-show, 
        div:where(.swal2-container).swal2-noanimation {
            background: rgba(15, 23, 42, 0.55) !important;
            backdrop-filter: blur(5px) !important;
            -webkit-backdrop-filter: blur(5px) !important;
        }

        .sf-swal-popup {
            border-radius: 24px !important;
            padding: 1.75rem 1.4rem 1.5rem !important;
            background: #ffffff !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            width: 90% !important;
            max-width: 420px !important;
            border: 1px solid rgba(226, 232, 240, 0.9) !important;
        }

        .sf-swal-title {
            font-size: 1.18rem !important;
            font-weight: 800 !important;
            color: #0f172a !important;
            margin-bottom: 0.35rem !important;
            letter-spacing: -0.2px;
        }

        .sf-swal-html {
            font-size: 0.88rem !important;
            color: #475569 !important;
            line-height: 1.5 !important;
            margin: 0.5rem 0 1.25rem !important;
        }

        .sf-swal-actions {
            gap: 0.6rem !important;
            width: 100% !important;
            margin-top: 1rem !important;
            display: flex !important;
            justify-content: center !important;
            flex-wrap: wrap !important;
        }

        .sf-swal-btn {
            border-radius: 50px !important;
            padding: 0.65rem 1.4rem !important;
            font-size: 0.88rem !important;
            font-weight: 700 !important;
            cursor: pointer !important;
            border: none !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 0.4rem !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            min-width: 110px !important;
        }

        .sf-swal-primary {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35) !important;
        }

        .sf-swal-primary:hover, .sf-swal-primary:active {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45) !important;
        }

        .sf-swal-danger {
            background: linear-gradient(135deg, #ef4444, #dc2626) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35) !important;
        }

        .sf-swal-danger:hover, .sf-swal-danger:active {
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(239, 68, 68, 0.45) !important;
        }

        .sf-swal-cancel {
            background: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #cbd5e1 !important;
        }

        .sf-swal-cancel:hover, .sf-swal-cancel:active {
            background: #e2e8f0 !important;
            color: #1e293b !important;
        }

        .sf-swal-toast {
            border-radius: 16px !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15) !important;
            font-family: 'Inter', sans-serif !important;
            font-weight: 600 !important;
            padding: 0.75rem 1rem !important;
        }
    </style>
