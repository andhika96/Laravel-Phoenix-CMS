<?php

namespace Tests\Feature;

use Tests\Concerns\InteractsWithPageBuilderElementorV24Modules;
use Tests\TestCase;

class PageBuilderElementorV24ProductColorSelectorWidgetTest extends TestCase
{
    use InteractsWithPageBuilderElementorV24Modules;
    public function test_product_color_selector_is_registered_as_a_pro_widget_with_its_definition(): void
    {
        $module = $this->pageBuilderV24Module('product_color_selector');

        $this->assertIsArray($module);
        $this->assertSame('product_color_selector', $module['type'] ?? null);
        $this->assertSame('Product Color Selector', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertSame('fas fa-palette', $module['icon'] ?? null);
        $this->assertFileExists($module['assets']['definition'] ?? 'missing');
        $this->assertFileExists($module['assets']['canvas'] ?? 'missing');
        $this->assertFileExists($module['assets']['settings'] ?? 'missing');
        $this->assertFileExists($module['assets']['view'] ?? 'missing');
    }

    public function test_product_color_selector_renders_responsive_media_and_safe_fallbacks(): void
    {
        $html = $this->pageBuilderV24ModuleViewByType('product_color_selector', [
            'node' => [
                'id' => 'pcs-render-test',
                'type' => 'product_color_selector',
                'settings' => [
                    'title' => 'Choose <script>alert(1)</script>',
                    'listPosition' => 'bottom',
                    'listPositionTablet' => 'right',
                    'items' => [
                        [
                            'id' => 'black',
                            'name' => 'Black',
                            'description' => 'Short description',
                            'swatchColor' => '#111827',
                            'imageUrl' => '/storage/black.webp',
                            'imageUrlTablet' => '/storage/black-tablet.webp',
                            'imageUrlMobile' => 'javascript:alert(1)',
                        ],
                    ],
                    'defaultItemId' => 'black',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('data-product-color-selector', $html);
        $this->assertStringContainsString('data-product-color-panel', $html);
        $this->assertStringContainsString('/storage/black-tablet.webp', $html);
        $this->assertStringNotContainsString('javascript:alert(1)', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
    }
}
