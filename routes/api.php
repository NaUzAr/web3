<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\SensorDataController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeviceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes untuk menerima data sensor dari device IoT
| Dan routes untuk mobile app Flutter
|
*/

// ==================== AUTH ROUTES (Flutter) ====================
Route::middleware('throttle:6,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/email/resend', [AuthController::class, 'resendVerification']);
    Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
});

// ==================== SANCTUM PROTECTED ROUTES ====================
Route::middleware('auth:sanctum')->group(function () {
    // User profile & notification token
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'updatePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/fcm-token', [AuthController::class, 'updateFcmToken']);
    Route::get('/token-status', function (Request $request) {
        $token = $request->user()->currentAccessToken();
        return response()->json([
            'success' => true,
            'token_name' => $token->name ?? 'unknown',
            'expires_at' => $token->expires_at ? $token->expires_at->toIso8601String() : null,
            'days_remaining' => $token->expires_at ? max(0, (int) ceil(now()->diffInDays($token->expires_at, false))) : null,
            'is_expired' => $token->expires_at ? $token->expires_at->isPast() : false,
            'user' => [
                'id' => $request->user()->id,
                'uuid' => $request->user()->uuid,
                'name' => $request->user()->name,
                'username' => $request->user()->username,
            ]
        ]);
    });

    // Device routes untuk Flutter
    Route::get('/devices', [DeviceController::class, 'index']);
    Route::post('/devices', [DeviceController::class, 'store']);
    Route::get('/devices/{id}', [DeviceController::class, 'show']);
    Route::put('/devices/{id}', [DeviceController::class, 'update']);
    Route::post('/devices/{id}/favorite', [DeviceController::class, 'toggleFavorite']);
    Route::post('/devices/{id}/relay-status', [DeviceController::class, 'checkRelayStatus']);
    Route::delete('/devices/{id}', [DeviceController::class, 'destroy']);
});

// ==================== SENSOR DATA ROUTES (IoT Device) ====================
// Throttle 120 requests per minute untuk perlindungan anti-spam IoT
Route::middleware('throttle:120,1')->group(function () {
    Route::post('/sensor-data', [SensorDataController::class, 'store']);
    Route::get('/sensor-data/{token}', [SensorDataController::class, 'show']);
});

// ==================== BOT & AI INTEGRATION (n8n & WAHA) ====================
Route::match(['get', 'post'], '/bot/check-member', [\App\Http\Controllers\Api\BotMemberController::class, 'checkMember'])->middleware('throttle:60,1');
Route::post('/bot/bind-member', [\App\Http\Controllers\Api\BotMemberController::class, 'checkMember'])->middleware('throttle:60,1');

