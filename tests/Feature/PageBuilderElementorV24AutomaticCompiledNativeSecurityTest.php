<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use ZipArchive;

class PageBuilderElementorV24AutomaticCompiledNativeSecurityTest extends TestCase
{
    public function test_automatic_analysis_requires_an_authenticated_v24_session(): void
    {
        $response = $this->post(route('cms.core.pagebuilder_elementor_v24.compiled_native.automatic_analyze'), [
            'source' => $this->htmlUpload('guest.html', '<main>Guest</main>'),
        ]);

        $this->assertContains($response->getStatusCode(), [302, 419]);
    }

    public function test_upload_limit_is_enforced_before_browser_measurement(): void
    {
        $this->withoutMiddleware();

        $this->postJson(route('cms.core.pagebuilder_elementor_v24.compiled_native.automatic_analyze'), [
            'source' => UploadedFile::fake()->create('too-large.html', 20481, 'text/html'),
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['source']);
    }

    public function test_scripts_handlers_and_javascript_urls_are_removed_from_the_measurement_bundle(): void
    {
        $this->withoutMiddleware();

        $response = $this->postJson(route('cms.core.pagebuilder_elementor_v24.compiled_native.automatic_analyze'), [
            'source' => $this->htmlUpload('unsafe.html', <<<'HTML'
<!doctype html><html><head><style>#safe{display:block;padding:20px}</style></head><body><main><section id="safe" onclick="window.wasExecuted=true"><a href="javascript:alert(1)">Safe</a></section></main><script>window.wasExecuted=true;</script></body></html>
HTML),
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonMissingPath('javascript:alert(1)');
        $html = (string) $response->json('html');
        $this->assertStringNotContainsString('<script', strtolower($html));
        $this->assertStringNotContainsString('onclick=', strtolower($html));
        $this->assertContains('source-scripts-removed', array_column($response->json('diagnostics', []), 'code'));
    }

    public function test_archive_traversal_is_rejected_and_the_source_workspace_stays_inside_storage(): void
    {
        $this->withoutMiddleware();

        $response = $this->postJson(route('cms.core.pagebuilder_elementor_v24.compiled_native.automatic_analyze'), [
            'source' => $this->zipUpload([
                '../outside.html' => '<main>outside</main>',
                'index.html' => '<main><section id="safe">Inside</section></main>',
            ]),
        ]);

        $response->assertOk()->assertJsonPath('entry', 'index.html');
        $this->assertContains('archive-path-rejected', array_column($response->json('diagnostics', []), 'code'));
        $this->assertFileDoesNotExist(storage_path('app/pagebuilder-v24-compiled-native/outside.html'));
    }

    private function htmlUpload(string $name, string $contents): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'pb-v24-auto-security-');
        file_put_contents($path, $contents);

        return new UploadedFile($path, $name, 'text/html', UPLOAD_ERR_OK, true);
    }

    /** @param array<string,string> $files */
    private function zipUpload(array $files): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'pb-v24-auto-security-zip-');
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::OVERWRITE);
        foreach ($files as $name => $contents) {
            $zip->addFromString($name, $contents);
        }
        $zip->close();

        return new UploadedFile($path, 'source.zip', 'application/zip', UPLOAD_ERR_OK, true);
    }
}
