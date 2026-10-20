<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\PersonalAccessToken;

class BotMemberController extends Controller
{
    /**
     * Verifikasi status member untuk AI Chatbot (n8n & WAHA)
     * Mendukung pencarian berdasarkan nomor WhatsApp (phone), UUID, atau API Token.
     */
    public function checkMember(Request $request)
    {
        // 0. Keamanan: Validasi Secret Key antara n8n dan Web Swaratani
        $botSecret = env('BOT_API_KEY');
        if (!empty($botSecret)) {
            $incomingSecret = $request->header('X-Bot-Secret') 
                ?: $request->header('X-Api-Key') 
                ?: $request->input('secret');

            if (!$incomingSecret || !hash_equals((string)$botSecret, (string)$incomingSecret)) {
                return response()->json([
                    'success' => false,
                    'is_member' => false,
                    'message' => 'Akses ditolak: Kunci rahasia bot (X-Bot-Secret) tidak valid.',
                ], 403);
            }
        }

        $phone = $request->input('phone');
        $uuid = $request->input('uuid');
        $token = $request->bearerToken() ?: $request->input('token');
        $message = $request->input('message');
        $action = $request->input('action');

        // Deteksi apakah ini upaya Pairing / Pendaftaran UUID + Token
        $isPairing = ($action === 'bind' || $action === 'register');
        $pairUuid = $uuid;
        $pairToken = $token;

        // Cek jika perintah pendaftaran dikirim melalui chat WA (bisa "DAFTAR 40db4d83 9|token...", multiline, atau kalimat bebas seperti "Min tolong sambungkan akun saya 40db4d83 9|token...")
        if ($message && preg_match('/\b([a-f0-9]{8})\b/i', (string)$message, $mUuid) && preg_match('/(\d+\|[A-Za-z0-9_\-]+)/', (string)$message, $mTok)) {
            $isPairing = true;
            $pairUuid = $mUuid[1];
            $pairToken = $mTok[1];
        } elseif ($uuid && $token) {
            $isPairing = true;
        }

        // Deteksi apakah ini permintaan Disconnect / Unbind nomor WA
        // 1. Lewat parameter eksplisit (dipanggil oleh AI Tool di n8n atau API)
        $isUnbind = in_array(strtolower((string)$action), ['unbind', 'disconnect', 'logout', 'cabut']);

        // 2. Lewat pesan chat murni (hanya jika pesan berupa satu kata perintah spesifik, bukan di tengah kalimat pertanian/konteks lain)
        if (!$isUnbind && $message) {
            $msgLower = trim(strtolower((string)$message));
            $cleanedMsg = trim($msgLower, " \t\n\r\0\x0B.!?,");

            $strictCommands = [
                'putuskan',
                'disconnect',
                'unbind',
                'unconnect',
                'unkonect',
                'putus sambungan',
                'keluar akun',
                'hapus wa',
                'hapus nomor wa',
                'logout wa',
                'cabut akun',
            ];

            if (in_array($cleanedMsg, $strictCommands, true)) {
                $isUnbind = true;
            }
        }

        

        // ==========================================
        // ALUR KHUSUS: Putuskan Sambungan (Unbind)
        // ==========================================
        if ($isUnbind) {
            $userToUnbind = null;
            if ($phone) {
                $cleanPhone = preg_replace('/[:@].*$/', '', (string)$phone);
                $digits = preg_replace('/\D/', '', $cleanPhone);
                $core = $digits;
                if (str_starts_with($digits, '62')) {
                    $core = substr($digits, 2);
                } elseif (str_starts_with($digits, '0')) {
                    $core = substr($digits, 1);
                }

                if (!empty($core)) {
                    $userToUnbind = User::where(function ($query) use ($digits, $core) {
                        $query->where('phone', $digits)
                              ->orWhere('phone', '0' . $core)
                              ->orWhere('phone', '62' . $core)
                              ->orWhere('phone', '+62' . $core)
                              ->orWhere('phone', 'like', '%' . $core . '%');

                        if (DB::getDriverName() === 'pgsql') {
                            $query->orWhereRaw("REGEXP_REPLACE(COALESCE(phone, ''), '[^0-9]', '', 'g') IN (?, ?)", [
                                '0' . $core,
                                '62' . $core
                            ]);
                        } else {
                            $query->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(phone, ''), ' ', ''), '-', ''), '+', '') IN (?, ?)", [
                                '0' . $core,
                                '62' . $core
                            ]);
                        }
                    })->first();
                }
            }

            if (!$userToUnbind && $uuid) {
                $userToUnbind = User::where('uuid', strtolower(trim($uuid)))->first();
            }

            if (!$userToUnbind) {
                return response()->json([
                    'is_member' => false,
                    'action' => 'unbind',
                    'phone_unbound' => false,
                    'message' => 'Nomor WhatsApp ini tidak terhubung ke akun Swaratani mana pun.',
                    'reply_for_user' => 'Nomor WhatsApp Anda belum terhubung ke akun Swaratani mana pun, sehingga tidak ada akun yang perlu diputuskan.',
                ], 200);
            }

            $userName = $userToUnbind->name;
            $userToUnbind->phone = null;
            $userToUnbind->saveQuietly();

            return response()->json([
                'is_member' => false,
                'action' => 'unbind',
                'phone_unbound' => true,
                'message' => "Nomor WhatsApp berhasil diputuskan dari akun {$userName}.",
                'reply_for_user' => "Nomor WhatsApp Anda berhasil diputuskan dari akun Swaratani ({$userName}).\n\nSekarang nomor ini kembali menjadi non-member. Jika ingin menghubungkan kembali akun Anda di kemudian hari, cukup kirimkan pesan:\nDAFTAR {$userToUnbind->uuid} <TOKEN>",
                'user' => [
                    'id' => $userToUnbind->id,
                    'uuid' => $userToUnbind->uuid,
                    'name' => $userName,
                ]
            ], 200);
        }

        // ==========================================
        // ALUR A: Upaya Pairing / Pendaftaran Nomor WA
        // ==========================================
        if ($isPairing) {
            if (!$pairUuid || !$pairToken) {
                return response()->json([
                    'is_member' => false,
                    'action' => 'bind',
                    'phone_bound' => false,
                    'message' => 'Parameter tidak lengkap: UUID dan API Token wajib disertakan.',
                    'reply_for_user' => 'Pendaftaran gagal: Mohon sertakan UUID dan Token dengan format: DAFTAR <UUID> <TOKEN>',
                ], 400);
            }

            $candidate = User::where('uuid', strtolower(trim($pairUuid)))->first();
            if (!$candidate) {
                return response()->json([
                    'is_member' => false,
                    'action' => 'bind',
                    'phone_bound' => false,
                    'message' => 'Gagal menghubungkan: UUID tidak ditemukan di sistem Swaratani.',
                    'reply_for_user' => 'Pendaftaran gagal: UUID tidak ditemukan. Silakan cek UUID akun Anda di menu Profil website Swaratani.',
                ], 200);
            }

            $tokenModel = PersonalAccessToken::findToken($pairToken);
            if (!$tokenModel || $tokenModel->tokenable_id !== $candidate->id || ($tokenModel->expires_at && $tokenModel->expires_at->isPast())) {
                return response()->json([
                    'is_member' => false,
                    'action' => 'bind',
                    'phone_bound' => false,
                    'message' => 'Gagal menghubungkan: API Token salah atau sudah kadaluarsa.',
                    'reply_for_user' => 'Pendaftaran gagal: API Token tidak valid atau sudah kadaluarsa. Silakan generate token baru di menu Profil website Swaratani.',
                ], 200);
            }

            // Validasi sukses! Simpan nomor telepon WhatsApp pengirim ke profil user
            $formattedPhone = null;
            if ($phone) {
                $clean = preg_replace('/[:@].*$/', '', (string)$phone);
                $rawDigits = preg_replace('/\D/', '', $clean);
                $formattedPhone = str_starts_with($rawDigits, '62') ? ('0' . substr($rawDigits, 2)) : $rawDigits;
                $candidate->phone = $formattedPhone;
                $candidate->saveQuietly();
            }

            // Hitung sisa hari masa aktif token
            $daysRemaining = $tokenModel->expires_at ? max(0, (int) ceil(now()->diffInDays($tokenModel->expires_at, false))) : 30;

            return response()->json([
                'is_member' => true,
                'action' => 'bind',
                'phone_bound' => true,
                'days_remaining' => $daysRemaining,
                'message' => "Nomor WhatsApp ({$formattedPhone}) berhasil dihubungkan ke akun {$candidate->name} ({$candidate->username})!",
                'reply_for_user' => "Selamat datang, {$candidate->name}! 🌱 Nomor WhatsApp Anda ({$formattedPhone}) berhasil dihubungkan ke akun Swaratani (Masa aktif: {$daysRemaining} hari).\n\nAnda tidak perlu lagi memasukkan token atau UUID selama masa aktif token ini berlaku. Silakan langsung kirim pertanyaan atau konsultasi Anda! 🌾",
                'user' => [
                    'id' => $candidate->id,
                    'uuid' => $candidate->uuid,
                    'name' => $candidate->name,
                    'username' => $candidate->username,
                    'phone' => $candidate->phone,
                    'role' => $candidate->role,
                    'is_admin' => $candidate->is_admin,
                ]
            ], 200);
        }

        // ==========================================
        // ALUR B: Pengecekan Member Rutin (Chat / Query)
        // ==========================================
        $user = null;

        // 1. Cek berdasarkan nomor WhatsApp (Phone)
        if ($phone) {
            // Bersihkan nomor dari domain WA (@s.whatsapp.net / @c.us) dan Device ID (:85, :1, dll)
            // Contoh format: "6285155456025:85@s.whatsapp.net" -> "6285155456025"
            $cleanPhone = preg_replace('/[:@].*$/', '', (string)$phone);
            $digits = preg_replace('/\D/', '', $cleanPhone);

            // Dapatkan nomor inti tanpa awalan 62 atau 0
            $core = $digits;
            if (str_starts_with($digits, '62')) {
                $core = substr($digits, 2);
            } elseif (str_starts_with($digits, '0')) {
                $core = substr($digits, 1);
            }

            if (!empty($core)) {
                $user = User::where(function ($query) use ($digits, $core) {
                    $query->where('phone', $digits)
                          ->orWhere('phone', '0' . $core)
                          ->orWhere('phone', '62' . $core)
                          ->orWhere('phone', '+62' . $core)
                          ->orWhere('phone', 'like', '%' . $core . '%');

                    // Bersihkan spasi, strip, atau tanda plus yang mungkin ada di kolom phone database
                    if (DB::getDriverName() === 'pgsql') {
                        $query->orWhereRaw("REGEXP_REPLACE(COALESCE(phone, ''), '[^0-9]', '', 'g') IN (?, ?)", [
                            '0' . $core,
                            '62' . $core
                        ]);
                    } else {
                        $query->orWhereRaw("REPLACE(REPLACE(REPLACE(COALESCE(phone, ''), ' ', ''), '-', ''), '+', '') IN (?, ?)", [
                            '0' . $core,
                            '62' . $core
                        ]);
                    }
                })->first();
            }
        }

        // 2. Cek berdasarkan UUID jika belum ditemukan
        if (!$user && $uuid) {
            $user = User::where('uuid', strtolower(trim($uuid)))->first();
        }

        // 3. Cek berdasarkan API Token jika belum ditemukan
        if (!$user && $token) {
            $tokenModel = PersonalAccessToken::findToken($token);
            if ($tokenModel && (!$tokenModel->expires_at || !$tokenModel->expires_at->isPast())) {
                $user = $tokenModel->tokenable;
            }
        }

        // Jika BUKAN member
        if (!$user) {
            return response()->json([
                'is_member' => false,
                'phone_bound' => false,
                'message' => 'Nomor WhatsApp belum terdaftar sebagai member Swaratani IoT.',
                'suggestion' => 'Ketik: DAFTAR <UUID> <TOKEN> untuk menghubungkan akun Anda, atau atur nomor WhatsApp di menu Profil web.',
                'reply_for_user' => 'Halo! Nomor WhatsApp Anda belum terdaftar di Swaratani IoT. Silakan daftarkan nomor Anda dengan mengirim pesan: DAFTAR <UUID> <TOKEN> (Dapatkan UUID & Token di menu Profil website Swaratani).',
            ], 200);
        }

        // 4. Periksa masa aktif token pengguna
        $activeToken = $user->thirdPartyToken() ?: $user->tokens()->latest()->first();
        $isTokenExpired = false;
        $daysRemaining = null;

        if ($activeToken) {
            if ($activeToken->expires_at && $activeToken->expires_at->isPast()) {
                $isTokenExpired = true;
            } elseif ($activeToken->expires_at) {
                $daysRemaining = max(0, (int) ceil(now()->diffInDays($activeToken->expires_at, false)));
            }
        }

        // Jika token sudah kadaluarsa: tolak akses member dan minta perbarui token
        if ($isTokenExpired) {
            return response()->json([
                'is_member' => false,
                'phone_bound' => false,
                'token_expired' => true,
                'message' => "Masa aktif akses bot WhatsApp untuk akun {$user->name} telah kadaluarsa.",
                'suggestion' => 'Silakan generate token baru di menu Profil website Swaratani untuk memperpanjang akses.',
                'reply_for_user' => "Halo Kak {$user->name}! ⚠️ Masa aktif akses bot WhatsApp Anda telah habis (kadaluarsa).\n\nSilakan masuk ke menu Profil di website https://swaratani.id untuk membuat Token baru, lalu kirimkan ke chat ini:\nDAFTAR {$user->uuid} <TOKEN_BARU>",
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                ]
            ], 200);
        }

        // Jika MEMBER terdaftar dan token aktif, ambil ringkasan device dan sensor terkini miliknya
        $userDevices = UserDevice::with('device')->where('user_id', $user->id)->get();
        $devicesSummary = [];

        foreach ($userDevices as $ud) {
            $dev = $ud->device;
            if (!$dev) {
                continue;
            }

            $latestSensor = null;
            if ($dev->table_name && Schema::hasTable($dev->table_name)) {
                $row = DB::table($dev->table_name)->orderBy('recorded_at', 'desc')->first();
                if ($row) {
                    $latestSensor = (array) $row;
                    unset($latestSensor['id']); // Hapus ID internal tabel sensor
                }
            }

            $devicesSummary[] = [
                'name' => $ud->custom_name ?: $dev->name,
                'type' => $dev->type ?? 'general',
                'is_online' => $dev->isOnline(),
                'last_seen' => $dev->lastSeenText(),
                'sensor_terkini' => $latestSensor,
            ];
        }

        return response()->json([
            'is_member' => true,
            'message' => 'Member terverifikasi.',
            'token_active' => true,
            'days_remaining' => $daysRemaining,
            'user' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'username' => $user->username,
                'phone' => $user->phone,
                'role' => $user->role ?? 'user',
                'is_admin' => $user->is_admin,
            ],
            'devices_count' => count($devicesSummary),
            'devices' => $devicesSummary,
        ], 200);
    }

        /**
     * Endpoint untuk mengambil log riwayat data sensor IoT berdasarkan user_id / uuid / phone
     * Mendukung filter: limit (jumlah), range (rentang waktu), start_date, end_date, dan sensor
     */
    public function getSensorLogs(Request $request)
    {
        // 1. Verifikasi Keamanan Secret Key
        $secret = $request->header('X-Bot-Secret', $request->input('secret'));
        $validSecret = config('services.bot.secret', env('BOT_SECRET_KEY', 'swa_bot_sec_8f93a1c27e4b5d6e'));
        if ($secret !== $validSecret) {
            return response()->json(['success' => false, 'message' => 'Unauthorized: Invalid Bot Secret'], 401);
        }

        // 2. Identifikasi Pengguna (bisa dari user_id, uuid, phone_number, phone, atau sender)
        $userId = $request->input('user_id');
        $uuid = $request->input('uuid');
        $phone = $request->input('phone') ?: $request->input('phone_number') ?: $request->input('sender');

        $user = null;
        if ($userId) {
            $user = User::find($userId);
        } elseif ($uuid) {
            $user = User::where('uuid', strtolower(trim($uuid)))->first();
        } elseif ($phone) {
            $cleanPhone = preg_replace('/[:@].*$/', '', (string)$phone);
            $digits = preg_replace('/\D/', '', $cleanPhone);
            $core = str_starts_with($digits, '62') ? substr($digits, 2) : (str_starts_with($digits, '0') ? substr($digits, 1) : $digits);
            if (!empty($core)) {
                $user = User::where(function ($q) use ($digits, $core) {
                    $q->where('phone', $digits)
                      ->orWhere('phone', '0' . $core)
                      ->orWhere('phone', '62' . $core)
                      ->orWhere('phone', '+62' . $core)
                      ->orWhere('phone', 'like', '%' . $core . '%');
                })->first();
            }
        }

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Pengguna tidak ditemukan.'], 404);
        }

        // 3. Tentukan Perangkat IoT Milik Pengguna
        $userDevices = $user->userDevices()->with('device.sensors')->get();
        if ($userDevices->isEmpty()) {
            return response()->json([
                'success' => false, 
                'message' => 'Belum ada perangkat IoT yang terhubung ke akun Anda.'
            ], 404);
        }

        $targetUserDevice = null;
        $deviceIdReq = $request->input('device_id');
        $deviceNameReq = strtolower(trim((string)$request->input('device_name', '')));

        if ($deviceIdReq) {
            $targetUserDevice = $userDevices->firstWhere('device_id', $deviceIdReq);
        } elseif ($deviceNameReq) {
            $targetUserDevice = $userDevices->first(function ($ud) use ($deviceNameReq) {
                return str_contains(strtolower($ud->custom_name), $deviceNameReq) ||
                       str_contains(strtolower($ud->device->name), $deviceNameReq);
            });
        }

        // Fallback: ambil perangkat yang memiliki tabel data dan berisi rekaman data
        if (!$targetUserDevice) {
            $targetUserDevice = $userDevices->first(function ($ud) {
                return $ud->device 
                    && $ud->device->table_name 
                    && \Schema::hasTable($ud->device->table_name)
                    && \DB::table($ud->device->table_name)->exists();
            }) ?: $userDevices->first();
        }

        $device = $targetUserDevice->device;
        $tableName = $device->table_name;

        if (!$tableName || !\Schema::hasTable($tableName)) {
            return response()->json([
                'success' => false,
                'message' => "Tabel data sensor untuk perangkat '{$targetUserDevice->custom_name}' belum tersedia.",
            ], 404);
        }

        // 4. Query Data Sensor & Terapkan Filter Rentang Waktu
        $query = DB::table($tableName);

        // A. Filter Kata Kunci Rentang Waktu (1h, 6h, 24h, today, yesterday, 7d, 30d)
        $range = strtolower(trim((string)$request->input('range', '')));
        $hasTimeFilter = false;
        $rangeLabel = '';

        if ($range === '1h') {
            $query->where('recorded_at', '>=', now()->subHour());
            $hasTimeFilter = true;
            $rangeLabel = '1 jam terakhir';
        } elseif ($range === '6h') {
            $query->where('recorded_at', '>=', now()->subHours(6));
            $hasTimeFilter = true;
            $rangeLabel = '6 jam terakhir';
        } elseif ($range === '24h' || $range === '1d') {
            $query->where('recorded_at', '>=', now()->subDay());
            $hasTimeFilter = true;
            $rangeLabel = '24 jam terakhir';
        } elseif ($range === 'today' || $range === 'hari_ini') {
            $query->whereDate('recorded_at', now()->today());
            $hasTimeFilter = true;
            $rangeLabel = 'hari ini (' . now()->format('d M Y') . ')';
        } elseif ($range === 'yesterday' || $range === 'kemarin') {
            $query->whereDate('recorded_at', now()->yesterday());
            $hasTimeFilter = true;
            $rangeLabel = 'kemarin (' . now()->yesterday()->format('d M Y') . ')';
        } elseif ($range === '7d' || $range === '1w' || $range === 'seminggu') {
            $query->where('recorded_at', '>=', now()->subDays(7));
            $hasTimeFilter = true;
            $rangeLabel = '7 hari terakhir';
        } elseif ($range === '30d' || $range === '1m' || $range === 'sebulan') {
            $query->where('recorded_at', '>=', now()->subDays(30));
            $hasTimeFilter = true;
            $rangeLabel = '30 hari terakhir';
        }

        // B. Filter Rentang Tanggal Spesifik (start_date & end_date)
        if ($request->filled('start_date')) {
            $hasTimeFilter = true;
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date', $startDate);
            $query->whereBetween('recorded_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
            $rangeLabel = ($startDate === $endDate) ? "tanggal {$startDate}" : "{$startDate} s/d {$endDate}";
        }

        // C. Logika Pengambilan Data:
        // - Jika ada filter waktu (misal hari ini, kemarin, 24h, tanggal):
        //   Backend me-load seluruh rekaman pada rentang tersebut (hingga 5000 baris) untuk menghitung rata-rata, min, max secara menyeluruh.
        // - Jika tanpa filter waktu: ambil data terbaru sesuai limit (default 10).
        $isExplicitLimit = $request->filled('limit');
        $explicitLimit = $isExplicitLimit ? (int) $request->input('limit') : null;
        if ($explicitLimit !== null) {
            if ($explicitLimit < 1) $explicitLimit = 10;
            if ($explicitLimit > 500) $explicitLimit = 500;
        }

        if ($hasTimeFilter) {
            $rawRows = $query->orderBy('recorded_at', 'desc')->limit(5000)->get();
        } else {
            $limit = $explicitLimit ?: 10;
            $rawRows = $query->orderBy('recorded_at', 'desc')->limit($limit)->get();
            $rangeLabel = "{$limit} data terakhir";
        }

        if ($rawRows->isEmpty()) {
            $latestRecord = DB::table($tableName)->orderBy('recorded_at', 'desc')->first();
            $lastActive = $latestRecord ? \Carbon\Carbon::parse($latestRecord->recorded_at)->format('d M Y H:i') : null;

            return response()->json([
                'success' => true,
                'device_name' => $targetUserDevice->custom_name ?: $device->name,
                'total_rekaman' => 0,
                'rentang' => $rangeLabel,
                'message' => 'Belum ada data sensor yang terekam pada rentang waktu tersebut.',
                'terakhir_aktif' => $lastActive ? "Data sensor terakhir terekam pada {$lastActive}" : null,
                'summary' => null,
                'logs' => [],
            ], 200);
        }

        // 5. Pemetaan Kolom Sensor & Normalisasi Nama
        $sensorCols = [
            'suhu' => ['ni_SUHU', 'suhu', 'temperature'],
            'kelembaban' => ['ni_KELEM', 'kelembaban', 'humidity'],
            'tds' => ['ni_TDS', 'tds'],
            'ph' => ['ni_PH', 'ph'],
            'ec' => ['ni_EC', 'ec'],
            'lux' => ['ni_LUX', 'lux'],
        ];

        $sensorUnits = [
            'suhu' => '°C',
            'kelembaban' => '%',
            'tds' => 'ppm',
            'ph' => 'pH',
            'ec' => 'mS/cm',
            'lux' => 'lux',
        ];

        // Format data baris log agar ringkas dan hemat token untuk AI
        $formattedLogs = [];
        foreach ($rawRows as $row) {
            $item = ['waktu' => \Carbon\Carbon::parse($row->recorded_at)->format('d M H:i')];
            foreach ($sensorCols as $key => $possibleNames) {
                foreach ($possibleNames as $col) {
                    if (property_exists($row, $col) && $row->$col !== null) {
                        $item[$key] = is_numeric($row->$col) ? (float) $row->$col : $row->$col;
                        break;
                    }
                }
            }
            $formattedLogs[] = $item;
        }

        // 6. Hitung Statistik Otomatis Menyeluruh (Rata-rata, Min, Max, Terkini)
        $summary = [];
        foreach ($sensorCols as $key => $possibleNames) {
            $values = collect($formattedLogs)->pluck($key)->filter(fn($v) => is_numeric($v));
            if ($values->isNotEmpty()) {
                $summary[$key] = [
                    'terkini' => $values->first(),
                    'rata_rata' => round($values->avg(), 1),
                    'min' => round($values->min(), 1),
                    'max' => round($values->max(), 1),
                    'satuan' => $sensorUnits[$key] ?? '',
                ];
            }
        }

        // 7. Pengiriman Sampel Baris Log (Hemat Token & Ringkas di Chat WhatsApp)
        // Jika user secara spesifik meminta 'limit' (misal: "tampilkan 20 data"), kirimkan sesuai limit.
        // Jika user bertanya kondisi rentang waktu (misal "hari ini"), kirim 10 baris sampel terbaru saja
        // agar respon AI cepat, ringkas, dan tidak menghabiskan token.
        $sampleLimit = $isExplicitLimit ? $explicitLimit : 10;
        $returnedLogs = array_slice($formattedLogs, 0, min($sampleLimit, count($formattedLogs)));

        return response()->json([
            'success' => true,
            'device_name' => $targetUserDevice->custom_name ?: $device->name,
            'device_type' => $device->type,
            'rentang' => $rangeLabel,
            'total_rekaman' => count($formattedLogs),
            'total_ambil' => count($returnedLogs),
            'summary' => $summary,
            'logs' => $returnedLogs,
            'logs_count' => count($returnedLogs),
            'catatan' => count($formattedLogs) > count($returnedLogs)
                ? "Statistik (summary) dihitung dari seluruh " . count($formattedLogs) . " rekaman sensor pada rentang tersebut. Menampilkan " . count($returnedLogs) . " data sampel terbaru."
                : null,
        ], 200);
    }


    /**
     * Endpoint khusus untuk Unbind / Disconnect Nomor WhatsApp (Ideal untuk AI Tool di n8n)
     */
    public function unbindMember(Request $request)
    {
        $request->merge(['action' => 'unbind']);
        return $this->checkMember($request);
    }
}

