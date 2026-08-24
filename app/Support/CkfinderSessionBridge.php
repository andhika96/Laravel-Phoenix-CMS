<?php

namespace App\Support;

use Illuminate\Http\Request;

final class CkfinderSessionBridge
{
    private const SESSION_KEYS = [
        'CKFinder_UserId',
        'CKFinder_UserRole_UUID',
        'CKFinder_UserRole',
        'CKFinder_AuthExpiresAt',
    ];

    private const ROLE_MAP = [
        'superadmin' => 'Super Admin',
        'administrator' => 'Administrator',
        'admin' => 'Administrator',
        'generalmember' => 'General Member',
    ];

    public function prepare(Request $request): bool
    {
        $user = $request->user();
        if (! $user) {
            $this->clear($request);

            return false;
        }

        $role = method_exists($user, 'getRoleNames')
            ? $user->getRoleNames()->first()
            : null;
        $role = $this->normalizeRole($role);

        $userId = trim((string) $user->getAuthIdentifier());
        $uuid = (string) ($user->uuid ?? '');
        $scope = $uuid === '' ? $userId : $uuid;

        if ($role === null || $userId === '' || preg_match('/\A[A-Za-z0-9_-]+\z/D', $scope) !== 1) {
            $this->clear($request);

            return false;
        }

        if (! $this->start($request)) {
            return false;
        }

        $hasCurrentIdentity = isset(
            $_SESSION['CKFinder_UserId'],
            $_SESSION['CKFinder_UserRole_UUID'],
            $_SESSION['CKFinder_UserRole'],
            $_SESSION['CKFinder_AuthExpiresAt'],
        )
            && is_scalar($_SESSION['CKFinder_UserId'])
            && is_scalar($_SESSION['CKFinder_UserRole_UUID'])
            && is_scalar($_SESSION['CKFinder_UserRole'])
            && hash_equals((string) $_SESSION['CKFinder_UserId'], $userId)
            && hash_equals((string) $_SESSION['CKFinder_UserRole_UUID'], $scope)
            && hash_equals((string) $_SESSION['CKFinder_UserRole'], $role)
            && is_int($_SESSION['CKFinder_AuthExpiresAt'])
            && $_SESSION['CKFinder_AuthExpiresAt'] >= time();

        if (! $hasCurrentIdentity && ! @session_regenerate_id(true)) {
            foreach (self::SESSION_KEYS as $key) {
                unset($_SESSION[$key]);
            }
            session_write_close();

            return false;
        }

        $_SESSION['CKFinder_UserId'] = $userId;
        $_SESSION['CKFinder_UserRole_UUID'] = $scope;
        $_SESSION['CKFinder_UserRole'] = $role;
        $_SESSION['CKFinder_AuthExpiresAt'] = time() + max(60, (int) config('session.lifetime', 120) * 60);

        session_write_close();

        return true;
    }

    public function clear(?Request $request = null): void
    {
        if (! $this->start($request)) {
            return;
        }

        foreach (self::SESSION_KEYS as $key) {
            unset($_SESSION[$key]);
        }

        @session_regenerate_id(true);
        session_write_close();
    }

    public function normalizeRole(mixed $role): ?string
    {
        $normalized = preg_replace('/[^a-z]/', '', strtolower(trim(is_scalar($role) ? (string) $role : ''))) ?? '';

        return self::ROLE_MAP[$normalized] ?? null;
    }

    private function start(?Request $request): bool
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return true;
        }

        $secure = $request?->isSecure() ?? false;
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        return @session_start();
    }
}
