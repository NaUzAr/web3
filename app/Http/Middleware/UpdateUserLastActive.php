<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserLastActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (Auth::check()) {
            $user = Auth::user();

            // Hanya update jika belum pernah aktif atau sudah lebih dari 2 menit yang lalu
            // (Throttled per 2 menit agar hemat performa database)
            if (!$user->last_active_at || $user->last_active_at->diffInMinutes(now()) >= 2) {
                $user->timestamps = false;
                $user->last_active_at = now();
                $user->save();
            }
        }

        return $response;
    }
}
