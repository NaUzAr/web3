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
            return Device::findOrFail($id);
        }

        $userDevice = UserDevice::where('user_id', Auth::id())
            ->where('id', $id)
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
        
        // For views, if admin we pass dummy userDevice so it doesn't break blade variables
        if ($isAdminView) {
            $userDevice = new UserDevice(['id' => $device->id, 'device_id' => $device->id, 'custom_name' => $device->name]);
            $userDevice->setRelation('device', $device);
        } else {
            $userDevice = UserDevice::where('id', $userDeviceId)->first();
        }

        // Check if device has schedule functionality
        $scheduleConfig = \App\Models\DeviceSchedule::where('device_id', $device->id)->first();

        if (!$scheduleConfig) {
            return redirect()->route('monitoring.show', $userDevice->id)
                ->with('error', 'Device ini tidak memiliki konfigurasi penjadwalan.');
        }

        // Get cached schedules
        $cacheKey = "device_schedules_{$device->id}";
        $cachedSchedules = \Cache::get($cacheKey, []);

        // Sort by slot key numerical value (sch1, sch2...)
        uksort($cachedSchedules, function ($a, $b) {
            $numA = (int) filter_var($a, FILTER_SANITIZE_NUMBER_INT);
            $numB = (int) filter_var($b, FILTER_SANITIZE_NUMBER_INT);
            return $numA - $numB;
        });

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
            $daysText = $request->filled('days')
                ? implode(', ', MqttSmartFarmService::bitmaskToDays($hariBitmask))
                : 'Setiap hari';
            $pupukText = $literPupuk10 > 0
                ? ', Pupuk: ' . ($literPupuk10 / 10) . 'L'
                : '';

            $displaySlot = $idx + 1;
            return response()->json([
                'success' => true,
                'message' => "Jadwal #{$displaySlot} berhasil dikirim! "
                    . "(Blok {$blok}, {$validated['on_time']} selama {$duration} menit{$pupukText}, {$daysText})",
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
        $success = $this->smartFarmService->sendSiramStop($topic);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Perintah stop penyiraman dikirim!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mengirim perintah stop.',
        ], 500);
    }
}
