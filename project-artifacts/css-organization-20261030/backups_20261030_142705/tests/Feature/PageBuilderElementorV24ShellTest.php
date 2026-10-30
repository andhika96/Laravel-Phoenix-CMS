<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use Tests\TestCase;

class PageBuilderElementorV24ShellTest extends TestCase
{
    public function test_v24_create_shell_loads_only_the_v24_editor_context_and_runtime(): void
    {
        $account = new Account();
        $account->forceFill([
            'id' => 1,
            'email' => 'v24-shell@example.com',
            'suspended_at' => null,
        ]);
        $account->exists = true;
        $account->setRelation('roles', collect());
        $this->actingAs($account);

        $html = $this->get(route('cms.core.pagebuilder_elementor_v24.create'))->assertOk()->getContent();

        $this->assertStringContainsString('id="pbElementorV24App"', $html);
        $this->assertStringContainsString('PAGE_BUILDER_ELEMENTOR_V24_CONTEXT', $html);
        $this->assertStringContainsString("editorVersion: '2.4'", $html);
        $this->assertStringContainsString('assets/css/pagebuilder_elementor_v24.css', $html);
        $this->assertStringContainsString('js/pagebuilder_elementor_v24/frontend-runtime.js', $html);
        $this->assertStringContainsString('js/pagebuilder_elementor_v24/widget-registry.js', $html);
        $this->assertStringContainsString('js/pagebuilder_elementor_v24/app.js', $html);
        $this->assertStringNotContainsString('js/pagebuilder_elementor/frontend-runtime.js', $html);
        $this->assertStringNotContainsString('js/pagebuilder_elementor/widget-registry.js', $html);
        $this->assertStringNotContainsString('js/pagebuilder_elementor/widgets/', $html);
        $this->assertStringNotContainsString('js/pagebuilder_elementor/app.js', $html);
    }
}
