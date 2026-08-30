<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use App\Support\PageBuilderElementorV24\StaticImport\StaticPageImportService;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PageBuilderElementorV24StaticImportCompiledFlowTest extends TestCase
{
    public function test_compiled_import_response_has_success_payload_and_exact_fallback_without_persistence(): void
    {
        $account = new Account();
        $account->forceFill(['id' => 1, 'email' => 'compiled-flow@example.com', 'suspended_at' => null]);
        $account->exists = true;
        $account->setRelation('roles', collect());
        $this->actingAs($account);
        $this->withoutMiddleware();

        $response = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'source' => UploadedFile::fake()->createWithContent(
                'compiled-flow.html',
                '<html><head><script src="https://cdn.tailwindcss.com"></script></head><body><main class="grid grid-cols-2"><h1 class="text-4xl font-bold">Compiled flow</h1></main></body></html>',
            ),
            'framework' => 'tailwind',
            'mode' => 'compiled',
        ])->assertOk();

        $response->assertJsonPath('success', true);
        $response->assertJsonPath('mode', 'compiled');
        $response->assertJsonPath('compilePayload.mode', 'compiled');
        $response->assertJsonPath('exactFallback.layout.0.type', 'static_html');
        $this->assertStringNotContainsString('<script src=', (string) $response->json('compilePayload.html'));
        $this->assertNull($response->json('assetBatchId'));
    }

    public function test_editor_contract_exposes_cancel_fallback_and_keeps_compile_unsaved(): void
    {
        $app = file_get_contents(base_path('public/js/pagebuilder_elementor_v24/app.js'));
        $this->assertIsString($app);
        $this->assertStringContainsString('new AbortController()', $app);
        $this->assertStringContainsString('cancelStaticImportCompile', $app);
        $this->assertStringContainsString('useExactStaticImportFallback', $app);
        $this->assertStringContainsString('saveState.value = \'dirty\'', $app);
        $this->assertStringNotContainsString('await savePage()', $app);
    }

    public function test_ceo_masters_tailwind_fixture_produces_framework_free_native_layout(): void
    {
        $path = 'E:\\Apps\\Laragon\\www\\ceo-masters\\index.html';
        if (!is_file($path)) {
            $this->markTestSkipped('CEO Masters fixture is not available on this machine.');
        }

        $result = app(StaticPageImportService::class)->convert(
            new UploadedFile($path, 'index.html', 'text/html', null, true),
            'auto',
            null,
            'compiled',
        );

        $layoutJson = json_encode($result['layout'], JSON_THROW_ON_ERROR);
        $this->assertSame('compiled', $result['mode']);
        $this->assertNotEmpty($result['compilePayload']['classMap']);
        $this->assertGreaterThan(0, $result['report']['compileEligibleNodes']);
        $this->assertArrayHasKey('relativeAssets', $result['report']);
        $this->assertStringNotContainsString('cdn.tailwindcss.com', $layoutJson);
        $this->assertStringNotContainsString('grid-cols-', $layoutJson);
        $this->assertStringNotContainsString('lg:', $layoutJson);
    }
}
