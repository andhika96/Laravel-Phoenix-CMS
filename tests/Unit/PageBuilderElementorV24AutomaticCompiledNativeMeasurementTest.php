<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeMeasurement;
use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeFrameworkLoader;
use App\Support\PageBuilderElementorV24\CompiledNative\AutomaticCompiledNativeSource;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use ZipArchive;

class PageBuilderElementorV24AutomaticCompiledNativeMeasurementTest extends TestCase
{
    public function test_measurement_wrapper_returns_normalized_multi_viewport_snapshot(): void
    {
        $fixture = base_path('tests/Fixtures/PageBuilderElementorV24/CompiledNative/automatic-two-column-grid.html');
        $source = new UploadedFile($fixture, 'automatic-two-column-grid.html', 'text/html', UPLOAD_ERR_OK, true);
        $package = AutomaticCompiledNativeSource::fromUpload($source);

        try {
            $snapshot = (new AutomaticCompiledNativeMeasurement)->measure($package, [
                ['name' => 'desktop', 'width' => 1180, 'height' => 900],
                ['name' => 'tablet', 'width' => 768, 'height' => 1024],
            ]);

            $hero = collect($snapshot['nodes'])->firstWhere('id', 'hero');
            $this->assertIsArray($hero);
            $this->assertSame('grid', $hero['computedStyle']['display']);
            $this->assertSame('80px', $hero['computedStyle']['paddingTop']);
            $this->assertStringContainsString('Two column hero', $hero['textContent']);
            $this->assertStringContainsString('hero-copy', $hero['innerHTML']);
            $this->assertSame('id', $hero['attributes'][0]['name']);
            $this->assertArrayHasKey('desktop', $hero['rectByViewport']);
            $this->assertArrayHasKey('tablet', $hero['rectByViewport']);
            $this->assertIsFloat($hero['rectByViewport']['desktop']['width']);
        } finally {
            $package->cleanup();
        }
    }

    public function test_measurement_uses_the_original_css_file_base_for_a_nested_zip_entry(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'pb-v24-auto-measure-zip-');
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::OVERWRITE);
        $zip->addFromString('pages/index.html', '<!doctype html><html><head><link rel="stylesheet" href="../css/site.css"></head><body><section id="hero"><div>Hero</div></section></body></html>');
        $zip->addFromString('css/site.css', '#hero{display:grid;grid-template-columns:42% 58%;padding:32px}');
        $zip->close();

        $upload = new UploadedFile($path, 'nested.zip', 'application/zip', UPLOAD_ERR_OK, true);
        $package = AutomaticCompiledNativeSource::fromUpload($upload);
        try {
            $bundle = (new AutomaticCompiledNativeFrameworkLoader)->prepare($package, 'auto');
            $snapshot = (new AutomaticCompiledNativeMeasurement)->measure($package, [
                ['name' => 'desktop', 'width' => 1180, 'height' => 900],
            ], $bundle);
            $hero = collect($snapshot['nodes'])->firstWhere('id', 'hero');
            $this->assertIsArray($hero);
            $this->assertSame('grid', $hero['computedStyle']['display']);
            $this->assertStringContainsString('px', $hero['computedStyle']['gridTemplateColumns']);
        } finally {
            $package->cleanup();
        }
    }

    private function zipUpload(array $files): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'pb-v24-auto-measure-');
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::OVERWRITE);
        foreach ($files as $name => $contents) {
            $zip->addFromString($name, $contents);
        }
        $zip->close();

        return new UploadedFile($path, 'source.zip', 'application/zip', UPLOAD_ERR_OK, true);
    }
}
