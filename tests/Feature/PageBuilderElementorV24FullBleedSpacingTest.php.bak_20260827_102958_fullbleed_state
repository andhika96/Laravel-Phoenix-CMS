<?php

namespace Tests\Feature;

use App\Support\PageBuilderElementorV24\WidgetAdvancedStyleResolver;
use Tests\Concerns\InteractsWithPageBuilderElementorV24Modules;
use Tests\TestCase;

class PageBuilderElementorV24FullBleedSpacingTest extends TestCase
{
    use InteractsWithPageBuilderElementorV24Modules;

    public function test_shared_resolver_preserves_custom_units_and_emits_full_bleed_width(): void
    {
        $result = app(WidgetAdvancedStyleResolver::class)->resolve([
            'fullBleed' => true,
            'paddingTop' => '2em',
            'paddingRight' => '3rem',
            'paddingBottom' => '4pt',
            'paddingLeft' => '5%',
        ], 'full-bleed-widget', request());

        $this->assertContains('pb-full-bleed', $result['classes']);
        $this->assertStringContainsString('padding-top:2em', $result['css']);
        $this->assertStringContainsString('padding-right:3rem', $result['css']);
        $this->assertStringContainsString('padding-bottom:4pt', $result['css']);
        $this->assertStringContainsString('padding-left:5%', $result['css']);
        $this->assertStringContainsString('width:100%', $result['css']);
        $this->assertStringContainsString('max-width:100%', $result['css']);
    }

    public function test_hero_slider_frontend_uses_shared_advanced_root_without_editor_gutter(): void
    {
        $html = $this->pageBuilderV24ModuleViewByType('hero_slider', [
            'node' => [
                'id' => 'hero-slider-full-bleed',
                'type' => 'hero_slider',
                'settings' => [
                    'fullBleed' => true,
                    'paddingTop' => '2em',
                    'paddingRight' => '3rem',
                    'paddingBottom' => '4pt',
                    'paddingLeft' => '5%',
                    'slides' => [
                        ['mediaType' => 'image', 'imageUrl' => '/assets/hero.webp'],
                    ],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('class="pb-hero-slider pb-advanced-widget pb-full-bleed', $html);
        $this->assertStringContainsString('padding-top:2em', $html);
        $this->assertStringContainsString('padding-right:3rem', $html);
        $this->assertStringContainsString('padding-bottom:4pt', $html);
        $this->assertStringContainsString('padding-left:5%', $html);
        $this->assertStringContainsString('width:100%', $html);
        $this->assertStringNotContainsString('pb-preview', $html);
    }
}

