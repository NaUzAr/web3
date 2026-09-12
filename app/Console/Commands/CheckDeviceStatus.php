<?php

namespace App\Console\Commands;

use App\Models\Device;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\FirebaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CheckDeviceStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-device-status {--timeout=10 : Menit batas waktu perangkat dianggap offline}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Periksa status heartbeat perangkat IoT dan kirim peringatan FCM jika offline';

    /**
     * Execute the console command.
     */
    public function handle(FirebaseService $firebase)
    {
        $timeoutMinutes = (int) $this->option('timeout');
        $threshold = now()->subMinutes($timeoutMinutes);

        $this->info("Memeriksa status perangkat IoT (Batas timeout: {$timeoutMinutes} menit)...");

        $devices = Device::all();
        $offlineCount = 0;
        $onlineCount = 0;

        foreach ($devices as $device) {
            $isOffline = is_null($device->last_seen_at) || $device->last_seen_at < $threshold;

            if ($isOffline) {
                $offlineCount++;
                $lastSeen = $device->last_seen_at ? $device->last_seen_at->diffForHumans() : 'Belum pernah terhubung';
                $this->warn("Perangkat '{$device->name}' (ID: {$device->id}) OFFLINE! Terakhir terlihat: {$lastSeen}");

                // Cek cooldown notifikasi (agar tidak spam, notifikasi dikirim maks 1x per 3 jam per device)
                $cooldownKey = "device_offline_alert_{$device->id}";
                if (!Cache::has($cooldownKey)) {
                    // Cari pemilik perangkat
                    $userIds = UserDevice::where('device_id', $device->id)->pluck('user_id');
                    $users = User::whereIn('id', $userIds)->whereNotNull('fcm_token')->get();

                    $title = "⚠️ Perangkat Offline: {$device->name}";
                    $body = "Perangkat '{$device->name}' tidak merespons selama lebih dari {$timeoutMinutes} menit. Periksa koneksi internet atau catu daya.";

                    $notifiedUsers = 0;
                    foreach ($users as $user) {
                        try {
                            $firebase->sendToToken($user->fcm_token, $title, $body, [
                                'device_id' => (string) $device->id,
                                'type' => 'device_offline',
                            ]);
                            $notifiedUsers++;
                        } catch (\Throwable $e) {
                            Log::error("Gagal mengirim notifikasi offline ke user {$user->id}: " . $e->getMessage());
                        }
                    }

                    if ($notifiedUsers > 0) {
                        $this->line("   -> Peringatan FCM dikirim ke {$notifiedUsers} pemilik perangkat.");
                    }

                    // Set cooldown 3 jam
                    Cache::put($cooldownKey, true, now()->addHours(3));
                }
            } else {
                $onlineCount++;
                // Hapus cooldown jika perangkat kembali online
                Cache::forget("device_offline_alert_{$device->id}");
            }
        }

        $this->info("Selesai. Online: {$onlineCount}, Offline: {$offlineCount}");
        return Command::SUCCESS;
    }
}
