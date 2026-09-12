<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class WebViewAuthController extends Controller
{
    /**
     * Authenticate a WebView request using a Sanctum Token
     * Request format: GET /webview/auto-login?token={sanctum_token}&redirect=/monitoring/device/{id}
     */
    public function autoLogin(Request $request)
    {
        $token = $request->query('token');
        $redirectUrl = $request->query('redirect', route('monitoring.index')); // Default redirect

        if (!$token) {
            return response('Token is required', 401);
        }

        // Cari token Sanctum di database
        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken || !$accessToken->tokenable) {
            return response('Invalid or expired token', 401);
        }

        // Login user ini ke dalam Session aplikasi web
        $user = $accessToken->tokenable;
        Auth::login($user);

        // Regenerate session untuk keamanan (mencegah session fixation)
        $request->session()->regenerate();

        // Prevent open redirect: validasi agar hanya redirect ke path lokal internal
        if (str_starts_with($redirectUrl, 'http://') || str_starts_with($redirectUrl, 'https://')) {
            $parsed = parse_url($redirectUrl);
            $appHost = parse_url(config('app.url'), PHP_URL_HOST);
            if (!isset($parsed['host']) || ($appHost && $parsed['host'] !== $appHost)) {
                $redirectUrl = route('monitoring.index');
            }
        } elseif (str_starts_with($redirectUrl, '//')) {
            $redirectUrl = route('monitoring.index');
        } elseif (!str_starts_with($redirectUrl, '/')) {
            $redirectUrl = route('monitoring.index');
        }

        // Redirect ke halaman yang diminta di WebView
        return redirect($redirectUrl);
    }
}
