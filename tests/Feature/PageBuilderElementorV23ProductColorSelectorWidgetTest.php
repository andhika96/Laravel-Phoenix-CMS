<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23ProductColorSelectorWidgetTest extends TestCase
{
    public function test_product_color_selector_is_registered_as_a_pro_widget_with_its_definition(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.product_color_selector');

        $this->assertIsArray($module);
        $this->assertSame('product_color_selector', $module['type'] ?? null);
        $this->assertSame('Product Color Selector', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertSame('fas fa-palette', $module['icon'] ?? null);
        $this->assertSame('pagebuilder_elementor_v23.widgets.pro.product-color-selector', $module['view'] ?? null);
        $this->assertFileExists(public_path($module['definition'] ?? 'missing'));
        $this->assertFileExists(public_path($module['canvas'] ?? 'missing'));
        $this->assertFileExists(public_path($module['settings'] ?? 'missing'));
        $this->assertTrue(view()->exists($module['view'] ?? 'missing'));
    }

    public function test_product_color_selector_renders_responsive_media_and_safe_fallbacks(): void
    {
        $html = view('pagebuilder_elementor_v23.widgets.pro.product-color-selector', [
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
