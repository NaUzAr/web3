<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceSetting;
use App\Models\UserDevice;
use App\Services\MqttAutomationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutomasiController extends Controller
{
    protected $mqttService;

    public function __construct(MqttAutomationService $mqttService)
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

    public function index($deviceId)
    {
        $device = $this->getDevice($deviceId);

        $hasClimate = $device->hasAutomationType('climate');
        $hasFertilizer = $device->hasAutomationType('fertilizer');

        // Load all settings from Cache
        $cacheKey = "device_automation_{$device->id}";
        $settings = \Cache::get($cacheKey, []);

        // Fallback: If cache invalid/empty, load from DB and reset cache
        if (empty($settings)) {
            $settings = DeviceSetting::where('device_id', $device->id)
                ->whereIn('key', [
                    'ats_suhu',
                    'bwh_suhu',
                    'ats_kelem',
                    'bwh_kelem',
                    'ats_tds',
                    'bwh_tds',
                    'ats_ph',
                    'bwh_ph'
                ])
                ->pluck('value', 'key')
                ->toArray();
            \Cache::put($cacheKey, $settings, now()->addDays(1));
        }

        // Tentukan kartu mana yang spesifik ditampilkan
        $hasSuhu = isset($settings['ats_suhu']);
        $hasKelem = isset($settings['ats_kelem']);
        $hasTds = isset($settings['ats_tds']);
        $hasPh = isset($settings['ats_ph']);

        // Fallback untuk device lama (dibuat sebelum ada toggle per-sensor)
        if ($hasClimate && !$hasSuhu && !$hasKelem) {
            $sensorNames = $device->sensors()->pluck('sensor_name')->toArray();
            $hasSuhu = in_array('ni_SUHU', $sensorNames);
            $hasKelem = in_array('ni_KELEM', $sensorNames);
        }

        if ($hasFertilizer && !$hasTds && !$hasPh) {
            $sensorNames = $device->sensors()->pluck('sensor_name')->toArray();
            $hasTds = in_array('ni_TDS', $sensorNames);
            $hasPh = in_array('ni_PH', $sensorNames);
        }

        $isAdminView = Auth::user()->is_admin;

        return view('automasi.index', compact(
            'device', 'hasClimate', 'hasFertilizer', 'settings', 'deviceId',
            'hasSuhu', 'hasKelem', 'hasTds', 'hasPh', 'isAdminView'
        ));
    }

    public function updateSingle(Request $request, $deviceId)
    {
        // Handle Fallback GET URL manually to redirect back
        if ($request->isMethod('get')) {
            return redirect()->route('automasi.index', ['id' => $deviceId]);
        }

        $device = $this->getDevice($deviceId);

        $validated = $request->validate([
            'sensor_type' => 'required|in:suhu,kelem,tds,ph',
            'ats_val' => 'required|numeric',
            'bwh_val' => 'required|numeric'
        ]);

        $sensorType = $validated['sensor_type'];
        $atsKey = "ats_{$sensorType}";
        $bwhKey = "bwh_{$sensorType}";

        $newData = [
            $atsKey => (float) $validated['ats_val'],
            $bwhKey => (float) $validated['bwh_val']
        ];

        // 1. Update Cache
        $cacheKey = "device_automation_{$device->id}";
        $currentSettings = \Cache::get($cacheKey, []);
        $updatedSettings = array_merge($currentSettings, $newData);
        \Cache::put($cacheKey, $updatedSettings, now()->addDays(1));

        // 2. Publish to MQTT
        $this->mqttService->sendCustomAutomationConfig($device->mqtt_topic, $device->token, $newData);

        // 3. Update DB (As Backup/Persistence) - Optional but recommended for reboot persistence
        foreach ($newData as $key => $value) {
            DeviceSetting::updateOrCreate(
                ['device_id' => $device->id, 'key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->route('automasi.index', ['id' => $deviceId])->with('success', "Setting {$sensorType} berhasil diperbarui!");
    }

}
