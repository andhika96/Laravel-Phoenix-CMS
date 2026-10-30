<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use App\Models\Page_Builder\Page_Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PageBuilderElementorV24RoutesAndPersistenceTest extends TestCase
{
    private string $originalConnection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalConnection = (string) config('database.default');
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => true,
        ]);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Schema::create('page_builder', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0);
            $table->string('uri')->unique();
            $table->string('page_name')->nullable()->unique();
            $table->text('custom_css')->nullable();
            $table->text('custom_js')->nullable();
            $table->string('custom_js_mode', 24)->default('disabled');
            $table->text('vars');
            $table->string('status')->default('publish');
            $table->string('editor_version', 10);
            $table->timestamps();
        });

        Schema::create('language', function (Blueprint $table): void {
            $table->integer('id')->nullable();
            $table->string('lang', 12);
            $table->string('lang_from', 255);
            $table->string('lang_to', 255);
        });

        $this->insertPage('v20-page', 'V20 Page', Page_Builder::EDITOR_VERSION_V20, $this->formLayout());
        $this->insertPage('0', 'V20 Zero Page', Page_Builder::EDITOR_VERSION_V20, '[]');
        $this->insertPage('v23-page', 'V23 Page', Page_Builder::EDITOR_VERSION_V23, $this->formLayout());
        $this->actingAsEditor();
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        config(['database.default' => $this->originalConnection]);
        DB::setDefaultConnection($this->originalConnection);

        parent::tearDown();
    }

    public function test_v24_route_family_resolves_all_eight_named_routes(): void
    {
        $this->assertSame('/pagebuilder-elementor/v2.4/create', route('cms.core.pagebuilder_elementor_v24.create', absolute: false));
        $this->assertSame('/pagebuilder-elementor/v2.4/store', route('cms.core.pagebuilder_elementor_v24.store', absolute: false));
        $this->assertSame('/pagebuilder-elementor/v2.4/edit/demo', route('cms.core.pagebuilder_elementor_v24.edit', 'demo', false));
        $this->assertSame('/pagebuilder-elementor/v2.4/update/demo', route('cms.core.pagebuilder_elementor_v24.update', 'demo', false));
        $this->assertSame('/pagebuilder-elementor/v2.4/data/demo', route('cms.core.pagebuilder_elementor_v24.data', 'demo', false));
        $this->assertSame('/pagebuilder-elementor/v2.4/image-rendition', route('cms.core.pagebuilder_elementor_v24.image_rendition', absolute: false));
        $this->assertSame('/pagebuilder-elementor/v2.4/preview/demo', route('cms.core.pagebuilder_elementor_v24.preview', 'demo', false));
        $this->assertSame('/pagebuilder-elementor/v2.4/form/demo/node-1', route('cms.core.pagebuilder_elementor_v24.form.submit', ['demo', 'node-1'], false));
    }

    public function test_v24_store_creates_a_v24_page(): void
    {
        $this->postJson(route('cms.core.pagebuilder_elementor_v24.store'), [
            'pageName' => 'V24 Page',
            'pageStatus' => 'draft',
            'layout' => json_encode([['id' => 'heading-1', 'type' => 'heading', 'settings' => ['text' => 'Hello']]]),
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas('page_builder', [
            'page_name' => 'V24 Page',
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
        ]);
    }

    public function test_v24_store_and_identifier_routes_are_scoped_to_the_authenticated_owner(): void
    {
        $this->actingAsEditor(7);
        $this->postJson(route('cms.core.pagebuilder_elementor_v24.store'), [
            'pageName' => 'Owner Seven Page',
            'pageStatus' => 'draft',
            'layout' => '[]',
        ])->assertOk();

        $page = Page_Builder::query()->where('page_name', 'Owner Seven Page')->firstOrFail();
        $this->assertSame(7, (int) $page->user_id);

        $this->actingAsEditor(8);
        $this->get('/pagebuilder-elementor/v2.4/edit/'.$page->uri)->assertNotFound();
        $this->getJson('/pagebuilder-elementor/v2.4/data/'.$page->uri)->assertNotFound();
        $this->postJson('/pagebuilder-elementor/v2.4/update/'.$page->uri, [
            'pageName' => 'Must Not Change Owner',
            'pageStatus' => 'draft',
            'layout' => '[]',
        ])->assertNotFound();
    }

    public function test_v24_identifier_endpoints_reject_a_v20_page_without_mutating_it(): void
    {
        $before = DB::table('page_builder')->where('uri', 'v20-page')->value('vars');

        $this->get('/pagebuilder-elementor/v2.4/edit/v20-page')->assertStatus(409);
        $this->getJson('/pagebuilder-elementor/v2.4/data/v20-page')->assertStatus(409)->assertJsonPath('editorVersion', '2.0');
        $this->postJson('/pagebuilder-elementor/v2.4/update/v20-page', [
            'pageName' => 'Must Not Change',
            'pageStatus' => 'draft',
            'layout' => '[]',
        ])->assertStatus(409)->assertJsonPath('editorVersion', '2.0');
        $this->get('/pagebuilder-elementor/v2.4/preview/v20-page')->assertStatus(409);
        $this->postJson('/pagebuilder-elementor/v2.4/form/v20-page/form-contact', [
            'name' => 'Aruna',
        ])->assertStatus(409)->assertJsonPath('editorVersion', '2.0');

        $this->assertSame($before, DB::table('page_builder')->where('uri', 'v20-page')->value('vars'));
    }

    public function test_v24_ownership_guard_treats_zero_uri_as_a_valid_v20_identifier(): void
    {
        $this->getJson('/pagebuilder-elementor/v2.4/data/0')
            ->assertStatus(409)
            ->assertJsonPath('editorVersion', '2.0');
    }

    public function test_v24_rejects_a_v23_page_and_v23_rejects_a_v24_page_without_mutation(): void
    {
        $v23Before = DB::table('page_builder')->where('uri', 'v23-page')->value('vars');

        $this->getJson('/pagebuilder-elementor/v2.4/data/v23-page')
            ->assertStatus(409)
            ->assertJsonPath('editorVersion', Page_Builder::EDITOR_VERSION_V23);
        $this->postJson('/pagebuilder-elementor/v2.4/update/v23-page', [
            'pageName' => 'Must Not Replace V23',
            'pageStatus' => 'draft',
            'layout' => '[]',
        ])->assertStatus(409)->assertJsonPath('editorVersion', Page_Builder::EDITOR_VERSION_V23);
        $this->assertSame($v23Before, DB::table('page_builder')->where('uri', 'v23-page')->value('vars'));

        $this->insertPage('v24-version-guard', 'V24 Version Guard', Page_Builder::EDITOR_VERSION_V24, $this->formLayout());
        $v24Before = DB::table('page_builder')->where('uri', 'v24-version-guard')->value('vars');

        $this->getJson('/pagebuilder-elementor/v2.3/data/v24-version-guard')
            ->assertStatus(409)
            ->assertJsonPath('editorVersion', Page_Builder::EDITOR_VERSION_V24);
        $this->postJson('/pagebuilder-elementor/v2.3/update/v24-version-guard', [
            'pageName' => 'Must Not Replace V24',
            'pageStatus' => 'draft',
            'layout' => '[]',
        ])->assertStatus(409)->assertJsonPath('editorVersion', Page_Builder::EDITOR_VERSION_V24);
        $this->assertSame($v24Before, DB::table('page_builder')->where('uri', 'v24-version-guard')->value('vars'));
    }

    public function test_v24_preview_uses_only_the_v24_renderer_shell(): void
    {
        $this->insertPage('v24-preview', 'V24 Preview', Page_Builder::EDITOR_VERSION_V24, '[]');

        $html = $this->get(route('cms.core.pagebuilder_elementor_v24.preview', 'v24-preview'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('assets/css/frontend_elementor_v24.css', $html);
        $this->assertStringContainsString('js/pagebuilder_elementor_v24/frontend-runtime.js', $html);
        $this->assertStringNotContainsString('assets/css/frontend_elementor.css', $html);
        $this->assertStringNotContainsString('js/pagebuilder_elementor/frontend-runtime.js', $html);
    }

    public function test_public_pages_route_renders_only_published_v24_pages(): void
    {
        $this->insertPage('public-v24-page', 'Public V24 Page', Page_Builder::EDITOR_VERSION_V24, json_encode([
            ['id' => 'public-heading', 'type' => 'heading', 'settings' => ['text' => 'Public V24 Content']],
        ], JSON_THROW_ON_ERROR));
        $this->insertPage('draft-v24-page', 'Draft V24 Page', Page_Builder::EDITOR_VERSION_V24, '[]', 'draft');

        $html = $this->get('/pages/public-v24-page')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('<title>Public V24 Page</title>', $html);
        $this->assertStringContainsString('Public V24 Content', $html);
        $this->assertStringContainsString('assets/css/frontend_elementor_v24.css', $html);
        $this->assertStringContainsString('js/pagebuilder_elementor_v24/frontend-runtime.js', $html);
        $this->get('/pages/draft-v24-page')->assertNotFound();
        $this->get('/pages/v20-page')->assertNotFound();
    }

    public function test_public_dispatcher_keeps_published_v23_pages_on_the_v23_renderer(): void
    {
        $html = $this->get('/pages/v23-page')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('assets/css/frontend_elementor_v23.css', $html);
        $this->assertStringContainsString('js/pagebuilder_elementor_v23/frontend-runtime.js', $html);
        $this->assertStringNotContainsString('assets/css/frontend_elementor_v24.css', $html);
        $this->assertStringNotContainsString('js/pagebuilder_elementor_v24/frontend-runtime.js', $html);
    }

    public function test_v24_editing_a_legacy_layout_does_not_mutate_its_vars_or_timestamp(): void
    {
        $legacyVars = json_encode([
            [
                'id' => 'legacy-container',
                'type' => 'container',
                'settings' => ['columns' => [['id' => 'legacy-column']]],
            ],
        ], JSON_THROW_ON_ERROR);
        $this->insertPage('v24-legacy', 'V24 Legacy', Page_Builder::EDITOR_VERSION_V24, $legacyVars);

        $before = DB::table('page_builder')->where('uri', 'v24-legacy')->first(['vars', 'updated_at']);

        $this->get('/pagebuilder-elementor/v2.4/edit/v24-legacy')->assertOk();

        $after = DB::table('page_builder')->where('uri', 'v24-legacy')->first(['vars', 'updated_at']);
        $this->assertSame($before->vars, $after->vars);
        $this->assertSame($before->updated_at, $after->updated_at);
    }

    public function test_v24_update_persists_flex_children_and_grid_columns(): void
    {
        $this->insertPage('v24-children', 'V24 Children', Page_Builder::EDITOR_VERSION_V24, '[]');

        $this->postJson('/pagebuilder-elementor/v2.4/update/v24-children', [
            'pageName' => 'V24 Children',
            'pageStatus' => 'publish',
            'layout' => json_encode([
                [
                    'id' => 'container-parent',
                    'type' => 'container',
                    'settings' => ['displayType' => 'flex'],
                    'children' => [
                        ['id' => 'container-child', 'type' => 'container', 'settings' => ['displayType' => 'flex'], 'children' => []],
                    ],
                ],
                [
                    'id' => 'grid-parent',
                    'type' => 'grid',
                    'settings' => ['columns' => 2, 'gridRows' => 1],
                    'columns' => [
                        ['id' => 'grid-cell-1', 'children' => [['id' => 'grid-heading', 'type' => 'heading', 'settings' => ['text' => 'Grid Heading']]]],
                        ['id' => 'grid-cell-2', 'children' => []],
                    ],
                    'children' => [],
                ],
            ], JSON_THROW_ON_ERROR),
        ])->assertOk()->assertJsonPath('success', true);

        $layout = json_decode(DB::table('page_builder')->where('uri', 'v24-children')->value('vars'), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('container-child', $layout[0]['children'][0]['id']);
        $this->assertArrayNotHasKey('columns', $layout[0]);
        $this->assertSame('grid-cell-1', $layout[1]['columns'][0]['id']);
        $this->assertSame('grid-heading', $layout[1]['columns'][0]['children'][0]['id']);
    }

    public function test_v24_update_validation_failure_leaves_vars_byte_for_byte_unchanged(): void
    {
        $storedVars = '[{"id":"container-existing","type":"container","settings":{"layout":"flex"},"children":[]}]';
        $this->insertPage('v24-invalid-update', 'V24 Invalid Update', Page_Builder::EDITOR_VERSION_V24, $storedVars);

        $this->postJson('/pagebuilder-elementor/v2.4/update/v24-invalid-update', [
            'pageName' => '',
            'pageStatus' => 'publish',
            'layout' => '[]',
        ])->assertStatus(422);

        $this->assertSame($storedVars, DB::table('page_builder')->where('uri', 'v24-invalid-update')->value('vars'));
    }

    public function test_v24_update_returns_an_error_when_persistence_is_rejected(): void
    {
        $storedVars = '[{"id":"container-existing","type":"container","settings":{"layout":"flex"},"children":[]}]';
        $this->insertPage('v24-rejected-update', 'V24 Rejected Update', Page_Builder::EDITOR_VERSION_V24, $storedVars);

        $originalDispatcher = Page_Builder::getEventDispatcher();
        Page_Builder::setEventDispatcher(new Dispatcher($this->app));
        Page_Builder::saving(static fn () => false);

        try {
            $this->postJson('/pagebuilder-elementor/v2.4/update/v24-rejected-update', [
                'pageName' => 'V24 Rejected Update',
                'pageStatus' => 'publish',
                'layout' => '[]',
            ])->assertStatus(500)->assertJsonPath('success', false);
        } finally {
            Page_Builder::setEventDispatcher($originalDispatcher);
        }

        $this->assertSame($storedVars, DB::table('page_builder')->where('uri', 'v24-rejected-update')->value('vars'));
    }

    private function formLayout(): string
    {
        return json_encode([
            [
                'id' => 'form-contact',
                'type' => 'form',
                'settings' => [
                    'formName' => 'V20 Contact Form',
                    'fields' => [
                        ['id' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ],
                    'submitActions' => ['message'],
                ],
            ],
        ], JSON_THROW_ON_ERROR);
    }

    private function insertPage(string $uri, string $pageName, string $editorVersion, string $vars, string $status = 'publish', int $ownerId = 1): void
    {
        DB::table('page_builder')->insert([
            'user_id' => $ownerId,
            'uri' => $uri,
            'page_name' => $pageName,
            'custom_css' => '',
            'vars' => $vars,
            'status' => $status,
            'editor_version' => $editorVersion,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function actingAsEditor(int $id = 1): void
    {
        $account = new Account();
        $account->forceFill([
            'id' => $id,
            'email' => 'editor-'.$id.'@example.com',
            'suspended_at' => null,
        ]);
        $account->exists = true;
        $account->setRelation('roles', collect());
        $this->actingAs($account);
    }
}
