<?php

namespace Tests\Feature;

use Tests\Concerns\InteractsWithPageBuilderElementorV24Modules;
use Tests\TestCase;

class PageBuilderElementorV24ImageWidgetParityTest extends TestCase
{
    use InteractsWithPageBuilderElementorV24Modules;
    public function test_frontend_image_renderer_outputs_content_style_responsive_and_advanced_contract(): void
    {
        $html = $this->pageBuilderV24ModuleViewByType('image', [
            'node' => [
                'id' => 'image-contract',
                'settings' => [
                    'src' => 'https://cdn.example.com/image.jpg',
                    'alt' => 'Example image',
                    'imageResolution' => 'full',
                    'captionType' => 'custom',
                    'customCaption' => 'Frontend caption',
                    'linkType' => 'custom',
                    'customLinkUrl' => '/models',
                    'linkTarget' => '_blank',
                    'linkNofollow' => true,
                    'linkCustomAttributes' => [
                        ['key' => 'data-track', 'value' => 'image'],
                        ['key' => 'onclick', 'value' => 'alert(1)'],
                    ],
                    'alignment' => 'right',
                    'width' => '75%',
                    'widthTablet' => '80%',
                    'widthMobile' => '100%',
                    'maxWidth' => '90%',
                    'height' => '320px',
                    'heightTablet' => '240px',
                    'objectFit' => 'cover',
                    'objectPosition' => 'top right',
                    'imageBorderType' => 'solid',
                    'imageBorderWidth' => '2px',
                    'imageBorderColor' => '#112233',
                    'imageBorderRadius' => '8px',
                    'captionAlignment' => 'center',
                    'captionColor' => '#123456',
                    'captionBackgroundColor' => '#ffffff',
                    'captionSpacing' => '12px',
                    'cssClass' => 'featured-image',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('id="pb-node-image-contract"', $html);
		$this->assertStringContainsString('class="el-widget-image pb-image', $html);
		$this->assertStringContainsString('featured-image', $html);
        $this->assertStringContainsString('href="/models"', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="noopener noreferrer nofollow"', $html);
        $this->assertStringContainsString('data-track="image"', $html);
        $this->assertStringNotContainsString('onclick=', $html);
        $this->assertStringContainsString('Frontend caption', $html);
        $this->assertStringContainsString('object-fit:cover', $html);
        $this->assertStringContainsString('object-position:top right', $html);
        $this->assertStringContainsString('@media (max-width: 1024px)', $html);
        $this->assertStringContainsString('@media (max-width: 767px)', $html);
    }

    public function test_frontend_image_renderer_rejects_unsafe_image_and_link_urls(): void
    {
        $html = $this->pageBuilderV24ModuleViewByType('image', [
            'node' => [
                'id' => 'unsafe-image',
                'settings' => [
                    'src' => 'javascript:alert(1)',
                    'linkType' => 'custom',
                    'customLinkUrl' => 'javascript:alert(2)',
                    'captionType' => 'none',
                ],
            ],
        ])->render();

        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('<a ', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }
}
