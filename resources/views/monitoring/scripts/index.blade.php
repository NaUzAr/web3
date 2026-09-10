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
                const selectedDurasi = 0; // Siram manual murni tanpa timer menit
                const includePupuk = false; // Siram manual murni air irigasi

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

                    // Animasi jeda sebentar (800ms)
                    statusHeadline.innerText = 'Menyalakan aliran pompa...';
                    statusSubline.innerText = 'STM32 menyalakan Pompa Utama secara otomatis';
                    await new Promise(r => setTimeout(r, 800));

                    // --- STEP 2: Pompa Utama Aktif Otomatis oleh STM32 ---
                    step2.classList.remove('opacity-50');
                    step2.innerHTML = `
                        <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <div>
                            <div class="fw-bold text-success">Langkah 2: Pompa Utama Menyala</div>
                            <div class="small text-muted">Air sedang mengalir ke Blok ${selectedBlok}</div>
                        </div>
                    `;
                    progressBar.style.width = '75%';
                    setOptimisticUI(pompaOutputId, true);

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
                    statusSubline.innerText = 'Penyiraman manual aktif (klik Stop Siram jika selesai).';

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
                    let pupukHtml = (sf.pupuk === 'ON') ? ' &bull; <span class="text-warning fw-bold"><i class="bi bi-droplet-half me-1"></i>Pupuk Aktif</span>' : '';
                    if (sisa > 0) {
                        const m = Math.floor(sisa / 60);
                        const s = sisa % 60;
                        detailSiram.innerHTML = `Menyiram <strong>Blok ${sf.blok || 1}</strong> &bull; Sisa Waktu: <strong><span id="sf-sisa-waktu">${m}m ${s}s</span></strong>${pupukHtml}`;
                    } else {
                        detailSiram.innerHTML = `Menyiram <strong>Blok ${sf.blok || 1}</strong> &bull; <span class="text-success fw-bold"><i class="bi bi-play-circle-fill me-1"></i>Manual Aktif</span>${pupukHtml}`;
                    }
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
