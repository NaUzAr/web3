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
                          ->orWhere('phone', 'like', '%' . $core);
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

        // 4. Cek apakah pesan berisi format pairing (misal: "CONNECT 40db4d83 2|token...")
        $message = $request->input('message');
        if (!$user && $message) {
            if (preg_match('/\b([a-f0-9]{8})\b/i', (string)$message, $mUuid)) {
                $candidate = User::where('uuid', strtolower($mUuid[1]))->first();
                if ($candidate) {
                    if (preg_match('/(\d+\|[A-Za-z0-9_-]{20,})/', (string)$message, $mTok)) {
                        $tokenModel = PersonalAccessToken::findToken($mTok[1]);
                        if ($tokenModel && $tokenModel->tokenable_id === $candidate->id && (!$tokenModel->expires_at || !$tokenModel->expires_at->isPast())) {
                            $user = $candidate;
                        }
                    }
                }
            }
        }

        // 5. Jika user terverifikasi dan belum punya nomor telepon, otomatis bind nomor WA pengirim
        if ($user && empty($user->phone) && $phone) {
            $clean = preg_replace('/[:@].*$/', '', (string)$phone);
            $rawDigits = preg_replace('/\D/', '', $clean);
            $user->phone = str_starts_with($rawDigits, '62') ? ('0' . substr($rawDigits, 2)) : $rawDigits;
            $user->saveQuietly();
        }

        // Jika BUKAN member
        if (!$user) {
            return response()->json([
                'is_member' => false,
                'message' => 'Nomor WhatsApp belum terdaftar sebagai member Swaratani IoT.',
                'suggestion' => 'Silakan lakukan pendaftaran akun dan simpan nomor WhatsApp Anda di menu Profil website Swaratani IoT.',
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
