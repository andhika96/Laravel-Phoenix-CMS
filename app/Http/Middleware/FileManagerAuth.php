<?php

namespace App\Http\Middleware;

use App\Models\Awesome_Admin\Account;
use App\Models\FmApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * FileManagerAuth Middleware
 *
 * Urutan autentikasi:
 *  1. Jika ada Authorization: Bearer {token} → coba Laravel Passport
 *  2. Jika ada header X-FM-Key atau query ?fm_key= → coba API Key
 *  3. Jika ada session auth (internal) → pakai langsung
 *  4. Tidak ada → 401
 *
 * Setelah auth berhasil, request akan memiliki:
 *   $request->fmUser    → Account model (atau account global untuk API key global)
 *   $request->fmApiKey  → FmApiKey model (null jika Passport/session auth)
 *   $request->fmMode    → 'passport' | 'api_key' | 'session'
 */
class FileManagerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        // ── 1. Coba Passport Bearer Token ─────────────────
        $bearerToken = $request->bearerToken();
        if ($bearerToken) {
            $user = $this->resolvePassportUser($bearerToken);
            if ($user) {
                $request->fmUser   = $user;
                $request->fmApiKey = null;
                $request->fmMode   = 'passport';
                Auth::setUser($user);
                return $this->handleCors($request, $next($request));
            }
        }

        // ── 2. Coba API Key ────────────────────────────────
        $apiKeyValue = $request->header('X-FM-Key')
                    ?? $request->query('fm_key');

        if ($apiKeyValue) {
            $apiKey = FmApiKey::where('key', $apiKeyValue)->first();

            if (!$apiKey || !$apiKey->isValid()) {
                return $this->unauthorized('API Key tidak valid atau sudah expired.');
            }

            // Cek origin (CORS)
            $origin = $request->header('Origin', '');
            if ($origin && !$apiKey->isOriginAllowed($origin)) {
                return $this->unauthorized('Origin tidak diizinkan untuk API Key ini.');
            }

            // Resolve account (pakai owner key, atau account id 1 jika global)
            $user = $apiKey->user ?? Account::find(1);
            if (!$user) {
                return $this->unauthorized('Account untuk API Key ini tidak ditemukan.');
            }

            $apiKey->recordUsage($request->ip());

            $request->fmUser   = $user;
            $request->fmApiKey = $apiKey;
            $request->fmMode   = 'api_key';
            Auth::setUser($user);

            return $this->handleCors($request, $next($request));
        }

        // ── 3. Coba Session / Web Auth ────────────────────
        // Coba session guard dulu (akses internal via browser)
        if (Auth::guard('web')->check()) {
            $request->fmUser   = Auth::guard('web')->user();
            $request->fmApiKey = null;
            $request->fmMode   = 'session';
            Auth::setUser($request->fmUser);
            return $this->handleCors($request, $next($request));
        }

        // ── 4. Tidak ada auth ──────────────────────────────
        return $this->unauthorized('Autentikasi diperlukan.');
    }

    // ── Private helpers ────────────────────────────────────

    protected function resolvePassportUser(string $token): ?Account
    {
        try {
            // Coba via guard 'api' dulu (jika config/auth.php sudah pointing ke Account)
            $guard = Auth::guard('api');
            $user  = $guard->user();
            if ($user instanceof Account) return $user;

            // Fallback: resolve manual via tabel oauth_access_tokens
            // Ini bekerja meskipun config/auth.php masih pointing ke User model lama
            $tokenRecord = \DB::table('oauth_access_tokens')
                ->where('id', $token)
                ->where('revoked', false)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->first();

            if (!$tokenRecord) return null;

            return Account::find($tokenRecord->user_id);
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function unauthorized(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 401)->withHeaders($this->corsHeaders());
    }

    /**
     * Tambahkan CORS headers ke response
     */
    protected function handleCors(Request $request, Response $response): Response
    {
        $origin = $request->header('Origin', '*');
        $apiKey = $request->fmApiKey ?? null;

        // Jika pakai API Key, cek allowed_origins untuk set header
        $allowOrigin = '*';
        if ($apiKey && !empty($apiKey->allowed_origins)) {
            $allowOrigin = in_array($origin, $apiKey->allowed_origins) ? $origin : $apiKey->allowed_origins[0];
        }

        return $response->withHeaders([
            'Access-Control-Allow-Origin'      => $allowOrigin,
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-FM-Key, X-Requested-With',
            'Access-Control-Allow-Credentials' => 'true',
            'X-Frame-Options'                  => 'ALLOWALL', // izinkan embed iframe
            'Content-Security-Policy'          => "frame-ancestors *", // izinkan iframe dari mana saja
        ]);
    }

    protected function corsHeaders(): array
    {
        return [
            'Access-Control-Allow-Origin'  => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-FM-Key, X-Requested-With',
        ];
    }
}
