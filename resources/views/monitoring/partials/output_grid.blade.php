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
