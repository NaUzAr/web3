<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    // Izinkan kolom ini diisi
    protected $fillable = [
        'name',
        'location',
        'latitude',
        'longitude',
        'type',
        'mqtt_topic',
        'token',
        'table_name',
        'max_time_schedules',
        'max_sensor_automations',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'last_seen_at' => 'datetime',
        ];
    }

    /**
     * Check if device is online (data received within last 5 minutes)
     */
    public function isOnline(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->diffInMinutes(now()) < 5;
    }

    /**
     * Get human-readable last seen text
     */
    public function lastSeenText(): string
    {
        if (!$this->last_seen_at) {
            return 'Belum pernah terhubung';
        }
        return $this->last_seen_at->diffForHumans();
    }

    /**
     * Relasi ke DeviceSensor (satu device punya banyak sensor)
     */
    public function sensors()
    {
        return $this->hasMany(DeviceSensor::class);
    }

    /**
     * Relasi ke DeviceOutput (satu device punya banyak output)
     */
    public function outputs()
    {
        return $this->hasMany(DeviceOutput::class);
    }

    /**
     * Relasi ke DeviceSchedule (satu device punya banyak schedule)
     */
    public function schedules()
    {
        return $this->hasMany(DeviceSchedule::class);
    }

    /**
     * Relasi ke OutputAutomationConfig (satu device punya banyak automation configs)
     */
    public function automationConfigs()
    {
        return $this->hasManyThrough(
            OutputAutomationConfig::class,
            DeviceOutput::class,
            'device_id',
            'device_output_id'
        );
    }

    /**
     * Check if device can add more time-based schedules
     */
    public function canAddTimeSchedule(): bool
    {
        $currentCount = $this->automationConfigs()
            ->where('automation_type', 'time')
            ->count();

        return $currentCount < $this->max_time_schedules;
    }

    /**
     * Check if device can add more sensor-based automations
     */
    public function canAddSensorAutomation(): bool
    {
        $currentCount = $this->automationConfigs()
            ->where('automation_type', 'sensor')
            ->count();

        return $currentCount < $this->max_sensor_automations;
    }

    /**
     * =====================================================
     * KONFIGURASI TIPE ALAT DAN SENSOR
     * Mudah diedit: Tambahkan tipe alat baru di sini
     * =====================================================
     */

    /**
     * Daftar Tipe Alat yang Tersedia
     * Tambahkan tipe baru dengan format: 'key' => 'Label Tampilan'
     */
    public static function getDeviceTypes(): array
    {
        return [
            'aws' => 'AWS (Automatic Weather Station)',
            'smart_gh' => 'Smart GH (Smart Greenhouse)',
            // Tambahkan tipe baru di sini:
            // 'water_quality' => 'Water Quality Sensor',
            // 'air_quality' => 'Air Quality Sensor',
        ];
    }

    /**
     * Daftar Preset Sensor yang Tersedia
     * Format: 'sensor_key' => ['label' => 'Label', 'unit' => 'satuan', 'icon' => 'bi-icon']
     * Sensor key harus lowercase tanpa spasi (digunakan sebagai prefix nama kolom)
     */
    public static function getAvailableSensors(): array
    {
        return [
            'ni_SUHU' => ['label' => 'Suhu (Temperature)', 'unit' => '°C', 'icon' => 'bi-thermometer-half'],
            'ni_KELEM' => ['label' => 'Kelembaban (Humidity)', 'unit' => '%', 'icon' => 'bi-droplet'],
            'rainfall' => ['label' => 'Curah Hujan (Rainfall)', 'unit' => 'mm', 'icon' => 'bi-cloud-rain'],
            'wind_speed' => ['label' => 'Kecepatan Angin', 'unit' => 'km/h', 'icon' => 'bi-wind'],
            'wind_direction' => ['label' => 'Arah Angin', 'unit' => '°', 'icon' => 'bi-compass'],
            'pressure' => ['label' => 'Tekanan Udara', 'unit' => 'hPa', 'icon' => 'bi-speedometer'],
            'uv_index' => ['label' => 'Indeks UV', 'unit' => '', 'icon' => 'bi-sun'],
            'ni_LUX' => ['label' => 'Intensitas Cahaya', 'unit' => 'lux', 'icon' => 'bi-brightness-high'],
            'soil_moisture' => ['label' => 'Kelembaban Tanah', 'unit' => '%', 'icon' => 'bi-moisture'],
            // 'soil_ph' => ['label' => 'pH Tanah', 'unit' => '', 'icon' => 'bi-droplet-half'],
            'soil_temperature' => ['label' => 'Suhu Tanah', 'unit' => '°C', 'icon' => 'bi-thermometer'],
            'water_level' => ['label' => 'Level Air', 'unit' => 'cm', 'icon' => 'bi-water'],
            'co2' => ['label' => 'CO2', 'unit' => 'ppm', 'icon' => 'bi-cloud'],
            'ni_EC' => ['label' => 'EC (Electrical Conductivity)', 'unit' => 'mS/cm', 'icon' => 'bi-lightning'],
            'ni_TDS' => ['label' => 'TDS (Total Dissolved Solids)', 'unit' => 'ppm', 'icon' => 'bi-water'],
            'ni_PH' => ['label' => 'pH', 'unit' => '', 'icon' => 'bi-droplet-half'],
            // Tambahkan sensor baru di sini:
            // 'pm25' => ['label' => 'PM2.5', 'unit' => 'µg/m³', 'icon' => 'bi-cloud-haze'],
        ];
    }

    /**
     * Sensor Default untuk Setiap Tipe Alat
     * Ini akan otomatis terpilih saat user memilih tipe alat
     * Format: ['sensor_key' => jumlah_default]
     */
    public static function getDefaultSensorsForType(string $type): array
    {
        $defaults = [
            'aws' => [
                // 'ni_SUHU' => 1,
                // 'ni_KELEM' => 1,
                // 'rainfall' => 1,
                // 'wind_speed' => 1,
                // 'pressure' => 1
            ],
            'smart_gh' => [
                // 'ni_SUHU' => 2,  // 2 sensor suhu
                // 'ni_KELEM' => 2,     // 2 sensor kelembaban
                // 'soil_moisture' => 1,
                // 'ni_LUX' => 1,
            ],
            'smart_gh_auto' => [
                'ni_SUHU' => 1,
                'ni_KELEM' => 1,
                'ni_PH' => 1,
                'ni_TDS' => 1,
                'ni_LUX' => 1,
                'co2' => 1,
            ],
            // Tambahkan default sensor untuk tipe baru:
            // 'water_quality' => ['water_level' => 1, 'ph' => 1, 'temperature' => 1],
        ];

        return $defaults[$type] ?? [];
    }

    /**
     * =====================================================
     * KONFIGURASI OUTPUT DEVICE
     * =====================================================
     */

    /**
     * Daftar Preset Output yang Tersedia
     * Format: 'output_key' => ['label' => 'Label', 'type' => 'tipe', 'unit' => 'satuan', 'icon' => 'bi-icon']
     * Tipe: boolean (on/off), number (angka), percentage (0-100%)
     */
    public static function getAvailableOutputs(): array
    {
        return [
            'relay' => ['label' => 'Relay', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-toggle-on'],
            'pump' => ['label' => 'Pompa Air', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-droplet-fill'],
            'fan' => ['label' => 'Kipas/Fan', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-fan'],
            'valve' => ['label' => 'Katup/Valve', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-moisture'],
            'motor' => ['label' => 'Motor Speed', 'type' => 'percentage', 'unit' => '%', 'icon' => 'bi-gear'],
            'led' => ['label' => 'LED', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-lightbulb'],
            'buzzer' => ['label' => 'Buzzer', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-bell'],
            'servo' => ['label' => 'Servo Motor', 'type' => 'number', 'unit' => '°', 'icon' => 'bi-arrow-repeat'],
            'heater' => ['label' => 'Pemanas/Heater', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-thermometer-high'],
            'sprinkler' => ['label' => 'Sprinkler', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-cloud-drizzle'],

            // Custom Outputs (User Request)
            'sts_air_input' => ['label' => 'Air Baku', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-arrow-right-circle'],
            'sts_mixing' => ['label' => 'Mixing Process', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-arrow-repeat'],
            'sts_pompa' => ['label' => 'Pompa Utama', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-droplet-fill'],
            'sts_fan' => ['label' => 'Kipas Exhaust', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-fan'],
            'sts_misting' => ['label' => 'Misting/Kabut', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-cloud-fog'],
            'sts_lampu' => ['label' => 'Lampu Grow Light', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-lightbulb-fill'],
            'sts_dosing' => ['label' => 'Dosing Pump', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-eyedropper'],
            'sts_ph_up' => ['label' => 'pH Up Pump', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-arrow-up-circle'],
            'sts_ph_down' => ['label' => 'pH Down Pump', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-arrow-down-circle'],
            'sts_air_baku' => ['label' => 'Air Baku Valve', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-water'],
            'sts_air_pupuk' => ['label' => 'Air Pupuk Valve', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-flower1'],
            'sts_sld_op' => ['label' => 'Shading Net Open', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-chevron-double-up'],
            'sts_sld_tu' => ['label' => 'Shading Net Close', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-chevron-double-down'],
            'sts_cool' => ['label' => 'Cooling System', 'type' => 'boolean', 'unit' => '', 'icon' => 'bi-snow'],

            // Pompa Irigasi Multi-Zona (Khusus dengan setting jumlah zona)
            'irrigation_pump' => [
                'label' => 'Pompa Irigasi (Multi-Zona)',
                'type' => 'multi_zone',
                'unit' => '',
                'icon' => 'bi-droplet-fill',
                'supports_zones' => true,
                'default_water_type' => 1  // 1 = Air Baku, 2 = Air Pupuk
            ],
        ];
    }

    /**
     * Output Default untuk Setiap Tipe Alat
     * Format: ['output_key' => jumlah_default]
     */
    public static function getDefaultOutputsForType(string $type): array
    {
        $defaults = [
            'aws' => [
                // AWS biasanya tidak punya output, hanya monitoring
            ],
            'smart_gh' => [
                // 'pump' => 1,
                // 'fan' => 1,
            ],
            'smart_gh_auto' => [
                'sts_air_input' => 1,
                'sts_mixing' => 1,
                'sts_pompa' => 1,
                'sts_fan' => 1,
                'sts_misting' => 1,
                'sts_lampu' => 1,
                'sts_dosing' => 1,
                'sts_ph_up' => 1,
                'sts_ph_down' => 1,
                'sts_air_baku' => 1,
                'sts_air_pupuk' => 1,
                'sts_sld_op' => 1,
                'sts_sld_tu' => 1,
                'sts_cool' => 1,
            ],
            // Tambahkan default output untuk tipe baru:
        ];

        return $defaults[$type] ?? [];
    }

    /**
     * Preset Otomasi Granular (Climate vs Fertilizer)
     */
    public static function getAutomationPresets(): array
    {
        return [
            'climate' => [
                'label' => 'Otomasi Iklim (Climate)',
                'description' => 'Kontrol Suhu, Kelembaban, dan Cahaya',
                'icon' => 'bi-thermometer-sun',
                'sensors' => [
                    'ni_SUHU' => 1,
                    'ni_KELEM' => 1,
                    'ni_LUX' => 1,
                    'co2' => 1
                ],
                'outputs' => [
                    'sts_fan' => 1,
                    'sts_misting' => 1,
                    'sts_lampu' => 1
                ]
            ],
            'fertilizer' => [
                'label' => 'Otomasi Pemupukan (Fertigation)',
                'description' => 'Kontrol Nutrisi (AB Mix), pH, dan Irigasi',
                'icon' => 'bi-flower1',
                'sensors' => [
                    'ni_PH' => 1,
                    'ni_TDS' => 1,
                    'water_level' => 1
                ],
                'outputs' => [
                    'sts_pompa' => 1,
                    'sts_air_input' => 1,
                    'sts_mixing' => 1,
                    'sts_dosing' => 1,
                    'sts_ph_up' => 1,
                    'sts_ph_down' => 1,
                    'sts_air_baku' => 1,
                    'sts_air_pupuk' => 1
                ]
            ]
        ];
    }


    /**
     * =====================================================
     * KONFIGURASI SCHEDULE DEVICE
     * =====================================================
     */

    /**
     * Daftar Tipe Penjadwalan yang Tersedia
     * Format: 'schedule_key' => ['label' => 'Label', 'description' => 'Deskripsi', 'icon' => 'bi-icon']
     * Mode: 
     *   - time: Waktu mulai dan selesai (contoh: 08:00 - 17:00)
     *   - time_duration: Waktu mulai + durasi menit (contoh: 08:00, durasi 5 menit)
     *   - time_days: Waktu + pilihan hari
     *   - time_days_duration: Waktu + hari + durasi
     *   - time_days_sector: Waktu + hari + sektor
     */
    public static function getAvailableScheduleTypes(): array
    {
        return [
            'time' => [
                'label' => 'Waktu Mulai-Selesai',
                'description' => 'Set jam mulai dan jam selesai',
                'icon' => 'bi-clock'
            ],
            'time_duration' => [
                'label' => 'Waktu + Durasi',
                'description' => 'Set jam mulai dan durasi (menit)',
                'icon' => 'bi-stopwatch'
            ],
            'time_days' => [
                'label' => 'Waktu + Hari',
                'description' => 'Set jam mulai-selesai dan pilih hari',
                'icon' => 'bi-calendar-week'
            ],
            'time_days_duration' => [
                'label' => 'Waktu + Hari + Durasi',
                'description' => 'Set jam mulai, durasi, dan pilih hari',
                'icon' => 'bi-calendar-check'
            ],
            'time_days_sector' => [
                'label' => 'Waktu + Hari + Sektor',
                'description' => 'Set jam, hari, dan sektor (untuk irigasi multi-zona)',
                'icon' => 'bi-grid-3x3'
            ],
            'time_days_duration_sector' => [
                'label' => 'Waktu + Hari + Durasi + Sektor',
                'description' => 'Set jam, durasi, hari, dan sektor',
                'icon' => 'bi-grid-fill'
            ],
            'time_days_duration_sector_type' => [
                'label' => 'Waktu + Hari + Durasi + Sektor + Jenis',
                'description' => 'Set jam, durasi, hari, sektor, dan jenis (Pupuk/Air Baku)',
                'icon' => 'bi-list-check'
            ],
        ];
    }

    /**
     * Relasi ke DeviceSetting
     */
    public function settings()
    {
        return $this->hasMany(DeviceSetting::class);
    }

    /**
     * Cek apakah device memiliki sensor DAN output yang mendukung tipe otomasi tertentu
     * Device harus punya minimal 1 sensor cocok DAN minimal 1 output cocok
     * ATAU admin secara eksplisit mengaktifkan otomasi (tersimpan di device_settings)
     * @param string $type 'climate' atau 'fertilizer'
     */
    public function hasAutomationType($type)
    {
        // 1. Cek dari explicit toggle (DeviceSettings)
        $settingKeys = [];
        if ($type === 'climate') {
            $settingKeys = ['ats_suhu', 'ats_kelem', 'ats_lux', 'ats_co2'];
        } elseif ($type === 'fertilizer') {
            $settingKeys = ['ats_tds', 'ats_ph', 'ats_wlevel'];
        }
        
        $hasExplicitSettings = \App\Models\DeviceSetting::where('device_id', $this->id)
            ->whereIn('key', $settingKeys)
            ->exists();
            
        if ($hasExplicitSettings) {
            return true;
        }

        // 2. Fallback: Cek implicit dari nama sensor & output
        $presets = self::getAutomationPresets();
        if (!isset($presets[$type]))
            return false;

        $requiredSensors = array_keys($presets[$type]['sensors']);
        $requiredOutputs = array_keys($presets[$type]['outputs']);

        // Fetch all sensor names for this device
        if ($this->relationLoaded('sensors')) {
            $sensorNames = $this->sensors->pluck('sensor_name')->toArray();
        } else {
            $sensorNames = $this->sensors()->pluck('sensor_name')->toArray();
        }

        // Fetch all output names for this device
        if ($this->relationLoaded('outputs')) {
            $outputNames = $this->outputs->pluck('output_name')->toArray();
        } else {
            $outputNames = $this->outputs()->pluck('output_name')->toArray();
        }

        // Cek apakah ada minimal 1 sensor yang cocok
        $hasSensor = false;
        foreach ($requiredSensors as $req) {
            foreach ($sensorNames as $name) {
                if ($name === $req || str_starts_with($name, $req . '_')) {
                    $hasSensor = true;
                    break 2;
                }
            }
        }

        if (!$hasSensor) return false;

        // Cek apakah ada minimal 1 output yang cocok
        foreach ($requiredOutputs as $req) {
            foreach ($outputNames as $name) {
                if ($name === $req || str_starts_with($name, $req . '_')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Cek apakah device memiliki otomasi apapun
     */
    public function hasAnyAutomation()
    {
        return $this->hasAutomationType('climate') || $this->hasAutomationType('fertilizer');
    }
}