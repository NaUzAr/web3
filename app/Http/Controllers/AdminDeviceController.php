<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

// --- IMPORT PENTING UNTUK DATABASE ---
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
// -------------------------------------

class AdminDeviceController extends Controller
{
    // Helper: Pastikan yang akses adalah Admin
    private function checkAdmin()
    {
        // Pastikan kolom 'role' sudah ada di tabel users
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Akses Ditolak. Halaman ini khusus Admin.');
        }
    }

    // Helper: Find sensor ID by name from device's sensors
    private function findSensorId($deviceId, $sensorName)
    {
        if (empty($sensorName)) {
            return null;
        }

        $sensor = \App\Models\DeviceSensor::where('device_id', $deviceId)
            ->where('sensor_name', $sensorName)
            ->first();

        return $sensor?->id;
    }

    // 1. HALAMAN LIST DEVICE (INDEX)
    public function index(Request $request)
    {
        $this->checkAdmin();
        
        $query = Device::query();
        
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('table_name', 'like', '%' . $search . '%')
                  ->orWhere('type', 'like', '%' . $search . '%');
        }

        // Sorting logic
        $sort = $request->get('sort', 'created_at'); // default sort
        $order = $request->get('order', 'desc'); // default order
        
        $allowedSorts = ['name', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $order === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }
        
        $devices = $query->get();
        return view('admin.index', compact('devices'));
    }

    // 2. HALAMAN FORM CREATE
    public function create()
    {
        $this->checkAdmin();

        // Use Device model for configuration to keep things in sync
        $deviceTypes = Device::getDeviceTypes();
        $availableSensors = Device::getAvailableSensors();
        $availableOutputs = Device::getAvailableOutputs();
        $scheduleTypes = Device::getAvailableScheduleTypes();
        $automationPresets = Device::getAutomationPresets();

        // Build default sensors/outputs from model
        $defaultSensors = [];
        $defaultOutputs = [];
        foreach (array_keys($deviceTypes) as $type) {
            $defaultSensors[$type] = Device::getDefaultSensorsForType($type);
            $defaultOutputs[$type] = Device::getDefaultOutputsForType($type);
        }

        return view('admin.create_device', compact(
            'deviceTypes',
            'availableSensors',
            'availableOutputs',
            'scheduleTypes',
            'defaultSensors',
            'defaultOutputs',
            'automationPresets'
        ));
    }

    // 3. PROSES SIMPAN DEVICE BARU (STORE)
    public function store(Request $request)
    {
        $this->checkAdmin();

        // A. Validasi Input - use dynamic device types from model
        $validTypes = implode(',', array_keys(Device::getDeviceTypes()));
        $request->validate([
            'name' => 'required|string|max:100',
            'mqtt_topic' => 'required|string|max:100',
            'type' => 'required|string|in:' . $validTypes,
            'sensors' => 'required|array|min:1',
            'sensors.*.type' => 'required|string',
        ]);

        // Get sensor and output configs from Device model
        $availableSensors = Device::getAvailableSensors();
        $availableOutputs = Device::getAvailableOutputs();

        // B. Generate Token Unik & Nama Tabel
        $token = Str::random(16);
        $tableName = 'log_' . $token;

        // C. Process sensors from form
        $sensors = $request->sensors;
        $sensorColumns = [];
        $sensorCounter = [];

        foreach ($sensors as $sensor) {
            $type = $sensor['type'];
            if (!isset($availableSensors[$type]))
                continue;

            // Count duplicates to generate unique names
            if (!isset($sensorCounter[$type])) {
                $sensorCounter[$type] = 0;
            }
            $sensorCounter[$type]++;

            // Generate column name (e.g., temperature, temperature_2)
            $columnName = $sensorCounter[$type] > 1 ? "{$type}_{$sensorCounter[$type]}" : $type;

            // Custom label from form or default
            $label = !empty($sensor['label']) ? $sensor['label'] : $availableSensors[$type]['label'];
            if ($sensorCounter[$type] > 1 && empty($sensor['label'])) {
                $label .= " {$sensorCounter[$type]}";
            }

            // mqtt_key: key yang dikirim dari ESP32 (contoh: ni_PH, ni_SUHU)
            // Jika user tidak mengisi, gunakan column name sebagai default
            $mqttKey = !empty($sensor['mqtt_key']) ? $sensor['mqtt_key'] : $columnName;

            $sensorColumns[] = [
                'name' => $columnName,
                'type' => $type,
                'label' => $label,
                'unit' => $availableSensors[$type]['unit'],
                'mqtt_key' => $mqttKey,
            ];
        }

        // D. BUAT TABEL LOG OTOMATIS dengan sensor columns
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($sensorColumns) {
                $table->id();
                foreach ($sensorColumns as $col) {
                    $table->float($col['name'])->nullable();
                }
                $table->timestamp('recorded_at')->useCurrent();
            });
        }

        // E. Simpan Device ke database
        $device = Device::create([
            'name' => $request->name,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'mqtt_topic' => $request->mqtt_topic,
            'token' => $token,
            'table_name' => $tableName,
            'type' => $request->type,
        ]);

        // F. Simpan Sensors ke device_sensors
        foreach ($sensorColumns as $sensor) {
            \App\Models\DeviceSensor::create([
                'device_id' => $device->id,
                'sensor_name' => $sensor['name'],
                'mqtt_key' => $sensor['mqtt_key'],
                'sensor_label' => $sensor['label'],
                'unit' => $sensor['unit'],
            ]);
        }

        // F2. Simpan setting otomasi jika diaktifkan (dari form Create)
        $autoKeyMap = [
            'ni_SUHU' => 'suhu',
            'ni_KELEM' => 'kelem',
            'ni_PH' => 'ph',
            'ni_TDS' => 'tds'
        ];

        $processedAutoKeys = [];

        foreach ($sensors as $sensor) {
            $type = $sensor['type'] ?? '';
            // Periksa jika auto_enabled dicentang dan tipenya ada di map
            if (!empty($sensor['auto_enabled']) && isset($autoKeyMap[$type])) {
                $settingKey = $autoKeyMap[$type];

                // Cegah duplikat jika user menambahkan 2 sensor suhu dan mencentang keduanya
                if (in_array($settingKey, $processedAutoKeys)) {
                    continue;
                }
                $processedAutoKeys[] = $settingKey;

                $atsVal = $sensor['ats_val'] ?? 0;
                $bwhVal = $sensor['bwh_val'] ?? 0;

                // Simpan batas atas
                \App\Models\DeviceSetting::create([
                    'device_id' => $device->id,
                    'key' => "ats_{$settingKey}",
                    'value' => $atsVal,
                ]);

                // Simpan batas bawah
                \App\Models\DeviceSetting::create([
                    'device_id' => $device->id,
                    'key' => "bwh_{$settingKey}",
                    'value' => $bwhVal,
                ]);
            }
        }

        // G. Simpan Outputs ke device_outputs (tanpa automation fields - pindah ke schedules)
        if ($request->has('outputs')) {
            $outputCounter = [];
            foreach ($request->outputs as $output) {
                if (empty($output['type']))
                    continue;

                $type = $output['type'];
                if (!isset($availableOutputs[$type]))
                    continue;

                // Count duplicates to generate unique names
                if (!isset($outputCounter[$type])) {
                    $outputCounter[$type] = 0;
                }
                $outputCounter[$type]++;

                // Generate output name (e.g., pump, pump_2)
                $outputName = $outputCounter[$type] > 1 ? "{$type}_{$outputCounter[$type]}" : $type;

                $outputConfig = $availableOutputs[$type];
                $label = !empty($output['label']) ? $output['label'] : $outputConfig['label'];
                if ($outputCounter[$type] > 1 && empty($output['label'])) {
                    $label .= " {$outputCounter[$type]}";
                }

                // Prepare output data
                $outputData = [
                    'device_id' => $device->id,
                    'output_name' => $outputName,
                    'output_label' => $label,
                    'output_type' => $outputConfig['type'],
                    'unit' => $outputConfig['unit'],
                ];

                // Handle multi-zone irrigation pump - simpan jumlah zona
                if ($type === 'irrigation_pump' && isset($output['zones']) && $output['zones'] > 0) {
                    $outputData['max_sectors'] = min((int) $output['zones'], 20); // Max 20 zones
                }

                \App\Models\DeviceOutput::create($outputData);
            }
        }

        // H. Simpan Schedule Type ke device_schedules (maksimal 1 per device)
        if ($request->filled('schedule_type')) {
            $scheduleTypes = Device::getAvailableScheduleTypes();
            $scheduleType = $request->schedule_type;

            if (isset($scheduleTypes[$scheduleType])) {
                $scheduleInfo = $scheduleTypes[$scheduleType];

                \App\Models\DeviceSchedule::create([
                    'device_id' => $device->id,
                    'schedule_name' => 'schedule',
                    'schedule_label' => $scheduleInfo['label'],
                    'output_key' => 'general', // Output umum, bisa dikonfigurasi nanti
                    'schedule_mode' => $scheduleType,
                    'max_slots' => $request->max_slots ?? 8,
                    'max_sectors' => $request->max_sectors ?? 1,
                ]);
            }
        }

        // I. Redirect ke Halaman List Device
        \Illuminate\Support\Facades\Cache::put('mqtt_devices_changed', true, now()->addMinutes(5));
        return redirect()->route('admin.devices.index')
            ->with('success', "Sukses! Device '{$request->name}' berhasil dibuat dengan " . count($sensorColumns) . " sensor.");
    }

    // 4. HALAMAN FORM EDIT
    public function edit($id)
    {
        $this->checkAdmin();
        $device = Device::findOrFail($id);
        
        $availableSensors = Device::getAvailableSensors();
        $availableOutputs = Device::getAvailableOutputs();
        
        // Settings untuk auto toggle
        $automationPresets = [
            'climate' => [
                'label' => 'Otomasi Iklim',
                'icon' => 'bi-thermometer-sun',
                'sensors' => ['ni_SUHU' => 1, 'ni_KELEM' => 1]
            ],
            'fertilizer' => [
                'label' => 'Otomasi Pemupukan',
                'icon' => 'bi-flower1',
                'sensors' => ['ni_PH' => 1, 'ni_TDS' => 1]
            ]
        ];
        
        return view('admin.edit', compact('device', 'availableSensors', 'availableOutputs', 'automationPresets'));
    }

    // 5. PROSES UPDATE DEVICE
    public function update(Request $request, $id)
    {
        $this->checkAdmin();

        $request->validate([
            'name' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'mqtt_topic' => 'required|string|max:100',
        ]);

        $device = Device::findOrFail($id);

        $device->update([
            'name' => $request->name,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'mqtt_topic' => $request->mqtt_topic,
            // Token & table_name JANGAN diupdate agar koneksi database aman
        ]);

        // Update jumlah zona & jadwal jika ada (fitur opsional)
        if ($request->has('max_sectors')) {
            $device->outputs()->where('output_type', 'irrigation_pump')->update([
                'max_sectors' => (int) $request->max_sectors
            ]);
            $device->schedules()->update([
                'max_sectors' => (int) $request->max_sectors
            ]);
        }

        if ($request->has('max_slots')) {
            $device->schedules()->update([
                'max_slots' => (int) $request->max_slots
            ]);
        }

        // --- B. HANDLE SENSORS ---
        if ($request->has('sensors') && is_array($request->sensors)) {
            $availableSensors = Device::getAvailableSensors();
            $submittedSensors = $request->sensors;
            
            $sensorColumns = [];
            $sensorCounter = [];
            
            $autoKeyMap = [
                'ni_SUHU' => 'suhu',
                'ni_KELEM' => 'kelem',
                'ni_PH' => 'ph',
                'ni_TDS' => 'tds'
            ];
            $processedAutoKeys = [];

            foreach ($submittedSensors as $sensor) {
                $type = $sensor['type'] ?? null;
                if (!$type || !isset($availableSensors[$type])) continue;

                if (!isset($sensorCounter[$type])) $sensorCounter[$type] = 0;
                $sensorCounter[$type]++;

                $columnName = $sensorCounter[$type] > 1 ? "{$type}_{$sensorCounter[$type]}" : $type;
                $label = !empty($sensor['label']) ? $sensor['label'] : $availableSensors[$type]['label'];
                if ($sensorCounter[$type] > 1 && empty($sensor['label'])) {
                    $label .= " {$sensorCounter[$type]}";
                }
                
                $mqttKey = !empty($sensor['mqtt_key']) ? $sensor['mqtt_key'] : $columnName;

                $sensorColumns[$columnName] = [
                    'type' => $type,
                    'label' => $label,
                    'unit' => $availableSensors[$type]['unit'],
                    'mqtt_key' => $mqttKey,
                    'auto_enabled' => !empty($sensor['auto_enabled']),
                    'ats_val' => $sensor['ats_val'] ?? 0,
                    'bwh_val' => $sensor['bwh_val'] ?? 0,
                ];
            }

            // Dapatkan sensor lama
            $oldSensors = \App\Models\DeviceSensor::where('device_id', $device->id)->get();
            $oldSensorNames = $oldSensors->pluck('sensor_name')->toArray();
            $newSensorNames = array_keys($sensorColumns);

            $sensorsToAdd = array_diff($newSensorNames, $oldSensorNames);
            $sensorsToRemove = array_diff($oldSensorNames, $newSensorNames);

            // Eksekusi penambahan & penghapusan kolom di tabel log
            if (!empty($sensorsToAdd) || !empty($sensorsToRemove)) {
                Schema::table($device->table_name, function (Blueprint $table) use ($sensorsToAdd, $sensorsToRemove) {
                    foreach ($sensorsToRemove as $colRemove) {
                        $table->dropColumn($colRemove);
                    }
                    foreach ($sensorsToAdd as $colAdd) {
                        $table->float($colAdd)->nullable();
                    }
                });
            }

            // Hapus record sensor lama dari DB
            if (!empty($sensorsToRemove)) {
                \App\Models\DeviceSensor::where('device_id', $device->id)
                    ->whereIn('sensor_name', $sensorsToRemove)->delete();
            }

            // Tambah/Update record sensor
            foreach ($sensorColumns as $colName => $data) {
                \App\Models\DeviceSensor::updateOrCreate(
                    ['device_id' => $device->id, 'sensor_name' => $colName],
                    [
                        'mqtt_key' => $data['mqtt_key'],
                        'sensor_label' => $data['label'],
                        'unit' => $data['unit']
                    ]
                );

                // Handle Automasi
                if (isset($autoKeyMap[$data['type']])) {
                    $settingKey = $autoKeyMap[$data['type']];
                    if (in_array($settingKey, $processedAutoKeys)) continue;
                    $processedAutoKeys[] = $settingKey;

                    if ($data['auto_enabled']) {
                        \App\Models\DeviceSetting::updateOrCreate(
                            ['device_id' => $device->id, 'key' => "ats_{$settingKey}"],
                            ['value' => $data['ats_val']]
                        );
                        \App\Models\DeviceSetting::updateOrCreate(
                            ['device_id' => $device->id, 'key' => "bwh_{$settingKey}"],
                            ['value' => $data['bwh_val']]
                        );
                    } else {
                        \App\Models\DeviceSetting::where('device_id', $device->id)
                            ->whereIn('key', ["ats_{$settingKey}", "bwh_{$settingKey}"])
                            ->delete();
                    }
                }
            }
        }

        // --- C. HANDLE OUTPUTS ---
        if ($request->has('outputs') && is_array($request->outputs)) {
            $availableOutputs = Device::getAvailableOutputs();
            $submittedOutputs = $request->outputs;
            
            $outputColumns = [];
            $outputCounter = [];

            foreach ($submittedOutputs as $output) {
                $type = $output['type'] ?? null;
                if (!$type || !isset($availableOutputs[$type])) continue;

                if (!isset($outputCounter[$type])) $outputCounter[$type] = 0;
                $outputCounter[$type]++;

                $outputName = $outputCounter[$type] > 1 ? "{$type}_{$outputCounter[$type]}" : $type;
                $outputConfig = $availableOutputs[$type];
                
                $label = !empty($output['label']) ? $output['label'] : $outputConfig['label'];
                if ($outputCounter[$type] > 1 && empty($output['label'])) {
                    $label .= " {$outputCounter[$type]}";
                }

                $outputData = [
                    'output_label' => $label,
                    'output_type' => $outputConfig['type'],
                    'unit' => $outputConfig['unit'],
                ];
                
                if ($type === 'irrigation_pump' && isset($output['zones']) && $output['zones'] > 0) {
                    $outputData['max_sectors'] = min((int) $output['zones'], 20);
                }

                $outputColumns[$outputName] = $outputData;
            }

            $oldOutputs = \App\Models\DeviceOutput::where('device_id', $device->id)->get();
            $oldOutputNames = $oldOutputs->pluck('output_name')->toArray();
            $newOutputNames = array_keys($outputColumns);

            $outputsToRemove = array_diff($oldOutputNames, $newOutputNames);

            if (!empty($outputsToRemove)) {
                \App\Models\DeviceOutput::where('device_id', $device->id)
                    ->whereIn('output_name', $outputsToRemove)->delete();
            }

            foreach ($outputColumns as $colName => $data) {
                \App\Models\DeviceOutput::updateOrCreate(
                    ['device_id' => $device->id, 'output_name' => $colName],
                    $data
                );
            }
        }

        return redirect()->route('admin.devices.index')
            ->with('success', 'Data device berhasil diperbarui!');
    }

    // 6. PROSES HAPUS DEVICE (DESTROY)
    public function destroy($id)
    {
        $this->checkAdmin();
        $device = Device::findOrFail($id);

        // A. Hapus Tabel Log fisiknya dari database (PENTING!)
        // Hati-hati, data sensor akan hilang permanen
        Schema::dropIfExists($device->table_name);

        // B. Hapus data dari tabel devices
        $device->delete();

        return redirect()->route('admin.devices.index')
            ->with('success', 'Device dan Tabel Log berhasil dihapus permanen.');
    }

    // 7. HALAMAN MONITORING DEVICE (ADMIN VIEW)
    public function showMonitoring(Request $request, $id)
    {
        $this->checkAdmin();
        $isAdminView = true;

        $device = Device::with(['sensors', 'outputs'])->findOrFail($id);
        $sensors = $device->sensors;
        $outputs = $device->outputs;

        // Default values
        $logData = collect();
        $chartData = collect();
        $latestData = null;

        if ($device->table_name && Schema::hasTable($device->table_name)) {
            // Ambil data terbaru untuk display sensor cards (selalu nilai absolut terakhir, tanpa filter)
            // Ambil data terbaru PER SENSOR
            $latestData = new \stdClass();
            foreach ($sensors as $sensor) {
                $sensorName = $sensor->sensor_name;
                if (Schema::hasColumn($device->table_name, $sensorName)) {
                    $latestRow = \DB::table($device->table_name)
                        ->whereNotNull($sensorName)
                        ->orderBy('recorded_at', 'desc')
                        ->first();
                    $latestData->$sensorName = $latestRow ? $latestRow->$sensorName : null;
                }
            }
            // Also get the latest recorded_at timestamp
            $lastRow = \DB::table($device->table_name)->orderBy('recorded_at', 'desc')->first();
            $latestData->recorded_at = $lastRow ? $lastRow->recorded_at : null;
        }

        // Ambil konfigurasi jadwal jika ada
        $scheduleConfig = $device->schedules()->first();

        // Cek ketersediaan otomasi (berdasarkan sensor yang ada)
        $hasAutomation = $device->hasAnyAutomation();

        // Check if device is online
        $lastSeen = \Cache::get("device_{$device->id}_last_seen", $device->last_seen_at);
        if (isset($latestData->recorded_at) && $latestData->recorded_at) {
            $isOnline = \Carbon\Carbon::parse($latestData->recorded_at)->greaterThanOrEqualTo(now()->subMinutes(5));
        } else {
            $isOnline = $lastSeen ? \Carbon\Carbon::parse($lastSeen)->greaterThanOrEqualTo(now()->subMinutes(5)) : false;
        }

        return view('monitoring.show', compact('device', 'sensors', 'outputs', 'latestData', 'isAdminView', 'scheduleConfig', 'hasAutomation', 'isOnline', 'lastSeen'));
    }

    // HALAMAN HISTORY (ADMIN VIEW)
    public function history(Request $request, $id)
    {
        $this->checkAdmin();
        $isAdminView = true;

        $device = Device::with(['sensors'])->findOrFail($id);
        $sensors = $device->sensors;

        // Default values
        $logData = collect();
        $chartData = collect();

        if ($device->table_name && Schema::hasTable($device->table_name)) {
            $query = \DB::table($device->table_name);
            
            // Apply date filters if they exist
            if ($request->has('start_date') && $request->start_date) {
                $startDate = \Carbon\Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
                $query->where('recorded_at', '>=', $startDate);
            }
            if ($request->has('end_date') && $request->end_date) {
                $endDate = \Carbon\Carbon::parse($request->end_date);
                if (strlen($request->end_date) <= 10) {
                    $endDate->endOfDay();
                }
                $query->where('recorded_at', '<=', $endDate->format('Y-m-d H:i:s'));
            }
            
            // Clone query for pagination
            $logQuery = clone $query;
            $chartQuery = clone $query;

            // Ambil 50 data terbaru untuk chart
            $chartData = $chartQuery
                ->orderBy('recorded_at', 'desc')
                ->limit(50)
                ->get()
                ->reverse()
                ->values();

            // Ambil data untuk tabel dengan pagination (20 per halaman)
            $logData = $logQuery
                ->orderBy('recorded_at', 'desc')
                ->paginate(20)
                ->appends($request->all());
        } else {
            $logData = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        return view('monitoring.history', compact('device', 'sensors', 'logData', 'chartData', 'isAdminView'));

    }

    // 8. TOGGLE OUTPUT (ADMIN - uses device_id directly)
    public function toggleOutput(Request $request, $deviceId, $outputId)
    {
        $this->checkAdmin();

        $device = Device::findOrFail($deviceId);

        // Ambil output dari device ini
        $output = \App\Models\DeviceOutput::where('id', $outputId)
            ->where('device_id', $device->id)
            ->firstOrFail();

        // Validasi request
        $request->validate([
            'value' => 'required',
        ]);

        $newValue = $request->value;

        // Untuk boolean, konversi ke 0 atau 1
        if ($output->output_type === 'boolean') {
            $newValue = filter_var($newValue, FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        } else {
            $newValue = (float) $newValue;
        }

        // Update current_value di database
        $output->current_value = $newValue;
        $output->save();

        // Publish ke MQTT untuk kirim perintah ke device
        try {
            $topic = rtrim($device->mqtt_topic, '/') . '/sub';

            // Custom format based on output name
            $val = $newValue ? '1' : '0';
            $name = strtolower($output->output_name);

            // 1. Specific Pumps (Dosing & pH)
            if (str_contains($name, 'pump_ab') || str_contains($name, 'dosing') || $name === 'st_dos') {
                $message = "<pmpAB#{$val}#>";
            } elseif (str_contains($name, 'ph_up') || str_contains($name, 'ph1') || $name === 'st_ph_u') {
                $message = "<pmpPH#{$val}#>";
            } elseif (str_contains($name, 'ph_down') || str_contains($name, 'ph2') || $name === 'st_ph_d') {
                $message = "<pmpPH2#{$val}#>";
            }
            // 2. Main Pump (Pompa Utama / Irigasi)
            elseif (str_contains($name, 'pompa') || str_contains($name, 'pump') || $name === 'st_pmp') {
                if ($newValue) {
                    // Format: <PMP_ON#waterType#zone#>
                    $message = "<PMP_ON#0#0#>";
                } else {
                    $message = "<PMP_OFF#>";
                }
            }
            // 3. Components
            elseif (str_contains($name, 'air_input') || $name === 'st_air') {
                $message = "<AIR#{$val}#>";
            } elseif (str_contains($name, 'mix')) {
                $message = "<MIX#{$val}#>";
            } elseif (str_contains($name, 'fan') || $name === 'st_fa') {
                $message = "<FAN#{$val}#>";
            } elseif (str_contains($name, 'mist') || $name === 'st_mis') {
                $message = "<MIS#{$val}#>";
            } elseif (str_contains($name, 'lamp') || $name === 'st_lam') {
                $message = "<LAM#{$val}#>";
            } elseif ($name === 'sts_sld_op') {
                $message = "<SLD_OP#{$val}#>";
            } elseif ($name === 'sts_sld_tu') {
                $message = "<SLD_TU#{$val}#>";
            } elseif ($name === 'sts_cool') {
                $message = "<COOL#{$val}#>";
            }
            // 4. Air Baku & Air Pupuk
            elseif ($name === 'st_bak') {
                $message = "<BAK#{$val}#>";
            } elseif ($name === 'st_ppk') {
                $message = "<PPK#{$val}#>";
            }
            // 5. Fallback
            else {
                $message = sprintf('<%s#%s#>', $output->output_name, $val);
            }

            // MQTT Connection
            $host = config('mqtt.host', env('MQTT_HOST', 'smartagri.web.id'));
            $port = config('mqtt.port', env('MQTT_PORT', 1883));
            $username = config('mqtt.username', env('MQTT_USERNAME'));
            $password = config('mqtt.password', env('MQTT_PASSWORD'));

            $connectionSettings = new \PhpMqtt\Client\ConnectionSettings();
            if ($username && $password) {
                $connectionSettings = $connectionSettings
                    ->setUsername($username)
                    ->setPassword($password);
            }
            $connectionSettings = $connectionSettings
                ->setKeepAliveInterval(60)
                ->setConnectTimeout(10);

            $mqtt = new \PhpMqtt\Client\MqttClient($host, $port, 'laravel-admin-control-' . uniqid());
            $mqtt->connect($connectionSettings, true);
            $mqtt->publish($topic, $message, 1);
            $mqtt->disconnect();

            \Log::info("MQTT Admin Output Control sent", ['topic' => $topic, 'message' => $message]);
        } catch (\Exception $e) {
            \Log::error("MQTT Admin Output Control failed: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'output_id' => $output->id,
            'output_name' => $output->output_name,
            'new_value' => $newValue,
            'message' => "Output {$output->output_label} berhasil diupdate!",
        ]);
    }

    /**
     * Control Dosing Pump by Volume (Admin)
     * Sends MQTT commands like <pmpab#10#>, <pmpph#10#>, <pmpph2#10#>
     */
    public function controlDosingByVolume(Request $request, $deviceId)
    {
        $this->checkAdmin();

        $device = Device::findOrFail($deviceId);

        $request->validate([
            'pump_type' => 'required|string|in:dosing,ph_up,ph_down',
            'volume' => 'required|integer|min:1|max:9999',
        ]);

        $pumpType = $request->input('pump_type');
        $volume = $request->input('volume');

        // Map pump type to MQTT command (lowercase!)
        $commandMap = [
            'dosing'  => 'pmpab',
            'ph_up'   => 'pmpph',
            'ph_down' => 'pmpph2',
        ];

        $command = $commandMap[$pumpType] ?? null;
        if (!$command) {
            return response()->json([
                'success' => false,
                'message' => 'Tipe pompa tidak valid.',
            ], 400);
        }

        $message = "<{$command}#{$volume}#>";

        try {
            $topic = rtrim($device->mqtt_topic, '/') . '/sub';

            $host = config('mqtt.host', env('MQTT_HOST', 'smartagri.web.id'));
            $port = config('mqtt.port', env('MQTT_PORT', 1883));
            $username = config('mqtt.username', env('MQTT_USERNAME'));
            $password = config('mqtt.password', env('MQTT_PASSWORD'));

            $connectionSettings = new \PhpMqtt\Client\ConnectionSettings();
            if ($username && $password) {
                $connectionSettings = $connectionSettings
                    ->setUsername($username)
                    ->setPassword($password);
            }
            $connectionSettings = $connectionSettings
                ->setKeepAliveInterval(60)
                ->setConnectTimeout(10);

            $mqtt = new \PhpMqtt\Client\MqttClient($host, $port, 'laravel-admin-dosing-' . uniqid());
            $mqtt->connect($connectionSettings, true);
            $mqtt->publish($topic, $message, 1);
            $mqtt->disconnect();

            \Log::info("MQTT Admin Dosing by Volume sent", [
                'topic' => $topic,
                'message' => $message,
                'pump_type' => $pumpType,
                'volume' => $volume,
            ]);

            $pumpLabels = [
                'dosing' => 'Dosing AB',
                'ph_up' => 'pH Up',
                'ph_down' => 'pH Down',
            ];

            ActivityLog::log('dosing_control', "Admin mengirim {$pumpLabels[$pumpType]} {$volume} mL pada device '{$device->name}'", null, [
                'device_id' => $device->id,
                'pump_type' => $pumpType,
                'volume' => $volume,
                'mqtt_message' => $message,
            ]);

            return response()->json([
                'success' => true,
                'message' => "{$pumpLabels[$pumpType]} {$volume} mL berhasil dikirim!",
            ]);

        } catch (\Exception $e) {
            \Log::error("MQTT Admin Dosing by Volume failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim perintah: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Control special pump with zone and input type selection (Admin)
     * MQTT Format: <PMP_ON#zone#inputType#> or <PMP_OFF#>
     */
    public function controlPump(Request $request, $deviceId)
    {
        $this->checkAdmin();
        $device = Device::findOrFail($deviceId);
        $action = $request->input('action', 'off');

        try {
            $topic = rtrim($device->mqtt_topic, '/') . '/sub';

            if ($action === 'on') {
                $inputType = $request->input('input_type', 0); // 0 = Air Baku, 1 = Air Pupuk
                $zone = $request->input('zone', 1);
                $message = "<PMP_ON#{$inputType}#{$zone}#>";
            } else {
                $message = "<PMP_OFF#>";
            }

            $host = config('mqtt.host', env('MQTT_HOST', 'smartagri.web.id'));
            $port = config('mqtt.port', env('MQTT_PORT', 1883));
            $username = config('mqtt.username', env('MQTT_USERNAME'));
            $password = config('mqtt.password', env('MQTT_PASSWORD'));

            $connectionSettings = new \PhpMqtt\Client\ConnectionSettings();
            if ($username && $password) {
                $connectionSettings = $connectionSettings
                    ->setUsername($username)
                    ->setPassword($password);
            }
            $connectionSettings = $connectionSettings
                ->setKeepAliveInterval(60)
                ->setConnectTimeout(10);

            $mqtt = new \PhpMqtt\Client\MqttClient($host, $port, 'laravel-admin-pump-' . uniqid());
            $mqtt->connect($connectionSettings, true);
            $mqtt->publish($topic, $message, 1);
            $mqtt->disconnect();

            \Log::info("MQTT Admin Pump Control sent", ['topic' => $topic, 'message' => $message]);

            return response()->json([
                'success' => true,
                'action' => $action,
                'message' => $message,
                'zone' => $request->input('zone'),
                'input_type' => $request->input('input_type'),
            ]);
        } catch (\Exception $e) {
            \Log::error("MQTT Admin Pump Control failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim perintah: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Control irrigation pump with zone selection (Admin)
     * MQTT Format: <PMP_ON#waterType#zone#> or <PMP_OFF#>
     */
    public function controlIrrigationPump(Request $request, $deviceId, $outputId)
    {
        $this->checkAdmin();
        $device = Device::findOrFail($deviceId);

        $output = \App\Models\DeviceOutput::where('id', $outputId)
            ->where('device_id', $device->id)
            ->firstOrFail();

        $turnOn = filter_var($request->input('turnOn', false), FILTER_VALIDATE_BOOLEAN);
        $waterType = $request->input('waterType', 1); // 1 = pupuk, 2 = baku
        $zone = $request->input('zone', 1);

        try {
            $topic = rtrim($device->mqtt_topic, '/') . '/sub';

            if ($turnOn) {
                $message = "<PMP_ON#{$waterType}#{$zone}#>";
            } else {
                $message = "<PMP_OFF#>";
            }

            $host = config('mqtt.host', env('MQTT_HOST', 'smartagri.web.id'));
            $port = config('mqtt.port', env('MQTT_PORT', 1883));
            $username = config('mqtt.username', env('MQTT_USERNAME'));
            $password = config('mqtt.password', env('MQTT_PASSWORD'));

            $connectionSettings = new \PhpMqtt\Client\ConnectionSettings();
            if ($username && $password) {
                $connectionSettings = $connectionSettings
                    ->setUsername($username)
                    ->setPassword($password);
            }
            $connectionSettings = $connectionSettings
                ->setKeepAliveInterval(60)
                ->setConnectTimeout(10);

            $mqtt = new \PhpMqtt\Client\MqttClient($host, $port, 'laravel-admin-irrigation-' . uniqid());
            $mqtt->connect($connectionSettings, true);
            $mqtt->publish($topic, $message, 1);
            $mqtt->disconnect();

            \Log::info("MQTT Admin Irrigation Pump Control sent", [
                'topic' => $topic,
                'message' => $message,
                'output_id' => $outputId,
                'zone' => $zone,
                'water_type' => $waterType
            ]);

            return response()->json([
                'success' => true,
                'message' => $turnOn ? "Pompa irigasi dinyalakan (Zona $zone)" : "Pompa irigasi dimatikan",
            ]);
        } catch (\Exception $e) {
            \Log::error("MQTT Admin Irrigation Pump Control failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim perintah: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get real-time status for Admin (Direct Device ID)
     */
    public function getStatus($id)
    {
        $this->checkAdmin();

        $device = Device::with(['outputs'])->findOrFail($id);

        // Get Output States
        $outputs = $device->outputs->map(function ($output) {
            return [
                'id' => $output->id,
                'name' => $output->output_name,
                'value' => $output->current_value,
                'label' => $output->output_label
            ];
        });

        // Get Latest Sensor Data
        $latestSensorData = null;
        if ($device->table_name && Schema::hasTable($device->table_name)) {
            $latestSensorData = \DB::table($device->table_name)
                ->orderBy('recorded_at', 'desc')
                ->first();
        }

        // Get active schedules
        $activeSchedules = \App\Models\DeviceScheduleData::where('device_id', $device->id)
            ->where('is_active', true)
            ->get()
            ->map(function ($schedule) {
                return [
                    'key' => $schedule->slot_key,
                    'name' => $schedule->name,
                    'time' => $schedule->display_time,
                    'duration' => $schedule->duration,
                    'sector' => $schedule->sector,
                    'days' => $schedule->display_days,
                ];
            });

        return response()->json([
            'success' => true,
            'outputs' => $outputs,
            'sensors' => $latestSensorData,
            'schedules' => $activeSchedules,
            'timestamp' => now()->toIso8601String()
        ]);
    }
}