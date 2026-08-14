<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23HeroSliderWidgetTest extends TestCase
{
    public function test_hero_slider_is_registered_as_a_dedicated_pro_widget(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.hero_slider');

        $this->assertSame('Hero Slider', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertSame('fas fa-photo-video', $module['icon'] ?? null);
        $this->assertSame('js/pagebuilder_elementor_v23/widgets/pro/hero-slider/Canvas.vue', $module['canvas'] ?? null);
        $this->assertSame('js/pagebuilder_elementor_v23/widgets/pro/hero-slider/Settings.vue', $module['settings'] ?? null);
        $this->assertSame('pagebuilder_elementor_v23.widgets.pro.hero-slider', $module['view'] ?? null);
    }

    public function test_frontend_renderer_outputs_mixed_media_and_responsive_runtime_config(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.hero_slider');
        $html = view($module['view'], [
            'node' => [
                'id' => 'hero-slider-test',
                'type' => 'hero_slider',
                'settings' => [
                    'direction' => 'vertical',
                    'directionTablet' => 'horizontal',
                    'directionMobile' => 'vertical',
                    'autoplay' => true,
                    'videoAutoplay' => true,
                    'videoDurationMode' => 'duration',
                    'heightMode' => 'adaptive',
                    'slides' => [
                        [
                            'id' => 'image-slide',
                            'mediaType' => 'image',
                            'imageUrl' => '/assets/hero.webp',
                            'imageUrlTablet' => '/assets/hero-tablet.webp',
                            'imageUrlMobile' => '/assets/hero-mobile.webp',
                            'imageAlt' => '<unsafe alt>',
                            'title' => '<Unsafe title>',
                        ],
                        [
                            'id' => 'video-slide',
                            'mediaType' => 'video',
                            'videoProvider' => 'youtube',
                            'videoUrl' => 'https://www.youtube.com/watch?v=h529sg3pEV4',
                            'videoPoster' => '/assets/poster.webp',
                            'videoAutoplay' => 'on',
                        ],
                        [
                            'id' => 'vimeo-slide',
                            'mediaType' => 'video',
                            'videoProvider' => 'vimeo',
                            'videoUrl' => 'https://vimeo.com/235215203',
                        ],
                        [
                            'id' => 'dailymotion-slide',
                            'mediaType' => 'video',
                            'videoProvider' => 'dailymotion',
                            'videoUrl' => 'https://www.dailymotion.com/video/x9demo',
                        ],
                    ],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('data-hero-slider', $html);
        $this->assertStringContainsString('data-hero-slider-config', $html);
        $this->assertStringContainsString('data-hero-slide', $html);
        $this->assertStringContainsString('data-hero-video', $html);
        $this->assertStringContainsString('https://www.youtube.com/embed/h529sg3pEV4', $html);
        $this->assertStringContainsString('player.vimeo.com/video/235215203', $html);
        $this->assertStringContainsString('https://www.dailymotion.com/embed/video/x9demo', $html);
        $this->assertStringContainsString('/assets/poster.webp', $html);
        $this->assertStringContainsString('/assets/hero-tablet.webp', $html);
        $this->assertStringContainsString('/assets/hero-mobile.webp', $html);
        $this->assertStringContainsString('adaptive', $html);
        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringNotContainsString('<Unsafe title>', $html);
    }

    public function test_frontend_renderer_keeps_generic_embed_as_safe_interval_fallback(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.hero_slider');
        $html = view($module['view'], [
            'node' => [
                'id' => 'hero-slider-embed',
                'type' => 'hero_slider',
                'settings' => [
                    'videoDurationMode' => 'duration',
                    'videoAutoplayFallback' => 'interval',
                    'slides' => [[
                        'mediaType' => 'video',
                        'videoProvider' => 'unknown-provider',
                        'videoUrl' => 'https://example.com/embed/video-1',
                    ]],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('data-video-provider="embed"', $html);
        $this->assertStringContainsString('https://example.com/embed/video-1', $html);
        $this->assertStringContainsString('data-video-duration-supported="false"', $html);
    }
}
