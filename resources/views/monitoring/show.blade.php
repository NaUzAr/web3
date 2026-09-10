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

    {{-- 1. Styles & CSS Overrides --}}
    @include('monitoring.partials.styles')
</head>

<body>
    <div class="bg-animation"></div>

    <!-- Navbar -->
    @include('partials.navbar')

    <div class="container pt-3 pb-5">
        {{-- 2. Page Header & Action Buttons --}}
        @include('monitoring.partials.header')

        {{-- 3. Dynamic Device Layout --}}
        @if($device->type === 'smart_farm')
            {{-- Smart Farm: Command Center, Pipeline Visualizer, Hub 1 & Hub 2 --}}
            @include('monitoring.types.smart_farm')
        @else
            {{-- Standard Device (AWS, Smart Greenhouse, etc.) --}}
            @include('monitoring.partials.sensor_grid')
            @include('monitoring.partials.output_grid')
        @endif
    </div>

    {{-- 4. Modals (Siram, RTC Sync, Irrigation Pump, pH Control) --}}
    @include('monitoring.partials.modals')

    {{-- 5. Scripts (WebSocket, Real-time status, Output Controls) --}}
    @include('monitoring.scripts.index')

    @include('partials.pwa-scripts')
</body>

</html>
