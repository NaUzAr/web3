<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\UserDevice;
use App\Models\DeviceOutput;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

class MonitoringController extends Controller
{
    /**
     * Halaman utama monitoring - list device user
     */
    public function index()
    {
        $userDevices = UserDevice::with(['device.sensors'])
            ->where('user_id', Auth::id())
            ->orderBy('is_favorite', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('monitoring.index', compact('userDevices'));
    }

    /**
     * Toggle favorite status device
     */
    public function toggleFavorite($id)
    {
        $userDevice = UserDevice::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $userDevice->is_favorite = !$userDevice->is_favorite;
        $userDevice->save();

        return response()->json([
            'success' => true,
            'is_favorite' => $userDevice->is_favorite,
        ]);
    }

    /**
     * Form tambah device via token
     */
    public function create()
    {
        return view('monitoring.add_device');
    }

    /**
     * Proses tambah device via token
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string|size:16',
            'custom_name' => 'nullable|string|max:100',
        ], [
            'token.required' => 'Token wajib diisi!',
            'token.size' => 'Token harus 16 karakter!',
        ]);

        // Cari device berdasarkan token
        $device = Device::where('token', $request->token)->first();

        if (!$device) {
            return back()->withErrors(['token' => 'Token tidak ditemukan! Pastikan token benar.'])->withInput();
        }

        // Cek apakah user sudah punya device ini
        $exists = UserDevice::where('user_id', Auth::id())
            ->where('device_id', $device->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['token' => 'Device ini sudah ada di daftar monitoring Anda.'])->withInput();
        }

        // Simpan ke user_devices
        UserDevice::create([
            'user_id' => Auth::id(),
            'device_id' => $device->id,
            'custom_name' => $request->custom_name ?: $device->name,
        ]);

        ActivityLog::log('add_device', "Menambahkan device '{$device->name}' ke monitoring");

        return redirect()->route('monitoring.index')
            ->with('success', "Device '{$device->name}' berhasil ditambahkan ke monitoring!");
    }

    /**
     * Halaman monitoring device - tampilkan data sensor
     */
    public function show(Request $request, $id)
    {
        // Pastikan user punya akses ke device ini
        $userDevice = UserDevice::with(['device.sensors', 'device.outputs'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $device = $userDevice->device;
        $sensors = $device->sensors;
        $outputs = $device->outputs;

        $latestData = null;

        if ($device->table_name && \Schema::hasTable($device->table_name)) {
            // Ambil data terbaru PER SENSOR (bukan dari satu baris)
            // Ini memastikan setiap sensor card menampilkan nilai terbaru meskipun datang dari paket berbeda
            $latestData = new \stdClass();
            foreach ($sensors as $sensor) {
                $sensorName = $sensor->sensor_name;
                if (\Schema::hasColumn($device->table_name, $sensorName)) {
                    $latestRow = DB::table($device->table_name)
                        ->whereNotNull($sensorName)
                        ->orderBy('recorded_at', 'desc')
                        ->first();
                    $latestData->$sensorName = $latestRow ? $latestRow->$sensorName : null;
                }
            }
            // Also get the latest recorded_at timestamp
            $lastRow = DB::table($device->table_name)->orderBy('recorded_at', 'desc')->first();
            $latestData->recorded_at = $lastRow ? $lastRow->recorded_at : null;
        }

        // Ambil konfigurasi jadwal jika ada
        $scheduleConfig = $device->schedules()->first();

        // Cek ketersediaan otomasi (berdasarkan sensor yang ada)
        $hasAutomation = $device->hasAnyAutomation();

        // Check if device is online (last seen within 5 minutes)
        $lastSeen = \Cache::get("device_{$device->id}_last_seen", $device->last_seen_at);
        $isOnline = $lastSeen ? now()->diffInMinutes(\Carbon\Carbon::parse($lastSeen)) <= 5 : false;

        return view('monitoring.show', compact('userDevice', 'device', 'sensors', 'outputs', 'latestData', 'scheduleConfig', 'hasAutomation', 'isOnline', 'lastSeen'));
    }

    /**
     * Halaman Riwayat Data (Tabel dan Grafik)
     */
    public function history(Request $request, $id)
    {
        // Pastikan user punya akses ke device ini
        $userDevice = UserDevice::with(['device.sensors'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $device = $userDevice->device;
        $sensors = $device->sensors;

        // Default values
        $logData = collect();
        $chartData = collect();
        $isAdminView = false; // Karena ini dari MonitoringController (user biasa)

        if ($device->table_name && \Schema::hasTable($device->table_name)) {
            $query = DB::table($device->table_name);
            
            // Apply date filters if they exist
            if ($request->has('start_date') && $request->start_date) {
                $startDate = \Carbon\Carbon::parse($request->start_date)->format('Y-m-d H:i:s');
                $query->where('recorded_at', '>=', $startDate);
            }
            if ($request->has('end_date') && $request->end_date) {
                $endDate = \Carbon\Carbon::parse($request->end_date);
                // Jika input hanya Y-m-d tanpa waktu, jadikan 23:59:59. Jika ada waktu, biarkan sesuai input.
                if (strlen($request->end_date) <= 10) {
                    $endDate->endOfDay();
                }
                $query->where('recorded_at', '<=', $endDate->format('Y-m-d H:i:s'));
            }
            
            // Clone query for pagination and chart
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

        return view('monitoring.history', compact('device', 'userDevice', 'sensors', 'logData', 'chartData', 'isAdminView'));
    }

    /**
     * Update data (custom name dan notes) device milik user
     */
    public function updateUserDevice(Request $request, $id)
    {
        $request->validate([
            'custom_name' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $userDevice = UserDevice::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $userDevice->update([
            'custom_name' => $request->custom_name,
            'notes' => $request->notes,
        ]);

        ActivityLog::log('update_device', "Memperbarui info device '{$userDevice->custom_name}'");

        return back()->with('success', "Informasi device berhasil diperbarui.");
    }

    /**
     * Hapus device dari monitoring user
     */
    public function destroy($id)
    {
        $userDevice = UserDevice::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $deviceName = $userDevice->custom_name;
        $userDevice->delete();

        ActivityLog::log('remove_device', "Menghapus device '{$deviceName}' dari monitoring");

        return redirect()->route('monitoring.index')
            ->with('success', "Device '{$deviceName}' berhasil dihapus dari monitoring.");
    }

    /**
     * Export data sensor ke CSV
     */
    public function exportCsv(Request $request, $id)
    {
        // Validasi user punya akses
        $userDevice = UserDevice::with(['device.sensors'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $device = $userDevice->device;
        $sensors = $device->sensors;

        // Validasi tanggal
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = $request->start_date . ' 00:00:00';
        $endDate = $request->end_date . ' 23:59:59';

        // Ambil data dari database
        if (!$device->table_name || !\Schema::hasTable($device->table_name)) {
            return back()->with('error', 'Tidak ada data untuk diexport.');
        }

        $query = DB::table($device->table_name)
            ->whereBetween('recorded_at', [$startDate, $endDate])
            ->orderBy('recorded_at', 'asc');

        if (!$query->exists()) {
            return back()->with('error', 'Tidak ada data pada rentang tanggal tersebut.');
        }

        // Generate CSV
        $filename = 'sensor_data_' . $device->token . '_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $callback = function () use ($query, $sensors) {
            // Hindari timeout jika data sangat besar
            set_time_limit(0);
            
            $file = fopen('php://output', 'w');

            // Header row
            $headerRow = ['No', 'Waktu'];
            foreach ($sensors as $sensor) {
                $headerRow[] = $sensor->sensor_label . ' (' . $sensor->unit . ')';
            }
            fputcsv($file, $headerRow);

            // Data rows di-load per baris (cursor)
            $no = 1;
            foreach ($query->cursor() as $row) {
                $dataRow = [$no++, $row->recorded_at];
                foreach ($sensors as $sensor) {
                    $dataRow[] = $row->{$sensor->sensor_name} ?? '';
                }
                fputcsv($file, $dataRow);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    /**
     * Toggle output state (AJAX endpoint)
     */
    public function toggleOutput(Request $request, $userDeviceId, $outputId)
    {
        // Validasi user punya akses ke device ini
        $userDevice = UserDevice::where('user_id', Auth::id())
            ->where('id', $userDeviceId)
            ->with('device')
            ->firstOrFail();

        // Ambil output dari device ini
        $output = DeviceOutput::where('id', $outputId)
            ->where('device_id', $userDevice->device_id)
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
            $device = $userDevice->device;
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
                    // Format: <PMP_ON#waterType#zone#> (1=pupuk/baku, 1=blok)
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

            $mqtt = new \PhpMqtt\Client\MqttClient($host, $port, 'laravel-control-' . uniqid());
            $mqtt->connect($connectionSettings, true);
            $mqtt->publish($topic, $message, 1);
            $mqtt->disconnect();

            \Log::info("MQTT Output Control sent", ['topic' => $topic, 'message' => $message]);
        } catch (\Exception $e) {
            \Log::error("MQTT Output Control failed: " . $e->getMessage());
            // Continue anyway, database already updated
        }

        $actionName = $newValue ? 'Menghidupkan' : 'Mematikan';
        ActivityLog::log('device_control', "{$actionName} {$output->output_label} pada device '{$device->name}'", null, [
            'device_id' => $device->id,
            'output_id' => $output->id,
            'output_name' => $output->output_name,
            'new_value' => $newValue,
        ]);

        return response()->json([
            'success' => true,
            'output_id' => $output->id,
            'output_name' => $output->output_name,
            'new_value' => $newValue,
            'message' => "Output {$output->output_label} berhasil diupdate!",
        ]);
    }

    /**
     * Control special pump with zone and input type selection
     * MQTT Format: <PMP_ON#zone#inputType#> or <PMP_OFF#>
     */
    public function controlPump(Request $request, $userDeviceId)
    {
        // Validasi user punya akses ke device ini
        $userDevice = UserDevice::where('user_id', Auth::id())
            ->where('id', $userDeviceId)
            ->with('device')
            ->firstOrFail();

        $device = $userDevice->device;
        $action = $request->input('action', 'off');

        try {
            $topic = rtrim($device->mqtt_topic, '/') . '/sub';

            if ($action === 'on') {
                $inputType = $request->input('input_type', 0); // 0 = Air Baku, 1 = Air Pupuk
                $zone = $request->input('zone', 1);
                // Format: <PMP_ON#waterType#zone#>
                $message = "<PMP_ON#{$inputType}#{$zone}#>";
            } else {
                $message = "<PMP_OFF#>";
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

            $mqtt = new \PhpMqtt\Client\MqttClient($host, $port, 'laravel-pump-' . uniqid());
            $mqtt->connect($connectionSettings, true);
            $mqtt->publish($topic, $message, 1);
            $mqtt->disconnect();

            \Log::info("MQTT Pump Control sent", ['topic' => $topic, 'message' => $message]);

            $actionName = $action === 'on' ? 'Menghidupkan' : 'Mematikan';
            ActivityLog::log('pump_control', "{$actionName} pompa spesial pada device '{$device->name}'", null, [
                'device_id' => $device->id,
                'action_type' => $action,
                'zone' => $request->input('zone'),
                'input_type' => $request->input('input_type'),
            ]);

            return response()->json([
                'success' => true,
                'action' => $action,
                'message' => $message,
                'zone' => $request->input('zone'),
                'input_type' => $request->input('input_type'),
            ]);
        } catch (\Exception $e) {
            \Log::error("MQTT Pump Control failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim perintah: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Control irrigation pump with zone selection (multi-zone)
     * MQTT Format: <PMP_ON#waterType#zone#> or <PMP_OFF#>
     * Water Type: 1 = pupuk, 2 = baku
     */
    public function controlIrrigationPump(Request $request, $userDeviceId, $outputId)
    {
        // Validasi user punya akses ke device ini
        $userDevice = UserDevice::where('user_id', Auth::id())
            ->where('id', $userDeviceId)
            ->with('device')
            ->firstOrFail();

        // Verify the output belongs to this device
        $output = DeviceOutput::where('id', $outputId)
            ->where('device_id', $userDevice->device_id)
            ->firstOrFail();

        $device = $userDevice->device;
        $turnOn = filter_var($request->input('turnOn', false), FILTER_VALIDATE_BOOLEAN);
        $waterType = $request->input('waterType', 1); // 1 = pupuk, 2 = baku
        $zone = $request->input('zone', 1);

        try {
            $topic = rtrim($device->mqtt_topic, '/') . '/sub';

            if ($turnOn) {
                // Format: <PMP_ON#waterType#zone#>
                $message = "<PMP_ON#{$waterType}#{$zone}#>";
            } else {
                $message = "<PMP_OFF#>";
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

            $mqtt = new \PhpMqtt\Client\MqttClient($host, $port, 'laravel-irrigation-' . uniqid());
            $mqtt->connect($connectionSettings, true);
            $mqtt->publish($topic, $message, 1);
            $mqtt->disconnect();

            \Log::info("MQTT Irrigation Pump Control sent", [
                'topic' => $topic,
                'message' => $message,
                'output_id' => $outputId,
                'zone' => $zone,
                'water_type' => $waterType
            ]);

            $actionName = $turnOn ? 'Menghidupkan' : 'Mematikan';
            ActivityLog::log('irrigation_control', "{$actionName} pompa irigasi pada device '{$device->name}' (Zona {$zone})", null, [
                'device_id' => $device->id,
                'output_id' => $outputId,
                'zone' => $zone,
                'water_type' => $waterType,
                'turn_on' => $turnOn,
            ]);

            return response()->json([
                'success' => true,
                'turnOn' => $turnOn,
                'message' => $message,
                'zone' => $zone,
                'waterType' => $waterType,
                'output_id' => $outputId,
            ]);
        } catch (\Exception $e) {
            \Log::error("MQTT Irrigation Pump Control failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim perintah: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Control dosing/pH pump by volume (milliliter)
     * MQTT Format (lowercase!):
     *   Dosing AB:  <pmpab#10#>  → 10 = 10 milliliter
     *   pH Up:      <pmpph#10#>  → 10 = 10 milliliter
     *   pH Down:    <pmpph2#10#> → 10 = 10 milliliter
     */
    public function controlDosingByVolume(Request $request, $userDeviceId)
    {
        $userDevice = UserDevice::where('user_id', Auth::id())
            ->where('id', $userDeviceId)
            ->with('device')
            ->firstOrFail();

        $device = $userDevice->device;

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

            $mqtt = new \PhpMqtt\Client\MqttClient($host, $port, 'laravel-dosing-' . uniqid());
            $mqtt->connect($connectionSettings, true);
            $mqtt->publish($topic, $message, 1);
            $mqtt->disconnect();

            \Log::info("MQTT Dosing by Volume sent", [
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

            ActivityLog::log('dosing_control', "Mengirim {$pumpLabels[$pumpType]} {$volume} mL pada device '{$device->name}'", null, [
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
            \Log::error("MQTT Dosing by Volume failed: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim perintah: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get real-time status (outputs & latest sensor data)
     * Polled by frontend
     */
    public function getStatus($id)
    {
        // Validasi user punya akses
        $userDevice = UserDevice::with(['device.outputs'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $device = $userDevice->device;

        // Get Output States from Cache
        $cachedOutputs = \Cache::get("device_outputs_{$device->id}", []);

        $outputs = $device->outputs->map(function ($output) use ($cachedOutputs) {
            $outputName = $output->output_name;
            $cachedVal = null;

            // Strategy 1: Direct match
            if (isset($cachedOutputs[$outputName])) {
                $cachedVal = $cachedOutputs[$outputName];
            }

            // Strategy 2: Try converting between sts_ and st_ prefixes
            if ($cachedVal === null) {
                if (str_starts_with($outputName, 'sts_')) {
                    // DB has sts_, cache might have st_
                    $stKey = 'st_' . substr($outputName, 4);
                    if (isset($cachedOutputs[$stKey])) {
                        $cachedVal = $cachedOutputs[$stKey];
                    }
                } elseif (str_starts_with($outputName, 'st_')) {
                    // DB has st_, cache might have sts_
                    $stsKey = 'sts_' . substr($outputName, 3);
                    if (isset($cachedOutputs[$stsKey])) {
                        $cachedVal = $cachedOutputs[$stsKey];
                    }
                }
            }

            // Strategy 3: Partial match - check if any cache key contains the core name
            if ($cachedVal === null) {
                $coreName = preg_replace('/^(sts_|st_)/', '', $outputName);
                foreach ($cachedOutputs as $cacheKey => $cacheValue) {
                    $cacheCore = preg_replace('/^(sts_|st_)/', '', $cacheKey);
                    if ($coreName === $cacheCore || str_contains($cacheCore, $coreName) || str_contains($coreName, $cacheCore)) {
                        $cachedVal = $cacheValue;
                        break;
                    }
                }
            }

            $val = $cachedVal !== null ? $cachedVal : $output->current_value;

            return [
                'id' => $output->id,
                'name' => $output->output_name,
                'value' => $val,
                'label' => $output->output_label
            ];
        });

        // Get Latest Sensor Data - PER SENSOR (bukan dari satu baris)
        $latestSensorData = null;
        if ($device->table_name && \Schema::hasTable($device->table_name)) {
            $latestSensorData = new \stdClass();
            $sensors = $device->sensors;
            foreach ($sensors as $sensor) {
                $sensorName = $sensor->sensor_name;
                if (\Schema::hasColumn($device->table_name, $sensorName)) {
                    $latestRow = DB::table($device->table_name)
                        ->whereNotNull($sensorName)
                        ->orderBy('recorded_at', 'desc')
                        ->first();
                    $latestSensorData->$sensorName = $latestRow ? $latestRow->$sensorName : null;
                }
            }
            // Also get the latest recorded_at timestamp
            $lastRow = DB::table($device->table_name)->orderBy('recorded_at', 'desc')->first();
            $latestSensorData->recorded_at = $lastRow ? $lastRow->recorded_at : null;
        }

        // Get Schedule Config
        $scheduleConfig = $device->schedules()->first();
        $maxSlots = $scheduleConfig?->max_slots ?? 14;
        $maxSectors = $scheduleConfig?->max_sectors ?? 1;
        $scheduleMode = $scheduleConfig?->schedule_mode ?? 'time_days_duration_sector_type';

        // Get Device Schedules from Cache (all schedules, not just active)
        $cachedSchedules = \Cache::get("device_schedules_{$device->id}", []);

        // Format schedules for frontend
        $schedules = collect($cachedSchedules)->map(function ($schedule) {
            $days = is_array($schedule['days']) ? implode(', ', $schedule['days']) : ($schedule['days'] ?? '-');
            $time = $schedule['on_time'] ? substr($schedule['on_time'], 0, 5) : '-';

            return [
                'key' => $schedule['slot_key'],
                'name' => $schedule['name'] ?? '-',
                'time' => $time,
                'duration' => $schedule['duration'] ?? 0,
                'sector' => $schedule['sector'] ?? 0,
                'days' => $days,
                'is_active' => $schedule['is_active'] ?? false,
            ];
        })->sortBy(function ($item) {
            return (int) str_replace('sch', '', $item['key']);
        })->values();

        return response()->json([
            'success' => true,
            'outputs' => $outputs,
            'sensors' => $latestSensorData,
            'schedules' => $schedules,
            'schedule_config' => [
                'max_slots' => $maxSlots,
                'max_sectors' => $maxSectors,
                'mode' => $scheduleMode,
            ],
            'timestamp' => now()->toIso8601String()
        ]);
    }
}
