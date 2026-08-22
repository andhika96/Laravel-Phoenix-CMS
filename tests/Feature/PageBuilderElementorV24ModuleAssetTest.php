<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use App\Support\PageBuilderElementorV24\ModuleCatalog;
use Tests\TestCase;

class PageBuilderElementorV24ModuleAssetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(
            ModuleCatalog::class,
            new ModuleCatalog(base_path('tests/Fixtures/PageBuilderElementorV24Modules')),
        );
    }

    public function test_module_assets_require_an_authenticated_v24_editor_session(): void
    {
        $this->get('/pagebuilder-elementor/v2.4/module-assets/button/definition.js')
            ->assertRedirect();
    }

    public function test_whitelisted_editor_assets_are_served_with_cache_validators_and_correct_content_types(): void
    {
        $this->actingAsAccount();

        $definition = $this->get('/pagebuilder-elementor/v2.4/module-assets/button/definition.js')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/javascript; charset=UTF-8');
        $this->assertStringContainsString(
            'PageBuilderElementorV24Widgets',
            $definition->baseResponse->getFile()->getContent(),
        );
        $this->assertNotSame('', (string) $definition->headers->get('ETag'));
        $this->assertNotSame('', (string) $definition->headers->get('Last-Modified'));

        $canvas = $this->get('/pagebuilder-elementor/v2.4/module-assets/button/canvas.vue')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertStringContainsString(
            'FixtureButtonCanvas',
            $canvas->baseResponse->getFile()->getContent(),
        );

        $settings = $this->get('/pagebuilder-elementor/v2.4/module-assets/button/settings.vue')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertStringContainsString(
            'FixtureButtonSettings',
            $settings->baseResponse->getFile()->getContent(),
        );
    }

    public function test_unknown_inactive_and_non_browser_asset_keys_fail_closed(): void
    {
        $this->actingAsAccount();

        $this->get('/pagebuilder-elementor/v2.4/module-assets/missing/definition.js')->assertNotFound();
        $this->get('/pagebuilder-elementor/v2.4/module-assets/button/missing.js')->assertNotFound();
        $this->get('/pagebuilder-elementor/v2.4/module-assets/button/view.blade.php')->assertNotFound();
        $this->get('/pagebuilder-elementor/v2.4/module-assets/button/canvas')->assertNotFound();
        $this->get('/pagebuilder-elementor/v2.4/module-assets/button/%2E%2E')->assertNotFound();
    }

    private function actingAsAccount(): void
    {
        $account = new Account();
        $account->forceFill([
            'id' => 1,
            'email' => 'v24-module-assets@example.com',
            'suspended_at' => null,
        ]);
        $account->exists = true;
        $account->setRelation('roles', collect());

        $this->actingAs($account);
    }
}
