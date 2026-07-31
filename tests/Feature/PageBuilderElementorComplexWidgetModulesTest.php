<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageBuilderElementorComplexWidgetModulesTest extends TestCase
{
    #[DataProvider('widgetProvider')]
    public function test_complex_widget_has_its_own_definition_canvas_settings_and_view(string $type, string $label): void
    {
        $catalog = config('pagebuilder_elementor_widgets');
        $this->assertArrayHasKey($type, $catalog);
        $module = $catalog[$type];

        $this->assertSame($label, $module['label']);
        $this->assertFileExists(public_path($module['definition']));
        $this->assertFileExists(public_path($module['canvas']));
        $this->assertFileExists(public_path($module['settings']));
        $this->assertTrue(view()->exists($module['view']));

        $definition = file_get_contents(public_path($module['definition']));
        $settings = file_get_contents(public_path($module['settings']));
        $app = file_get_contents(public_path('js/pagebuilder_elementor/app.js'));
        $this->assertStringContainsString("type: '{$type}'", $definition);
        $this->assertStringContainsString('defaults', $definition);
        $this->assertStringContainsString('normalize(node)', $definition);
        $this->assertStringContainsString('<template>', $settings);
        $this->assertStringNotContainsString("case '{$type}':", $app);
        $this->assertStringNotContainsString("<template v-if=\"selectedType==='{$type}'\">", $app);
        $this->assertStringNotContainsString("{ type:'{$type}'", $app);
    }

    public static function widgetProvider(): array
    {
        return [
            'image box' => ['image_box', 'Image Box'],
            'icon box' => ['icon_box', 'Icon Box'],
            'image carousel' => ['image_carousel', 'Image Carousel'],
            'basic gallery' => ['basic_gallery', 'Basic Gallery'],
            'icon list' => ['icon_list', 'Icon List'],
            'tabs' => ['tabs', 'Tabs'],
            'accordion' => ['accordion', 'Accordion'],
        ];
    }
}
