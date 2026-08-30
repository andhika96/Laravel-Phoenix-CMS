<?php

namespace Tests\Feature;

use Tests\Concerns\InteractsWithPageBuilderElementorV24Modules;
use Tests\TestCase;

class PageBuilderElementorV24EventMediaGridWidgetTest extends TestCase
{
    use InteractsWithPageBuilderElementorV24Modules;

    public function test_event_media_grid_is_registered_as_a_general_widget(): void
    {
        $module = $this->pageBuilderV24Module('event_media_grid');

        $this->assertSame('event_media_grid', $module['type'] ?? null);
        $this->assertSame('Event Media Grid', $module['label'] ?? null);
        $this->assertSame('general', $module['category'] ?? null);
        $this->assertSame(57, $module['order'] ?? null);
        $this->assertSame('widget', $module['advanced']['profile'] ?? null);
        $this->assertContains('pro-icon-targets', $module['capabilities'] ?? []);

        foreach (['definition', 'canvas', 'settings', 'view'] as $asset) {
            $this->assertFileExists($module['assets'][$asset] ?? 'missing');
        }
    }

    public function test_frontend_renders_six_default_cards_with_semantic_media_and_footer(): void
    {
        $html = $this->pageBuilderV24ModuleViewByType('event_media_grid', [
            'node' => ['id' => 'event-media-grid-default', 'type' => 'event_media_grid', 'settings' => []],
        ])->render();

        $this->assertStringContainsString('data-event-media-grid', $html);
        $this->assertSame(6, preg_match_all('/class="event-media-grid__card(?:\s|")/', $html, $matches));
        $this->assertStringContainsString('event-media-grid__image-placeholder', $html);
        $this->assertStringContainsString('fal fa-users', $html);
        $this->assertStringContainsString('Pairs Scramble', $html);
        $this->assertStringContainsString('Qualification', $html);
        $this->assertStringContainsString('Novelty Awards', $html);
        $this->assertStringContainsString('Prize information is subject to final event terms and conditions.', $html);
        $this->assertStringContainsString('#091d31', $html);
        $this->assertStringContainsString('#0a1e33', $html);
        $this->assertStringContainsString('border-color:#3a413f', $html);
        $this->assertStringContainsString('max-width:1636px', $html);
        $this->assertStringContainsString('grid-template-columns:repeat(3,minmax(0,1fr))', $html);
        $this->assertStringContainsString('min-height:472px', $html);
        $this->assertStringContainsString('gap:32px', $html);
        $this->assertTrue(strpos($html, 'event-media-grid__cards') < strpos($html, 'event-media-grid__footer'));
    }

    public function test_frontend_supports_image_modes_icon_toggle_surfaces_and_responsive_columns(): void
    {
        $html = $this->pageBuilderV24ModuleViewByType('event_media_grid', [
            'node' => [
                'id' => 'event-media-grid-modes',
                'type' => 'event_media_grid',
                'settings' => [
                    'gridColumns' => '12',
                    'gridColumnsTablet' => '5',
                    'gridColumnsMobile' => '1',
                    'gridContentWidthMode' => 'full',
                    'footerPosition' => 'top',
                    'footerAlign' => 'center',
                    'cards' => [
                        ['id' => 'element', 'imageUrl' => 'https://example.com/element.webp', 'imageAlt' => 'Element image', 'imagePresentation' => 'element', 'showIcon' => false, 'title' => 'Element card', 'description' => 'Element description'],
                        ['id' => 'background', 'imageUrl' => 'https://example.com/background.webp', 'imageAlt' => 'Background image', 'imagePresentation' => 'background', 'surface' => 'light', 'title' => 'Background card', 'description' => 'Background description'],
                        ['id' => 'custom', 'showImage' => false, 'showIcon' => true, 'surface' => 'custom', 'customBackgroundColor' => '#112233', 'customBorderColor' => '#d8ad5e', 'title' => 'Custom card', 'description' => 'Custom description'],
                    ],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('src="https://example.com/element.webp"', $html);
        $this->assertStringContainsString('alt="Element image"', $html);
        $this->assertStringContainsString('background-image:url(&quot;https://example.com/background.webp&quot;)', $html);
        $this->assertStringContainsString('surface-light', $html);
        $this->assertStringContainsString('surface-custom', $html);
        $this->assertStringContainsString('max-width:100%', $html);
        $this->assertStringContainsString('@media (max-width: 1024px)', $html);
        $this->assertStringContainsString('grid-template-columns:repeat(5,minmax(0,1fr))', $html);
        $this->assertStringContainsString('@media (max-width: 767px)', $html);
        $this->assertStringContainsString('grid-template-columns:repeat(1,minmax(0,1fr))', $html);
        $this->assertTrue(strpos($html, 'event-media-grid__footer') < strpos($html, 'event-media-grid__cards'));
        $this->assertStringNotContainsString('event-media-grid__icon-glyph fal fa-users', $html);
    }

    public function test_frontend_escapes_content_and_rejects_unsafe_image_and_svg(): void
    {
        $html = $this->pageBuilderV24ModuleViewByType('event_media_grid', [
            'node' => [
                'id' => 'event-media-grid-safe',
                'type' => 'event_media_grid',
                'settings' => [
                    'footerText' => '<script>alert(1)</script>',
                    'cards' => [[
                        'id' => 'unsafe',
                        'imageUrl' => 'javascript:alert(2)',
                        'iconSource' => 'svg',
                        'iconSvg' => '<svg><script>alert(3)</script></svg>',
                        'title' => '<img src=x onerror=alert(4)>',
                        'description' => '<b>Unsafe</b>',
                    ]],
                ],
            ],
        ])->render();

        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringContainsString('&lt;img src=x onerror=alert(4)&gt;', $html);
        $this->assertStringContainsString('&lt;b&gt;Unsafe&lt;/b&gt;', $html);
    }
}
