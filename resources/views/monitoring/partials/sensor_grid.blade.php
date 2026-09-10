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
