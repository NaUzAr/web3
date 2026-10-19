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

            return response()->json([
                'is_member' => true,
                'action' => 'bind',
                'phone_bound' => true,
                'message' => "Nomor WhatsApp ({$formattedPhone}) berhasil dihubungkan ke akun {$candidate->name} ({$candidate->username})!",
                'reply_for_user' => "Selamat datang, {$candidate->name}! Nomor WhatsApp Anda ({$formattedPhone}) berhasil dihubungkan ke akun Swaratani. Sekarang Anda dapat menggunakan layanan bot AI IoT.",
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

        // Jika MEMBER terdaftar, ambil ringkasan device dan sensor terkini miliknya
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
}
