<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ==================== OPERATIONAL IoT SCHEDULES ====================

// 1. Device Offline Watchdog: Periksa konektivitas IoT setiap 10 menit
Schedule::command('app:check-device-status')->everyTenMinutes()->withoutOverlapping();

// 2. Data Pruning: Bersihkan log sensor lebih dari 60 hari setiap hari pukul 02:00
Schedule::command('sensors:prune --days=60')->dailyAt('02:00');

// 3. Database Backup: Jalankan dump cadangan harian setiap pukul 03:00
Schedule::command('db:backup')->dailyAt('03:00');
