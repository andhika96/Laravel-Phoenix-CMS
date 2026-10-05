<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class WorkflowAccess
{
    public static function allows($user, string $permission): bool
    {
        if (! $user) {
            return false;
        }
        if ($user->hasRole(['Super Admin', 'Administrator'])) {
            return true;
        }
        if (! Schema::hasTable('custom_permissions')) {
            return false;
        }
        return DB::table('custom_permissions')->whereIn('role_id', $user->roles->pluck('id'))->where('menu_link', 'workflow')
            ->get()->contains(fn ($row) => in_array($permission, json_decode($row->permissions ?? '[]', true) ?: [], true));
    }

    public function handle(Request $request, Closure $next, string $permission = 'read data'): Response
    {
        abort_unless(self::allows($request->user(), $permission), 403);
        return $next($request);
    }
}
