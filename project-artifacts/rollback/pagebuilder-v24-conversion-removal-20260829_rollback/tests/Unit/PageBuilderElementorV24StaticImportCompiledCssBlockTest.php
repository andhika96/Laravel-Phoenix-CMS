<?php

namespace Tests\Unit;

use Tests\TestCase;

class PageBuilderElementorV24StaticImportCompiledCssBlockTest extends TestCase
{
    public function test_browser_css_block_manager_contract_exists_before_compiled_mode_is_enabled(): void
    {
        $path = base_path('public/js/pagebuilder_elementor_v24/static-import-css.js');
        $this->assertFileExists($path);

        $source = file_get_contents($path);
        $this->assertIsString($source);
        $this->assertStringContainsString('PHOENIX_STATIC_IMPORT_COMPILED_START', $source);
        $this->assertStringContainsString('PHOENIX_STATIC_IMPORT_COMPILED_END', $source);
        $this->assertStringContainsString('replaceGeneratedStaticImportCss', $source);
    }
}
