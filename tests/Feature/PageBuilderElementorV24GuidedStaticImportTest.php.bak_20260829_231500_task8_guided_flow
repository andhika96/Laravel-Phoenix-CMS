<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use App\Models\Page_Builder\Page_Builder;
use App\Http\Requests\Page_Builder_Elementor_V24\ImportStaticPageRequest;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PageBuilderElementorV24GuidedStaticImportTest extends TestCase
{
    public function test_static_import_rejects_an_invalid_phase_before_service_invocation(): void
    {
        $this->authenticateEditor();

        $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'phase' => 'invalid-phase',
            'mode' => 'compiled',
            'source' => UploadedFile::fake()->createWithContent('regions.html', '<section>Hero</section>'),
        ])->assertUnprocessable()->assertJsonValidationErrors('phase');
    }

    public function test_compile_phase_requires_source_hash_and_mapping_before_service_invocation(): void
    {
        $this->authenticateEditor();

        $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'phase' => 'compile',
            'mode' => 'compiled',
            'source' => UploadedFile::fake()->createWithContent('regions.html', '<section>Hero</section>'),
        ])->assertUnprocessable()->assertJsonValidationErrors(['sourceHash', 'mapping']);
    }

    public function test_compile_phase_rejects_mapping_larger_than_512_kilobytes(): void
    {
        $this->authenticateEditor();

        $mapping = json_encode(['version' => 1, 'nodes' => str_repeat('x', 524288)], JSON_THROW_ON_ERROR);

        $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), $this->compilePayload($mapping))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('mapping');
    }

    public function test_compile_phase_rejects_mapping_over_512_kilobytes_by_utf8_byte_length(): void
    {
        $this->authenticateEditor();

        $mapping = json_encode([
            'version' => 1,
            'nodes' => str_repeat("\u{00E9}", 262144),
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $this->assertLessThan(524288, mb_strlen($mapping));
        $this->assertGreaterThan(524288, strlen($mapping));

        $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), $this->compilePayload($mapping))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('mapping');
    }

    public function test_compile_phase_rejects_malformed_mapping_json(): void
    {
        $this->authenticateEditor();

        $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), $this->compilePayload('{invalid-json'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('mapping');
    }

    public function test_compile_phase_rejects_an_unsupported_mapping_version(): void
    {
        $this->authenticateEditor();

        $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), $this->compilePayload('{"version":2}'))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('mapping');
    }

    public function test_request_normalizes_the_phase_and_decodes_the_mapping_payload(): void
    {
        $analyze = ImportStaticPageRequest::create('/import/static', 'POST');
        $compile = ImportStaticPageRequest::create('/import/static', 'POST', [
            'phase' => 'compile',
            'mapping' => '{"version":1,"regions":["hero"]}',
        ]);

        $this->assertSame('analyze', $analyze->phase());
        $this->assertSame([], $analyze->mappingPayload());
        $this->assertSame('compile', $compile->phase());
        $this->assertSame(['version' => 1, 'regions' => ['hero']], $compile->mappingPayload());
    }

    public function test_analyze_returns_regions_and_preview_without_layout_or_custom_css(): void
    {
        $this->authenticateEditor();

        $response = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'phase' => 'analyze',
            'mode' => 'compiled',
            'framework' => 'tailwind',
            'source' => UploadedFile::fake()->createWithContent(
                'guided-analyze.html',
                '<html><body><header><h1>Header</h1></header><section id="hero"><p>Hero</p></section><footer>Footer</footer></body></html>',
            ),
        ])->assertOk();

        $payload = $response->json();
        $this->assertSame(true, $payload['success']);
        $this->assertSame('analyze', $payload['phase']);
        $this->assertSame('compiled', $payload['mode']);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', (string) ($payload['sourceHash'] ?? ''));
        $this->assertNotEmpty($payload['regions']);
        $this->assertNotEmpty($payload['regions'][0]['blocks']);
        $this->assertIsArray($payload['previewPayload']);
        $this->assertStringContainsString('data-pb-import-node', (string) $payload['previewPayload']['html']);
        $this->assertArrayNotHasKey('layout', $payload);
        $this->assertArrayNotHasKey('customCss', $payload);
    }

    public function test_compile_resends_the_source_and_mapping_without_persistence(): void
    {
        $this->authenticateEditor();
        $html = '<html><body><header><h1>Header</h1></header><section id="hero"><p>Hero</p></section><footer>Footer</footer></body></html>';
        $before = Page_Builder::query()->count();

        $analysis = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'phase' => 'analyze',
            'mode' => 'compiled',
            'source' => UploadedFile::fake()->createWithContent('guided-compile.html', $html),
        ])->assertOk();

        $mapping = $this->autoMapping($analysis->json('regions'));
        $response = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'phase' => 'compile',
            'mode' => 'compiled',
            'sourceHash' => $analysis->json('sourceHash'),
            'mapping' => json_encode($mapping, JSON_THROW_ON_ERROR),
            'source' => UploadedFile::fake()->createWithContent('guided-compile.html', $html),
        ])->assertOk();

        $payload = $response->json();
        $this->assertSame('compile', $payload['phase']);
        $this->assertArrayHasKey('layout', $payload);
        $this->assertArrayHasKey('compilePayload', $payload);
        $this->assertArrayHasKey('mappingReport', $payload);
        $this->assertSame(count($mapping['regions']), $payload['mappingReport']['regions']);
        $this->assertSame($before, Page_Builder::query()->count());
        $this->assertNull($payload['assetBatchId']);
    }

    public function test_compile_rejects_a_source_hash_mismatch(): void
    {
        $this->authenticateEditor();
        $sourceA = '<html><body><section id="hero"><h1>Alpha</h1></section></body></html>';
        $sourceB = '<html><body><section id="hero"><h1>Beta</h1></section></body></html>';

        $analysis = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'phase' => 'analyze',
            'mode' => 'compiled',
            'source' => UploadedFile::fake()->createWithContent('hash-a.html', $sourceA),
        ])->assertOk();

        $response = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'phase' => 'compile',
            'mode' => 'compiled',
            'sourceHash' => $analysis->json('sourceHash'),
            'mapping' => json_encode($this->autoMapping($analysis->json('regions')), JSON_THROW_ON_ERROR),
            'source' => UploadedFile::fake()->createWithContent('hash-b.html', $sourceB),
        ])->assertStatus(422);

        $this->assertFalse($response->json('success'));
        $this->assertContains('source-hash-mismatch', array_column($response->json('errors', []), 'code'));
        $this->assertArrayNotHasKey('layout', $response->json());
    }

    public function test_compile_applies_mixed_region_strategies_in_source_order(): void
    {
        $this->authenticateEditor();
        $html = '<html><body><header><h1>Header</h1></header><section id="hero"><h2>Hero</h2></section><section id="about"><p>About</p></section><footer>Footer</footer></body></html>';

        $analysis = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'phase' => 'analyze',
            'mode' => 'compiled',
            'source' => UploadedFile::fake()->createWithContent('guided-mixed.html', $html),
        ])->assertOk();

        $regions = $analysis->json('regions');
        $mapping = ['version' => 1, 'regions' => []];
        foreach ($regions as $region) {
            $strategy = match ($region['sourceId'] ?? '') {
                'hero' => 'guided_native',
                'about' => 'exact_visual',
                default => ($region['kind'] === 'footer' ? 'skip' : 'auto_native'),
            };
            $mapping['regions'][] = [
                'regionId' => $region['id'],
                'strategy' => $strategy,
                'blocks' => $strategy === 'guided_native' ? $this->guidedBlocks($region['blocks']) : [],
            ];
        }

        $response = $this->postJson(route('cms.core.pagebuilder_elementor_v24.import_static'), [
            'phase' => 'compile',
            'mode' => 'compiled',
            'sourceHash' => $analysis->json('sourceHash'),
            'mapping' => json_encode($mapping, JSON_THROW_ON_ERROR),
            'source' => UploadedFile::fake()->createWithContent('guided-mixed.html', $html),
        ])->assertOk();

        $payload = $response->json();
        $this->assertSame(2, $payload['mappingReport']['nativeRegions']);
        $this->assertSame(1, $payload['mappingReport']['exactRegions']);
        $this->assertSame(1, $payload['mappingReport']['skippedRegions']);

        $children = $payload['layout'][0]['children'] ?? [];
        $markers = array_map(
            static fn (array $node): string => (string) (($node['settings']['importNodeKey'] ?? '')),
            array_values(array_filter($children, 'is_array')),
        );
        $expectedMarkers = array_map(
            static fn (array $region): string => (string) $region['marker'],
            array_values(array_filter($regions, static fn (array $region): bool => ($region['sourceId'] ?? '') !== '' || $region['kind'] !== 'footer')),
        );
        $this->assertSame($expectedMarkers, $markers);
        $aboutMarker = (string) collect($regions)->firstWhere('sourceId', 'about')['marker'];
        $footerMarker = (string) collect($regions)->firstWhere('kind', 'footer')['marker'];
        $this->assertContains('static_html', array_column($children, 'type'));
        $this->assertContains($aboutMarker, $markers);
        $this->assertNotContains($footerMarker, $markers);
    }

    private function authenticateEditor(): void
    {
        $this->withoutMiddleware();

        $account = new Account();
        $account->forceFill(['id' => 1, 'email' => 'guided-import@example.com', 'suspended_at' => null]);
        $account->exists = true;
        $account->setRelation('roles', collect());

        $this->actingAs($account);
    }

    private function compilePayload(string $mapping): array
    {
        return [
            'phase' => 'compile',
            'mode' => 'compiled',
            'sourceHash' => str_repeat('a', 64),
            'mapping' => $mapping,
            'source' => UploadedFile::fake()->createWithContent('regions.html', '<section>Hero</section>'),
        ];
    }

    /** @param array<int, array<string, mixed>> $regions @return array<string, mixed> */
    private function autoMapping(array $regions): array
    {
        return [
            'version' => 1,
            'regions' => array_map(static fn (array $region): array => [
                'regionId' => $region['id'],
                'strategy' => 'auto_native',
                'blocks' => [],
            ], $regions),
        ];
    }

    /** @param array<int, array<string, mixed>> $blocks @return array<int, array<string, string>> */
    private function guidedBlocks(array $blocks): array
    {
        $result = [];
        foreach ($blocks as $block) {
            if (! is_array($block) || ! isset($block['id'], $block['recommendedWidget'])) continue;
            $result[] = ['blockId' => $block['id'], 'widgetType' => $block['recommendedWidget']];
            if (is_array($block['children'] ?? null)) $result = array_merge($result, $this->guidedBlocks($block['children']));
        }
        return $result;
    }
}
