<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use Tests\TestCase;

class PageBuilderElementorV24SharedAssetTest extends TestCase
{
    public function test_authenticated_editor_can_fetch_only_whitelisted_shared_vue_assets(): void
    {
        $this->actingAsAccount();

        foreach ([
            'advanced.vue',
            'typography.vue',
            'link.vue',
            'dynamic-tag.vue',
            'css-filter.vue',
            'text-stroke.vue',
            'text-shadow.vue',
            'grid-column-style.vue',
        ] as $assetFile) {
            $response = $this->get(route('cms.core.pagebuilder_elementor_v24.shared_asset', ['assetFile' => $assetFile]));
            $response->assertOk()
                ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
                ->assertHeader('X-Content-Type-Options', 'nosniff');
            $this->assertNotSame('', $response->baseResponse->getFile()->getContent());
        }
    }

    public function test_shared_asset_route_rejects_guests_unknown_keys_blade_and_traversal(): void
    {
        $this->get('/pagebuilder-elementor/v2.4/shared-assets/advanced.vue')->assertRedirect();

        $this->actingAsAccount();
        $this->get('/pagebuilder-elementor/v2.4/shared-assets/frontend.vue')->assertNotFound();
        $this->get('/pagebuilder-elementor/v2.4/shared-assets/advanced')->assertNotFound();
        $this->get('/pagebuilder-elementor/v2.4/shared-assets/advanced.blade.php')->assertNotFound();
        $this->get('/pagebuilder-elementor/v2.4/shared-assets/%2e%2e')->assertNotFound();
    }

    private function actingAsAccount(): void
    {
        $account = new Account();
        $account->forceFill([
            'id' => 1,
            'email' => 'v24-shared-assets@example.com',
            'suspended_at' => null,
        ]);
        $account->exists = true;
        $account->setRelation('roles', collect());

        $this->actingAs($account);
    }
}
