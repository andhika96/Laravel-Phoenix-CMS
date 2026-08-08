<?php

namespace App\Support\PageBuilderElementorV23;

use Closure;
use Illuminate\Support\Facades\Cache;
use Throwable;

final class WidgetFragmentCache
{
    private const RENDERER_VERSION = 'accordion-v1';

    public function remember(array $node, array $context, Closure $render): string
    {
        if (($node['settings']['cacheMode'] ?? 'default') !== 'active') {
            return (string) $render();
        }

        try {
            return (string) Cache::remember($this->key($node, $context), $this->ttl(), $render);
        } catch (Throwable) {
            return (string) $render();
        }
    }

    public function key(array $node, array $context): string
    {
        $payload = [
            'renderer' => self::RENDERER_VERSION,
            'node' => $node,
            'locale' => app()->getLocale(),
            'page_id' => $context['page_id'] ?? null,
            'page_slug' => $context['page_slug'] ?? null,
            'auth' => $context['auth'] ?? 'guest',
            'roles' => array_values(array_map('strval', is_array($context['roles'] ?? null) ? $context['roles'] : [])),
        ];

        return 'pagebuilder_elementor:widget:' . hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: serialize($payload));
    }

    private function ttl(): int
    {
        $configured = (int) config('pagebuilder_elementor.fragment_cache_ttl', 3600);

        return max(60, min(86400, $configured));
    }
}
