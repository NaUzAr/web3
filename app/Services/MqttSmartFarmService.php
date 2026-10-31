<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Device;

/**
 * MqttSmartFarmService
 * 
 * Service khusus untuk device tipe 'smart_farm' (Irigasi Multi-Zona).
 * Menggunakan format protokol serial CMD: yang diteruskan ESP32 via MQTT.
 * 
 * Protokol: STM32 (Kontroller) <-> ESP32 (Jembatan WiFi/Server)
 * Format pesan: teks satu baris, delimiter ":", diakhiri newline
 * 
 * Coil mapping:
 *   0 = Pompa Utama
 *   1 = Blok 1 (Zona 1)
 *   2 = Blok 2 (Zona 2)
 *   3 = Blok 3 (Zona 3)
 *   4 = Pompa Pupuk
 */
class MqttSmartFarmService
{
    private $host;
    private $port;
    private $username;
    private $password;

    /**
     * Mapping output_name (di database) ke nomor coil relay
     */
    public const COIL_MAP = [
        'sf_pompa' => 0,
        'sf_blok1' => 1,
        'sf_blok2' => 2,
        'sf_blok3' => 3,
        'sf_pupuk' => 4,
    ];

    public function __construct()
    {
        $this->host = config('mqtt.host', env('MQTT_HOST', 'smartagri.web.id'));
        $this->port = config('mqtt.port', env('MQTT_PORT', 1883));
        $this->username = config('mqtt.username', env('MQTT_USERNAME'));
        $this->password = config('mqtt.password', env('MQTT_PASSWORD'));
    }

    /**
     * Kontrol pemilihan blok irigasi
     * 
     * @param string $mqttTopic MQTT topic device (e.g. /irigasi1)
     * @param int $blok Nomor blok (1-3) atau 0 untuk mematikan semua blok & pompa
     * @return bool
     * 
     * Format: CMD:BLOK:<blok>
     * Response: OK:BLOK:blok1=0:blok2=1:blok3=0:pompa=1
     */
    public function sendBlok(string $mqttTopic, int $blok): bool
    {
        $message = "CMD:BLOK:{$blok}";
        return $this->publish($mqttTopic, $message, 'Blok control');
    }

    /**
     * Kontrol relay ON/OFF
     * 
     * @param string $mqttTopic MQTT topic device (e.g. /irigasi1)
     * @param int $coil Nomor coil (0-4)
     * @param int $state 1=ON, 0=OFF
     * @return bool
     * 
     * Format: CMD:RELAY:<coil>:<state>
     * Response: OK:RELAY:<coil>:<state> atau ERR:*
     */
    public function sendRelay(string $mqttTopic, int $coil, int $state): bool
    {
        $message = "CMD:RELAY:{$coil}:{$state}";
        return $this->publish($mqttTopic, $message, 'Relay control');
    }

    /**
     * Kontrol relay berdasarkan output_name
     * 
     * @param string $mqttTopic MQTT topic device
     * @param string $outputName Nama output (sf_pompa, sf_blok1, dll)
     * @param int $state 1=ON, 0=OFF
     * @return bool
     */
    public function sendRelayByName(string $mqttTopic, string $outputName, int $state): bool
    {
        // Blok 1, 2, 3 dikontrol via CMD:BLOK:<1|2|3|0>
        if (in_array($outputName, ['sf_blok1', 'sf_blok2', 'sf_blok3'])) {
            if ($state == 1) {
                $blokNum = (int) str_replace('sf_blok', '', $outputName);
                return $this->sendBlok($mqttTopic, $blokNum);
            } else {
                return $this->sendBlok($mqttTopic, 0);
            }
        }

        // Mematikan sf_pompa juga mematikan semua blok via CMD:BLOK:0
        if ($outputName === 'sf_pompa') {
            if ($state == 0) {
                return $this->sendBlok($mqttTopic, 0);
            } else {
                return $this->sendRelay($mqttTopic, 0, $state);
            }
        }

        $coil = self::COIL_MAP[$outputName] ?? null;
        if ($coil === null) {
            Log::warning("Smart Farm: Unknown output name '{$outputName}', cannot map to coil");
            return false;
        }
        return $this->sendRelay($mqttTopic, $coil, $state);
    }

    /**
     * Set/Edit jadwal irigasi
     * 
     * @param string $mqttTopic MQTT topic device
     * @param int $idx Index jadwal (0-9)
     * @param array $data Jadwal data: jam, menit, durasi, blok, liter_pupuk_10, hari_bitmask, aktif
     * @return bool
     * 
     * Format: CMD:JADWAL_SET:<idx>:<jam>:<menit>:<durasi>:<blok>:<literPupuk10>:<hari_bitmask>:<aktif>
     * 
     * Validasi (di-clamp oleh kontroller):
     *   jam: 0-23, menit: 0-59, durasi: 1-120, blok: 1-3
     *   literPupuk10: 0-500 (0=tanpa pupuk), hari: 0-127 (bitmask), aktif: 0/1
     */
    public function sendJadwalSet(string $mqttTopic, int $idx, array $data): bool
    {
        $jam = (int) ($data['jam'] ?? 0);
        $menit = (int) ($data['menit'] ?? 0);
        $durasi = (int) ($data['durasi'] ?? 5);
        $blok = (int) ($data['blok'] ?? 1);
        $literPupuk10 = (int) ($data['liter_pupuk_10'] ?? 0);
        $hariBitmask = (int) ($data['hari_bitmask'] ?? 127); // default setiap hari
        $aktif = (int) ($data['aktif'] ?? 1);

        $message = "CMD:JADWAL_SET:{$idx}:{$jam}:{$menit}:{$durasi}:{$blok}:{$literPupuk10}:{$hariBitmask}:{$aktif}";
        return $this->publish($mqttTopic, $message, 'Jadwal set');
    }

    /**
     * Hapus jadwal
     * 
     * @param string $mqttTopic MQTT topic device
     * @param int $idx Index jadwal (0-9)
     * @return bool
     * 
     * Format: CMD:JADWAL_DEL:<idx>
     */
    public function sendJadwalDel(string $mqttTopic, int $idx): bool
    {
        $message = "CMD:JADWAL_DEL:{$idx}";
        return $this->publish($mqttTopic, $message, 'Jadwal delete');
    }

    /**
     * Ambil semua jadwal dari device
     * 
     * Format: CMD:JADWAL_GET
     * Response: OK:JADWAL:<idx>:<jam>:<menit>:<durasi>:<blok>:<literPupuk10>:<hari_bitmask>:<aktif>
     *           ... (baris per baris)
     *           OK:JADWAL_END
     */
    public function sendJadwalGet(string $mqttTopic): bool
    {
        $message = "CMD:JADWAL_GET";
        return $this->publish($mqttTopic, $message, 'Jadwal get');
    }

    /**
     * Jalankan jadwal irigasi secara manual
     * 
     * @param string $mqttTopic MQTT topic device
     * @param int $idx Index jadwal (0-9)
     * @return bool
     * 
     * Format: CMD:SIRAM_START:<index>
     * Response: OK:SIRAM_START:<index> atau ERR:SEDANG_SIRAM / ERR:JADWAL_INVALID
     */
    public function sendSiramStart(string $mqttTopic, int $idx): bool
    {
        $message = "CMD:SIRAM_START:{$idx}";
        return $this->publish($mqttTopic, $message, 'Siram start');
    }

    /**
     * Stop penyiraman yang sedang berjalan
     * 
     * Format: CMD:SIRAM_STOP
     * Response: OK:SIRAM_STOP atau ERR:TIDAK_SIRAM
     */
    public function sendSiramStop(string $mqttTopic): bool
    {
        $message = "CMD:SIRAM_STOP";
        return $this->publish($mqttTopic, $message, 'Siram stop');
    }

    /**
     * Request status sistem lengkap
     * 
     * Format: CMD:STATUS
     * Response: OK:STATUS:siram=<0/1>:blok=<no>:sisa=<mmdd>:pupuk=<NONE/TUNGGU/ON>:jam=<hhmm>:hari=<0-6>:error=<0/1>
     */
    public function sendStatus(string $mqttTopic): bool
    {
        $message = "CMD:STATUS";
        return $this->publish($mqttTopic, $message, 'Status request');
    }

    /**
     * Request status relay lengkap dari device (Ground Truth)
     * 
     * Format: CMD:RELAY_STATUS
     * Response: OK:RELAY_STATUS:blok1=%d:blok2=%d:blok3=%d:pompa=%d:pupuk=%d
     */
    public function sendRelayStatus(string $mqttTopic): bool
    {
        $message = "CMD:RELAY_STATUS";
        return $this->publish($mqttTopic, $message, 'Relay status request');
    }

    /**
     * Ping koneksi
     * 
     * Format: CMD:PING
     * Response: OK:PING
     */
    public function sendPing(string $mqttTopic): bool
    {
        $message = "CMD:PING";
        return $this->publish($mqttTopic, $message, 'Ping');
    }

    /**
     * Reset error relay
     * 
     * Format: CMD:RESET_ERROR
     * Response: OK:RESET_ERROR
     */
    public function sendResetError(string $mqttTopic): bool
    {
        $message = "CMD:RESET_ERROR";
        return $this->publish($mqttTopic, $message, 'Reset error');
    }

    /**
     * Set waktu RTC device
     * 
     * @param string $mqttTopic MQTT topic device
     * @param int $tahun Tahun (e.g. 2026)
     * @param int $bulan Bulan (1-12)
     * @param int $tanggal Tanggal (1-31)
     * @param int $jam Jam (0-23)
     * @param int $menit Menit (0-59)
     * @return bool
     * 
     * Format: CMD:SET_RTC:<tahun>:<bulan>:<tanggal>:<jam>:<menit>
     */
    public function sendSetRtc(string $mqttTopic, int $tahun, int $bulan, int $tanggal, int $jam, int $menit): bool
    {
        $message = "CMD:SET_RTC:{$tahun}:{$bulan}:{$tanggal}:{$jam}:{$menit}";
        return $this->publish($mqttTopic, $message, 'Set RTC');
    }

    /**
     * Konversi array hari (e.g. [1,2,3,4,5,6,7] atau ['Min','Sen',...]) ke bitmask
     * 1=Senin (bit 1) .. 6=Sabtu (bit 6), 7/0=Minggu (bit 7 & bit 0)
     * 
     * @param array|string|int $days Array of day indices atau day names
     * @return int Bitmask
     */
    public static function daysToBitmask($days): int
    {
        if (empty($days)) {
            return 255; // Default: setiap hari
        }

        if (is_numeric($days) && (int)$days <= 255 && (int)$days >= 0 && strlen((string)$days) <= 3 && !preg_match('/^[1-7]{2,}$/', (string)$days)) {
            return (int) $days;
        }

        if (is_string($days)) {
            // Format angka berurutan seperti "1234567" (1=Senin..7=Minggu)
            if (preg_match('/^[0-7]+$/', $days)) {
                $days = str_split($days);
                $bitmask = 0;
                foreach ($days as $d) {
                    $val = (int)$d;
                    if ($val === 7 || $val === 0) {
                        // Set bit 7 dan bit 0 agar kompatibel ganda (firmware 1-7 maupun 0-6)
                        $bitmask |= (1 << 7) | (1 << 0);
                    } else {
                        $bitmask |= (1 << $val);
                    }
                }
                return $bitmask ?: 255;
            }
            $days = explode(',', $days);
        }

        if (!is_array($days)) {
            return 255;
        }

        $dayMap = [
            'min' => 7, 'minggu' => 7, 'sun' => 7, 'sunday' => 7,
            'sen' => 1, 'senin' => 1, 'mon' => 1, 'monday' => 1,
            'sel' => 2, 'selasa' => 2, 'tue' => 2, 'tuesday' => 2,
            'rab' => 3, 'rabu' => 3, 'wed' => 3, 'wednesday' => 3,
            'kam' => 4, 'kamis' => 4, 'thu' => 4, 'thursday' => 4,
            'jum' => 5, 'jumat' => 5, 'fri' => 5, 'friday' => 5,
            'sab' => 6, 'sabtu' => 6, 'sat' => 6, 'saturday' => 6,
        ];

        $bitmask = 0;
        foreach ($days as $day) {
            if (is_numeric($day)) {
                $val = (int) $day;
            } else {
                $val = $dayMap[strtolower(trim($day))] ?? null;
            }
            if ($val !== null && $val >= 0 && $val <= 7) {
                if ($val === 7 || $val === 0) {
                    $bitmask |= (1 << 7) | (1 << 0);
                } else {
                    $bitmask |= (1 << $val);
                }
            }
        }
        return $bitmask ?: 255;
    }

    /**
     * Konversi bitmask ke array nama hari (Indonesia)
     * Mendukung pembacaan bit 7 maupun bit 0 sebagai Minggu.
     * 
     * @param int $bitmask
     * @return array
     */
    public static function bitmaskToDays(int $bitmask): array
    {
        $dayNames = [
            1 => 'Sen',
            2 => 'Sel',
            3 => 'Rab',
            4 => 'Kam',
            5 => 'Jum',
            6 => 'Sab',
            7 => 'Min',
        ];
        $result = [];

        // Cek bit 1 s/d 7 (1=Senin ... 6=Sabtu, 7=Minggu)
        for ($i = 1; $i <= 7; $i++) {
            if ($bitmask & (1 << $i)) {
                $result[] = $dayNames[$i];
            }
        }

        // Fallback jika ada firmware lama yang mengirim Minggu sebagai bit 0
        if (($bitmask & (1 << 0)) && !in_array('Min', $result)) {
            $result[] = 'Min';
        }

        return $result;
    }

    /**
     * Parse EVT:STATUS response string
     * 
     * Input: "EVT:STATUS:siram=1:blok=2:sisa=1230:pupuk=ON:jam=0630:hari=3:error=0"
     * Output: ['siram' => 1, 'blok' => 2, 'sisa' => '1230', 'pupuk' => 'ON', 'jam' => '0630', 'hari' => 3, 'error' => 0]
     */
    public static function parseStatusString(string $statusLine): ?array
    {
        // Remove prefix EVT:STATUS: or OK:STATUS:
        $line = preg_replace('/^(EVT|OK):STATUS:/', '', $statusLine);
        if (empty($line)) return null;

        $result = [];
        $parts = explode(':', $line);
        foreach ($parts as $part) {
            if (str_contains($part, '=')) {
                [$key, $value] = explode('=', $part, 2);
                $result[$key] = is_numeric($value) ? (int) $value : $value;
            }
        }
        return $result;
    }

    /**
     * Deteksi zona waktu (WIB, WITA, WIT) dari perbandingan jam RTC alat terhadap UTC server saat ini.
     * 
     * WIB  = UTC + 7
     * WITA = UTC + 8
     * WIT  = UTC + 9
     * 
     * @param string $rawJam Format HHMM (misal: "0624", "1130")
     * @return string|null 'WIB'|'WITA'|'WIT' atau null jika drift terlalu besar (> 35 menit)
     */
    public static function detectTimezoneFromRtcJam(string $rawJam): ?string
    {
        $rawJam = str_pad($rawJam, 4, '0', STR_PAD_LEFT);
        $deviceHour = (int) substr($rawJam, 0, 2);
        $deviceMinute = (int) substr($rawJam, 2, 2);

        if ($deviceHour < 0 || $deviceHour > 23 || $deviceMinute < 0 || $deviceMinute > 59) {
            return null;
        }

        $deviceMinutes = $deviceHour * 60 + $deviceMinute;

        $candidateTimezones = [
            'WIB'  => 'Asia/Jakarta',
            'WITA' => 'Asia/Makassar',
            'WIT'  => 'Asia/Jayapura',
        ];

        $closestTz = null;
        $minDiff = 9999;

        foreach ($candidateTimezones as $tzCode => $tzIdentifier) {
            $candidateNow = now()->setTimezone($tzIdentifier);
            $candidateMinutes = $candidateNow->hour * 60 + $candidateNow->minute;

            $diff = abs($deviceMinutes - $candidateMinutes);
            if ($diff > 720) {
                $diff = 1440 - $diff; // Handle midnight crossover (e.g. 23:58 vs 00:02)
            }

            if ($diff < $minDiff) {
                $minDiff = $diff;
                $closestTz = $tzCode;
            }
        }

        // Jika selisih terkecil masih dalam rentang 35 menit, kita anggap itu zona waktunya
        // (karena perbedaan antar zona waktu adalah 60 menit)
        if ($minDiff <= 35) {
            return $closestTz;
        }

        return null;
    }

    /**
     * Mendeteksi atau mengambil zona waktu (WIB, WITA, WIT) untuk perangkat Smart Farm.
     * 
     * Prioritas deteksi:
     * 1. Pengaturan manual yang tersimpan di Cache (device_timezone_{id})
     * 2. Jam sebelumnya dari RTC alat terhadap waktu UTC (Offset ~7h = WIB, ~8h = WITA, ~9h = WIT)
     * 3. Koordinat Bujur (Longitude) perangkat di tabel devices
     * 4. Analisis teks kolom 'location' perangkat
     * 5. Default fallback: WIB
     */
    public static function resolveTimezone(Device $device, ?string $rawJam = null): string
    {
        // 1. Cek cache preferensi user jika sudah pernah disetel manual
        if ($device->id) {
            try {
                $cachedTz = Cache::get("device_timezone_{$device->id}");
                if ($cachedTz && in_array(strtoupper($cachedTz), ['WIB', 'WITA', 'WIT'])) {
                    return strtoupper($cachedTz);
                }
            } catch (\Throwable $e) {
                // Abaikan jika cache store sedang down
            }
        }

        // 2. Deteksi dari jam RTC alat sebelumnya (offset terhadap UTC)
        if ($rawJam && strlen($rawJam) >= 4) {
            $detectedFromRtc = self::detectTimezoneFromRtcJam($rawJam);
            if ($detectedFromRtc) {
                if ($device->id) {
                    try {
                        Cache::put("device_timezone_{$device->id}", $detectedFromRtc, now()->addDays(30));
                    } catch (\Throwable $e) {}
                }
                return $detectedFromRtc;
            }
        }

        // 3. Deteksi dari Longitude
        if (!is_null($device->longitude) && is_numeric($device->longitude)) {
            $lon = (float) $device->longitude;
            // Wilayah bujur Indonesia: ~95°BT s/d 142°BT
            if ($lon >= 95.0 && $lon <= 142.0) {
                $tz = ($lon < 114.5) ? 'WIB' : (($lon <= 126.5) ? 'WITA' : 'WIT');
                if ($device->id) {
                    try {
                        Cache::put("device_timezone_{$device->id}", $tz, now()->addDays(30));
                    } catch (\Throwable $e) {}
                }
                return $tz;
            }
        }

        // 4. Deteksi dari teks kolom location
        if (!empty($device->location)) {
            $loc = strtolower($device->location);
            // WIT keywords
            if (preg_match('/\b(papua|maluku|ambon|jayapura|merauke|sorong|manokwari|biak|ternate|tidore)\b/i', $loc)) {
                $tz = 'WIT';
                if ($device->id) {
                    try {
                        Cache::put("device_timezone_{$device->id}", $tz, now()->addDays(30));
                    } catch (\Throwable $e) {}
                }
                return $tz;
            }
            // WITA keywords
            if (preg_match('/\b(bali|denpasar|lombok|mataram|ntb|ntt|kupang|flores|sumbawa|makassar|sulawesi|manado|palu|kalsel|banjarmasin|kaltim|samarinda|balikpapan|kaltara|tarakan|gorontalo|kendari)\b/i', $loc)) {
                $tz = 'WITA';
                if ($device->id) {
                    try {
                        Cache::put("device_timezone_{$device->id}", $tz, now()->addDays(30));
                    } catch (\Throwable $e) {}
                }
                return $tz;
            }
            // WIB keywords
            if (preg_match('/\b(jakarta|bogor|depok|tangerang|bekasi|bandung|jabar|jateng|jatim|semarang|surabaya|yogyakarta|jogja|banten|sumatera|medan|palembang|padang|riau|aceh|lampung|kalbar|pontianak|kalteng|palangkaraya)\b/i', $loc)) {
                $tz = 'WIB';
                if ($device->id) {
                    try {
                        Cache::put("device_timezone_{$device->id}", $tz, now()->addDays(30));
                    } catch (\Throwable $e) {}
                }
                return $tz;
            }
        }

        // 5. Default fallback
        return 'WIB';
    }

    /**
     * Publish pesan ke MQTT topic device (append /sub)
     */
    private function publish(string $mqttTopic, string $message, string $context = ''): bool
    {
        try {
            $mqtt = $this->connect();
            $baseTopic = preg_replace('/\/(sub|pub|status)$/', '', rtrim($mqttTopic, '/'));
            $topic = $baseTopic . '/sub';

            $mqtt->publish($topic, $message . "\n", 1); // QoS 1, append newline
            $mqtt->disconnect();

            Log::info("Smart Farm [{$context}] sent to {$topic}", [
                'message' => $message,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Smart Farm [{$context}] MQTT failed: " . $e->getMessage(), [
                'mqtt_topic' => $mqttTopic,
                'message' => $message,
            ]);
            return false;
        }
    }

    private function connect(): MqttClient
    {
        $connectionSettings = new ConnectionSettings();

        if ($this->username && $this->password) {
            $connectionSettings = $connectionSettings
                ->setUsername($this->username)
                ->setPassword($this->password);
        }

        $connectionSettings = $connectionSettings
            ->setKeepAliveInterval(60)
            ->setConnectTimeout(10);

        $mqtt = new MqttClient($this->host, $this->port, 'laravel-smartfarm-' . uniqid());
        $mqtt->connect($connectionSettings, true);

        return $mqtt;
    }
}
