<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementorV24\StaticImport\StaticPageImportService;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PageBuilderElementorV24StaticPageImportServiceTest extends TestCase
{
    public function test_it_rejects_an_html_source_over_the_conservative_limit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('2 MB');

        app(StaticPageImportService::class)->convert(
            UploadedFile::fake()->createWithContent('large.html', str_repeat('x', 2 * 1024 * 1024 + 1)),
        );
    }

    public function test_it_rejects_zip_path_traversal(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'pb-import-unsafe-');
        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('../home.html', '<h1>Unsafe</h1>');
        $zip->close();

        try {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionMessage('unsafe path');
            app(StaticPageImportService::class)->convert(new UploadedFile($path, 'unsafe.zip', 'application/zip', null, true));
        } finally {
            @unlink($path);
        }
    }

    public function test_it_reads_home_html_from_a_zip_without_executing_scripts(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'pb-import-');
        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('home.html', '<html><head><title>ZIP Home</title><script>alert(1)</script></head><body><h1>ZIP</h1></body></html>');
        $zip->addFromString('assets/hero.jpg', 'not-an-image');
        $zip->close();

        try {
            $result = app(StaticPageImportService::class)->convert(
                new UploadedFile($path, 'landing.zip', 'application/zip', null, true),
            );

            $this->assertSame('ZIP Home', $result['pageName']);
            $this->assertSame(1, $result['report']['droppedScripts']);
            $this->assertSame('heading', $result['layout'][0]['children'][0]['type']);
        } finally {
            @unlink($path);
        }
    }

    public function test_it_maps_bootstrap_markup_to_editable_v24_nodes_and_drops_scripts(): void
    {
        $html = <<<'HTML'
<!doctype html>
<html><head><title>Bootstrap Home</title><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"></head>
<body><div class="container"><div class="row"><div class="col-md-6"><h1>Welcome</h1><p>Intro <strong>copy</strong>.</p><a class="btn btn-primary" href="/start">Start</a><img src="/images/hero.jpg" alt="Hero"><script>alert(1)</script></div></div></div></body></html>
HTML;

        $result = app(StaticPageImportService::class)->convert(
            UploadedFile::fake()->createWithContent('home.html', $html),
        );

        $this->assertSame(['bootstrap5'], $result['frameworks']);
        $this->assertSame('Bootstrap Home', $result['pageName']);
        $this->assertCount(1, $result['layout']);
        $this->assertSame('container', $result['layout'][0]['type']);
        $this->assertSame('column', $result['layout'][0]['settings']['direction']);
        $this->assertSame('container', $result['layout'][0]['children'][0]['type']);
        $row = $result['layout'][0]['children'][0]['children'][0];
        $this->assertSame('row', $row['settings']['direction']);

        $column = $row['children'][0];
        $this->assertSame('50%', $column['settings']['containerWidthTablet']);
        $this->assertSame('heading', $column['children'][0]['type']);
        $this->assertSame('Welcome', $column['children'][0]['settings']['text']);
        $this->assertSame('text_editor', $column['children'][1]['type']);
        $this->assertStringContainsString('<strong>copy</strong>', $column['children'][1]['settings']['html']);
        $this->assertSame('button', $column['children'][2]['type']);
        $this->assertSame('/start', $column['children'][2]['settings']['url']);
        $this->assertSame('image', $column['children'][3]['type']);
        $this->assertSame('/images/hero.jpg', $column['children'][3]['settings']['src']);
        $this->assertSame(1, $result['report']['droppedScripts']);
        $this->assertStringNotContainsString('alert', json_encode($result['layout']));
    }

    public function test_it_maps_tailwind_layout_and_spacing_utilities(): void
    {
        $html = <<<'HTML'
<!doctype html><html><head><title>Tailwind Home</title><script src="https://cdn.tailwindcss.com"></script></head>
<body><main class="flex flex-col gap-4 p-6"><h2 class="text-xl font-bold">Tailwind</h2><p class="text-gray-600">Body</p></main></body></html>
HTML;

        $result = app(StaticPageImportService::class)->convert(
            UploadedFile::fake()->createWithContent('home.html', $html),
        );

        $this->assertSame(['tailwind'], $result['frameworks']);
        $main = $result['layout'][0]['children'][0];
        $this->assertSame('container', $main['type']);
        $this->assertSame('column', $main['settings']['direction']);
        $this->assertSame('1rem', $main['settings']['flexRowGap']);
        $this->assertSame('1.5rem', $main['settings']['paddingTop']);
        $this->assertSame('heading', $main['children'][0]['type']);
        $this->assertSame('text_editor', $main['children'][1]['type']);
        $this->assertSame(1, $result['report']['droppedScripts']);
    }

    public function test_it_sanitizes_unsafe_links_while_preserving_safe_rich_text(): void
    {
        $result = app(StaticPageImportService::class)->convert(
            UploadedFile::fake()->createWithContent('home.html', '<p><strong>Safe</strong> <a href="javascript:alert(1)">link</a></p>'),
        );

        $html = $result['layout'][0]['children'][0]['settings']['html'];
        $this->assertStringContainsString('<strong>Safe</strong>', $html);
        $this->assertStringNotContainsString('javascript:', $html);
    }
}
