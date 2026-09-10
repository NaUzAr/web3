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
                            
                            <div class="d-flex flex-column gap-2.5 mb-3">
                                <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between sf-blok-card" style="cursor: pointer;" for="sf-blok-1">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="sf-modal-blok-avatar" style="background: rgba(16, 185, 129, 0.15); color: #059669; border: 1.5px solid rgba(16, 185, 129, 0.3); font-weight: 800; font-size: 1.15rem;">
                                            1
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold text-dark" style="font-size: 0.95rem;">Blok 1</span>
                                                <span class="badge rounded-pill" style="background: rgba(16, 185, 129, 0.12); color: #059669; font-size: 0.68rem; font-weight: 800; border: 1px solid rgba(16, 185, 129, 0.25);">ZONA 1</span>
                                            </div>
                                            <div class="small text-muted" style="font-size: 0.76rem; margin-top: 2px;">
                                                <i class="bi bi-cpu text-muted me-1"></i>Katup Solenoid #1 &bull; Tanaman Utama
                                            </div>
                                        </div>
                                    </div>
                                    <input class="form-check-input fs-5 m-0" type="radio" name="sf_target_blok" id="sf-blok-1" value="1" checked>
                                </label>

                                <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between sf-blok-card" style="cursor: pointer;" for="sf-blok-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="sf-modal-blok-avatar" style="background: rgba(14, 165, 233, 0.15); color: #0284c7; border: 1.5px solid rgba(14, 165, 233, 0.3); font-weight: 800; font-size: 1.15rem;">
                                            2
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold text-dark" style="font-size: 0.95rem;">Blok 2</span>
                                                <span class="badge rounded-pill" style="background: rgba(14, 165, 233, 0.12); color: #0284c7; font-size: 0.68rem; font-weight: 800; border: 1px solid rgba(14, 165, 233, 0.25);">ZONA 2</span>
                                            </div>
                                            <div class="small text-muted" style="font-size: 0.76rem; margin-top: 2px;">
                                                <i class="bi bi-cpu text-muted me-1"></i>Katup Solenoid #2 &bull; Hortikultura / Sayur
                                            </div>
                                        </div>
                                    </div>
                                    <input class="form-check-input fs-5 m-0" type="radio" name="sf_target_blok" id="sf-blok-2" value="2">
                                </label>

                                <label class="p-3 border rounded-3 d-flex align-items-center justify-content-between sf-blok-card" style="cursor: pointer;" for="sf-blok-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="sf-modal-blok-avatar" style="background: rgba(139, 92, 246, 0.15); color: #7c3aed; border: 1.5px solid rgba(139, 92, 246, 0.3); font-weight: 800; font-size: 1.15rem;">
                                            3
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold text-dark" style="font-size: 0.95rem;">Blok 3</span>
                                                <span class="badge rounded-pill" style="background: rgba(139, 92, 246, 0.12); color: #7c3aed; font-size: 0.68rem; font-weight: 800; border: 1px solid rgba(139, 92, 246, 0.25);">ZONA 3</span>
                                            </div>
                                            <div class="small text-muted" style="font-size: 0.76rem; margin-top: 2px;">
                                                <i class="bi bi-cpu text-muted me-1"></i>Katup Solenoid #3 &bull; Bibit & Pembesaran
                                            </div>
                                        </div>
                                    </div>
                                    <input class="form-check-input fs-5 m-0" type="radio" name="sf_target_blok" id="sf-blok-3" value="3">
                                </label>
                            </div>

                            <div class="modal-actions mt-3">
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
