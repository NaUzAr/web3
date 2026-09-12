<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService
{
    protected $messaging = null;
    protected $lastError = null;

    public function __construct()
    {
        $factory = new Factory();
        $configured = false;

        // 1. Coba dari file kredensial (default: firebase-adminsdk.json di root)
        $credentialsEnv = env('FIREBASE_CREDENTIALS', 'firebase-adminsdk.json');
        $credentialsPath = str_starts_with($credentialsEnv, '/') || (strlen($credentialsEnv) > 2 && $credentialsEnv[1] === ':')
            ? $credentialsEnv
            : base_path($credentialsEnv);

        if (file_exists($credentialsPath)) {
            try {
                $factory = $factory->withServiceAccount($credentialsPath);
                $this->messaging = $factory->createMessaging();
                $configured = true;
            } catch (\Throwable $e) {
                $this->lastError = "Gagal memuat file kredensial Firebase: " . $e->getMessage();
                \Log::error($this->lastError);
            }
        }

        // 2. Fallback: Kredensial via ENV Base64 (sangat cocok untuk Docker tanpa copy file)
        if (!$configured && env('FIREBASE_CREDENTIALS_BASE64')) {
            try {
                $decoded = base64_decode(env('FIREBASE_CREDENTIALS_BASE64'));
                $array = json_decode($decoded, true);
                if (is_array($array)) {
                    $factory = $factory->withServiceAccount($array);
                    $this->messaging = $factory->createMessaging();
                    $configured = true;
                }
            } catch (\Throwable $e) {
                $this->lastError = "Gagal memuat kredensial Base64: " . $e->getMessage();
                \Log::error($this->lastError);
            }
        }

        // 3. Fallback: Kredensial via ENV JSON
        if (!$configured && env('FIREBASE_CREDENTIALS_JSON')) {
            try {
                $array = json_decode(env('FIREBASE_CREDENTIALS_JSON'), true);
                if (is_array($array)) {
                    $factory = $factory->withServiceAccount($array);
                    $this->messaging = $factory->createMessaging();
                    $configured = true;
                }
            } catch (\Throwable $e) {
                $this->lastError = "Gagal memuat kredensial JSON: " . $e->getMessage();
                \Log::error($this->lastError);
            }
        }

        if (!$configured && !$this->lastError) {
            $this->lastError = "File kredensial Firebase tidak ditemukan di: {$credentialsPath}.";
            \Log::warning("Firebase credentials not found. FCM will be disabled.");
        }
    }

    /**
     * Cek apakah service FCM siap digunakan
     */
    public function isConfigured(): bool
    {
        return $this->messaging !== null;
    }

    /**
     * Dapatkan pesan error terakhir
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * Send notification to a specific device
     */
    public function sendToToken($fcmToken, $title, $body, $data = [])
    {
        if (!$this->messaging) {
            $this->lastError = "Service FCM belum terkonfigurasi.";
            return false;
        }

        if (!$fcmToken) {
            $this->lastError = "Token FCM tujuan kosong.";
            return false;
        }

        try {
            $messageConfig = [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ];

            if (!empty($data)) {
                $messageConfig['data'] = $data;
            }

            $message = CloudMessage::fromArray($messageConfig);
            $this->messaging->send($message);
            return true;
        } catch (\Throwable $e) {
            $this->lastError = $e->getMessage();
            \Log::error('Firebase Notification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send multicast notification to multiple tokens
     * 
     * @param array $tokens Array of valid FCM tokens
     * @return \Kreait\Firebase\Messaging\MulticastSendReport|null
     */
    public function sendToTokens(array $tokens, $title, $body, $data = [])
    {
        if (!$this->messaging) {
            $this->lastError = "Service FCM belum terkonfigurasi. File firebase-adminsdk.json tidak ditemukan.";
            return null;
        }

        if (empty($tokens)) {
            $this->lastError = "Daftar token penerima kosong.";
            return null;
        }

        try {
            $messageConfig = [
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
            ];

            if (!empty($data)) {
                $messageConfig['data'] = $data;
            }

            $message = CloudMessage::fromArray($messageConfig);
            $report = $this->messaging->sendMulticast($message, $tokens);

            return $report;
        } catch (\Throwable $e) {
            $this->lastError = $e->getMessage();
            \Log::error('Firebase Multicast Error: ' . $e->getMessage());
            return null;
        }
    }
}
