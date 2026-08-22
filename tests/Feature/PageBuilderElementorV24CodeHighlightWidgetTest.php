<?php

namespace Tests\Feature;

use Tests\Concerns\InteractsWithPageBuilderElementorV24Modules;
use Tests\TestCase;

class PageBuilderElementorV24CodeHighlightWidgetTest extends TestCase
{
    use InteractsWithPageBuilderElementorV24Modules;
    public function test_code_highlight_is_registered_and_frontend_renderer_outputs_safe_markup(): void
    {
        $module = $this->pageBuilderV24Module('code_highlight');

        $this->assertSame('Code Highlight', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertFileExists($module['assets']['view'] ?? 'missing');
        $this->assertFileExists($module['assets']['definition'] ?? 'missing');

        $html = $this->pageBuilderV24ModuleView($module, [
            'node' => [
                'id' => 'code-highlight-test',
                'type' => 'code_highlight',
                'settings' => [
                    'language' => 'php',
                    'code' => "<?php\n<script>alert(1)</script>\necho 'safe';",
                    'lineNumbers' => true,
                    'copyButton' => true,
                    'highlightLines' => '2',
                    'wordWrap' => true,
                    'theme' => 'light',
                    'height' => '320px',
                    'fontSize' => '15px',
                ],
            ],
        ])->render();

        $this->assertStringContainsString('data-pro-widget="code_highlight"', $html);
        $this->assertStringContainsString('data-code-highlight', $html);
        $this->assertStringContainsString('language-php', $html);
        $this->assertStringContainsString('data-code-copy', $html);
        $this->assertStringContainsString('data-code-source', $html);
        $this->assertStringContainsString('pb-pro-code-highlight__line-number', $html);
        $this->assertStringContainsString('is-highlighted', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('--code-highlight-height:320px', $html);
        $this->assertStringContainsString('--code-highlight-font-size:15px', $html);
    }
}
