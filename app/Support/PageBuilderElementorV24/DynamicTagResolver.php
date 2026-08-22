<?php

namespace App\Support\PageBuilderElementorV24;

final class DynamicTagResolver
{
    private const OPTIONS = [
        'page_title' => 'Page Title',
        'page_excerpt' => 'Page Excerpt',
        'featured_image' => 'Featured Image',
        'page_url' => 'Page URL',
        'site_title' => 'Site Title',
        'site_url' => 'Site URL',
        'user_display_name' => 'User Display Name',
    ];

    public function options(): array
    {
        return self::OPTIONS;
    }

    public function resolve(string $field, mixed $fallback, array $bindings, array $context): mixed
    {
        $binding = $bindings[$field] ?? null;

        if (! is_string($binding) || ! array_key_exists($binding, self::OPTIONS)) {
            return $fallback;
        }

        $value = match ($binding) {
            'page_title' => $this->first($context, ['page.page_name', 'page.title']),
            'page_excerpt' => $this->first($context, ['page_excerpt', 'page.excerpt', 'page.summary']),
            'featured_image' => $this->first($context, ['featured_image', 'page.featured_image', 'page.cover_image']),
            'page_url' => $this->pageUrl($context),
            'site_title' => $this->first($context, ['site_title']) ?? config('app.name'),
            'site_url' => $this->first($context, ['site_url']) ?? config('app.url'),
            'user_display_name' => $this->first($context, ['user.name', 'user.display_name', 'user.username']),
        };

        return $this->scalarOrFallback($value, $fallback);
    }

    private function pageUrl(array $context): mixed
    {
        $explicit = $this->first($context, ['page_url', 'page.url']);
        if ($explicit !== null) {
            return $explicit;
        }

        $uri = $this->first($context, ['page.uri']);
        if (! is_scalar($uri) || trim((string) $uri) === '') {
            return null;
        }

        return url('/'.ltrim((string) $uri, '/'));
    }

    private function first(array $context, array $paths): mixed
    {
        foreach ($paths as $path) {
            $value = data_get($context, $path);
            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    private function scalarOrFallback(mixed $value, mixed $fallback): mixed
    {
        if (! is_scalar($value)) {
            return $fallback;
        }

        $normalized = (string) $value;

        return trim($normalized) === '' ? $fallback : $normalized;
    }
}
