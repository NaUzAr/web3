<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\UserDevice;
use App\Models\DeviceOutput;
use App\Services\MqttScheduleService;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    private $mqttService;

    public function __construct(MqttScheduleService $mqttService)
    {
        $this->mqttService = $mqttService;
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
     * Supports modes: time, time_days, time_days_sector, time_duration, time_days_duration
     */
    public function storeTimeSchedules(Request $request, $userDeviceId)
    {
        $device = $this->getDevice($userDeviceId);
        $scheduleConfig = \App\Models\DeviceSchedule::where('device_id', $device->id)->firstOrFail();

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

        // Send to MQTT
        $success = $this->mqttService->sendSingleTimeSchedule(
            $device->mqtt_topic,
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

    // storeSensorRule removed for now as per route update or kept if needed but updated
    // Keeping it commented or empty to avoid errors if called, 
    // but routes commented it out. I will just omit it for now or return error 
    // to strictly clean up.
    /**
     * Delete/Disable schedule slot
     */
    public function destroy($userDeviceId, $slotId)
    {
        $device = $this->getDevice($userDeviceId);

        // MQTT command to delete
        $success = $this->mqttService->deleteSchedule(
            $device->mqtt_topic,
            $device->token,
            (int) $slotId
        );

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
}
