<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\UserDevice;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    /**
     * GET /api/devices
     * Ambil semua device yang terhubung dengan user login
     */
    public function index(Request $request)
    {
        $devices = $request->user()
            ->userDevices()
            ->with(['device.sensors', 'device.outputs'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => $devices,
        ]);
    }

    /**
     * POST /api/devices
     * Tambah device ke monitoring user via token unik
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string|size:16',
            'custom_name' => 'nullable|string|max:100',
        ]);

        $device = Device::where('token', $request->token)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan! Pastikan token benar.',
            ], 404);
        }

        $userId = $request->user()->id;
        $exists = UserDevice::where('user_id', $userId)
            ->where('device_id', $device->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Device ini sudah ada di daftar monitoring Anda.',
            ], 409);
        }

        $userDevice = UserDevice::create([
            'user_id' => $userId,
            'device_id' => $device->id,
            'custom_name' => $request->custom_name ?: $device->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Device '{$device->name}' berhasil ditambahkan!",
            'data' => $userDevice->load('device'),
        ], 201);
    }

    /**
     * GET /api/devices/{id}
     * Detail user device beserta sensors dan outputs
     */
    public function show(Request $request, $id)
    {
        $userDevice = UserDevice::with(['device.sensors', 'device.outputs'])
            ->where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$userDevice) {
            return response()->json([
                'success' => false,
                'message' => 'Device tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $userDevice,
        ]);
    }

    /**
     * DELETE /api/devices/{id}
     * Hapus device dari monitoring user
     */
    public function destroy(Request $request, $id)
    {
        $userDevice = UserDevice::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$userDevice) {
            return response()->json([
                'success' => false,
                'message' => 'Device tidak ditemukan.',
            ], 404);
        }

        $deviceName = $userDevice->custom_name;
        $userDevice->delete();

        return response()->json([
            'success' => true,
            'message' => "Device '{$deviceName}' berhasil dihapus.",
        ]);
    }

    /**
     * PUT /api/devices/{id}
     * Update custom_name dan notes device
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'custom_name' => 'required|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $userDevice = UserDevice::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$userDevice) {
            return response()->json([
                'success' => false,
                'message' => 'Device tidak ditemukan.',
            ], 404);
        }

        $userDevice->update([
            'custom_name' => $request->custom_name,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device berhasil diperbarui.',
            'data' => $userDevice->load('device'),
        ]);
    }

    /**
     * POST /api/devices/{id}/favorite
     * Toggle status favorit device
     */
    public function toggleFavorite(Request $request, $id)
    {
        $userDevice = UserDevice::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->first();

        if (!$userDevice) {
            return response()->json([
                'success' => false,
                'message' => 'Device tidak ditemukan.',
            ], 404);
        }

        $userDevice->is_favorite = !$userDevice->is_favorite;
        $userDevice->save();

        return response()->json([
            'success' => true,
            'is_favorite' => $userDevice->is_favorite,
            'message' => $userDevice->is_favorite ? 'Ditambahkan ke favorit.' : 'Dihapus dari favorit.',
        ]);
    }
}
