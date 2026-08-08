<?php

namespace Tests\Feature;

use App\Models\Page_Builder\Page_Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PageBuilderElementorV23RoutesAndPersistenceTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        config(['database.default' => $this->originalConnection]);
        DB::setDefaultConnection($this->originalConnection);

        parent::tearDown();
    }

    public function test_v23_route_family_resolves_all_eight_named_routes(): void
    {
        $this->assertSame('/pagebuilder-elementor/v2.3/create', route('cms.core.pagebuilder_elementor_v23.create', absolute: false));
        $this->assertSame('/pagebuilder-elementor/v2.3/store', route('cms.core.pagebuilder_elementor_v23.store', absolute: false));
        $this->assertSame('/pagebuilder-elementor/v2.3/edit/demo', route('cms.core.pagebuilder_elementor_v23.edit', 'demo', false));
        $this->assertSame('/pagebuilder-elementor/v2.3/update/demo', route('cms.core.pagebuilder_elementor_v23.update', 'demo', false));
        $this->assertSame('/pagebuilder-elementor/v2.3/data/demo', route('cms.core.pagebuilder_elementor_v23.data', 'demo', false));
        $this->assertSame('/pagebuilder-elementor/v2.3/image-rendition', route('cms.core.pagebuilder_elementor_v23.image_rendition', absolute: false));
        $this->assertSame('/pagebuilder-elementor/v2.3/preview/demo', route('cms.core.pagebuilder_elementor_v23.preview', 'demo', false));
        $this->assertSame('/pagebuilder-elementor/v2.3/form/demo/node-1', route('cms.core.pagebuilder_elementor_v23.form.submit', ['demo', 'node-1'], false));
    }

    public function test_v23_store_creates_a_v23_page(): void
    {
        $this->postJson(route('cms.core.pagebuilder_elementor_v23.store'), [
            'pageName' => 'V23 Page',
            'pageStatus' => 'draft',
            'layout' => json_encode([['id' => 'heading-1', 'type' => 'heading', 'settings' => ['text' => 'Hello']]]),
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas('page_builder', [
            'page_name' => 'V23 Page',
            'editor_version' => Page_Builder::EDITOR_VERSION_V23,
        ]);
    }

    public function test_v23_identifier_endpoints_reject_a_v20_page_without_mutating_it(): void
    {
        $before = DB::table('page_builder')->where('uri', 'v20-page')->value('vars');

        $this->get('/pagebuilder-elementor/v2.3/edit/v20-page')->assertStatus(409);
        $this->getJson('/pagebuilder-elementor/v2.3/data/v20-page')->assertStatus(409)->assertJsonPath('editorVersion', '2.0');
        $this->postJson('/pagebuilder-elementor/v2.3/update/v20-page', [
            'pageName' => 'Must Not Change',
            'pageStatus' => 'draft',
            'layout' => '[]',
        ])->assertStatus(409)->assertJsonPath('editorVersion', '2.0');
        $this->get('/pagebuilder-elementor/v2.3/preview/v20-page')->assertStatus(409);
        $this->postJson('/pagebuilder-elementor/v2.3/form/v20-page/form-contact', [
            'name' => 'Aruna',
        ])->assertStatus(409)->assertJsonPath('editorVersion', '2.0');

        $this->assertSame($before, DB::table('page_builder')->where('uri', 'v20-page')->value('vars'));
    }

    public function test_v23_ownership_guard_treats_zero_uri_as_a_valid_v20_identifier(): void
    {
        $this->getJson('/pagebuilder-elementor/v2.3/data/0')
            ->assertStatus(409)
            ->assertJsonPath('editorVersion', '2.0');
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

    private function insertPage(string $uri, string $pageName, string $editorVersion, string $vars): void
    {
        DB::table('page_builder')->insert([
            'user_id' => 1,
            'uri' => $uri,
            'page_name' => $pageName,
            'custom_css' => '',
            'vars' => $vars,
            'status' => 'publish',
            'editor_version' => $editorVersion,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
