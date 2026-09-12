<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    /**
     * Tampilkan daftar user beserta statistik device
     */
    public function index(Request $request)
    {
        $query = User::withCount('userDevices');

        // Fitur pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            });
        }

        // Filter role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $totalUsers = User::count();
        $totalAdmins = User::where('role', 'admin')->count();
        $totalRegular = User::where('role', '!=', 'admin')->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'totalAdmins', 'totalRegular'));
    }

    /**
     * Ubah status role user (admin / user)
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:admin,user',
        ]);

        $user = User::findOrFail($id);

        // Jangan izinkan admin mencabut hak akses dirinya sendiri
        if ($user->id === Auth::id() && $request->role !== 'admin') {
            return back()->with('error', 'Anda tidak dapat mencabut hak akses Admin dari akun Anda sendiri.');
        }

        $oldRole = $user->role;
        $user->role = $request->role;
        $user->save();

        ActivityLog::log(
            'role_change',
            "Admin " . Auth::user()->name . " mengubah role {$user->name} dari '{$oldRole}' menjadi '{$user->role}'"
        );

        return back()->with('success', "Role user '{$user->name}' berhasil diubah menjadi '{$user->role}'.");
    }

    /**
     * Hapus akun user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;

        // Hapus relasi user_devices
        $user->userDevices()->delete();
        $user->delete();

        ActivityLog::log(
            'user_delete',
            "Admin " . Auth::user()->name . " menghapus akun pengguna '{$userName}'"
        );

        return back()->with('success', "Akun '{$userName}' dan akses monitoringnya berhasil dihapus.");
    }
}
