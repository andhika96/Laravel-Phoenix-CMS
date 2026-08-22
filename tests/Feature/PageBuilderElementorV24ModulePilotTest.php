<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use App\Support\PageBuilderElementorV24\ModuleCatalog;
use Tests\TestCase;

class PageBuilderElementorV24ModulePilotTest extends TestCase
{
    public function test_production_button_module_is_discovered_from_the_canonical_root(): void
    {
        $catalog = app(ModuleCatalog::class);

        $button = $catalog->find('button');

        $this->assertIsArray($button);
        $this->assertSame('basic', $button['category']);
        $this->assertSame(60, $button['order']);
        $this->assertStringEndsWith('modules/widgets/basic/button/definition.js', str_replace('\\', '/', $button['assets']['definition']));
        $this->assertStringEndsWith('modules/widgets/basic/button/frontend.blade.php', str_replace('\\', '/', $button['assets']['view']));
    }

    public function test_authenticated_shell_configures_the_catalog_and_loads_discovered_definitions_from_active_modules(): void
    {
        $this->actingAsAccount();

        $html = $this->get(route('cms.core.pagebuilder_elementor_v24.create'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('PageBuilderElementorV24Widgets.configure', $html);
        $this->assertSame(1, substr_count($html, 'data-pb-module-definition="button"'));
        $this->assertStringNotContainsString('js/pagebuilder_elementor_v24/widgets/basic/button/definition.js', $html);
        $this->assertSame(1, substr_count($html, 'data-pb-module-definition="heading"'));
        $this->assertStringNotContainsString('js/pagebuilder_elementor_v24/widgets/basic/heading/definition.js', $html);
        $this->assertSame(1, substr_count($html, 'data-pb-module-definition="form"'));
        $this->assertStringNotContainsString('js/pagebuilder_elementor_v24/widgets/pro/form/definition.js', $html);
    }

    public function test_authenticated_shell_inlines_editor_module_code_and_styles_without_a_request_storm(): void
    {
        $this->actingAsAccount();

        $catalog = app(ModuleCatalog::class);
        $html = $this->get(route('cms.core.pagebuilder_elementor_v24.create'))
            ->assertOk()
            ->getContent();

        foreach ($catalog->all() as $type => $module) {
            $this->assertSame(1, substr_count($html, 'data-pb-module-definition="'.$type.'"'));
            $this->assertStringNotContainsString(
                '<script src="'.$catalog->clientCatalog()[$type]['assets']['definition'].'"',
                $html,
            );

            if (isset($module['assets']['runtime'])) {
                $this->assertSame(1, substr_count($html, 'data-pb-module-runtime="'.$type.'"'));
            }

            if (isset($module['assets']['styles'])) {
                $this->assertSame(1, substr_count($html, 'data-pb-module-style="'.$type.'"'));
            }
        }
    }

    public function test_frontend_renderer_prefers_an_active_module_view_over_the_legacy_config_view(): void
    {
        $this->app->instance(
            ModuleCatalog::class,
            new ModuleCatalog(base_path('tests/Fixtures/PageBuilderElementorV24Modules')),
        );

        $html = view('pagebuilder_elementor_v24.partials.render_node', [
            'node' => [
                'id' => 'fixture-button',
                'type' => 'button',
                'settings' => ['text' => 'Legacy text must not render'],
            ],
        ])->render();

        $this->assertStringContainsString('<button type="button">Button</button>', $html);
        $this->assertStringNotContainsString('Legacy text must not render', $html);
    }

    private function actingAsAccount(): void
    {
        $account = new Account();
        $account->forceFill([
            'id' => 1,
            'email' => 'v24-module-pilot@example.com',
            'suspended_at' => null,
        ]);
        $account->exists = true;
        $account->setRelation('roles', collect());

        $this->actingAs($account);
    }
}
