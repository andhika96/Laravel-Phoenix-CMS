<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * FmAdminMiddleware
 *
 * Cek apakah user yang sudah terautentikasi oleh FileManagerAuth
 * adalah admin. Middleware ini harus dipakai SETELAH FileManagerAuth.
 *
 * Logika admin check (sesuaikan dengan struktur user Arunika):
 *  - Cek field `role` di tabel users: 'admin' | 'superadmin'
 *  - Atau cek field `is_admin`
 *  - Atau cek via Spatie Permission jika dipakai
 *
 * Sesuaikan method isAdmin() di bawah dengan struktur DB Anda.
 */
class FmAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->fmUser ?? null;

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Autentikasi diperlukan.',
            ], 401);
        }

        if (!$this->isAdmin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat menggunakan fitur ini.',
            ], 403);
        }

        return $next($request);
    }

    /**
     * ─────────────────────────────────────────────────────
     *  SESUAIKAN dengan struktur user Arunika Anda.
     *
     *  Pilih salah satu cara (uncomment yang sesuai):
     * ─────────────────────────────────────────────────────
     */
    protected function isAdmin($user): bool
    {
        // ── Opsi A: field 'role' di tabel users ───────────
        // return in_array($user->role, ['admin', 'superadmin']);

        // ── Opsi B: field 'is_admin' (boolean) ───────────
        // return (bool) $user->is_admin;

        // ── Opsi C: Spatie Permission ─────────────────────
        // return $user->hasRole(['admin', 'superadmin']);

        // ── Opsi D: user id tertentu (untuk dev/testing) ──
        // return $user->id === 1;

        // ── DEFAULT: cek field 'role' ─────────────────────
        // Ganti sesuai nama field dan value di DB Anda
        if (isset($user->role)) {
            return in_array($user->role, ['admin', 'superadmin', 'super_admin']);
        }

        if (isset($user->is_admin)) {
            return (bool) $user->is_admin;
        }

        // Fallback: user pertama (id=1) dianggap admin
        return $user->id === 1;
    }
}
