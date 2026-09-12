<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\User;
use App\Services\FirebaseService;
use Carbon\Carbon;

class AdminAnnouncementController extends Controller
{
    /**
     * Tampilkan halaman pengumuman (list history dan form kirim).
     */
    public function index()
    {
        $announcements = Announcement::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Proses pengiriman multicast ke semua device.
     */
    public function send(Request $request, FirebaseService $firebaseService)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Ambil semua token FCM user yang tidak null atau kosong
        $users = User::whereNotNull('fcm_token')->where('fcm_token', '!=', '')->get();
        $tokens = $users->pluck('fcm_token')->toArray();
        $tokens = array_unique($tokens); // Hapus token duplikat

        // Simpan langsung ke database meskipun belum dikirim
        $announcement = new Announcement();
        $announcement->title = $request->title;
        $announcement->message = $request->message;
        $announcement->success_count = 0;
        $announcement->failure_count = 0;
        $announcement->sent_at = Carbon::now();
        $announcement->save();

        if (!$firebaseService->isConfigured()) {
            return redirect()->back()->with('error', 'Gagal: ' . ($firebaseService->getLastError() ?: 'Kredensial Firebase (firebase-adminsdk.json) belum ditemukan di server.'));
        }

        if (empty($tokens)) {
            return redirect()->back()->with('error', 'Tidak ada perangkat yang terdaftar untuk menerima notifikasi (belum ada user yang login via aplikasi mobile).');
        }

        // Kirim via firebase (Multicast)
        $report = $firebaseService->sendToTokens($tokens, $request->title, $request->message);

        if ($report) {
            // Update history dengan hasil pengiriman
            $announcement->success_count = $report->successes()->count();
            $announcement->failure_count = $report->failures()->count();
            $announcement->save();

            $msg = "Pengumuman berhasil dikirim ke {$announcement->success_count} perangkat.";
            if ($announcement->failure_count > 0) {
                $msg .= " (Gagal mengirim ke {$announcement->failure_count} perangkat).";
            }
            return redirect()->back()->with('success', $msg);
        }

        $detailError = $firebaseService->getLastError() ?: 'Service FCM tidak merespons atau tidak diatur dengan benar.';
        return redirect()->back()->with('error', 'Gagal mengirim pengumuman: ' . $detailError);
    }
}
