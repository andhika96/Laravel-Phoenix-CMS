<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use App\Models\Page_Builder\Page_Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PageBuilderElementorV24CustomJavaScriptTest extends TestCase
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
        $this->actingAsEditor();
        $this->withoutMiddleware();
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        config(['database.default' => $this->originalConnection]);
        DB::setDefaultConnection($this->originalConnection);
        parent::tearDown();
    }

    public function test_store_and_update_persist_custom_javascript_and_mode(): void
    {
        $code = "document.documentElement.dataset.ready = '1';";
        $storeResponse = $this->postJson(route('cms.core.pagebuilder_elementor_v24.store'), [
            'pageName' => 'Custom JS Page',
            'pageStatus' => 'draft',
            'customJs' => $code,
            'customJsMode' => 'exact_sandbox',
            'layout' => '[]',
        ]);
        $storeResponse->assertOk();

        $page = Page_Builder::query()->where('page_name', 'Custom JS Page')->firstOrFail();
        $this->assertSame($code, $page->custom_js);
        $this->assertSame('exact_sandbox', $page->custom_js_mode);

        $updated = "document.body.dataset.updated = '1';";
        $this->postJson(route('cms.core.pagebuilder_elementor_v24.update', $page->uri), [
            'pageName' => 'Custom JS Page',
            'pageStatus' => 'publish',
            'customJs' => $updated,
            'customJsMode' => 'published',
            'layout' => '[]',
        ])->assertOk();

        $this->assertSame($updated, DB::table('page_builder')->where('id', $page->id)->value('custom_js'));
        $this->assertSame('published', DB::table('page_builder')->where('id', $page->id)->value('custom_js_mode'));
    }

    public function test_blocked_custom_javascript_request_returns_diagnostics_without_echoing_code(): void
    {
        $response = $this->postJson(route('cms.core.pagebuilder_elementor_v24.store'), [
            'pageName' => 'Blocked Request JS',
            'pageStatus' => 'draft',
            'customJs' => '<script>alert("secret")</script>',
            'customJsMode' => 'published',
            'layout' => '[]',
        ])->assertStatus(422);

        $response->assertJsonPath('customJsDiagnostics.code', '');
        $this->assertStringNotContainsString('secret', $response->getContent());
    }

    public function test_published_custom_javascript_is_emitted_only_for_a_published_public_page(): void
    {
        $page = Page_Builder::create([
			'user_id' => 1,
			'uri' => 'published-js',
            'page_name' => 'Published JS',
            'status' => 'publish',
            'custom_js' => "document.body.dataset.customJs = 'enabled';",
            'custom_js_mode' => 'published',
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
			'vars' => '[]',
        ]);

		$html = $this->get('/pages/published-js')->assertOk()->getContent();

        $this->assertStringContainsString('data-pb-custom-javascript="published"', $html);
        $this->assertStringContainsString("document.body.dataset.customJs = 'enabled';", $html);
    }

    public function test_editor_preview_never_emits_published_custom_javascript(): void
    {
        $page = Page_Builder::create([
            'user_id' => 1,
            'uri' => 'editor-preview-js',
            'page_name' => 'Editor Preview JS',
            'status' => 'publish',
            'custom_js' => "document.body.dataset.customJs = 'editor';",
            'custom_js_mode' => 'published',
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
            'vars' => '[]',
        ]);

        $html = $this->get(route('cms.core.pagebuilder_elementor_v24.preview', $page->uri))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('data-pb-custom-javascript=', $html);
        $this->assertStringNotContainsString("document.body.dataset.customJs = 'editor';", $html);
    }

    public function test_blocked_published_custom_javascript_is_not_emitted(): void
    {
        $page = Page_Builder::create([
            'user_id' => 1,
            'uri' => 'blocked-published-js',
            'page_name' => 'Blocked Published JS',
            'status' => 'publish',
            'custom_js' => '<script>alert(1)</script>',
            'custom_js_mode' => 'published',
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
            'vars' => '[]',
        ]);

        $html = $this->get('/pages/'.$page->uri)->assertOk()->getContent();

        $this->assertStringNotContainsString('data-pb-custom-javascript=', $html);
        $this->assertStringNotContainsString('alert(1)', $html);
    }

    public function test_v23_public_pages_do_not_consume_the_v24_custom_javascript_fields(): void
    {
        Page_Builder::create([
            'user_id' => 1,
            'uri' => 'v23-script-isolation',
            'page_name' => 'V23 Script Isolation',
            'status' => 'publish',
            'custom_js' => "document.body.dataset.customJs = 'v23';",
            'custom_js_mode' => 'published',
            'editor_version' => Page_Builder::EDITOR_VERSION_V23,
            'vars' => '[]',
        ]);

        $html = $this->get('/pages/v23-script-isolation')->assertOk()->getContent();

        $this->assertStringNotContainsString('data-pb-custom-javascript=', $html);
        $this->assertStringNotContainsString("document.body.dataset.customJs = 'v23';", $html);
    }

    public function test_disabled_and_draft_custom_javascript_never_becomes_an_executable_script(): void
    {
        foreach ([['disabled', 'publish'], ['published', 'draft'], ['exact_sandbox', 'publish']] as [$mode, $status]) {
            $page = (new Page_Builder())->forceFill([
                'page_name' => 'Suppressed JS',
                'status' => $status,
                'custom_js' => "document.body.dataset.customJs = 'no';",
                'custom_js_mode' => $mode,
                'editor_version' => Page_Builder::EDITOR_VERSION_V24,
            ]);
            $html = view('pagebuilder_elementor_v24.frontend_renderer', [
                'page' => $page, 'pageData' => $page, 'nodes' => [],
            ])->render();

            $this->assertStringNotContainsString('data-pb-custom-javascript=', $html);
        }
    }

    public function test_custom_javascript_update_keeps_the_existing_owner_boundary(): void
    {
        $page = Page_Builder::create([
            'user_id' => 1,
            'uri' => 'owner-boundary-js',
            'page_name' => 'Owner Boundary JS',
            'status' => 'draft',
            'custom_js' => '',
            'custom_js_mode' => 'disabled',
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
            'vars' => '[]',
        ]);

        $this->actingAsEditor(2);
        $this->postJson(route('cms.core.pagebuilder_elementor_v24.update', $page->uri), [
            'pageName' => 'Must Not Change JS',
            'pageStatus' => 'publish',
            'customJs' => "document.body.dataset.customJs = 'intruder';",
            'customJsMode' => 'published',
            'layout' => '[]',
        ])->assertNotFound();

        $this->assertSame('', DB::table('page_builder')->where('id', $page->id)->value('custom_js'));
        $this->assertSame('disabled', DB::table('page_builder')->where('id', $page->id)->value('custom_js_mode'));
    }

    private function actingAsEditor(int $id = 1): void
    {
        $account = new Account();
        $account->forceFill(['id' => $id, 'email' => 'custom-js-'.$id.'@example.com', 'suspended_at' => null]);
        $account->exists = true;
        $account->setRelation('roles', collect());
        $this->actingAs($account);
    }
}
