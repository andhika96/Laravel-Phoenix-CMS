<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PageBuilderElementorV24StaticImportTest extends TestCase
{
    public function test_static_import_requires_authentication(): void
    {
        $this->post(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'source' => UploadedFile::fake()->createWithContent('home.html', '<h1>Home</h1>'),
        ])->assertRedirect(route('cms.core.auth.login'));
    }

    public function test_authenticated_static_import_returns_an_unsaved_layout_payload(): void
    {
        $account = new Account();
        $account->forceFill(['id' => 1, 'email' => 'editor@example.com', 'suspended_at' => null]);
        $account->exists = true;
        $account->setRelation('roles', collect());
        $this->actingAs($account);

        $response = $this->post(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'source' => UploadedFile::fake()->createWithContent('home.html', '<html><head><title>Imported</title></head><body><h1>Home</h1></body></html>'),
            'framework' => 'auto',
        ])->assertOk();

        $response->assertJsonPath('success', true);
        $response->assertJsonPath('pageName', 'Imported');
        $response->assertJsonPath('layout.0.type', 'container');
        $response->assertJsonPath('layout.0.children.0.type', 'heading');
        $response->assertJsonPath('layout.0.children.0.settings.text', 'Home');
        $this->assertNull($response->json('assetBatchId'));
    }

    public function test_source_script_code_is_returned_only_for_an_explicit_include_scripts_request(): void
    {
        $account = new Account();
        $account->forceFill(['id' => 1, 'email' => 'editor-scripts@example.com', 'suspended_at' => null]);
        $account->exists = true;
        $account->setRelation('roles', collect());
        $this->actingAs($account);
		$this->withoutMiddleware();

        $code = "document.querySelector('#hero')?.classList.add('ready');";
        $default = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'source' => UploadedFile::fake()->createWithContent('home.html', '<main id="hero"><script>'.$code.'</script></main>'),
        ])->assertOk();

        $this->assertArrayNotHasKey('inlineCode', $default->json('report.sourceScripts'));

        $explicit = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'source' => UploadedFile::fake()->createWithContent('home.html', '<main id="hero"><script>'.$code.'</script></main>'),
            'includeScripts' => 1,
        ])->assertOk();

        $explicit->assertJsonPath('report.sourceScripts.copyRequested', true);
        $explicit->assertJsonPath('report.sourceScripts.inlineCode.0', $code);
    }

    public function test_authenticated_compiled_import_returns_native_compile_payload_and_exact_fallback(): void
    {
        $account = new Account();
        $account->forceFill(['id' => 1, 'email' => 'editor-compiled@example.com', 'suspended_at' => null]);
        $account->exists = true;
        $account->setRelation('roles', collect());
        $this->actingAs($account);
        $this->withoutMiddleware();

        $response = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'source' => UploadedFile::fake()->createWithContent(
                'compiled.html',
                '<html><head><script src="https://cdn.tailwindcss.com"></script></head><body><main class="grid grid-cols-2 lg:text-[6.2rem]"><h1 class="font-bold">Compiled</h1></main></body></html>',
            ),
            'framework' => 'tailwind',
            'mode' => 'compiled',
        ])->assertOk();

        $response->assertJsonPath('mode', 'compiled');
        $response->assertJsonPath('layout.0.type', 'container');
        $response->assertJsonPath('compilePayload.mode', 'compiled');
        $response->assertJsonPath('exactFallback.layout.0.type', 'static_html');
        $this->assertNotEmpty($response->json('compilePayload.classMap'));
        $this->assertArrayNotHasKey('inlineCode', $response->json('report.sourceScripts'));
    }

    public function test_authenticated_zip_import_selects_home_entry_and_reports_local_assets(): void
    {
        $account = new Account();
        $account->forceFill(['id' => 1, 'email' => 'editor@example.com', 'suspended_at' => null]);
        $account->exists = true;
        $account->setRelation('roles', collect());
        $this->actingAs($account);

        $path = tempnam(sys_get_temp_dir(), 'pb-import-feature-');
        $zip = new \ZipArchive();
        $zip->open($path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $zip->addFromString('home.html', '<title>ZIP page</title><h1>ZIP</h1>');
        $zip->addFromString('assets/hero.jpg', 'asset');
        $zip->close();

        try {
            $response = $this->post(route('cms.core.pagebuilder_elementor_v24.import_static'), [
                'source' => new UploadedFile($path, 'landing.zip', 'application/zip', null, true),
            ])->assertOk();

            $response->assertJsonPath('status', 'partial');
            $response->assertJsonPath('pageName', 'ZIP page');
            $response->assertJsonPath('report.entry', 'home.html');
            $response->assertJsonPath('report.missingAssets', 1);
        } finally {
            @unlink($path);
        }
    }
}
