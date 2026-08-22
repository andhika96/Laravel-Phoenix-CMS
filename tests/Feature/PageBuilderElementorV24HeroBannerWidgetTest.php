<?php

namespace Tests\Feature;

use Tests\Concerns\InteractsWithPageBuilderElementorV24Modules;
use Tests\TestCase;

class PageBuilderElementorV24HeroBannerWidgetTest extends TestCase
{
    use InteractsWithPageBuilderElementorV24Modules;
    public function test_hero_banner_is_registered_as_a_dedicated_pro_widget(): void
    {
        $module = $this->pageBuilderV24Module('hero_banner');

        $this->assertSame('Hero Banner', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertSame('fas fa-image', $module['icon'] ?? null);
        foreach (['definition', 'canvas', 'settings', 'view'] as $asset) {
            $this->assertFileExists($module['assets'][$asset] ?? 'missing');
        }
    }

    public function test_frontend_renderer_outputs_responsive_safe_actions_and_limits_buttons(): void
    {
        $module = $this->pageBuilderV24Module('hero_banner');
        $html = $this->pageBuilderV24ModuleView($module, [
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
        $module = $this->pageBuilderV24Module('hero_banner');
        $html = $this->pageBuilderV24ModuleView($module, [
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

    public function test_frontend_renderer_applies_center_anchor_geometry_from_percent_settings(): void
    {
        $module = $this->pageBuilderV24Module('hero_banner');
        $html = $this->pageBuilderV24ModuleView($module, [
            'node' => [
                'id' => 'hero-center-anchor',
                'type' => 'hero_banner',
                'settings' => [
                    'positioningMode' => 'grouped',
                    'groupAnchor' => 'center',
                    'groupX' => '50%',
                    'groupY' => '50%',
                    'groupWidth' => '70%',
                    'groupAlign' => 'center',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('left:50%;top:50%;width:70%;text-align:center;transform:translate(-50%,-50%);--hero-content-align:center', $html);
    }

    public function test_frontend_renderer_follows_group_alignment_until_button_override(): void
    {
        $module = $this->pageBuilderV24Module('hero_banner');
        $base = [
            'positioningMode' => 'grouped',
            'groupAlign' => 'center',
            'buttons' => [['text' => 'Watch', 'actionType' => 'link', 'linkUrl' => '/watch']],
        ];

        $inherited = $this->pageBuilderV24ModuleView($module, [
            'node' => ['id' => 'hero-follow-content', 'type' => 'hero_banner', 'settings' => $base],
        ])->render();

        $this->assertStringContainsString('justify-content:center;align-items:initial', $inherited);

        $overridden = $this->pageBuilderV24ModuleView($module, [
            'node' => [
                'id' => 'hero-button-override',
                'type' => 'hero_banner',
                'settings' => $base + ['buttonAlignMode' => 'custom', 'buttonAlign' => 'left'],
            ],
        ])->render();

        $this->assertStringContainsString('justify-content:flex-start;align-items:initial', $overridden);
    }

    public function test_frontend_renderer_outputs_natural_image_layout_with_its_image_element(): void
    {
        $module = $this->pageBuilderV24Module('hero_banner');
        $html = $this->pageBuilderV24ModuleView($module, [
            'node' => [
                'id' => 'hero-natural-image',
                'type' => 'hero_banner',
                'settings' => [
                    'imageUrl' => '/assets/hero-natural.webp',
                    'imageLayout' => 'natural',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('is-natural-image', $html);
        $this->assertStringContainsString('<picture class="pb-hero-banner__picture">', $html);
        $this->assertStringContainsString('src="/assets/hero-natural.webp"', $html);
    }
}
