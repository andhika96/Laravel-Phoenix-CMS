<?php

namespace Tests\Feature;

use Tests\Concerns\InteractsWithPageBuilderElementorV24Modules;
use Tests\TestCase;

class PageBuilderElementorV24ImageBoxWidgetTest extends TestCase
{
    use InteractsWithPageBuilderElementorV24Modules;

    public function test_horizontal_and_responsive_image_box_widths_are_owned_by_the_media_track(): void
    {
        $html = $this->pageBuilderV24ModuleViewByType('image_box', [
            'node' => [
                'id' => 'image-box-width-contract',
                'settings' => [
                    'imageUrl' => '/vehicle.jpg',
                    'imageAlt' => 'Vehicle',
                    'title' => 'Vehicle title',
                    'description' => 'Vehicle description',
                    'imagePosition' => 'left',
                    'imagePositionTablet' => 'top',
                    'imagePositionMobile' => 'right',
                    'imageWidth' => '40%',
                    'imageWidthTablet' => '60%',
                    'imageWidthMobile' => '50%',
                    'alignment' => 'left',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('pb-image-box--position-left', $html);
        $this->assertMatchesRegularExpression('/pb-image-box__media" style="[^"]*width:40%;[^"]*flex:0 0 40%/', $html);
        $this->assertMatchesRegularExpression('/pb-image-box__image"[^>]*style="width:100%;max-width:100%/', $html);
        $this->assertStringContainsString('@media (max-width: 1024px)', $html);
        $this->assertStringContainsString('width:100%;flex:0 0 auto', $html);
        $this->assertStringContainsString('width:60%', $html);
        $this->assertStringContainsString('@media (max-width: 767px)', $html);
        $this->assertStringContainsString('width:50%;flex:0 0 50%', $html);
        $this->assertStringContainsString('.pb-image-box__image-link{display:flex;width:100%', $html);
    }
}
