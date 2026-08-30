<?php

namespace Tests\Feature;

use App\Support\PageBuilderElementorV24\ModuleCatalog;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Tests\TestCase;

class PageBuilderElementorV24AssetIsolationTest extends TestCase
{
    public function test_v24_owns_a_thin_core_and_manifest_discovered_modules_without_v23_references(): void
    {
        $v24Assets = $this->activeRelativeFiles(public_path('js/pagebuilder_elementor_v24'));
        $this->assertSame([
            'app.js',
            'frontend-runtime.js',
            'vue-draggable-plus.iife.js',
            'widget-registry.js',
        ], $v24Assets);
        $this->assertDirectoryDoesNotExist(public_path('js/pagebuilder_elementor_v24/widgets'));
        $this->assertNotEmpty($this->activeRelativeFiles(public_path('js/pagebuilder_elementor_v23')));

        $files = [
            ...$this->absoluteFiles(app_path('Http/Controllers/Web/PageBuilderElementorV24')),
            ...$this->absoluteFiles(app_path('Http/Requests/Page_Builder_Elementor_V24')),
            ...$this->absoluteFiles(app_path('Models/PageBuilderElementorV24')),
            ...$this->absoluteFiles(app_path('Support/PageBuilderElementorV24')),
            ...$this->absoluteFiles(public_path('js/pagebuilder_elementor_v24')),
            ...$this->absoluteFiles(resource_path('pagebuilder_elementor_v24/modules')),
            ...$this->absoluteFiles(resource_path('pagebuilder_elementor_v24/shared')),
            ...$this->absoluteFiles(resource_path('views/pagebuilder_elementor_v24')),
            app_path('Mail/PageBuilderElementorV24FormMail.php'),
            public_path('assets/css/pagebuilder_elementor_v24.css'),
            public_path('assets/css/frontend_elementor_v24.css'),
            resource_path('data/pagebuilder_elementor_v24_shapes.json'),
            resource_path('views/emails/pagebuilder-elementor-v24-form-text.blade.php'),
            base_path('routes/pagebuilder_elementor_v24.php'),
            database_path('migrations/2026_08_22_210800_create_pagebuilder_elementor_v24_form_datasets_table.php'),
        ];

        foreach ($files as $file) {
            $this->assertFileExists($file);
            $source = file_get_contents($file);
            $this->assertIsString($source);

            foreach ([
                '/js/pagebuilder_elementor_v23/',
                'assets/css/pagebuilder_elementor_v23.css',
                'assets/css/frontend_elementor_v23.css',
                "config('pagebuilder_elementor_v23_widgets",
                "view('pagebuilder_elementor_v23.",
                "@include('pagebuilder_elementor_v23.",
                'App\\Support\\PageBuilderElementorV23\\',
                'App\\Models\\PageBuilderElementorV23\\',
                'App\\Http\\Requests\\Page_Builder_Elementor_V23\\',
                'cms.core.pagebuilder_elementor_v23.',
                'pagebuilder_elementor_v23_form_datasets',
                'pagebuilder:v23-',
                'phoenix-pagebuilder-v23',
                'phoenix.pagebuilder.v23',
                'Page Builder v2.3',
                'Page Builder 2.3',
            ] as $forbidden) {
                $this->assertStringNotContainsString($forbidden, $source, $file);
            }

            foreach ([
                'PAGE_BUILDER_ELEMENTOR_V23_CONTEXT',
                'PageBuilderElementorV23',
                'PB_ELEMENTOR_V23',
                'V23_',
            ] as $forbidden) {
                $matches = preg_match(
                    '/(?<![A-Za-z0-9_])'.preg_quote($forbidden, '/').'(?![A-Za-z0-9_])/',
                    $source,
                );

                $this->assertSame(0, $matches, $file);
            }
        }

        $catalog = (new ModuleCatalog)->all();
        $this->assertCount(53, $catalog);

        foreach ($catalog as $module) {
            foreach (['definition', 'canvas', 'settings', 'view'] as $asset) {
                $this->assertFileExists($module['assets'][$asset], $module['type'].' '.$asset);
            }
        }
    }

    /**
     * @return list<string>
     */
    private function activeRelativeFiles(string $directory): array
    {
        $this->assertDirectoryExists($directory);

        $files = [];
        foreach ($this->absoluteFiles($directory) as $file) {
            $relative = substr($file, strlen(rtrim($directory, DIRECTORY_SEPARATOR)) + 1);
            $files[] = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
        }

        sort($files);

        return $files;
    }

    /**
     * @return list<string>
     */
    private function absoluteFiles(string $directory): array
    {
        $this->assertDirectoryExists($directory);

        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if ($file->isFile() && ! str_contains($file->getFilename(), '.bak')) {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        return $files;
    }
}
