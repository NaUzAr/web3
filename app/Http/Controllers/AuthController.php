<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    // Menampilkan Halaman Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Memproses Login
    public function login(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // 2. Cek apakah user ada
        $user = User::where('username', $credentials['username'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->onlyInput('username');
        }

        // 3. Cek apakah email sudah diverifikasi
        if (!$user->hasVerifiedEmail()) {
            // Simpan email di session untuk resend
            $request->session()->put('pending_verification_email', $user->email);

            return redirect()->route('verification.notice')
                ->with('warning', 'Email belum diverifikasi. Silakan cek email atau kirim ulang verifikasi.');
        }

        // 4. Login jika sudah verified
        Auth::login($user);
        $request->session()->regenerate();

        // Update last_active_at
        $user->timestamps = false;
        $user->last_active_at = now();
        $user->save();

        // Log activity
        ActivityLog::log('login', "User {$user->name} berhasil login");

        // Detect PWA mode
        if ($request->has('pwa') || $request->session()->get('is_pwa')) {
            $request->session()->put('is_pwa', true);
            return redirect()->route('monitoring.index');
        }

        return redirect()->intended('/');
    }

    // Memproses Registrasi
    public function register(Request $request)
    {
        // A. Validasi Input
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // B. Buat User Baru di Database
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // C. Simpan email di session untuk ditampilkan di halaman verify
        $request->session()->put('pending_verification_email', $user->email);

        // D. TIDAK auto-login dan TIDAK auto-send email, user harus klik manual
        return redirect()->route('verification.notice')
            ->with('status', 'Akun berhasil dibuat! Klik tombol dibawah untuk mengirim email verifikasi.');
    }

    // Logout
    public function logout(Request $request)
    {
        $userName = Auth::user()->name ?? 'Unknown';
        ActivityLog::log('logout', "User {$userName} logout");

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}