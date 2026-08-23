<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use Tests\TestCase;

class PageBuilderElementorV24LegacyBridgeTest extends TestCase
{
    public function test_legacy_bridge_is_empty_after_all_fifty_modules_are_discovered(): void
    {
        $this->assertSame([], config('pagebuilder_elementor_v24_widgets', []));
    }

    public function test_shell_keeps_baseline_category_and_pro_module_order_across_the_bridge(): void
    {
        $account = new Account();
        $account->forceFill([
            'id' => 1,
            'email' => 'v24-bridge-order@example.com',
            'suspended_at' => null,
        ]);
        $account->exists = true;
        $account->setRelation('roles', collect());
        $this->actingAs($account);

        $html = $this->get(route('cms.core.pagebuilder_elementor_v24.create'))
            ->assertOk()
            ->getContent();

        $positions = array_map(
            static fn (string $needle): int|false => strpos($html, $needle),
            [
                'data-pb-module-definition="container"',
                'data-pb-module-definition="heading"',
                'data-pb-module-definition="image_box"',
                'data-pb-module-definition="form"',
                'data-pb-module-definition="product_lead_form"',
                'data-pb-module-definition="media_carousel"',
                'data-pb-module-definition="hero_banner"',
                'data-pb-module-definition="hero_slider"',
                'data-pb-module-definition="flip_box"',
                'data-pb-module-definition="video_playlist"',
                'data-pb-module-definition="product_color_selector"',
            ],
        );

        $this->assertNotContains(false, $positions);
        $sorted = $positions;
        sort($sorted);
        $this->assertSame($sorted, $positions);
    }
}
