<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserActivityController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $user = Auth::user();

        // Ambil daftar perangkat milik user untuk dropdown filter
        $userDevices = $user->userDevices()->with('device')->get();

        // Base query riwayat aktivitas milik user yang login
        $query = ActivityLog::where('user_id', $userId);

        // 1. Filter Kategori Aksi
        $category = $request->get('category', 'all');
        if ($category === 'control') {
            $query->where('action', 'device_control');
        } elseif ($category === 'pump') {
            $query->whereIn('action', ['pump_control', 'irrigation_control']);
        } elseif ($category === 'dosing') {
            $query->where('action', 'dosing_control');
        } elseif ($category === 'device') {
            $query->whereIn('action', ['add_device', 'update_device', 'remove_device']);
        } elseif ($category === 'account') {
            $query->whereIn('action', ['login', 'logout', 'profile_update', 'password_change']);
        }

        // 2. Filter Perangkat spesifik
        if ($request->filled('device_id')) {
            $deviceId = (string) $request->get('device_id');
            $query->whereRaw("details->>'device_id' = ?", [$deviceId]);
        }

        // 3. Filter Rentang Waktu
        $dateRange = $request->get('date_range', 'all');
        if ($dateRange === 'today') {
            $query->whereDate('created_at', now()->toDateString());
        } elseif ($dateRange === '7days') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($dateRange === '30days') {
            $query->where('created_at', '>=', now()->subDays(30));
        }

        // 4. Pencarian Kata Kunci
        if ($request->filled('q')) {
            $keyword = trim($request->get('q'));
            $query->where(function ($sub) use ($keyword) {
                $sub->where('description', 'ILIKE', "%{$keyword}%")
                    ->orWhereRaw("details::text ILIKE ?", ["%{$keyword}%"]);
            });
        }

        // Ambil data dengan pagination dan pertahankan parameter query
        $logs = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Hitung Ringkasan KPI Statistik (Semua riwayat user)
        $stats = [
            'total' => ActivityLog::where('user_id', $userId)->count(),
            'today' => ActivityLog::where('user_id', $userId)->whereDate('created_at', now()->toDateString())->count(),
            'control' => ActivityLog::where('user_id', $userId)->where('action', 'device_control')->count(),
            'pump_dosing' => ActivityLog::where('user_id', $userId)->whereIn('action', ['pump_control', 'irrigation_control', 'dosing_control'])->count(),
        ];

        return view('riwayat.index', compact('logs', 'userDevices', 'stats', 'category', 'dateRange'));
    }
}

