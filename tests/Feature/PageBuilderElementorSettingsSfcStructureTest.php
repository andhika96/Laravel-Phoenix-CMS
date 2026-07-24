<?php

namespace Tests\Feature;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class PageBuilderElementorSettingsSfcStructureTest extends TestCase
{
    public function test_all_widget_settings_templates_have_balanced_div_elements(): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(public_path('js/pagebuilder_elementor/widgets'))
        );
        $settingsFiles = [];

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getFilename() === 'Settings.vue') {
                $settingsFiles[] = $file->getPathname();
            }
        }

        sort($settingsFiles);
        $this->assertNotEmpty($settingsFiles);

        foreach ($settingsFiles as $path) {
            $source = file_get_contents($path);
            $this->assertIsString($source);

            preg_match('/<template>(.*)<\/template>/s', $source, $templateMatch);
            $this->assertArrayHasKey(1, $templateMatch, "Missing template block: {$path}");

            $template = $templateMatch[1];
            $openingDivs = preg_match_all('/<div(?:\s|>)/i', $template);
            $closingDivs = preg_match_all('/<\/div\s*>/i', $template);

            $this->assertSame(
                $openingDivs,
                $closingDivs,
                "Unbalanced div elements in {$path}: {$openingDivs} opening, {$closingDivs} closing"
            );
        }
    }
}
