<?php

namespace Tests\Feature;

use Tests\Concerns\InteractsWithPageBuilderElementorV24Modules;
use Tests\TestCase;

class PageBuilderElementorV24MediaCarouselWidgetTest extends TestCase
{
    use InteractsWithPageBuilderElementorV24Modules;
    public function test_media_carousel_is_registered_and_frontend_renderer_outputs_safe_interactive_markup(): void
    {
        $module = $this->pageBuilderV24Module('media_carousel');

        $this->assertSame('Media Carousel', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertFileExists($module['assets']['view'] ?? 'missing');
        $this->assertFileExists($module['assets']['definition'] ?? 'missing');

        $html = $this->pageBuilderV24ModuleView($module, [
            'node' => [
                'id' => 'media-carousel-test',
                'type' => 'media_carousel',
                'settings' => [
                    'skin' => 'slideshow',
                    'slidesName' => 'Media Slides',
                    'slidesToShow' => 1,
                    'slidesToShowTablet' => 1,
                    'slidesToShowMobile' => 1,
                    'slidesToScroll' => 1,
                    'thumbsSlidesToShow' => 2,
                    'thumbsSlidesToShowTablet' => 4,
                    'thumbsSlidesToShowMobile' => 3,
                    'thumbsRatio' => '1:1',
                    'centeredSlides' => true,
                    'arrows' => true,
                    'pagination' => 'dots',
                    'previousArrowIcon' => 'fas fa-angle-left',
                    'nextArrowIcon' => 'fas fa-angle-right',
                    'arrowPosition' => 'outside',
                    'arrowEdgeOffset' => '12px',
                    'arrowButtonSize' => '44px',
                    'arrowIconSize' => '19px',
                    'arrowColor' => '#112233',
                    'arrowBackground' => '#ddeeff',
                    'arrowHoverColor' => '#ffffff',
                    'arrowHoverBackground' => '#334455',
                    'arrowRadiusTop' => '4px',
                    'arrowRadiusRight' => '8px',
                    'arrowRadiusBottom' => '12px',
                    'arrowRadiusLeft' => '16px',
                    'autoplay' => true,
                    'overlay' => 'text',
                    'captionSource' => 'title',
                    'items' => [
                        [
                            'id' => 'media-1',
                            'type' => 'image',
                            'imageUrl' => '/storage/media-one.jpg',
                            'title' => 'Media One',
                            'linkType' => 'custom',
                            'linkUrl' => '/media-one',
                            'linkTarget' => '_blank',
                            'linkNofollow' => true,
                            'linkCustomAttributes' => [
                                ['key' => 'data-media', 'value' => 'one'],
                                ['key' => 'onclick', 'value' => 'alert(1)'],
                            ],
                        ],
                        [
                            'id' => 'media-2',
                            'type' => 'video',
                            'imageUrl' => '/storage/media-two.jpg',
                            'videoUrl' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                            'title' => 'Media Two',
                        ],
                    ],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('data-pro-widget="media_carousel"', $html);
        $this->assertStringContainsString('data-pro-carousel', $html);
        $this->assertStringContainsString('pb-pro-media-carousel--slideshow', $html);
        $this->assertStringContainsString('pb-pro-media-carousel__thumbnail', $html);
        $this->assertStringContainsString('pb-pro-media-carousel__thumbnail-track', $html);
        $this->assertStringContainsString('pb-pro-media-carousel__thumbnails is-centered', $html);
        $this->assertStringContainsString('--media-thumbs-ratio:1 / 1', $html);
        $this->assertStringContainsString('--media-thumbs-per-view:2!important', $html);
        $this->assertStringContainsString('--media-thumbs-per-view:4!important', $html);
        $this->assertStringContainsString('--media-thumbs-per-view:3!important', $html);
        $this->assertStringContainsString('data-pro-media-lightbox', $html);
        $this->assertStringContainsString('href="/media-one"', $html);
        $this->assertStringContainsString('rel="noopener noreferrer nofollow"', $html);
        $this->assertStringContainsString('data-media="one"', $html);
        $this->assertStringNotContainsString('onclick=', $html);
        $this->assertStringNotContainsString('javascript:', $html);
        foreach ([
            '--carousel-arrow-button-size:44px',
            '--carousel-arrow-icon-size:19px',
            '--carousel-arrow-edge-position:calc(0px - 44px - 12px)',
            '--carousel-arrow-color:#112233',
            '--carousel-arrow-background:#ddeeff',
            '--carousel-arrow-hover-color:#ffffff',
            '--carousel-arrow-hover-background:#334455',
            '--carousel-arrow-radius:4px 8px 12px 16px',
            'fas fa-angle-left',
            'fas fa-angle-right',
        ] as $expected) {
            $this->assertStringContainsString($expected, $html);
        }
    }

    public function test_frontend_renderer_preserves_legacy_arrow_size_for_old_saved_nodes(): void
    {
        $module = $this->pageBuilderV24Module('media_carousel');
        $html = $this->pageBuilderV24ModuleView($module, [
            'node' => [
                'id' => 'legacy-media-carousel-arrow-test',
                'type' => 'media_carousel',
                'settings' => [
                    'slidesToShow' => 1,
                    'arrows' => true,
                    'arrowsSize' => '34px',
                    'items' => [
                        ['id' => 'one', 'type' => 'image', 'imageUrl' => '/storage/one.jpg'],
                        ['id' => 'two', 'type' => 'image', 'imageUrl' => '/storage/two.jpg'],
                    ],
                ],
            ],
        ])->render();

        $this->assertStringContainsString('--carousel-arrow-button-size:34px', $html);
        $this->assertStringContainsString('--carousel-arrow-icon-size:16px', $html);
    }
}
