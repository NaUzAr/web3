<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil pengguna
     */
    public function index()
    {
        $user = Auth::user();
        return view('profile.index', compact('user'));
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
}
