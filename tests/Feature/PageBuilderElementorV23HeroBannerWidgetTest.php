<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23HeroBannerWidgetTest extends TestCase
{
    public function test_hero_banner_is_registered_as_a_dedicated_pro_widget(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.hero_banner');

        $this->assertSame('Hero Banner', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertSame('fas fa-image', $module['icon'] ?? null);
        $this->assertSame('js/pagebuilder_elementor_v23/widgets/pro/hero-banner/Canvas.vue', $module['canvas'] ?? null);
        $this->assertSame('js/pagebuilder_elementor_v23/widgets/pro/hero-banner/Settings.vue', $module['settings'] ?? null);
        $this->assertSame('pagebuilder_elementor_v23.widgets.pro.hero-banner', $module['view'] ?? null);
    }

    public function test_frontend_renderer_outputs_responsive_safe_actions_and_limits_buttons(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.hero_banner');
        $html = view($module['view'], [
            'node' => [
                'id' => 'hero-test',
                'type' => 'hero_banner',
                'settings' => [
                    'title' => '<Unsafe title>',
                    'subtitle' => 'Light Up Desire',
                    'imageUrl' => '/assets/hero-desktop.webp',
                    'imageUrlTablet' => '/assets/hero-tablet.webp',
                    'imageUrlMobile' => 'javascript:alert(1)',
                    'contentOrder' => ['subtitle', 'title', 'buttons'],
                    'buttons' => [
                        ['id' => 'one', 'text' => 'Details', 'actionType' => 'link', 'linkUrl' => '/details', 'linkTarget' => '_blank', 'linkNofollow' => true, 'linkCustomAttributes' => [['key' => 'data-track', 'value' => 'hero']]],
                        ['id' => 'two', 'text' => 'Watch', 'actionType' => 'video_popup', 'videoSource' => 'youtube', 'videoUrl' => 'https://www.youtube.com/watch?v=h529sg3pEV4'],
                        ['id' => 'three', 'text' => 'Gallery', 'actionType' => 'image_popup', 'imageUrl' => '/assets/popup.webp', 'imageAlt' => 'Popup asset'],
                        ['id' => 'four', 'text' => 'Must not render', 'actionType' => 'link', 'linkUrl' => '/four'],
                    ],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('data-hero-banner', $html);
        $this->assertStringContainsString('&lt;Unsafe title&gt;', $html);
        $this->assertStringContainsString('href="/details"', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="noopener noreferrer nofollow"', $html);
        $this->assertStringContainsString('data-track="hero"', $html);
        $this->assertStringContainsString('data-hero-media', $html);
        $this->assertStringContainsString('https://www.youtube.com/embed/h529sg3pEV4', $html);
        $this->assertStringContainsString('/assets/popup.webp', $html);
        $this->assertStringContainsString('@media(max-width:1024px)', $html);
        $this->assertStringContainsString('@media(max-width:767px)', $html);
        $this->assertStringNotContainsString('javascript:', $html);
        $this->assertStringNotContainsString('Must not render', $html);
    }

    public function test_frontend_renderer_supports_dailymotion_and_self_hosted_video_sources(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.hero_banner');
        $html = view($module['view'], [
            'node' => [
                'id' => 'hero-video-sources',
                'type' => 'hero_banner',
                'settings' => [
                    'buttons' => [
                        ['text' => 'Dailymotion', 'actionType' => 'video_popup', 'videoSource' => 'dailymotion', 'videoUrl' => 'https://www.dailymotion.com/video/x9demo'],
                        ['text' => 'Hosted', 'actionType' => 'video_popup', 'videoSource' => 'self_hosted', 'videoUrl' => '/media/hero.webm'],
                    ],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('https://www.dailymotion.com/embed/video/x9demo', $html);
        $this->assertStringContainsString('data-media-src="/media/hero.webm"', $html);
    }
}
