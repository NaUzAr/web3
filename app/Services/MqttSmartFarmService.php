<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use Illuminate\Support\Facades\Log;

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
     * Konversi array hari (e.g. [0,1,2,3,4,5,6] atau ['Min','Sen',...]) ke bitmask
     * bit0=Minggu, bit1=Senin, ..., bit6=Sabtu
     * 
     * @param array $days Array of day indices (0=Minggu..6=Sabtu) atau day names
     * @return int Bitmask (0-127)
     */
    public static function daysToBitmask($days): int
    {
        if (empty($days)) {
            return 127; // Default: setiap hari
        }

        if (is_numeric($days) && (int)$days <= 127 && (int)$days >= 0 && strlen((string)$days) <= 3 && !preg_match('/^[1-7]{2,}$/', (string)$days)) {
            return (int) $days;
        }

        if (is_string($days)) {
            // Format angka berurutan seperti "1234567" (1=Senin..7=Minggu)
            if (preg_match('/^[1-7]+$/', $days)) {
                $days = str_split($days);
                $bitmask = 0;
                foreach ($days as $d) {
                    $val = (int)$d;
                    $bit = ($val === 7) ? 0 : $val; // 7=Minggu (bit 0), 1=Senin (bit 1)
                    $bitmask |= (1 << $bit);
                }
                return $bitmask ?: 127;
            }
            $days = explode(',', $days);
        }

        if (!is_array($days)) {
            return 127;
        }

        $dayMap = [
            'min' => 0, 'minggu' => 0, 'sun' => 0, 'sunday' => 0,
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
                $bit = ($val === 7) ? 0 : $val;
            } else {
                $bit = $dayMap[strtolower(trim($day))] ?? null;
            }
            if ($bit !== null && $bit >= 0 && $bit <= 6) {
                $bitmask |= (1 << $bit);
            }
        }
        return $bitmask ?: 127;
    }

    /**
     * Konversi bitmask ke array nama hari (Indonesia)
     * 
     * @param int $bitmask
     * @return array
     */
    public static function bitmaskToDays(int $bitmask): array
    {
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $result = [];
        for ($i = 0; $i <= 6; $i++) {
            if ($bitmask & (1 << $i)) {
                $result[] = $dayNames[$i];
            }
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
