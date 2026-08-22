<?php

namespace Tests\Feature;

use Tests\Concerns\InteractsWithPageBuilderElementorV24Modules;
use Tests\TestCase;

class PageBuilderElementorV24SlidesWidgetTest extends TestCase
{
    use InteractsWithPageBuilderElementorV24Modules;

    public function test_slides_name_labels_the_frontend_carousel(): void
    {
        $html = $this->pageBuilderV24ModuleViewByType('slides', [
            'node' => [
                'id' => 'slides-name-contract',
                'settings' => [
                    'slidesName' => 'Launch & Learn',
                    'slides' => [
                        ['id' => 'slide-one', 'title' => 'One'],
                    ],
                    'navigation' => 'none',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('aria-label="Launch &amp; Learn"', $html);
    }
}
