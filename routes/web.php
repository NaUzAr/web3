<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDeviceController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\AutomationConfigController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\WebViewAuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Chatbot
Route::post('/chatbot', [ChatbotController::class, 'respond'])->name('chatbot.respond');

// === WEBVIEW AUTO-LOGIN (From Flutter) ===
Route::get('/webview/auto-login', [WebViewAuthController::class, 'autoLogin'])->name('webview.auto-login');

Route::middleware(['auth', 'verified'])->group(function () {

    // Grouping khusus URL awalan /admin
    Route::prefix('admin')->name('admin.')->group(function () {

        // List Semua Device
        Route::get('/devices', [AdminDeviceController::class, 'index'])->name('devices.index');

        // Create Device (Yg sudah dibuat sebelumnya)
        Route::get('/create-device', [AdminDeviceController::class, 'create'])->name('device.create');
        Route::post('/create-device', [AdminDeviceController::class, 'store'])->name('device.store');

        // Edit Device
        Route::get('/device/{id}/edit', [AdminDeviceController::class, 'edit'])->name('device.edit');
        Route::put('/device/{id}', [AdminDeviceController::class, 'update'])->name('device.update');

        // Delete Device
        Route::delete('/device/{id}', [AdminDeviceController::class, 'destroy'])->name('device.destroy');

        // Monitoring Device (Admin View)
        Route::get('/device/{id}/monitoring', [AdminDeviceController::class, 'showMonitoring'])->name('device.monitoring');
        Route::get('/device/{id}/history', [AdminDeviceController::class, 'history'])->name('device.history');

        // Toggle Output (Admin)
        Route::post('/device/{deviceId}/output/{outputId}/toggle', [AdminDeviceController::class, 'toggleOutput'])->name('device.output.toggle');

        // Dosing by Volume (Admin)
        Route::post('/device/{deviceId}/dosing/volume', [AdminDeviceController::class, 'controlDosingByVolume'])->name('device.dosing.volume');
        
        // Pump Control (Admin)
        Route::post('/device/{deviceId}/pump/control', [AdminDeviceController::class, 'controlPump'])->name('device.pump.control');
        Route::post('/device/{deviceId}/output/{outputId}/irrigation-pump', [AdminDeviceController::class, 'controlIrrigationPump'])->name('device.irrigation.pump');

        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs');

        // Admin Tickets
        Route::get('/tickets', [\App\Http\Controllers\AdminTicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{ticket}', [\App\Http\Controllers\AdminTicketController::class, 'show'])->name('tickets.show');
        Route::put('/tickets/{ticket}', [\App\Http\Controllers\AdminTicketController::class, 'update'])->name('tickets.update');


        // Status (Admin Polling)
        Route::get('/device/{id}/status', [AdminDeviceController::class, 'getStatus'])->name('device.status');

        // Global Sensor Rules (Admin Peringatan Dini FCM)
        Route::get('/sensor-rules', [\App\Http\Controllers\AdminSensorRuleController::class, 'index'])->name('sensor-rules.index');
        Route::post('/sensor-rules', [\App\Http\Controllers\AdminSensorRuleController::class, 'store'])->name('sensor-rules.store');
        Route::post('/sensor-rules/test', [\App\Http\Controllers\AdminSensorRuleController::class, 'testNotification'])->name('sensor-rules.test');
        Route::put('/sensor-rules/{id}', [\App\Http\Controllers\AdminSensorRuleController::class, 'update'])->name('sensor-rules.update');
        Route::delete('/sensor-rules/{id}', [\App\Http\Controllers\AdminSensorRuleController::class, 'destroy'])->name('sensor-rules.destroy');

        // Broadcast Announcements
        Route::get('/announcements', [\App\Http\Controllers\AdminAnnouncementController::class, 'index'])->name('announcements.index');
        Route::post('/announcements/send', [\App\Http\Controllers\AdminAnnouncementController::class, 'send'])->name('announcements.send');
    });

    // === MONITORING ROUTES (untuk semua user yang login) ===
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/', [MonitoringController::class, 'index'])->name('index');
        Route::get('/add', [MonitoringController::class, 'create'])->name('create');
        Route::post('/add', [MonitoringController::class, 'store'])->name('store');
        Route::get('/device/{id}', [MonitoringController::class, 'show'])->name('show');
        Route::get('/device/{id}/history', [MonitoringController::class, 'history'])->name('history');
        Route::put('/device/{id}', [MonitoringController::class, 'updateUserDevice'])->name('update');
        Route::delete('/device/{id}', [MonitoringController::class, 'destroy'])->name('destroy');
        Route::match(['get', 'post'], '/device/{id}/export', [MonitoringController::class, 'exportCsv'])->name('export');
        Route::post('/device/{id}/output/{outputId}/toggle', [MonitoringController::class, 'toggleOutput'])->name('output.toggle');
        Route::post('/device/{id}/pump/control', [MonitoringController::class, 'controlPump'])->name('pump.control');
        Route::post('/device/{id}/output/{outputId}/irrigation-pump', [MonitoringController::class, 'controlIrrigationPump'])->name('irrigation.pump');
        Route::post('/device/{id}/dosing/volume', [MonitoringController::class, 'controlDosingByVolume'])->name('dosing.volume');
        Route::get('/device/{id}/status', [MonitoringController::class, 'getStatus'])->name('status');
        Route::post('/device/{id}/favorite', [MonitoringController::class, 'toggleFavorite'])->name('favorite');
    });

    // === AUTOMATION ROUTES (untuk user kelola automation) ===
    Route::prefix('device/{deviceId}/automation')->name('automation.')->group(function () {
        Route::get('/', [AutomationConfigController::class, 'index'])->name('index');
        Route::get('/create', [AutomationConfigController::class, 'create'])->name('create');
        Route::post('/', [AutomationConfigController::class, 'store'])->name('store');
    });

    Route::prefix('automation')->name('automation.')->group(function () {
        Route::get('/{id}/edit', [AutomationConfigController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AutomationConfigController::class, 'update'])->name('update');
        Route::delete('/{id}', [AutomationConfigController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle', [AutomationConfigController::class, 'toggle'])->name('toggle');
        Route::get('/device/{deviceId}/sensors', [AutomationConfigController::class, 'getSensorsForDevice'])->name('sensors');
    });

    // === AUTOMASI CUSTOM ROUTES ===
    Route::prefix('device/{id}/automasi')->name('automasi.')->group(function () {
        Route::get('/', [App\Http\Controllers\AutomasiController::class, 'index'])->name('index');
        Route::match(['get', 'post'], '/update-single', [App\Http\Controllers\AutomasiController::class, 'updateSingle'])->name('update_single');
    });

    // === SCHEDULE MANAGEMENT ROUTES (Real-time MQTT) ===
    Route::prefix('device/{userDeviceId}/schedule')->name('schedule.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::post('/time', [ScheduleController::class, 'storeTimeSchedules'])->name('time.store');
        Route::delete('/{slotId}', [ScheduleController::class, 'destroy'])->name('destroy');
    });

    // === USER RIWAYAT (Activity Log Frontend) ===
    Route::get('/riwayat', [\App\Http\Controllers\UserActivityController::class, 'index'])->name('riwayat.index');

    // === TICKET SYSTEM (User Frontend) ===
    Route::get('/tickets', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [\App\Http\Controllers\TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [\App\Http\Controllers\TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [\App\Http\Controllers\TicketController::class, 'reply'])->name('tickets.reply');

});

// === EMAIL VERIFICATION ROUTES ===
// Halaman notice (bisa diakses guest setelah register)
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

// Handle klik link verifikasi dari email (TANPA AUTH - manual verification)
Route::get('/email/verify/{id}/{hash}', function ($id, $hash) {
    $user = \App\Models\User::findOrFail($id);

    // Verifikasi hash
    if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Link verifikasi tidak valid.');
    }

    // Tandai email sebagai verified
    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    return view('auth.verified');
})->middleware('signed')->name('verification.verify');

// Kirim ulang email verifikasi
Route::post('/email/resend', function (Request $request) {
    // Cari user berdasarkan email di session
    $email = $request->session()->get('pending_verification_email');
    if ($email) {
        $user = \App\Models\User::where('email', $email)->first();
        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
            return back()->with('status', 'Link verifikasi telah dikirim ulang!');
        }
    }
    return back()->withErrors(['email' => 'Tidak dapat mengirim ulang. Silakan register ulang.']);
})->middleware('throttle:1,1')->name('verification.resend');

// Beranda (public)
Route::get('/', function () {
    if (auth()->check()) {
        return view('page.beranda');
    }
    return redirect()->route('login');
})->name('home');

// Privacy Policy
Route::get('/privacy-policy', function () {
    return view('page.privacy');
})->name('privacy.policy');

// --- LOGIN ---
route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- REGISTER ---
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.perform');

// --- FORGOT PASSWORD ---
Route::get('/password/forgot', [ForgotPasswordController::class, 'showForm'])->name('password.request');
Route::post('/password/forgot', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/password/reset/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

