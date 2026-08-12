<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageBuilderElementorV23CodeHighlightWidgetTest extends TestCase
{
    public function test_code_highlight_is_registered_and_frontend_renderer_outputs_safe_markup(): void
    {
        $module = config('pagebuilder_elementor_v23_widgets.code_highlight');

        $this->assertSame('Code Highlight', $module['label'] ?? null);
        $this->assertSame('pro', $module['category'] ?? null);
        $this->assertSame('pagebuilder_elementor_v23.partials.render_pro_widget', $module['view'] ?? null);
        $this->assertFileExists(public_path($module['definition'] ?? 'missing'));

        $html = view($module['view'], [
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
