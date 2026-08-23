<?php

namespace Tests\Feature;

use App\Mail\PageBuilderElementorV24FormMail;
use App\Models\Awesome_Admin\Account;
use App\Models\Page_Builder\Page_Builder;
use App\Support\PageBuilderElementorV24\ModuleCatalog;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\Concerns\InteractsWithPageBuilderElementorV24Modules;
use Tests\TestCase;

class PageBuilderElementorV24FormSubmissionTest extends TestCase
{
    use InteractsWithPageBuilderElementorV24Modules;
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
            $table->string('page_name')->nullable();
            $table->text('custom_css')->nullable();
            $table->text('vars');
            $table->string('status')->default('publish');
            $table->string('editor_version', 10)->default('2.0');
            $table->timestamps();
        });

        Schema::create('page_builder_elementor_form_submissions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('page_builder_id')->nullable()->index();
            $table->string('page_uri');
            $table->string('node_id');
            $table->string('form_name');
            $table->json('fields');
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('pagebuilder_elementor_v24_form_datasets', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(1)->index();
            $table->string('name', 120);
            $table->string('slug', 140);
            $table->unsignedTinyInteger('schema_version')->default(1);
            $table->json('nodes');
            $table->timestamps();
            $table->unique(['user_id', 'slug']);
        });
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        config(['database.default' => $this->originalConnection]);
        DB::setDefaultConnection($this->originalConnection);

        parent::tearDown();
    }

    public function test_saved_form_executes_collect_email_email2_webhook_and_returns_redirect(): void
    {
        Mail::fake();
        Http::fake([
            'https://hooks.example.com/*' => Http::response('', 204),
        ]);

        $this->createPageWithForm([
            'submitActions' => ['message', 'collect', 'email', 'email2', 'webhook', 'redirect'],
            'emailTo' => 'owner@example.com',
            'emailSubject' => 'Submission from [field id="name"]',
            'emailContent' => '[all-fields]',
            'emailReplyTo' => 'email',
            'email2To' => 'visitor@example.com',
            'email2Subject' => 'We received your message',
            'email2Content' => 'Hello [field id="name"]',
            'webhookUrl' => 'https://hooks.example.com/forms',
            'redirectUrl' => '/thank-you',
            'successMessage' => 'Thanks, your message was sent.',
            'customMessages' => true,
        ]);

        $response = $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/form-contact', [
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
            'message' => 'Please contact me.',
        ]);

        $response->assertOk()->assertJson([
            'success' => true,
            'message' => 'Thanks, your message was sent.',
            'redirect' => '/thank-you',
        ]);

        $stored = DB::table('page_builder_elementor_form_submissions')->sole();
        $this->assertSame('contact-page', $stored->page_uri);
        $this->assertSame('form-contact', $stored->node_id);
        $this->assertSame([
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
            'message' => 'Please contact me.',
        ], json_decode($stored->fields, true));

        Mail::assertSent(PageBuilderElementorV24FormMail::class, 2);
        Mail::assertSent(PageBuilderElementorV24FormMail::class, function (PageBuilderElementorV24FormMail $mail): bool {
            return $mail->hasTo('owner@example.com')
                && $mail->hasReplyTo('aruna@example.com')
                && $mail->subjectLine === 'Submission from Aruna'
                && str_contains($mail->body, 'Please contact me.');
        });

        Http::assertSent(function ($request): bool {
            return $request->url() === 'https://hooks.example.com/forms'
                && $request['form']['name'] === 'Contact Form'
                && $request['fields']['email'] === 'aruna@example.com';
        });
    }

    public function test_product_lead_form_validates_and_prepends_trusted_product_selection_to_all_actions(): void
    {
        Mail::fake();
        Http::fake(['https://hooks.example.com/*' => Http::response('', 204)]);
        $datasetId = $this->createProductDataset();
        $this->createPageWithProductLeadForm($datasetId, [
            'submitActions' => ['message', 'collect', 'email', 'webhook'],
            'emailTo' => 'owner@example.com',
            'emailSubject' => 'Lead for [field id="product_model"]',
            'emailContent' => '[all-fields]',
            'webhookUrl' => 'https://hooks.example.com/product-leads',
        ]);

        $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/product-lead-contact', [
            'product_model' => 'MGS5_EV',
            'product_type' => 'LUXURY',
            'product_variant' => 'LONG_RANGE',
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertOk()->assertJsonPath('success', true);

        $stored = DB::table('page_builder_elementor_form_submissions')->sole();
        $fields = json_decode($stored->fields, true, flags: JSON_THROW_ON_ERROR);
        $meta = json_decode($stored->meta, true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame([
            'product_model' => 'MGS5_EV',
            'product_type' => 'LUXURY',
            'product_variant' => 'LONG_RANGE',
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ], $fields);
        $this->assertSame(['model-s5', 'type-luxury', 'variant-long-range'], array_column($meta['product_selection'], 'id'));
        $this->assertSame(['mgs5ev', 'luxury', 'long-range'], array_column($meta['product_selection'], 'code'));

        Mail::assertSent(PageBuilderElementorV24FormMail::class, fn (PageBuilderElementorV24FormMail $mail): bool =>
            $mail->subjectLine === 'Lead for MGS5_EV'
            && str_contains($mail->body, 'LONG_RANGE')
        );
        Http::assertSent(fn ($request): bool =>
            $request->url() === 'https://hooks.example.com/product-leads'
            && $request['fields']['product_variant'] === 'LONG_RANGE'
            && $request['meta']['product_selection'][2]['id'] === 'variant-long-range'
        );
    }

    public function test_product_lead_form_rejects_tampered_or_cross_parent_product_values_before_side_effects(): void
    {
        Mail::fake();
        Http::fake();
        $datasetId = $this->createProductDataset();
        $this->createPageWithProductLeadForm($datasetId, [
            'submitActions' => ['collect', 'email', 'webhook'],
            'emailTo' => 'owner@example.com',
            'webhookUrl' => 'https://hooks.example.com/product-leads',
        ]);

        $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/product-lead-contact', [
            'product_model' => 'MGS5_EV',
            'product_type' => 'ACTIVATE',
            'product_variant' => 'HIDDEN',
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertUnprocessable()->assertJsonPath('success', false);

        $this->assertSame(0, DB::table('page_builder_elementor_form_submissions')->count());
        Mail::assertNothingSent();
        Http::assertNothingSent();
    }

    public function test_editor_draft_accepts_product_lead_form_through_the_same_capability_boundary(): void
    {
        $this->actingAsEditor();
        $datasetId = $this->createProductDataset();
        $node = $this->productLeadNode($datasetId, ['submitActions' => ['message']]);

        $this->postJson('/pagebuilder-elementor/v2.4/form/editor-draft', [
            '__pb_editor_node' => json_encode($node, JSON_THROW_ON_ERROR),
            'product_model' => 'MGS5_EV',
            'product_type' => 'LUXURY',
            'product_variant' => 'LONG_RANGE',
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertOk()->assertJson([
            'success' => true,
            'editorTest' => true,
        ]);
    }

    public function test_product_selection_values_drive_existing_form_conditional_logic(): void
    {
        $datasetId = $this->createProductDataset();
        $this->createPageWithProductLeadForm($datasetId, [
            'productLevelCount' => 1,
            'fields' => [
                ['id' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                [
                    'id' => 'interest',
                    'label' => 'Interest',
                    'type' => 'text',
                    'required' => true,
                    'conditionalLogic' => [
                        'enabled' => true,
                        'relation' => 'all',
                        'rules' => [['fieldId' => 'product_model', 'operator' => 'equals', 'value' => 'MGS5_EV']],
                    ],
                ],
            ],
        ]);

        $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/product-lead-contact', [
            'product_model' => 'MGS5_EV',
            'name' => 'Aruna',
        ])->assertUnprocessable()->assertJsonValidationErrors(['interest']);

        $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/product-lead-contact', [
            'product_model' => 'MG_ZS',
            'name' => 'Aruna',
        ])->assertOk()->assertJsonPath('success', true);
    }

    public function test_product_lead_form_submits_when_nested_in_a_grid_column(): void
    {
        $datasetId = $this->createProductDataset();
        DB::table('page_builder')->insert([
            'user_id' => 1,
            'uri' => 'contact-page',
            'page_name' => 'Grid Product Lead',
            'custom_css' => '',
            'vars' => json_encode([[
                'id' => 'grid-root',
                'type' => 'grid',
                'columns' => [[
                    'id' => 'grid-column-1',
                    'children' => [$this->productLeadNode($datasetId)],
                ]],
                'children' => [],
            ]], JSON_THROW_ON_ERROR),
            'status' => 'publish',
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/product-lead-contact', [
            'product_model' => 'MGS5_EV',
            'product_type' => 'LUXURY',
            'product_variant' => 'LONG_RANGE',
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertOk()->assertJsonPath('success', true);
    }

    public function test_editor_draft_route_requires_authentication_and_keeps_editor_middleware(): void
    {
        $route = app('router')->getRoutes()->getByName('cms.core.pagebuilder_elementor_v24.form.editor_draft');

        $this->assertNotNull($route);
        $this->assertContains('auth', $route->gatherMiddleware());
        $this->assertContains('checkSuspended', $route->gatherMiddleware());
        $this->assertContains('throttle:10,1', $route->gatherMiddleware());

        $this->postJson('/pagebuilder-elementor/v2.4/form/editor-draft', [
            '__pb_editor_node' => json_encode($this->draftFormNode(), JSON_THROW_ON_ERROR),
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertUnauthorized();
    }

    public function test_editor_and_dataset_routes_share_auth_boundary_while_published_form_submission_stays_public(): void
    {
        $protectedRoutes = [
            'cms.core.pagebuilder_elementor_v24.create',
            'cms.core.pagebuilder_elementor_v24.store',
            'cms.core.pagebuilder_elementor_v24.edit',
            'cms.core.pagebuilder_elementor_v24.update',
            'cms.core.pagebuilder_elementor_v24.data',
            'cms.core.pagebuilder_elementor_v24.image_rendition',
            'cms.core.pagebuilder_elementor_v24.preview',
            'cms.core.pagebuilder_elementor_v24.form.editor_draft',
            'cms.core.pagebuilder_elementor_v24.datasets.index',
            'cms.core.pagebuilder_elementor_v24.datasets.store',
            'cms.core.pagebuilder_elementor_v24.datasets.update',
            'cms.core.pagebuilder_elementor_v24.datasets.destroy',
        ];

        foreach ($protectedRoutes as $routeName) {
            $route = app('router')->getRoutes()->getByName($routeName);
            $this->assertNotNull($route, $routeName.' should exist');
            $this->assertContains('auth', $route->gatherMiddleware(), $routeName.' should require auth');
            $this->assertContains('checkSuspended', $route->gatherMiddleware(), $routeName.' should reject suspended accounts');
        }

        $publicForm = app('router')->getRoutes()->getByName('cms.core.pagebuilder_elementor_v24.form.submit');
        $this->assertNotNull($publicForm);
        $this->assertNotContains('auth', $publicForm->gatherMiddleware());
        $this->assertContains('throttle:20,1', $publicForm->gatherMiddleware());
    }

    public function test_editor_draft_executes_current_unsaved_actions_and_marks_collected_data_as_test(): void
    {
        $this->actingAsEditor();
        Mail::fake();
        Http::fake(['https://hooks.example.com/*' => Http::response('', 204)]);
        $node = $this->draftFormNode([
            'submitActions' => ['message', 'collect', 'email', 'email2', 'webhook', 'redirect'],
            'emailTo' => 'owner@example.com',
            'emailSubject' => 'Editor test from [field id="name"]',
            'emailContent' => '[all-fields]',
            'email2To' => 'visitor@example.com',
            'email2Subject' => 'Editor copy',
            'email2Content' => 'Hello [field id="name"]',
            'webhookUrl' => 'https://hooks.example.com/forms',
            'redirectUrl' => '/thank-you',
            'customMessages' => true,
            'successMessage' => 'Draft actions completed.',
        ]);

        $response = $this->postJson('/pagebuilder-elementor/v2.4/form/editor-draft', [
            '__pb_editor_node' => json_encode($node, JSON_THROW_ON_ERROR),
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
            'message' => 'Unsaved draft value',
        ]);

        $response->assertOk()->assertJson([
            'success' => true,
            'editorTest' => true,
            'message' => 'Draft actions completed.',
            'redirect' => '/thank-you',
        ]);
        $stored = DB::table('page_builder_elementor_form_submissions')->sole();
        $meta = json_decode($stored->meta, true, flags: JSON_THROW_ON_ERROR);
        $this->assertNull($stored->page_builder_id);
        $this->assertSame('editor-draft', $stored->page_uri);
        $this->assertTrue($meta['editor_test']);
        $this->assertSame('create', $meta['editor_mode']);
        $this->assertSame(1, $meta['editor_user_id']);
        Mail::assertSent(PageBuilderElementorV24FormMail::class, 2);
        Http::assertSent(fn ($request): bool => $request->url() === 'https://hooks.example.com/forms'
            && $request['meta']['editor_test'] === true
            && $request['fields']['message'] === 'Unsaved draft value');
    }

    public function test_editor_draft_uses_current_node_settings_and_keeps_the_owned_saved_page_identity(): void
    {
        $this->actingAsEditor();
        $this->createPageWithForm([
            'submitActions' => ['message'],
            'customMessages' => true,
            'successMessage' => 'Saved settings were used.',
        ]);
        $node = $this->draftFormNode([
            'submitActions' => ['message', 'collect', 'redirect'],
            'customMessages' => true,
            'successMessage' => 'Unsaved settings were used.',
            'redirectUrl' => '/draft-thanks',
        ]);

        $response = $this->postJson('/pagebuilder-elementor/v2.4/form/editor-draft', [
            '__pb_editor_node' => json_encode($node, JSON_THROW_ON_ERROR),
            '__pb_editor_page' => 'contact-page',
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
            'message' => 'Current Canvas value',
        ]);

        $response->assertOk()->assertJson([
            'editorTest' => true,
            'message' => 'Unsaved settings were used.',
            'redirect' => '/draft-thanks',
        ]);
        $stored = DB::table('page_builder_elementor_form_submissions')->sole();
        $page = Page_Builder::query()->where('uri', 'contact-page')->firstOrFail();
        $meta = json_decode($stored->meta, true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame($page->getKey(), $stored->page_builder_id);
        $this->assertSame('contact-page', $stored->page_uri);
        $this->assertSame('edit', $meta['editor_mode']);
    }

    public function test_editor_draft_rejects_a_saved_page_owned_by_another_account(): void
    {
        $this->actingAsEditor(1);
        $this->createPageWithForm([], Page_Builder::EDITOR_VERSION_V24, 2);

        $this->postJson('/pagebuilder-elementor/v2.4/form/editor-draft', [
            '__pb_editor_node' => json_encode($this->draftFormNode(), JSON_THROW_ON_ERROR),
            '__pb_editor_page' => 'contact-page',
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertNotFound();
    }

    public function test_editor_draft_rejects_non_object_json_as_validation_error(): void
    {
        $this->actingAsEditor();

        $this->postJson('/pagebuilder-elementor/v2.4/form/editor-draft', [
            '__pb_editor_node' => json_encode('not-a-form-node', JSON_THROW_ON_ERROR),
            'name' => 'Aruna',
        ])->assertUnprocessable()->assertJsonValidationErrors('__pb_editor_node');

        $this->assertSame(0, DB::table('page_builder_elementor_form_submissions')->count());
    }

    public function test_server_validation_uses_the_saved_form_definition_before_running_actions(): void
    {
        Mail::fake();
        Http::fake();
        $this->createPageWithForm([
            'submitActions' => ['collect', 'email', 'webhook'],
            'emailTo' => 'owner@example.com',
            'webhookUrl' => 'https://hooks.example.com/forms',
        ]);

        $response = $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/form-contact', [
            'name' => '',
            'email' => 'not-an-email',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['name', 'email']);
        $this->assertSame(0, DB::table('page_builder_elementor_form_submissions')->count());
        Mail::assertNothingSent();
        Http::assertNothingSent();
    }

    public function test_form_submission_rejects_unknown_nodes_and_private_webhook_targets(): void
    {
        Mail::fake();
        Http::fake();
        $this->createPageWithForm([
            'submitActions' => ['webhook'],
            'webhookUrl' => 'http://127.0.0.1/internal',
        ]);

        $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/missing-node', [
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertNotFound();

        $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/form-contact', [
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertUnprocessable()->assertJson([
            'success' => false,
        ]);

        Http::assertNothingSent();
    }

    public function test_saved_option_numeric_and_file_constraints_cannot_be_bypassed(): void
    {
        $this->createPageWithForm([
            'submitActions' => ['message'],
            'fields' => [
                [
                    'id' => 'plan',
                    'label' => 'Plan',
                    'type' => 'select',
                    'required' => true,
                    'optionsText' => "Basic|basic\nPro|pro",
                ],
                [
                    'id' => 'score',
                    'label' => 'Score',
                    'type' => 'number',
                    'required' => true,
                    'min' => '1',
                    'max' => '10',
                ],
                [
                    'id' => 'resume',
                    'label' => 'Resume',
                    'type' => 'file',
                    'required' => true,
                    'fileTypes' => '.pdf,.jpg',
                ],
            ],
        ]);

        $response = $this->withHeader('Accept', 'application/json')->post(
            '/pagebuilder-elementor/v2.4/form/contact-page/form-contact',
            [
                'plan' => 'enterprise',
                'score' => '99',
                'resume' => UploadedFile::fake()->create('resume.exe', 10, 'application/octet-stream'),
            ],
        );

        $response->assertUnprocessable()->assertJsonValidationErrors(['plan', 'score', 'resume']);
    }

    public function test_conditional_required_field_is_validated_only_when_visible(): void
    {
        $this->createPageWithForm([
            'submitActions' => ['message'],
            'fields' => [
                ['id' => 'country', 'label' => 'Country', 'type' => 'select', 'required' => true, 'optionsText' => "Indonesia|ID\nMalaysia|MY"],
                [
                    'id' => 'province',
                    'label' => 'Province',
                    'type' => 'text',
                    'required' => true,
                    'conditionalLogic' => [
                        'enabled' => true,
                        'relation' => 'all',
                        'rules' => [['fieldId' => 'country', 'operator' => 'equals', 'value' => 'ID']],
                    ],
                ],
            ],
        ]);

        $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/form-contact', [
            'country' => 'MY',
        ])->assertOk();

        $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/form-contact', [
            'country' => 'ID',
        ])->assertUnprocessable()->assertJsonValidationErrors(['province']);
    }

    public function test_dataset_select_rejects_a_child_value_from_the_wrong_parent(): void
    {
        DB::table('pagebuilder_elementor_v24_form_datasets')->insert([
            'user_id' => 1,
            'name' => 'Locations',
            'slug' => 'locations',
            'schema_version' => 1,
            'nodes' => json_encode([
                ['id' => 'id', 'parentId' => null, 'label' => 'Indonesia', 'value' => 'ID', 'active' => true],
                ['id' => 'id-jb', 'parentId' => 'id', 'label' => 'Jawa Barat', 'value' => 'ID-JB', 'active' => true],
                ['id' => 'my', 'parentId' => null, 'label' => 'Malaysia', 'value' => 'MY', 'active' => true],
                ['id' => 'my-selangor', 'parentId' => 'my', 'label' => 'Selangor', 'value' => 'MY-10', 'active' => true],
            ], JSON_THROW_ON_ERROR),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->createPageWithForm([
            'submitActions' => ['message'],
            'fields' => [
                ['id' => 'country', 'label' => 'Country', 'type' => 'select', 'datasetMode' => 'dataset', 'datasetId' => 1, 'required' => true],
                ['id' => 'province', 'label' => 'Province', 'type' => 'select', 'datasetMode' => 'dataset', 'datasetId' => 1, 'datasetParentFieldId' => 'country', 'required' => true],
            ],
        ]);

        $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/form-contact', [
            'country' => 'ID',
            'province' => 'MY-10',
        ])->assertUnprocessable()->assertJsonValidationErrors(['province']);
    }

    public function test_saved_form_renderer_exposes_only_its_server_submit_endpoint(): void
    {
        $this->createPageWithForm(['submitActions' => ['message', 'email']]);
        $page = Page_Builder::query()->where('uri', 'contact-page')->firstOrFail();
        $node = json_decode($page->vars, true, flags: JSON_THROW_ON_ERROR)[0]['children'][0];
        $submitUrl = route('cms.core.pagebuilder_elementor_v24.form.submit', [
            'idOrSlug' => 'contact-page',
            'nodeId' => 'form-contact',
        ]);

        $html = $this->pageBuilderV24ModuleViewByType('form', [
            'node' => $node,
            'pageData' => $page,
        ])->render();

        $this->assertStringContainsString('method="post"', $html);
        $this->assertStringContainsString('enctype="multipart/form-data"', $html);
        $this->assertStringContainsString('action="'.$submitUrl.'"', $html);
        $this->assertStringContainsString('name="_token"', $html);
        $this->assertStringContainsString(e($submitUrl), $html);
        $this->assertStringNotContainsString('emailTo', $html);
        $this->assertStringNotContainsString('webhookUrl', $html);
    }

    public function test_product_lead_renderer_infers_query_ancestors_and_renders_media_selectors_and_trusted_values(): void
    {
        $datasetId = $this->createProductDataset();
        $this->createPageWithProductLeadForm($datasetId);
        $page = Page_Builder::query()->where('uri', 'contact-page')->firstOrFail();
        $node = json_decode($page->vars, true, flags: JSON_THROW_ON_ERROR)[0]['children'][0];
        request()->query->replace(['variant' => 'long-range', 'utm_source' => 'qa']);

        try {
            $html = $this->pageBuilderV24ModuleViewByType('product_lead_form', [
                'node' => $node,
                'pageData' => $page,
            ])->render();
        } finally {
            request()->query->replace([]);
        }

        $this->assertStringContainsString('data-product-lead-form', $html);
        $this->assertStringContainsString('data-product-lead-config=', $html);
        $this->assertStringContainsString('data-product-level="model"', $html);
        $this->assertStringContainsString('data-product-node-id="model-s5"', $html);
        $this->assertStringContainsString('data-product-label-placement="below"', $html);
        $this->assertStringContainsString('--product-card-height:auto', $html);
        $this->assertStringContainsString('--product-card-image-radius:50%', $html);
        $this->assertStringContainsString('--product-card-check-icon-size:10px', $html);
        $this->assertStringContainsString('--product-card-label-gap:12px', $html);
        $this->assertStringContainsString('--product-card-border-width-hover:1px', $html);
        $this->assertStringContainsString('--product-card-border-width-selected:1px', $html);
        $this->assertStringContainsString('name="product_model" value="MGS5_EV"', $html);
        $this->assertStringContainsString('name="product_type" value="LUXURY"', $html);
        $this->assertStringContainsString('name="product_variant" value="LONG_RANGE"', $html);
        $this->assertStringContainsString('src="/assets/s5-luxury.webp"', $html);
        $this->assertStringContainsString('Because Everyone Matters', $html);
        $this->assertStringContainsString('Explore Long Range', $html);
        $this->assertStringContainsString('action="'.route('cms.core.pagebuilder_elementor_v24.form.submit', ['idOrSlug' => 'contact-page', 'nodeId' => 'product-lead-contact']).'"', $html);
    }

    public function test_product_lead_renderer_applies_selected_card_dimensions_spacing_and_check_icon_style(): void
    {
        $datasetId = $this->createProductDataset();
        $this->createPageWithProductLeadForm($datasetId, [
            'productLevelStyles' => [
                [
                    'cardWidth' => '42%',
                    'cardWidthTablet' => '60%',
                    'cardHeightMode' => 'fixed',
                    'cardHeight' => '240px',
                    'cardPadding' => '20px 24px',
                    'cardMargin' => '4px',
                    'selectedBorderColor' => '#123456',
                    'hoverBorderColor' => '#abcdef',
                    'hoverBorderWidth' => '3px',
                    'selectedBorderWidth' => '4px',
                    'selectedBackground' => '#fef3c7',
                    'selectedCheckVisible' => true,
                    'selectedCheckPosition' => 'bottom-left',
                    'selectedCheckSize' => '24px',
                    'selectedCheckColor' => '#ffffff',
                    'selectedCheckBackground' => '#d97706',
                ],
            ],
        ]);
        $page = Page_Builder::query()->where('uri', 'contact-page')->firstOrFail();
        request()->query->replace(['model' => 'mgs5ev']);

        try {
            $html = $this->pageBuilderV24ModuleViewByType('product_lead_form', [
                'node' => json_decode($page->vars, true, flags: JSON_THROW_ON_ERROR)[0]['children'][0],
                'pageData' => $page,
            ])->render();
        } finally {
            request()->query->replace([]);
        }

        $this->assertStringContainsString('--product-card-width:42%', $html);
        $this->assertStringContainsString('--product-card-height:240px', $html);
        $this->assertStringContainsString('--product-card-padding:20px 24px', $html);
        $this->assertStringContainsString('--product-card-margin:4px', $html);
        $this->assertStringContainsString('--product-card-border-selected:#123456', $html);
        $this->assertStringContainsString('--product-card-bg-selected:#fef3c7', $html);
        $this->assertStringContainsString('product-card-check is-bottom-left fas fa-check', $html);
        $this->assertStringContainsString('--product-card-check-size:24px', $html);
        $this->assertStringContainsString('--product-card-check-background:#d97706', $html);
        $this->assertStringContainsString('--product-card-width:60%', $html);
        $this->assertStringContainsString('--product-card-border-hover:#abcdef', $html);
        $this->assertStringContainsString('--product-card-border-width-hover:3px', $html);
        $this->assertStringContainsString('--product-card-border-width-selected:4px', $html);
    }

    public function test_product_lead_renderer_supports_title_placements_and_responsive_form_alignment(): void
    {
        $datasetId = $this->createProductDataset();
        $this->createPageWithProductLeadForm($datasetId);
        $page = Page_Builder::query()->where('uri', 'contact-page')->firstOrFail();

        $cases = [
            ['placement' => 'media-above', 'descriptionPlacement' => 'media-above', 'media' => 'right', 'align' => 'right', 'gap' => '12px', 'formAlign' => 'bottom'],
            ['placement' => 'media-below', 'descriptionPlacement' => 'media-below', 'media' => 'left', 'align' => 'left', 'gap' => '4px', 'formAlign' => 'top'],
            ['placement' => 'form-above', 'descriptionPlacement' => 'form-above', 'media' => 'right', 'align' => 'center', 'gap' => '8px', 'formAlign' => 'center'],
        ];

        foreach ($cases as $case) {
            $node = $this->productLeadNode($datasetId, [
                'productTitlePlacement' => $case['placement'],
                'productDescriptionPlacement' => $case['descriptionPlacement'],
                'productMediaPosition' => $case['media'],
                'productTitleAlign' => $case['align'],
                'productTitleAlignTablet' => 'center',
                'productTitleAlignMobile' => 'right',
                'productTitleGap' => $case['gap'],
                'productFormVerticalAlign' => $case['formAlign'],
                'productFormVerticalAlignTablet' => 'center',
                'productFormVerticalAlignMobile' => 'bottom',
            ]);

            $html = $this->pageBuilderV24ModuleViewByType('product_lead_form', [
                'node' => $node,
                'pageData' => $page,
            ])->render();
            $markup = explode('<style>', $html, 2)[0];

            $this->assertStringContainsString('data-title-placement="'.$case['placement'].'"', $html);
            $this->assertStringContainsString('data-description-placement="'.$case['descriptionPlacement'].'"', $html);
            $this->assertStringContainsString('data-media-position="'.$case['media'].'"', $html);
            $this->assertStringContainsString('--product-title-align:'.$case['align'], $html);
            $this->assertStringContainsString('--product-title-gap:'.$case['gap'], $html);
            $this->assertStringContainsString('--product-form-vertical-align:', $html);

            $titlePosition = strpos($markup, 'data-product-title');
            $descriptionPosition = strpos($markup, 'data-product-description');
            $imagePosition = strpos($markup, 'data-product-main-image');
            $this->assertIsInt($titlePosition);
            $this->assertIsInt($descriptionPosition);
            $this->assertIsInt($imagePosition);

            if ($case['placement'] === 'media-above') {
                $this->assertLessThan($imagePosition, $titlePosition);
                $this->assertStringNotContainsString('pb-product-lead__form-title', $markup);
            } elseif ($case['placement'] === 'media-below') {
                $this->assertGreaterThan($imagePosition, $titlePosition);
                $this->assertStringNotContainsString('pb-product-lead__form-title', $markup);
            } else {
                $this->assertStringContainsString('class="pb-product-lead__form-title" data-product-title', $markup);
            }

            if ($case['descriptionPlacement'] === 'media-above') {
                $this->assertLessThan($imagePosition, $descriptionPosition);
                $this->assertStringNotContainsString('pb-product-lead__form-description', $markup);
            } elseif ($case['descriptionPlacement'] === 'media-below') {
                $this->assertGreaterThan($imagePosition, $descriptionPosition);
                $this->assertStringNotContainsString('pb-product-lead__form-description', $markup);
            } else {
                $this->assertStringContainsString('class="pb-product-lead__form-description" data-product-description', $markup);
            }

            $this->assertLessThan($descriptionPosition, $titlePosition, 'Title must precede description when both use the same placement.');
        }
    }

    public function test_invalid_action_configuration_fails_before_any_side_effect(): void
    {
        Mail::fake();
        $this->createPageWithForm([
            'submitActions' => ['collect', 'email'],
            'emailTo' => 'not-an-email',
        ]);

        $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/form-contact', [
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertUnprocessable();

        $this->assertSame(0, DB::table('page_builder_elementor_form_submissions')->count());
        Mail::assertNothingSent();
    }

    public function test_v24_form_route_rejects_a_v20_page_without_side_effects(): void
    {
        Mail::fake();
        Http::fake();
        $this->createPageWithForm([
            'submitActions' => ['collect', 'email', 'webhook'],
            'emailTo' => 'owner@example.com',
            'webhookUrl' => 'https://hooks.example.com/forms',
        ], Page_Builder::EDITOR_VERSION_V20);

        $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/form-contact', [
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertStatus(409)->assertJson([
            'success' => false,
            'editorVersion' => Page_Builder::EDITOR_VERSION_V20,
        ]);

        $this->assertSame(0, DB::table('page_builder_elementor_form_submissions')->count());
        Mail::assertNothingSent();
        Http::assertNothingSent();
    }

    public function test_v24_form_route_keeps_the_submission_throttle(): void
    {
        $route = app('router')->getRoutes()->getByName('cms.core.pagebuilder_elementor_v24.form.submit');

        $this->assertNotNull($route);
        $this->assertContains('throttle:20,1', $route->gatherMiddleware());
    }

    public function test_form_backend_endpoints_fail_closed_when_the_form_module_is_inactive(): void
    {
        Mail::fake();
        Http::fake();
        $this->actingAsEditor();
        $this->app->instance(
            ModuleCatalog::class,
            new ModuleCatalog(base_path('tests/Fixtures/PageBuilderElementorV24ModulesMissing')),
        );

        $this->postJson(route('cms.core.pagebuilder_elementor_v24.form.editor_draft'), [
            '__pb_editor_node' => '{}',
        ])->assertNotFound();

        $this->postJson(route('cms.core.pagebuilder_elementor_v24.form.submit', [
            'idOrSlug' => 'missing-page',
            'nodeId' => 'missing-form',
        ]), [])->assertNotFound();

        $this->getJson(route('cms.core.pagebuilder_elementor_v24.datasets.index'))
            ->assertNotFound();

        $this->postJson(route('cms.core.pagebuilder_elementor_v24.datasets.store'), [
            'name' => 'Must not persist',
            'schemaVersion' => 1,
            'nodes' => [],
        ])->assertNotFound();

        $this->assertSame(0, DB::table('pagebuilder_elementor_v24_form_datasets')->count());
        $this->assertSame(0, DB::table('page_builder_elementor_form_submissions')->count());
        Mail::assertNothingSent();
        Http::assertNothingSent();
    }

    public function test_v24_form_discards_an_unsafe_redirect_target(): void
    {
        $this->createPageWithForm([
            'submitActions' => ['message', 'redirect'],
            'redirectUrl' => 'javascript:alert(1)',
        ]);

        $this->postJson('/pagebuilder-elementor/v2.4/form/contact-page/form-contact', [
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertOk()->assertJson([
            'success' => true,
            'redirect' => '',
        ]);
    }

    private function createPageWithForm(array $settings, string $editorVersion = Page_Builder::EDITOR_VERSION_V24, int $ownerId = 1): void
    {
        $form = $this->draftFormNode($settings);

        DB::table('page_builder')->insert([
            'user_id' => $ownerId,
            'uri' => 'contact-page',
            'page_name' => 'Contact Page',
            'custom_css' => '',
            'vars' => json_encode([[
                'id' => 'root-container',
                'type' => 'container',
                'children' => [$form],
            ]], JSON_THROW_ON_ERROR),
            'status' => 'publish',
            'editor_version' => $editorVersion,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createPageWithProductLeadForm(int $datasetId, array $settings = [], int $ownerId = 1): void
    {
        DB::table('page_builder')->insert([
            'user_id' => $ownerId,
            'uri' => 'contact-page',
            'page_name' => 'Product Lead Page',
            'custom_css' => '',
            'vars' => json_encode([[
                'id' => 'root-container',
                'type' => 'container',
                'children' => [$this->productLeadNode($datasetId, $settings)],
            ]], JSON_THROW_ON_ERROR),
            'status' => 'publish',
            'editor_version' => Page_Builder::EDITOR_VERSION_V24,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function productLeadNode(int $datasetId, array $settings = []): array
    {
        return [
            'id' => 'product-lead-contact',
            'type' => 'product_lead_form',
            'settings' => array_merge([
                'formName' => 'Product Lead Form',
                'productData' => ['datasetMode' => 'dataset', 'datasetId' => (string) $datasetId],
                'productLevelCount' => 3,
                'productLevels' => [
                    ['key' => 'model', 'label' => 'Model', 'fieldId' => 'product_model', 'queryKey' => 'model', 'presentation' => 'cards', 'required' => true, 'defaultNodeId' => ''],
                    ['key' => 'type', 'label' => 'Type', 'fieldId' => 'product_type', 'queryKey' => 'type', 'presentation' => 'select', 'required' => true, 'defaultNodeId' => ''],
                    ['key' => 'variant', 'label' => 'Variant', 'fieldId' => 'product_variant', 'queryKey' => 'variant', 'presentation' => 'select', 'required' => true, 'defaultNodeId' => ''],
                ],
                'fields' => [
                    ['id' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['id' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                ],
                'submitActions' => ['message'],
                'successMessage' => 'The form was sent successfully.',
                'errorMessage' => 'An error occurred.',
            ], $settings),
        ];
    }

    private function createProductDataset(): int
    {
        $now = now();

        return DB::table('pagebuilder_elementor_v24_form_datasets')->insertGetId([
            'user_id' => 1,
            'name' => 'Vehicle Catalog',
            'slug' => 'vehicle-catalog',
            'schema_version' => 1,
            'nodes' => json_encode([
                ['id' => 'model-s5', 'parentId' => null, 'label' => 'MGS5 EV', 'code' => 'mgs5ev', 'value' => 'MGS5_EV', 'active' => true, 'sortOrder' => 1, 'meta' => ['thumbnailSource' => 'ckfinder', 'thumbnailUrl' => '/assets/s5-thumb.webp', 'thumbnailAlt' => 'MGS5 EV thumbnail', 'imageSource' => 'ckfinder', 'imageUrl' => '/assets/s5.webp', 'imageAlt' => 'MGS5 EV', 'description' => 'Because Everyone Matters', 'detailUrl' => '/models/mgs5ev', 'detailLabel' => 'Learn More']],
                ['id' => 'type-luxury', 'parentId' => 'model-s5', 'label' => 'Luxury', 'code' => 'luxury', 'value' => 'LUXURY', 'active' => true, 'sortOrder' => 1, 'meta' => ['imageSource' => 'ckfinder', 'imageUrl' => '/assets/s5-luxury.webp', 'imageAlt' => 'MGS5 EV Luxury']],
                ['id' => 'variant-long-range', 'parentId' => 'type-luxury', 'label' => 'Long Range', 'code' => 'long-range', 'value' => 'LONG_RANGE', 'active' => true, 'sortOrder' => 1, 'meta' => ['detailLabel' => 'Explore Long Range']],
                ['id' => 'model-zs', 'parentId' => null, 'label' => 'MG ZS', 'code' => 'mgzs', 'value' => 'MG_ZS', 'active' => true, 'sortOrder' => 2],
                ['id' => 'type-activate', 'parentId' => 'model-zs', 'label' => 'Activate', 'code' => 'activate', 'value' => 'ACTIVATE', 'active' => true, 'sortOrder' => 1],
                ['id' => 'variant-hidden', 'parentId' => 'type-activate', 'label' => 'Hidden', 'code' => 'hidden', 'value' => 'HIDDEN', 'active' => false, 'sortOrder' => 1],
            ], JSON_THROW_ON_ERROR),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function draftFormNode(array $settings = []): array
    {
        return [
            'id' => 'form-contact',
            'type' => 'form',
            'settings' => array_merge([
                'formName' => 'Contact Form',
                'fields' => [
                    ['id' => 'name', 'label' => 'Name', 'type' => 'text', 'required' => true],
                    ['id' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                    ['id' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => false],
                ],
                'submitActions' => ['message'],
                'successMessage' => 'The form was sent successfully.',
                'errorMessage' => 'An error occurred.',
            ], $settings),
        ];
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
        $this->actingAs($account);
    }
}
