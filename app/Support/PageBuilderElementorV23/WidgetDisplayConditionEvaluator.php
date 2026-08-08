<?php

namespace App\Support\PageBuilderElementorV23;

use DateTimeImmutable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

final class WidgetDisplayConditionEvaluator
{
    public function allows(array $groups, Request $request, ?Authenticatable $user): bool
    {
        if ($groups === []) {
            return true;
        }

        foreach ($groups as $group) {
            if (is_array($group) && $this->groupAllows($group, $request, $user)) {
                return true;
            }
        }

        return false;
    }

    private function groupAllows(array $group, Request $request, ?Authenticatable $user): bool
    {
        $rules = is_array($group['rules'] ?? null) ? $group['rules'] : [];
        if ($rules === []) {
            return false;
        }

        $evaluated = 0;
        foreach ($rules as $rule) {
            if (!is_array($rule)) {
                continue;
            }

            $result = $this->ruleAllows($rule, $request, $user);
            if ($result === null) {
                return false;
            }

            $evaluated++;
            if (!$result) {
                return false;
            }
        }

        return $evaluated > 0;
    }

    private function ruleAllows(array $rule, Request $request, ?Authenticatable $user): ?bool
    {
        $source = strtolower(trim((string) ($rule['source'] ?? '')));
        $effect = strtolower(trim((string) ($rule['effect'] ?? 'include')));
        $operator = strtolower(trim((string) ($rule['operator'] ?? 'is')));
        $expected = trim((string) ($rule['value'] ?? ''));

        if (!in_array($effect, ['include', 'exclude'], true)
            || !in_array($operator, ['is', 'is-not', 'contains'], true)
            || !in_array($source, ['page-id', 'page-slug', 'auth-state', 'user-role', 'date-range', 'device'], true)) {
            return null;
        }

        $matches = match ($source) {
            'page-id' => $this->compare($this->pageId($request), $expected, $operator),
            'page-slug' => $this->compare($this->pageSlug($request), $expected, $operator),
            'auth-state' => $this->compare($user ? 'authenticated' : 'guest', $expected, $operator),
            'user-role' => $this->roleMatches($user, $expected, $operator),
            'date-range' => $this->dateRangeMatches($request, $expected),
            'device' => $this->compare($this->deviceClass($request), $expected, $operator),
        };

        return $effect === 'exclude' ? !$matches : $matches;
    }

    private function compare(string $actual, string $expected, string $operator): bool
    {
        if ($expected === '') {
            return false;
        }

        $actual = mb_strtolower(trim($actual));
        $expected = mb_strtolower(trim($expected));

        return match ($operator) {
            'is-not' => $actual !== $expected,
            'contains' => str_contains($actual, $expected),
            default => $actual === $expected,
        };
    }

    private function pageId(Request $request): string
    {
        $value = $request->attributes->get('pagebuilder_page_id')
            ?? $request->route('page')
            ?? $request->route('id')
            ?? '';

        if (is_object($value) && isset($value->id)) {
            $value = $value->id;
        }

        return trim((string) $value);
    }

    private function pageSlug(Request $request): string
    {
        $value = $request->attributes->get('pagebuilder_page_slug')
            ?? $request->route('slug')
            ?? basename(trim($request->path(), '/'));

        return trim((string) $value);
    }

    private function roleMatches(?Authenticatable $user, string $expected, string $operator): bool
    {
        if (!$user || $expected === '') {
            return false;
        }

        $roles = [];
        if (method_exists($user, 'getRoleNames')) {
            $roles = collect($user->getRoleNames())->map(fn ($role) => mb_strtolower(trim((string) $role)))->all();
        } elseif (isset($user->role)) {
            $roles = [mb_strtolower(trim((string) $user->role))];
        }

        $expected = mb_strtolower($expected);
        $matched = $operator === 'contains'
            ? collect($roles)->contains(fn ($role) => str_contains($role, $expected))
            : in_array($expected, $roles, true);

        return $operator === 'is-not' ? !$matched : $matched;
    }

    private function dateRangeMatches(Request $request, string $value): bool
    {
        $parts = array_map('trim', explode('|', $value, 2));
        if (count($parts) !== 2 || $parts[0] === '' || $parts[1] === '') {
            return false;
        }

        try {
            $nowValue = $request->attributes->get('pagebuilder_now');
            $now = new DateTimeImmutable($nowValue ? (string) $nowValue : 'now');
            $start = new DateTimeImmutable($parts[0]);
            $end = new DateTimeImmutable($parts[1]);
        } catch (\Throwable) {
            return false;
        }

        return $start <= $end && $now >= $start && $now <= $end;
    }

    private function deviceClass(Request $request): string
    {
        $userAgent = mb_strtolower((string) $request->userAgent());
        if (preg_match('/ipad|tablet|kindle|silk/', $userAgent)) {
            return 'tablet';
        }
        if (preg_match('/mobile|iphone|ipod|android.*mobile|windows phone/', $userAgent)) {
            return 'mobile';
        }

        return 'desktop';
    }
}
