<?php

namespace Tests\Feature;

use App\Models\Page_Builder\Page_Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PageBuilderElementorV20VersionGuardTest extends TestCase
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

        $this->insertPage('v20-page', 'V20 Page', Page_Builder::EDITOR_VERSION_V20, '[]');
        $this->insertPage('v23-page', 'V23 Page', Page_Builder::EDITOR_VERSION_V23, json_encode([
            [
                'id' => 'form-contact',
                'type' => 'form',
                'settings' => [
                    'formName' => 'V23 Contact Form',
                    'fields' => [
                        ['id' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ],
                    'submitActions' => ['message'],
                ],
            ],
        ], JSON_THROW_ON_ERROR));
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        config(['database.default' => $this->originalConnection]);
        DB::setDefaultConnection($this->originalConnection);

        parent::tearDown();
    }

    public function test_v20_data_endpoint_does_not_disclose_a_v23_page(): void
    {
        $this->getJson('/pagebuilder-elementor/data/v20-page')->assertOk();

        $this->getJson('/pagebuilder-elementor/data/v23-page')
            ->assertStatus(409)
            ->assertJsonPath('editorVersion', '2.3');
    }

    public function test_v20_data_endpoint_does_not_disclose_a_v23_page_by_numeric_id(): void
    {
        $v23PageId = DB::table('page_builder')->where('uri', 'v23-page')->value('id');

        $this->getJson('/pagebuilder-elementor/data/'.$v23PageId)
            ->assertStatus(409)
            ->assertJsonPath('editorVersion', '2.3');
    }

    public function test_v20_update_endpoint_does_not_overwrite_a_v23_page(): void
    {
        $before = DB::table('page_builder')->where('uri', 'v23-page')->value('vars');

        $this->postJson('/pagebuilder-elementor/update/v23-page', [
            'pageName' => 'Must Not Change',
            'pageStatus' => 'draft',
            'layout' => '[]',
        ])->assertStatus(409)->assertJsonPath('editorVersion', '2.3');

        $this->assertSame($before, DB::table('page_builder')->where('uri', 'v23-page')->value('vars'));
    }

    public function test_v20_edit_endpoint_does_not_open_a_v23_page_in_the_editor(): void
    {
        $this->get('/pagebuilder-elementor/edit/v23-page')->assertStatus(409);
    }

    public function test_v20_preview_endpoint_does_not_render_a_v23_page(): void
    {
        $this->get('/pagebuilder-elementor/preview/v23-page')->assertStatus(409);
    }

    public function test_v20_form_endpoint_does_not_submit_a_v23_form(): void
    {
        $this->postJson('/pagebuilder-elementor/form/v23-page/form-contact', [
            'name' => 'Aruna',
        ])->assertStatus(409)->assertJsonPath('editorVersion', '2.3');
    }

    public function test_v20_store_stamps_new_pages_with_the_v20_editor_version(): void
    {
        $this->postJson('/pagebuilder-elementor/store', [
            'pageName' => 'New V20 Page',
            'pageStatus' => 'draft',
            'layout' => '[]',
        ])->assertOk();

        $this->assertSame(
            Page_Builder::EDITOR_VERSION_V20,
            DB::table('page_builder')->where('uri', 'new-v20-page')->value('editor_version'),
        );
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
