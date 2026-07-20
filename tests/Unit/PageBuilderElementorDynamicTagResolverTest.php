<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementor\DynamicTagResolver;
use Tests\TestCase;

class PageBuilderElementorDynamicTagResolverTest extends TestCase
{
    public function test_it_resolves_only_allowlisted_local_context_values(): void
    {
        $resolver = app(DynamicTagResolver::class);
        $context = [
            'page' => [
                'page_name' => 'Documentation',
                'excerpt' => 'A concise page summary.',
                'featured_image' => '/storage/ckfinder/images/docs.jpg',
                'uri' => 'documentation',
            ],
            'page_url' => 'https://example.test/documentation',
            'site_title' => 'Phoenix CMS',
            'site_url' => 'https://example.test',
            'user' => (object) ['name' => 'Cahyo'],
        ];

        $cases = [
            'page_title' => 'Documentation',
            'page_excerpt' => 'A concise page summary.',
            'featured_image' => '/storage/ckfinder/images/docs.jpg',
            'page_url' => 'https://example.test/documentation',
            'site_title' => 'Phoenix CMS',
            'site_url' => 'https://example.test',
            'user_display_name' => 'Cahyo',
        ];

        foreach ($cases as $tag => $expected) {
            $this->assertSame($expected, $resolver->resolve('title', 'Static fallback', ['title' => $tag], $context));
        }

        $this->assertSame(array_keys($cases), array_keys($resolver->options()));
    }

    public function test_it_falls_back_for_missing_empty_unknown_or_non_scalar_values(): void
    {
        $resolver = app(DynamicTagResolver::class);

        $this->assertSame('Static fallback', $resolver->resolve('title', 'Static fallback', [], []));
        $this->assertSame('Static fallback', $resolver->resolve('title', 'Static fallback', ['title' => 'unknown_tag'], []));
        $this->assertSame('Static fallback', $resolver->resolve('title', 'Static fallback', ['title' => 'page_title'], ['page' => ['page_name' => '']]));
        $this->assertSame('Static fallback', $resolver->resolve('title', 'Static fallback', ['title' => 'page_title'], ['page' => ['page_name' => ['nested']]]));
        $this->assertSame('0', $resolver->resolve('title', 'Static fallback', ['title' => 'page_title'], ['page' => ['page_name' => 0]]));
    }

    public function test_it_never_interprets_templates_code_property_paths_or_callables(): void
    {
        $resolver = app(DynamicTagResolver::class);
        $dangerousBindings = [
            '{{ config("app.key") }}',
            '<?php phpinfo(); ?>',
            'page.secret.value',
            'system',
        ];

        foreach ($dangerousBindings as $binding) {
            $this->assertSame('Safe', $resolver->resolve('title', 'Safe', ['title' => $binding], [
                'system' => static fn () => 'unsafe',
                'page' => ['secret' => ['value' => 'unsafe']],
            ]));
        }
    }
}
