<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23MediaCarouselWidgetTest extends TestCase
{
    public function test_media_carousel_is_registered_and_frontend_renderer_outputs_safe_interactive_markup(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.media_carousel');

        $this->assertSame('Media Carousel', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertSame('pagebuilder_elementor_v23.partials.render_pro_widget', $module['view'] ?? null);
        $this->assertFileExists(public_path($module['definition'] ?? 'missing'));

        $html = view($module['view'], [
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
                    'arrows' => true,
                    'pagination' => 'dots',
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
        $this->assertStringContainsString('data-pro-media-lightbox', $html);
        $this->assertStringContainsString('href="/media-one"', $html);
        $this->assertStringContainsString('rel="noopener noreferrer nofollow"', $html);
        $this->assertStringContainsString('data-media="one"', $html);
        $this->assertStringNotContainsString('onclick=', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }
}
