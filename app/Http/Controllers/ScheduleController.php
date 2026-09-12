<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\UserDevice;
use App\Models\DeviceOutput;
use App\Services\MqttScheduleService;
use App\Services\MqttSmartFarmService;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    private $mqttService;
    private $smartFarmService;

    public function __construct(MqttScheduleService $mqttService, MqttSmartFarmService $smartFarmService)
    {
        $this->mqttService = $mqttService;
        $this->smartFarmService = $smartFarmService;
    }

    private function getDevice($id)
    {
        if (Auth::user()->is_admin) {
            $device = Device::find($id);
            if ($device) {
                return $device;
            }

            $userDevice = UserDevice::find($id);
            if ($userDevice && $userDevice->device) {
                return $userDevice->device;
            }

            throw (new \Illuminate\Database\Eloquent\ModelNotFoundException)->setModel(Device::class, [$id]);
        }

        $userDevice = UserDevice::where('user_id', Auth::id())
            ->where(function ($query) use ($id) {
                $query->where('id', $id)
                      ->orWhere('device_id', $id);
            })
            ->firstOrFail();

        return $userDevice->device;
    }

    /**
     * Show schedule management page for specific device
     */
    public function index($userDeviceId)
    {
        $device = $this->getDevice($userDeviceId);
        $isAdminView = Auth::user()->is_admin;
        
        // For views, resolve proper UserDevice or pass dummy so it doesn't break blade variables
        if ($isAdminView) {
            $foundUserDevice = UserDevice::find($userDeviceId);
            if ($foundUserDevice && $foundUserDevice->device_id == $device->id) {
                $userDevice = $foundUserDevice;
            } else {
                $userDevice = new UserDevice(['device_id' => $device->id, 'custom_name' => $device->name]);
                $userDevice->id = $device->id;
                $userDevice->setRelation('device', $device);
            }
        } else {
            $userDevice = UserDevice::where('user_id', Auth::id())
                ->where(function ($query) use ($userDeviceId) {
                    $query->where('id', $userDeviceId)
                          ->orWhere('device_id', $userDeviceId);
                })
                ->first();
        }

        // Check if device has schedule functionality
        $scheduleConfig = \App\Models\DeviceSchedule::where('device_id', $device->id)->first();

        if (!$scheduleConfig) {
            return redirect()->route('monitoring.show', $userDevice->id)
                ->with('error', 'Device ini tidak memiliki konfigurasi penjadwalan.');
        }

        // Get cached schedules (with fallback to legacy cache prefixes if any)
        $cachedSchedules = $this->getCachedSchedulesForDevice($device->id);

        // Smart Farm: Auto-request jadwal dari device setiap kali halaman dibuka / di-load
        if ($device->type === 'smart_farm') {
            $topic = $device->mqtt_topic_schedule ?: $device->mqtt_topic;
            $this->smartFarmService->sendJadwalGet($topic);
        }

        return view('schedule.index', compact('userDevice', 'device', 'scheduleConfig', 'cachedSchedules', 'isAdminView'));
    }

    /**
     * Send schedule to device
     * Supports modes: time, time_days, time_days_sector, time_duration, time_days_duration, irigasi_jadwal
     */
    public function storeTimeSchedules(Request $request, $userDeviceId)
    {
        $device = $this->getDevice($userDeviceId);
        $scheduleConfig = \App\Models\DeviceSchedule::where('device_id', $device->id)->firstOrFail();

        // === SMART FARM: Format CMD:JADWAL_SET ===
        if ($device->type === 'smart_farm' || $scheduleConfig->schedule_mode === 'irigasi_jadwal') {
            return $this->storeSmartFarmSchedule($request, $device);
        }

        // === DEVICE LAIN: Format legacy <sch#...> ===
        // Flexible validation - accept both duration and off_time
        $rules = [
            'slot_id' => 'required|integer|min:1',
            'on_time' => 'required|date_format:H:i',
            'schedule_type' => 'nullable|string|in:BAKU,PUPUK',
            'duration' => 'nullable|integer|min:1|max:1440',
            'off_time' => 'nullable|date_format:H:i',
            'days' => 'nullable|string|max:7',
            'sector' => 'nullable|integer|min:1',
        ];

        $validated = $request->validate($rules);

        // Prepare schedule payload
        $onTime = $validated['on_time'];
        $offTime = null;

        // Calculate off_time from duration if provided
        if (!empty($validated['duration'])) {
            $startTime = \Carbon\Carbon::createFromFormat('H:i', $onTime);
            $endTime = $startTime->copy()->addMinutes((int) $validated['duration']);
            $offTime = $endTime->format('H:i');
        } elseif (!empty($validated['off_time'])) {
            $offTime = $validated['off_time'];
        } else {
            // Default: 5 minutes duration
            $startTime = \Carbon\Carbon::createFromFormat('H:i', $onTime);
            $offTime = $startTime->copy()->addMinutes(5)->format('H:i');
        }

        // Build schedule array
        $schedule = [
            'id' => $validated['slot_id'],
            'on' => $onTime,
            'off' => $offTime,
        ];

        // Add days if provided
        if (!empty($validated['days'])) {
            $schedule['days'] = $validated['days'];
        }

        // Add sector if provided
        if (!empty($validated['sector'])) {
            $schedule['sector'] = $validated['sector'];
        }

        // Get schedule type (Jenis) - use as output_key if schedule_type is provided
        $outputKey = $validated['schedule_type'] ?? $scheduleConfig->output_key ?? 'general';

        $topic = $device->mqtt_topic_schedule ? $device->mqtt_topic_schedule : $device->mqtt_topic;

        // Send to MQTT
        $success = $this->mqttService->sendSingleTimeSchedule(
            $topic,
            $device->token,
            $outputKey,
            $schedule,
            $scheduleConfig->schedule_mode
        );

        if ($success) {
            $msg = 'Jadwal slot ' . $validated['slot_id'] . ' berhasil dikirim!';
            if (!empty($validated['duration'])) {
                $msg .= " (Durasi: {$validated['duration']} menit, selesai pukul {$offTime})";
            }

            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim jadwal ke device via MQTT.',
        ], 500);
    }

    /**
     * Smart Farm: Simpan jadwal irigasi via CMD:JADWAL_SET
     * 
     * Format: CMD:JADWAL_SET:<idx>:<jam>:<menit>:<durasi>:<blok>:<literPupuk10>:<hari_bitmask>:<aktif>
     */
    private function storeSmartFarmSchedule(Request $request, Device $device)
    {
        $slotId = (int) $request->input('slot_id');
        // Map slot 1-10 ke index 0-9 untuk STM32
        $idx = ($slotId >= 1 && $slotId <= 10) ? ($slotId - 1) : $slotId;
        $idx = max(0, min(9, $idx));

        $validated = $request->validate([
            'on_time' => 'required|date_format:H:i',
            'duration' => 'nullable|integer|min:1|max:120',
            'blok' => 'nullable|integer|min:1|max:3',
            'sector' => 'nullable|integer|min:1|max:3',
            'liter_pupuk' => 'nullable|numeric|min:0|max:50',    // liter asli (bukan x10)
            'aktif' => 'nullable|boolean',
        ]);

        $blok = (int) ($validated['blok'] ?? $validated['sector'] ?? 1);
        $duration = (int) ($validated['duration'] ?? 5);

        // Parse time
        $timeParts = explode(':', $validated['on_time']);
        $jam = (int) $timeParts[0];
        $menit = (int) $timeParts[1];

        // Konversi liter pupuk ke x10 (2.5L -> 25)
        $literPupuk10 = (int) round(($request->input('liter_pupuk') ?? 0) * 10);

        // Konversi days ke bitmask (bisa array, string angka '1234567', atau null)
        $hariBitmask = 127; // default setiap hari
        if ($request->filled('days')) {
            $hariBitmask = MqttSmartFarmService::daysToBitmask($request->input('days'));
        }

        $topic = $device->mqtt_topic_schedule ?: $device->mqtt_topic;

        $success = $this->smartFarmService->sendJadwalSet($topic, $idx, [
            'jam' => $jam,
            'menit' => $menit,
            'durasi' => $duration,
            'blok' => $blok,
            'liter_pupuk_10' => $literPupuk10,
            'hari_bitmask' => $hariBitmask,
            'aktif' => $request->input('aktif', 1),
        ]);

        if ($success) {
            $daysArray = MqttSmartFarmService::bitmaskToDays($hariBitmask);
            $daysText = $request->filled('days')
                ? implode(', ', $daysArray)
                : 'Setiap hari';
            $pupukText = $literPupuk10 > 0
                ? ', Pupuk: ' . ($literPupuk10 / 10) . 'L'
                : '';

            $displaySlot = $idx + 1;
            $slotKey = "sch{$displaySlot}";

            // Simpan ke cache agar tampilan web langsung terupdate
            $cacheKey = "device_schedules_{$device->id}";
            $cachedSchedules = \Cache::get($cacheKey, []);
            $cachedSchedules[$slotKey] = [
                'slot_key' => $slotKey,
                'on_time' => $validated['on_time'],
                'duration' => $duration,
                'blok' => $blok,
                'sector' => $blok,
                'liter_pupuk' => $literPupuk10 / 10,
                'liter_pupuk_10' => $literPupuk10,
                'days' => $daysArray,
                'hari_bitmask' => $hariBitmask,
                'is_active' => (bool) $request->input('aktif', 1),
                'updated_at' => now()->toIso8601String(),
            ];
            \Cache::put($cacheKey, $cachedSchedules, now()->addDays(30));

            // Smart Farm: Request lagi jadwal dari device setelah edit untuk memastikan data EEPROM terbaru tersinkron
            usleep(250000); // jeda 250ms agar STM32 selesai commit EEPROM
            $this->smartFarmService->sendJadwalGet($topic);
            session()->put("sf_schedule_requested_{$device->id}", true);

            return response()->json([
                'success' => true,
                'message' => "Jadwal #{$displaySlot} berhasil disimpan! Data disinkronkan ke alat.",
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim jadwal ke device.',
        ], 500);
    }

    /**
     * Delete/Disable schedule slot
     */
    public function destroy($userDeviceId, $slotId)
    {
        $device = $this->getDevice($userDeviceId);

        $topic = $device->mqtt_topic_schedule ? $device->mqtt_topic_schedule : $device->mqtt_topic;

        // === SMART FARM: CMD:JADWAL_DEL ===
        if ($device->type === 'smart_farm') {
            $rawSlot = (int) $slotId;
            $idx = ($rawSlot >= 1 && $rawSlot <= 10) ? ($rawSlot - 1) : $rawSlot;
            $idx = max(0, min(9, $idx));
            $success = $this->smartFarmService->sendJadwalDel($topic, $idx);

            if ($success) {
                // Smart Farm: Request lagi jadwal dari device setelah hapus
                usleep(250000);
                $this->smartFarmService->sendJadwalGet($topic);
                session()->put("sf_schedule_requested_{$device->id}", true);

                $displaySlot = $idx + 1;
                $slotKey = "sch{$displaySlot}";
                $cacheKey = "device_schedules_{$device->id}";
                $cachedSchedules = \Cache::get($cacheKey, []);
                unset($cachedSchedules[$slotKey]);
                \Cache::put($cacheKey, $cachedSchedules, now()->addDays(30));
            }
        }
        // === DEVICE LAIN: Format legacy ===
        else {
            $success = $this->mqttService->deleteSchedule(
                $topic,
                $device->token,
                (int) $slotId
            );
        }

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Perintah hapus jadwal slot ' . $slotId . ' dikirim.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim perintah hapus.'
        ], 500);
    }

    /**
     * Smart Farm: Jalankan jadwal irigasi secara manual
     * 
     * Format: CMD:SIRAM_START:<index>
     */
    public function siramStart(Request $request, $userDeviceId)
    {
        $device = $this->getDevice($userDeviceId);

        if ($device->type !== 'smart_farm') {
            return response()->json([
                'success' => false,
                'message' => 'Fitur ini hanya untuk device Smart Farm.',
            ], 400);
        }

        $validated = $request->validate([
            'jadwal_index' => 'required|integer|min:0|max:9',
        ]);

        $topic = $device->mqtt_topic_schedule ?: $device->mqtt_topic;
        $success = $this->smartFarmService->sendSiramStart($topic, $validated['jadwal_index']);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => "Perintah siram jadwal #{$validated['jadwal_index']} dikirim!",
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim perintah siram.',
        ], 500);
    }

    /**
     * Smart Farm: Stop penyiraman yang sedang berjalan
     * 
     * Format: CMD:SIRAM_STOP
     */
    public function siramStop($userDeviceId)
    {
        $device = $this->getDevice($userDeviceId);

        if ($device->type !== 'smart_farm') {
            return response()->json([
                'success' => false,
                'message' => 'Fitur ini hanya untuk device Smart Farm.',
            ], 400);
        }

        $topic = $device->mqtt_topic_schedule ?: $device->mqtt_topic;
        
        // 1. Kirim CMD:SIRAM_STOP (menghentikan rutinitas siram terjadwal)
        $this->smartFarmService->sendSiramStop($topic);

        // 2. Kirim perintah matikan semua blok & pompa via CMD:BLOK:0 dan pupuk OFF via CMD:RELAY:4:0
        $this->smartFarmService->sendBlok($topic, 0);   // Blok & Pompa OFF
        $this->smartFarmService->sendRelay($topic, 4, 0); // Pupuk OFF

        // 3. Reset status di DB & cache
        \App\Models\DeviceOutput::where('device_id', $device->id)
            ->whereIn('output_name', ['sf_pompa', 'sf_blok1', 'sf_blok2', 'sf_blok3', 'sf_pupuk'])
            ->update(['current_value' => 0]);

        $cachedOutputs = \Cache::get("device_outputs_{$device->id}", []);
        foreach (['sf_pompa', 'sf_blok1', 'sf_blok2', 'sf_blok3', 'sf_pupuk'] as $k) {
            $cachedOutputs[$k] = 0;
        }
        \Cache::put("device_outputs_{$device->id}", $cachedOutputs, now()->addHours(24));

        $sfStatus = \Cache::get("device_sf_status_{$device->id}", []);
        $sfStatus['siram'] = 0;
        $sfStatus['blok'] = 0;
        $sfStatus['pompa'] = 0;
        $sfStatus['pupuk'] = 'NONE';
        $sfStatus['sisa'] = 0;
        $sfStatus['sisa_formatted'] = '00:00';
        $sfStatus['mode'] = 'STANDBY';
        $sfStatus['mode_label'] = 'Standby (Siaga)';
        \Cache::put("device_sf_status_{$device->id}", $sfStatus, now()->addHours(1));
        \Cache::forget("device_was_siram_{$device->id}");

        // 4. Broadcast realtime update ke UI
        $deviceOutputs = \App\Models\DeviceOutput::where('device_id', $device->id)->get();
        $formattedOutputs = $deviceOutputs->map(fn($o) => ['id' => $o->id, 'value' => 0])->toArray();
        $lastSeen = \Cache::get("device_{$device->id}_last_seen", $device->last_seen_at);
        $sensorBuffer = \Cache::get("sensor_buffer_{$device->id}", []);
        event(new \App\Events\DeviceStatusUpdated($device->id, $sensorBuffer, $formattedOutputs, $lastSeen, $sfStatus));

        return response()->json([
            'success' => true,
            'message' => 'Penyiraman dan semua relay berhasil dihentikan!',
        ]);
    }

    /**
     * Smart Farm: Reset Error Relay (CMD:RESET_ERROR)
     */
    public function resetError($userDeviceId)
    {
        $device = $this->getDevice($userDeviceId);

        if ($device->type !== 'smart_farm') {
            return response()->json([
                'success' => false,
                'message' => 'Fitur ini hanya untuk device Smart Farm.',
            ], 400);
        }

        $topic = $device->mqtt_topic_schedule ?: $device->mqtt_topic;
        $success = $this->smartFarmService->sendResetError($topic);

        if ($success) {
            $sfStatus = \Cache::get("device_sf_status_{$device->id}", []);
            $sfStatus['error'] = 0;
            \Cache::put("device_sf_status_{$device->id}", $sfStatus, now()->addHours(1));

            return response()->json([
                'success' => true,
                'message' => 'Perintah reset error relay berhasil dikirim!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim perintah reset error.',
        ], 500);
    }

    /**
     * Smart Farm: Cek Status Relay (CMD:RELAY_STATUS)
     */
    public function checkRelayStatus($userDeviceId)
    {
        $device = $this->getDevice($userDeviceId);

        if ($device->type !== 'smart_farm') {
            return response()->json([
                'success' => false,
                'message' => 'Fitur ini hanya untuk device Smart Farm.',
            ], 400);
        }

        $topic = $device->mqtt_topic_output ?: $device->mqtt_topic;
        $success = $this->smartFarmService->sendRelayStatus($topic);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Permintaan status relay dikirim ke device!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim permintaan status relay.',
        ], 500);
    }

    /**
     * Smart Farm: Sinkronkan Waktu RTC (CMD:SET_RTC)
     */
    public function setRtc(Request $request, $userDeviceId)
    {
        $device = $this->getDevice($userDeviceId);

        if ($device->type !== 'smart_farm') {
            return response()->json([
                'success' => false,
                'message' => 'Fitur ini hanya untuk device Smart Farm.',
            ], 400);
        }

        $tz = strtoupper($request->input('timezone', 'WIB'));
        $tzMap = [
            'WIB' => 'Asia/Jakarta',
            'WITA' => 'Asia/Makassar',
            'WIT' => 'Asia/Jayapura',
        ];
        $targetTz = $tzMap[$tz] ?? 'Asia/Jakarta';
        $targetTime = now()->setTimezone($targetTz);

        $tahun = (int) $request->input('tahun', $targetTime->year);
        $bulan = (int) $request->input('bulan', $targetTime->month);
        $tanggal = (int) $request->input('tanggal', $targetTime->day);
        $jam = (int) $request->input('jam', $targetTime->hour);
        $menit = (int) $request->input('menit', $targetTime->minute);

        $topic = $device->mqtt_topic_schedule ?: $device->mqtt_topic;
        $success = $this->smartFarmService->sendSetRtc($topic, $tahun, $bulan, $tanggal, $jam, $menit);

        if ($success) {
            $jamFormatted = sprintf('%02d:%02d', $jam, $menit);
            $sfStatus = \Cache::get("device_sf_status_{$device->id}", []);
            $sfStatus['jam'] = sprintf('%02d%02d', $jam, $menit);
            $sfStatus['timezone'] = $tz;
            \Cache::put("device_sf_status_{$device->id}", $sfStatus, now()->addHours(1));
            \Cache::put("device_timezone_{$device->id}", $tz, now()->addYears(1));

            return response()->json([
                'success' => true,
                'message' => "Waktu RTC alat berhasil disinkronkan ke {$jamFormatted} {$tz} ({$tanggal}/{$bulan}/{$tahun})!",
                'jam' => $jamFormatted,
                'timezone' => $tz,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim perintah sinkronisasi waktu RTC.',
        ], 500);
    }

    /**
     * Smart Farm: Request sinkronisasi jadwal dari perangkat (CMD:JADWAL_GET)
     */
    public function syncFromDevice($userDeviceId)
    {
        $device = $this->getDevice($userDeviceId);

        if ($device->type !== 'smart_farm') {
            return response()->json([
                'success' => false,
                'message' => 'Fitur sinkronisasi ini khusus perangkat Smart Farm.',
            ], 400);
        }

        $topic = $device->mqtt_topic_schedule ?: $device->mqtt_topic;
        $success = $this->smartFarmService->sendJadwalGet($topic);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Permintaan jadwal (CMD:JADWAL_GET) terkirim ke perangkat! Data sedang disinkronkan...',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim permintaan jadwal ke perangkat.',
        ], 500);
    }

    /**
     * Smart Farm: Ambil data cached schedules terkini dalam format JSON (untuk auto-refresh / sync polling)
     */
    public function getSchedulesData($userDeviceId)
    {
        $device = $this->getDevice($userDeviceId);
        $cachedSchedules = $this->getCachedSchedulesForDevice($device->id);

        $syncedAt = \Cache::get("device_schedules_synced_at_{$device->id}");
        if (!$syncedAt) {
            $legacySync = \DB::table('cache')
                ->where('key', 'like', "%device_schedules_synced_at_{$device->id}")
                ->orderByDesc('expiration')
                ->first();
            if ($legacySync && !empty($legacySync->value)) {
                $syncedAt = @unserialize($legacySync->value);
            }
        }

        return response()->json([
            'success' => true,
            'device_id' => $device->id,
            'schedules' => $cachedSchedules,
            'count' => count($cachedSchedules),
            'synced_at' => $syncedAt,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Helper to get cached schedules with fallback across cache key prefix mismatches
     */
    private function getCachedSchedulesForDevice($deviceId): array
    {
        $cacheKey = "device_schedules_{$deviceId}";
        $cached = \Cache::get($cacheKey, []);

        if (empty($cached)) {
            $prefix = config('cache.prefix', '');
            $legacy = \DB::table('cache')
                ->where('key', 'like', "%device_schedules_{$deviceId}")
                ->where('key', '!=', $prefix . $cacheKey)
                ->orderByDesc('expiration')
                ->first();

            if ($legacy && !empty($legacy->value)) {
                try {
                    $unserialized = @unserialize($legacy->value);
                    if (is_array($unserialized)) {
                        $cached = $unserialized;
                        \Cache::put($cacheKey, $cached, now()->addDays(30));
                    }
                } catch (\Throwable $e) {}
            }
        }

        uksort($cached, function ($a, $b) {
            $numA = (int) filter_var($a, FILTER_SANITIZE_NUMBER_INT);
            $numB = (int) filter_var($b, FILTER_SANITIZE_NUMBER_INT);
            return $numA - $numB;
        });

        return $cached;
    }
}
