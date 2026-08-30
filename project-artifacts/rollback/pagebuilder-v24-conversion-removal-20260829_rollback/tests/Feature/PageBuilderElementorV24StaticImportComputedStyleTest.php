<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PageBuilderElementorV24StaticImportComputedStyleTest extends TestCase
{
    public function test_compiled_response_keeps_scanner_contract_separate_from_saved_layout(): void
    {
        $account = new Account();
        $account->forceFill(['id' => 1, 'email' => 'computed-style@example.com', 'suspended_at' => null]);
        $account->exists = true;
        $account->setRelation('roles', collect());
        $this->actingAs($account);
        $this->withoutMiddleware();

        $response = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'source' => UploadedFile::fake()->createWithContent(
                'computed-style.html',
                '<html><body><section id="hero"><div class="px-5"><h1>Hero</h1></div></section></body></html>',
            ),
            'framework' => 'tailwind',
            'mode' => 'compiled',
        ])->assertOk();

        $response->assertJsonPath('mode', 'compiled');
        $response->assertJsonPath('compilePayload.viewports.0.key', 'mobile');
        $response->assertJsonPath('compilePayload.viewports.0.width', 390);
        $response->assertJsonPath('compilePayload.sections.0.sourceId', 'hero');
        $response->assertJsonPath('compilePayload.sections.0.fallback', false);
        $response->assertJsonPath('report.fallbackSections', 0);
        $response->assertJsonPath('report.compiledSections', 1);
        $this->assertSame('', (string) $response->json('customCss'));
        $this->assertNotEmpty($response->json('layout.0.settings.staticImport'));
    }

    public function test_static_import_rejects_unsafe_asset_base_url(): void
    {
        $account = new Account();
        $account->forceFill(['id' => 1, 'email' => 'computed-base-url@example.com', 'suspended_at' => null]);
        $account->exists = true;
        $account->setRelation('roles', collect());
        $this->actingAs($account);
        $this->withoutMiddleware();

        $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'source' => UploadedFile::fake()->createWithContent('base-url.html', '<main><img src="asset.jpg"></main>'),
            'framework' => 'tailwind',
            'mode' => 'compiled',
            'baseUrl' => 'javascript:alert(1)',
        ])->assertStatus(422)->assertJsonPath('success', false);
    }

    public function test_guided_compile_retains_the_browser_scanner_payload_after_mapping(): void
    {
        $account = new Account();
        $account->forceFill(['id' => 1, 'email' => 'guided-scanner@example.com', 'suspended_at' => null]);
        $account->exists = true;
        $account->setRelation('roles', collect());
        $this->actingAs($account);
        $this->withoutMiddleware();

        $html = '<html><body><section id="hero"><h1>Hero</h1></section></body></html>';
        $analysis = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'phase' => 'analyze',
            'mode' => 'compiled',
            'source' => UploadedFile::fake()->createWithContent('guided-scanner.html', $html),
        ])->assertOk();
        $regions = $analysis->json('regions');
        $mapping = [
            'version' => 1,
            'regions' => array_map(static fn (array $region): array => [
                'regionId' => $region['id'],
                'strategy' => 'auto_native',
                'blocks' => [],
            ], $regions),
        ];

        $response = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'phase' => 'compile',
            'mode' => 'compiled',
            'sourceHash' => $analysis->json('sourceHash'),
            'mapping' => json_encode($mapping, JSON_THROW_ON_ERROR),
            'source' => UploadedFile::fake()->createWithContent('guided-scanner.html', $html),
        ])->assertOk();

        $response->assertJsonPath('phase', 'compile');
        $response->assertJsonPath('compilePayload.viewports.2.width', 1180);
        $response->assertJsonPath('compilePayload.mode', 'compiled');
        $response->assertJsonPath('customCss', '');
        $this->assertArrayHasKey('mappingReport', $response->json());
    }
}
