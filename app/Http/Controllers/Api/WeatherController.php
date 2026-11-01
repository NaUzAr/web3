<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherController extends Controller
{
    /**
     * GET /api/weather
     * Mengambil data cuaca terkini & prediksi cuaca pertanian via Open-Meteo
     * dengan Caching Server-Side 2 Jam untuk 100% perlindungan dari limit request.
     */
    public function index(Request $request)
    {
        $deviceId = $request->query('device_id');
        $lat = $request->query('latitude') ?? $request->query('lat');
        $long = $request->query('longitude') ?? $request->query('long') ?? $request->query('lon');
        $locationName = $request->query('location_name');

        // 1. Jika device_id diberikan, coba cari koordinat dari device
        if ($deviceId) {
            try {
                $device = Device::find($deviceId);
                if ($device) {
                    if (!empty($device->latitude) && !empty($device->longitude)) {
                        $lat = (float) $device->latitude;
                        $long = (float) $device->longitude;
                    }
                    $locationName = $device->location ?: $device->name;
                }
            } catch (\Throwable $e) {
                // Ignore DB error if offline
            }
        }

        // 2. Jika user terautentikasi dan belum ada lat/long, coba cari dari device milik user
        if ((!$lat || !$long) && $request->user()) {
            try {
                $firstUserDevice = $request->user()->userDevices()->with('device')->first();
                if ($firstUserDevice && $firstUserDevice->device) {
                    $d = $firstUserDevice->device;
                    if (!empty($d->latitude) && !empty($d->longitude)) {
                        $lat = (float) $d->latitude;
                        $long = (float) $d->longitude;
                        $locationName = $firstUserDevice->custom_name ?: ($d->location ?: $d->name);
                    }
                }
            } catch (\Throwable $e) {
                // Ignore DB error
            }
        }

        // 3. Fallback default jika tidak ada lokasi (Surabaya / Jawa Timur)
        if (!$lat || !$long) {
            $lat = -7.2575;
            $long = 112.7521;
            if (!$locationName) {
                $locationName = 'Surabaya, Jawa Timur';
            }
        } else {
            $lat = (float) $lat;
            $long = (float) $long;
            if (!$locationName) {
                $locationName = 'Lahan Pertanian';
            }
        }

        // Bulatkan koordinat 2 desimal (~1 km grid) agar cache terbagi merata untuk area sekitar
        $cacheLat = round($lat, 2);
        $cacheLong = round($long, 2);
        $cacheKey = "weather_forecast_{$cacheLat}_{$cacheLong}";

        $cacheTtlSeconds = 7200; // 2 Jam (7200 detik)

        // Cek Cache (menggunakan File/Database cache driver sesuai konfigurasi)
        $cachedData = null;
        try {
            $cachedData = Cache::get($cacheKey);
        } catch (\Throwable $e) {
            try {
                $cachedData = Cache::store('file')->get($cacheKey);
            } catch (\Throwable $err) {
                // Ignore
            }
        }

        if ($cachedData && is_array($cachedData)) {
            $cachedData['source'] = 'server_cache';
            $cachedData['location']['name'] = $locationName;
            return response()->json($cachedData);
        }

        // Fetch dari Open-Meteo API
        try {
            $response = Http::timeout(6)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $lat,
                'longitude' => $long,
                'current' => 'temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,weather_code,wind_speed_10m',
                'hourly' => 'temperature_2m,relative_humidity_2m,precipitation_probability,precipitation,weather_code',
                'daily' => 'weather_code,temperature_2m_max,temperature_2m_min,precipitation_sum,precipitation_probability_max,wind_speed_10m_max',
                'timezone' => 'Asia/Jakarta',
                'forecast_days' => 7,
            ]);

            if ($response->successful()) {
                $raw = $response->json();
                $formatted = $this->formatWeatherData($raw, $lat, $long, $locationName);

                // Simpan ke Cache
                try {
                    Cache::put($cacheKey, $formatted, $cacheTtlSeconds);
                } catch (\Throwable $e) {
                    try {
                        Cache::store('file')->put($cacheKey, $formatted, $cacheTtlSeconds);
                    } catch (\Throwable $err) {
                        Log::warning('Failed to store weather cache: ' . $err->getMessage());
                    }
                }

                $formatted['source'] = 'live_api';
                return response()->json($formatted);
            }
        } catch (\Throwable $e) {
            Log::error('Open-Meteo fetch error: ' . $e->getMessage());
        }

        // Jika terjadi kegagalan jaringan eksternal, gunakan data fallback yang aman
        return response()->json($this->getFallbackWeatherData($lat, $long, $locationName));
    }

    /**
     * Memformat data dari Open-Meteo ke struktur data yang bersih & ramah petani
     */
    private function formatWeatherData(array $raw, float $lat, float $long, string $locationName): array
    {
        $current = $raw['current'] ?? [];
        $hourly = $raw['hourly'] ?? [];
        $daily = $raw['daily'] ?? [];

        $weatherCode = (int) ($current['weather_code'] ?? 0);
        $wmoInfo = $this->translateWmoCode($weatherCode);

        // Format 24 Jam Terdepan (Hourly)
        $hourlyList = [];
        $hourlyTimes = $hourly['time'] ?? [];
        $now = now()->setTimezone('Asia/Jakarta');
        $nowStr = $now->format('Y-m-d\TH:00');

        $startIndex = 0;
        foreach ($hourlyTimes as $idx => $t) {
            if ($t >= $nowStr) {
                $startIndex = $idx;
                break;
            }
        }

        for ($i = $startIndex; $i < min($startIndex + 24, count($hourlyTimes)); $i++) {
            $hCode = (int) ($hourly['weather_code'][$i] ?? 0);
            $hWmo = $this->translateWmoCode($hCode);
            $timeLabel = date('H:i', strtotime($hourlyTimes[$i]));

            $hourlyList[] = [
                'time' => $timeLabel,
                'datetime' => $hourlyTimes[$i],
                'temperature' => round((float) ($hourly['temperature_2m'][$i] ?? 0), 1),
                'humidity' => (int) ($hourly['relative_humidity_2m'][$i] ?? 0),
                'precipitation_probability' => (int) ($hourly['precipitation_probability'][$i] ?? 0),
                'precipitation' => (float) ($hourly['precipitation'][$i] ?? 0),
                'weather_code' => $hCode,
                'weather_desc' => $hWmo['label'],
                'weather_icon' => $hWmo['icon'],
            ];
        }

        // Format 7 Hari (Daily)
        $dailyList = [];
        $dailyTimes = $daily['time'] ?? [];
        for ($j = 0; $j < count($dailyTimes); $j++) {
            $dCode = (int) ($daily['weather_code'][$j] ?? 0);
            $dWmo = $this->translateWmoCode($dCode);
            $dateTimestamp = strtotime($dailyTimes[$j]);
            $dayName = $this->getIndonesianDayName((int) date('w', $dateTimestamp));

            $dailyList[] = [
                'date' => $dailyTimes[$j],
                'day_name' => $j === 0 ? 'Hari ini' : ($j === 1 ? 'Besok' : $dayName),
                'temp_max' => round((float) ($daily['temperature_2m_max'][$j] ?? 0), 1),
                'temp_min' => round((float) ($daily['temperature_2m_min'][$j] ?? 0), 1),
                'precipitation_probability_max' => (int) ($daily['precipitation_probability_max'][$j] ?? 0),
                'precipitation_sum' => (float) ($daily['precipitation_sum'][$j] ?? 0),
                'weather_code' => $dCode,
                'weather_desc' => $dWmo['label'],
                'weather_icon' => $dWmo['icon'],
            ];
        }

        $temp = round((float) ($current['temperature_2m'] ?? 28), 1);
        $humidity = (int) ($current['relative_humidity_2m'] ?? 75);
        $precipProb = count($hourlyList) > 0 ? $hourlyList[0]['precipitation_probability'] : 0;
        $precipAmount = (float) ($current['precipitation'] ?? 0);

        return [
            'success' => true,
            'location' => [
                'name' => $locationName,
                'latitude' => $lat,
                'longitude' => $long,
            ],
            'current' => [
                'temperature' => $temp,
                'apparent_temperature' => round((float) ($current['apparent_temperature'] ?? $temp), 1),
                'humidity' => $humidity,
                'precipitation' => $precipAmount,
                'precipitation_probability' => $precipProb,
                'wind_speed' => round((float) ($current['wind_speed_10m'] ?? 0), 1),
                'weather_code' => $weatherCode,
                'weather_desc' => $wmoInfo['label'],
                'weather_icon' => $wmoInfo['icon'],
                'is_day' => (int) ($current['is_day'] ?? 1),
            ],
            'agri_advisory' => $this->generateAgriAdvisory($temp, $humidity, $precipProb, $weatherCode),
            'hourly' => $hourlyList,
            'daily' => $dailyList,
            'updated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Terjemahan Kode Cuaca WMO ke Bahasa Indonesia & Identifikasi Ikon
     */
    private function translateWmoCode(int $code): array
    {
        return match ($code) {
            0 => ['label' => 'Cerah', 'icon' => 'clear_day'],
            1 => ['label' => 'Cerah Berawan', 'icon' => 'mostly_clear'],
            2 => ['label' => 'Sebagian Berawan', 'icon' => 'partly_cloudy'],
            3 => ['label' => 'Mendung Berawan', 'icon' => 'cloudy'],
            45, 48 => ['label' => 'Berkabut', 'icon' => 'fog'],
            51, 53, 55 => ['label' => 'Gerimis Ringan', 'icon' => 'drizzle'],
            56, 57 => ['label' => 'Gerimis Dingin', 'icon' => 'drizzle'],
            61 => ['label' => 'Hujan Ringan', 'icon' => 'rain_light'],
            63 => ['label' => 'Hujan Sedang', 'icon' => 'rain_moderate'],
            65 => ['label' => 'Hujan Lebat', 'icon' => 'rain_heavy'],
            66, 67 => ['label' => 'Hujan Es Dingin', 'icon' => 'rain_heavy'],
            71, 73, 75, 77 => ['label' => 'Hujan Salju/Es', 'icon' => 'snow'],
            80 => ['label' => 'Hujan Lokal Ringan', 'icon' => 'rain_light'],
            81 => ['label' => 'Hujan Deras Sporadis', 'icon' => 'rain_moderate'],
            82 => ['label' => 'Hujan Sangat Deras', 'icon' => 'rain_heavy'],
            85, 86 => ['label' => 'Hujan Es Ringan', 'icon' => 'snow'],
            95 => ['label' => 'Badai Petir', 'icon' => 'thunderstorm'],
            96, 99 => ['label' => 'Badai Petir & Hujan Es', 'icon' => 'thunderstorm'],
            default => ['label' => 'Berawan', 'icon' => 'partly_cloudy'],
        };
    }

    /**
     * Menghasilkan Saran Tani Cerdas (Smart Agriculture Advisory)
     */
    private function generateAgriAdvisory(float $temp, int $humidity, int $precipProb, int $weatherCode): array
    {
        if ($weatherCode >= 95) {
            return [
                'type' => 'danger',
                'title' => 'Waspada Badai & Petir',
                'message' => 'Hindari aktivitas di lapangan terbuka. Pastikan instalasi perangkat IoT dan tiang sensor aman dari petir.',
            ];
        }

        if ($precipProb >= 65 || $weatherCode >= 61) {
            return [
                'type' => 'warning',
                'title' => 'Peluang Hujan Tinggi',
                'message' => 'Tunda pemupukan daun dan penyemprotan pestisida agar tidak hanyut terbuang oleh air hujan.',
            ];
        }

        if ($temp >= 33.5) {
            return [
                'type' => 'info',
                'title' => 'Cuaca Panas Terik',
                'message' => 'Lakukan penyiraman ekstra pada sore hari untuk menjaga kelembaban tanah dan mencegah stres pada tanaman.',
            ];
        }

        if ($humidity >= 85 && $temp < 25) {
            return [
                'type' => 'warning',
                'title' => 'Kelembaban Sangat Tinggi',
                'message' => 'Kondisi lembab memicu jamur & bakteri. Pantau sirkulasi udara di area tanam.',
            ];
        }

        return [
            'type' => 'success',
            'title' => 'Cuaca Kondusif untuk Lahan',
            'message' => 'Kondisi cuaca sangat baik untuk perawatan rutin tanaman, pemupukan berimbang, atau kegiatan pemanenan.',
        ];
    }

    private function getIndonesianDayName(int $dayOfWeek): string
    {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $days[$dayOfWeek] ?? 'Hari';
    }

    private function getFallbackWeatherData(float $lat, float $long, string $locationName): array
    {
        return [
            'success' => true,
            'source' => 'fallback',
            'location' => [
                'name' => $locationName,
                'latitude' => $lat,
                'longitude' => $long,
            ],
            'current' => [
                'temperature' => 29.0,
                'apparent_temperature' => 32.0,
                'humidity' => 75,
                'precipitation' => 0.0,
                'precipitation_probability' => 20,
                'wind_speed' => 10.0,
                'weather_code' => 1,
                'weather_desc' => 'Cerah Berawan',
                'weather_icon' => 'mostly_clear',
                'is_day' => 1,
            ],
            'agri_advisory' => [
                'type' => 'success',
                'title' => 'Cuaca Kondusif',
                'message' => 'Kondisi cuaca baik untuk pemeliharaan rutin tanaman pertanian.',
            ],
            'hourly' => [],
            'daily' => [],
            'updated_at' => now()->toIso8601String(),
        ];
    }
}

