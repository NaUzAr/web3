<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Crypt;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil pengguna
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil token akses pihak ketiga
        $thirdPartyToken = $user->tokens()
            ->where('name', 'third_party_access')
            ->latest()
            ->first();

        $tokenExpired = false;
        $daysRemaining = null;
        if ($thirdPartyToken && $thirdPartyToken->expires_at) {
            $tokenExpired = $thirdPartyToken->expires_at->isPast();
            $diff = now()->diffInDays($thirdPartyToken->expires_at, false);
            $daysRemaining = $diff > 0 ? (int) ceil($diff) : 0;
        }

        // Cek aturan cooldown 7 hari untuk regenerate
        $canRegenerate = true;
        $daysUntilRegenerate = 0;
        if ($thirdPartyToken && $thirdPartyToken->created_at) {
            $canRegenerateAt = $thirdPartyToken->created_at->copy()->addDays(7);
            if (now()->lessThan($canRegenerateAt) && !$tokenExpired) {
                $canRegenerate = false;
                $diffWait = now()->diffInDays($canRegenerateAt, false);
                $daysUntilRegenerate = max(1, (int) ceil($diffWait));
            }
        }

        // Ambil token terenkripsi untuk tombol copy (tanpa ditampilkan langsung di teks biasa)
        $decryptedToken = null;
        if ($user->api_token && $thirdPartyToken && !$tokenExpired) {
            try {
                $decryptedToken = Crypt::decryptString($user->api_token);
            } catch (\Throwable $e) {
                $decryptedToken = null;
            }
        }

        return view('profile.index', compact(
            'user', 
            'thirdPartyToken', 
            'tokenExpired', 
            'daysRemaining',
            'canRegenerate',
            'daysUntilRegenerate',
            'decryptedToken'
        ));
    }

    /**
     * Perbarui data profil (nama, no HP, email)
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        // Cek jika kolom phone ada di database
        if (Schema::hasColumn('users', 'phone')) {
            $user->phone = $request->phone;
        }

        $user->save();

        ActivityLog::log('profile_update', "User {$user->name} memperbarui informasi profil");

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui!');
    }

    /**
     * Perbarui password pengguna
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        ActivityLog::log('password_change', "User {$user->name} berhasil mengubah password");

        return redirect()->route('profile.index')->with('password_success', 'Password berhasil diubah!');
    }

    /**
     * Regenerate token API untuk integrasi aplikasi pihak ketiga (bisa setting masa aktif & cooldown 7 hari)
     */
    public function regenerateToken(Request $request)
    {
        $user = Auth::user();

        // Validasi input masa berlaku (default 30 hari jika tidak dipilih)
        $request->validate([
            'validity_days' => 'nullable|integer|min:1|max:365',
        ]);
        $validityDays = (int) $request->input('validity_days', 30);
        if ($validityDays < 1) {
            $validityDays = 30;
        }

        // Cek token yang ada
        $existingToken = $user->tokens()->where('name', 'third_party_access')->latest()->first();

        // Aturan: Hanya bisa regenerate setelah 7 hari sejak dibuat (kecuali jika token sudah kedaluwarsa)
        if ($existingToken && $existingToken->created_at) {
            $canRegenerateAt = $existingToken->created_at->copy()->addDays(7);
            if (now()->lessThan($canRegenerateAt) && (!$existingToken->expires_at || !$existingToken->expires_at->isPast())) {
                $waitDays = max(1, (int) ceil(now()->diffInDays($canRegenerateAt, false)));
                return redirect()->route('profile.index')
                    ->with('error', "Token baru dapat di-regenerate setelah 7 hari sejak pembuatan (tersisa {$waitDays} hari lagi).");
            }
        }

        // 1. Hapus token third-party lama jika ada
        $user->tokens()->where('name', 'third_party_access')->delete();

        // 2. Buat token baru dengan masa aktif yang dipilih
        $newToken = $user->createToken('third_party_access', ['*'], now()->addDays($validityDays));

        // 3. Simpan token secara terenkripsi agar bisa disalin kapan saja oleh user
        $user->api_token = Crypt::encryptString($newToken->plainTextToken);
        $user->saveQuietly();

        ActivityLog::log('third_party_token_regenerated', "User {$user->name} me-regenerate API token pihak ketiga (masa berlaku {$validityDays} hari)");

        return redirect()->route('profile.index')
            ->with('token_success', "Token pihak ketiga berhasil diperbarui dan aktif selama {$validityDays} hari ke depan.");
    }

    /**
     * Cabut / hapus token akses pihak ketiga
     */
    public function revokeToken(Request $request)
    {
        $user = Auth::user();
        $user->tokens()->where('name', 'third_party_access')->delete();
        $user->api_token = null;
        $user->saveQuietly();

        ActivityLog::log('third_party_token_revoked', "User {$user->name} mencabut token akses pihak ketiga");

        return redirect()->route('profile.index')
            ->with('token_success', 'Akses token pihak ketiga berhasil dicabut.');
    }
}
