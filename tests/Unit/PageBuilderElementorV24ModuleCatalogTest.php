<?php

namespace Tests\Unit;

use App\Support\PageBuilderElementorV24\ModuleCatalog;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class PageBuilderElementorV24ModuleCatalogTest extends TestCase
{
    private string $workspace;

    private string $modulesRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $qaRoot = base_path('project-artifacts/qa/pagebuilder-v24-module-catalog-tests');
        File::ensureDirectoryExists($qaRoot);

        $this->workspace = $qaRoot.DIRECTORY_SEPARATOR.Str::uuid();
        $this->modulesRoot = $this->workspace.DIRECTORY_SEPARATOR.'modules';
        File::ensureDirectoryExists($this->modulesRoot);
    }

    protected function tearDown(): void
    {
        $qaRoot = realpath(base_path('project-artifacts/qa/pagebuilder-v24-module-catalog-tests'));
        $workspaceParent = realpath(dirname($this->workspace));

        if ($qaRoot !== false && $workspaceParent === $qaRoot && File::isDirectory($this->workspace)) {
            File::deleteDirectory($this->workspace);
        }

        parent::tearDown();
    }

    public function test_it_discovers_a_valid_module_and_builds_a_browser_safe_catalog(): void
    {
        File::copyDirectory(
            base_path('tests/Fixtures/PageBuilderElementorV24Modules'),
            $this->modulesRoot,
        );

        $catalog = new ModuleCatalog($this->modulesRoot);

        $this->assertSame(['button'], array_keys($catalog->all()));
        $this->assertTrue($catalog->active('button'));
        $this->assertStringEndsWith('Settings.vue', $catalog->find('button')['assets']['settings']);
        $this->assertSame('button', $catalog->toolbox()['basic'][0]['type']);
        $this->assertSame([], $catalog->diagnostics());

        $client = $catalog->clientCatalog();
        $this->assertSame('Button', $client['button']['label']);
        $this->assertSame(
            '/pagebuilder-elementor/v2.4/module-assets/button/definition.js',
            $client['button']['assets']['definition'],
        );
        $this->assertSame(
            '/pagebuilder-elementor/v2.4/module-assets/button/canvas.vue',
            $client['button']['assets']['canvas'],
        );
        $this->assertSame(
            '/pagebuilder-elementor/v2.4/module-assets/button/settings.vue',
            $client['button']['assets']['settings'],
        );
        $this->assertArrayNotHasKey('view', $client['button']['assets']);
        $this->assertStringNotContainsString(str_replace('\\', '/', $this->workspace), json_encode($client));
    }

    public function test_it_skips_invalid_json_schema_missing_assets_and_traversal_paths_without_hiding_valid_modules(): void
    {
        $this->writeModule('widgets/basic/button', ['type' => 'button', 'label' => 'Button', 'order' => 10]);

        $invalidJson = $this->modulesRoot.'/widgets/basic/invalid-json';
        File::ensureDirectoryExists($invalidJson);
        File::put($invalidJson.'/module.json', '{invalid');

        $this->writeModule('widgets/basic/schema-two', ['type' => 'schema_two', 'schemaVersion' => 2]);
        $this->writeModule('widgets/basic/missing-settings', ['type' => 'missing_settings']);
        File::delete($this->modulesRoot.'/widgets/basic/missing-settings/Settings.vue');
        $this->writeModule('widgets/basic/traversal', [
            'type' => 'traversal',
            'assets' => [
                'definition' => 'definition.js',
                'canvas' => 'Canvas.vue',
                'settings' => '../outside.vue',
                'view' => 'frontend.blade.php',
            ],
        ]);

        $catalog = new ModuleCatalog($this->modulesRoot);

        $this->assertSame(['button'], array_keys($catalog->all()));
        $this->assertCount(4, $catalog->diagnostics());
        $reasons = implode(' | ', array_column($catalog->diagnostics(), 'reason'));
        $this->assertStringContainsString('Invalid JSON', $reasons);
        $this->assertStringContainsString('schemaVersion', $reasons);
        $this->assertStringContainsString('Missing required asset', $reasons);
        $this->assertStringContainsString('must stay inside', $reasons);
    }

    public function test_duplicate_types_are_all_disabled_instead_of_using_first_wins(): void
    {
        $this->writeModule('widgets/basic/first-button', ['type' => 'button', 'label' => 'First Button']);
        $this->writeModule('widgets/basic/second-button', ['type' => 'button', 'label' => 'Second Button']);

        $catalog = new ModuleCatalog($this->modulesRoot);

        $this->assertFalse($catalog->active('button'));
        $this->assertSame([], $catalog->all());
        $this->assertCount(1, $catalog->diagnostics());
        $this->assertSame('Duplicate module type: button', $catalog->diagnostics()[0]['reason']);
    }

    public function test_toolbox_order_is_deterministic_and_hidden_runtime_modules_stay_registered(): void
    {
        $this->writeModule('widgets/general/zeta', [
            'type' => 'zeta',
            'label' => 'Zeta',
            'category' => 'general',
            'order' => 10,
        ]);
        $this->writeModule('widgets/basic/beta', [
            'type' => 'beta',
            'label' => 'Beta',
            'category' => 'basic',
            'order' => 20,
        ]);
        $this->writeModule('widgets/basic/alpha', [
            'type' => 'alpha',
            'label' => 'Alpha',
            'category' => 'basic',
            'order' => 20,
        ]);
        $this->writeModule('layout/container-fluid', [
            'type' => 'container_fluid',
            'label' => 'Container Fluid',
            'category' => 'layout',
            'order' => 20,
            'toolbox' => false,
            'advanced' => ['profile' => 'layout', 'capabilities' => []],
        ]);

        $catalog = new ModuleCatalog($this->modulesRoot);

        $this->assertSame(
            ['container_fluid', 'alpha', 'beta', 'zeta'],
            array_keys($catalog->all()),
        );
        $this->assertTrue($catalog->active('container_fluid'));
        $this->assertArrayNotHasKey('layout', $catalog->toolbox());
        $this->assertSame(['alpha', 'beta'], array_column($catalog->toolbox()['basic'], 'type'));
        $this->assertSame(['zeta'], array_column($catalog->toolbox()['general'], 'type'));
    }

    public function test_capability_queries_are_driven_by_active_module_manifests(): void
    {
        $this->writeModule('widgets/pro/form', [
            'type' => 'form',
            'label' => 'Form',
            'category' => 'pro',
            'capabilities' => ['form-submission', 'form-datasets'],
        ]);
        $this->writeModule('widgets/pro/product-lead-form', [
            'type' => 'product_lead_form',
            'label' => 'Product Lead Form',
            'category' => 'pro',
            'capabilities' => ['form-submission', 'form-datasets', 'product-selection'],
        ]);

        $catalog = new ModuleCatalog($this->modulesRoot);

        $this->assertTrue($catalog->supports('form', 'form-submission'));
        $this->assertTrue($catalog->supports('product_lead_form', 'product-selection'));
        $this->assertFalse($catalog->supports('form', 'product-selection'));
        $this->assertFalse($catalog->supports('missing', 'form-submission'));
        $this->assertTrue($catalog->anySupports('form-datasets'));
        $this->assertFalse($catalog->anySupports('missing-capability'));
    }

    public function test_moving_a_module_folder_outside_the_root_removes_it_and_restoring_the_folder_adds_it_back(): void
    {
        $modulePath = $this->writeModule('widgets/basic/button', ['type' => 'button', 'label' => 'Button']);
        $disabledPath = $this->workspace.'/disabled-button';

        $this->assertTrue((new ModuleCatalog($this->modulesRoot))->active('button'));

        File::moveDirectory($modulePath, $disabledPath);
        try {
            $this->assertFalse((new ModuleCatalog($this->modulesRoot))->active('button'));
        } finally {
            File::moveDirectory($disabledPath, $modulePath);
        }

        $this->assertTrue((new ModuleCatalog($this->modulesRoot))->active('button'));
    }

    private function writeModule(string $relativePath, array $overrides = []): string
    {
        $directory = $this->modulesRoot.'/'.str_replace('\\', '/', $relativePath);
        File::ensureDirectoryExists($directory);

        $manifest = array_replace_recursive([
            'schemaVersion' => 1,
            'type' => 'fixture',
            'label' => 'Fixture',
            'category' => 'basic',
            'icon' => 'fas fa-square',
            'order' => 10,
            'toolbox' => true,
            'assets' => [
                'definition' => 'definition.js',
                'canvas' => 'Canvas.vue',
                'settings' => 'Settings.vue',
                'view' => 'frontend.blade.php',
            ],
            'advanced' => [
                'profile' => 'widget',
                'capabilities' => [],
            ],
        ], $overrides);

        File::put($directory.'/module.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        File::put($directory.'/definition.js', 'window.fixture = true;');
        File::put($directory.'/Canvas.vue', '<template><div>Canvas</div></template>');
        File::put($directory.'/Settings.vue', '<template><div>Settings</div></template>');
        File::put($directory.'/frontend.blade.php', '<div>Frontend</div>');

        return $directory;
    }
}
