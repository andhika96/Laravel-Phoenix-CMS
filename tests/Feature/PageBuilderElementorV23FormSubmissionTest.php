<?php

namespace Tests\Feature;

use App\Mail\PageBuilderElementorV23FormMail;
use App\Models\Page_Builder\Page_Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PageBuilderElementorV23FormSubmissionTest extends TestCase
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

        Schema::create('pagebuilder_elementor_v23_form_datasets', function (Blueprint $table): void {
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

        $response = $this->postJson('/pagebuilder-elementor/v2.3/form/contact-page/form-contact', [
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

        Mail::assertSent(PageBuilderElementorV23FormMail::class, 2);
        Mail::assertSent(PageBuilderElementorV23FormMail::class, function (PageBuilderElementorV23FormMail $mail): bool {
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

    public function test_server_validation_uses_the_saved_form_definition_before_running_actions(): void
    {
        Mail::fake();
        Http::fake();
        $this->createPageWithForm([
            'submitActions' => ['collect', 'email', 'webhook'],
            'emailTo' => 'owner@example.com',
            'webhookUrl' => 'https://hooks.example.com/forms',
        ]);

        $response = $this->postJson('/pagebuilder-elementor/v2.3/form/contact-page/form-contact', [
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

        $this->postJson('/pagebuilder-elementor/v2.3/form/contact-page/missing-node', [
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertNotFound();

        $this->postJson('/pagebuilder-elementor/v2.3/form/contact-page/form-contact', [
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
            '/pagebuilder-elementor/v2.3/form/contact-page/form-contact',
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

        $this->postJson('/pagebuilder-elementor/v2.3/form/contact-page/form-contact', [
            'country' => 'MY',
        ])->assertOk();

        $this->postJson('/pagebuilder-elementor/v2.3/form/contact-page/form-contact', [
            'country' => 'ID',
        ])->assertUnprocessable()->assertJsonValidationErrors(['province']);
    }

    public function test_dataset_select_rejects_a_child_value_from_the_wrong_parent(): void
    {
        DB::table('pagebuilder_elementor_v23_form_datasets')->insert([
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

        $this->postJson('/pagebuilder-elementor/v2.3/form/contact-page/form-contact', [
            'country' => 'ID',
            'province' => 'MY-10',
        ])->assertUnprocessable()->assertJsonValidationErrors(['province']);
    }

    public function test_saved_form_renderer_exposes_only_its_server_submit_endpoint(): void
    {
        $this->createPageWithForm(['submitActions' => ['message', 'email']]);
        $page = Page_Builder::query()->where('uri', 'contact-page')->firstOrFail();
        $node = json_decode($page->vars, true, flags: JSON_THROW_ON_ERROR)[0]['children'][0];
        $submitUrl = route('cms.core.pagebuilder_elementor_v23.form.submit', [
            'idOrSlug' => 'contact-page',
            'nodeId' => 'form-contact',
        ]);

        $html = view('pagebuilder_elementor_v23.partials.render_pro_widget', [
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

    public function test_invalid_action_configuration_fails_before_any_side_effect(): void
    {
        Mail::fake();
        $this->createPageWithForm([
            'submitActions' => ['collect', 'email'],
            'emailTo' => 'not-an-email',
        ]);

        $this->postJson('/pagebuilder-elementor/v2.3/form/contact-page/form-contact', [
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertUnprocessable();

        $this->assertSame(0, DB::table('page_builder_elementor_form_submissions')->count());
        Mail::assertNothingSent();
    }

    public function test_v23_form_route_rejects_a_v20_page_without_side_effects(): void
    {
        Mail::fake();
        Http::fake();
        $this->createPageWithForm([
            'submitActions' => ['collect', 'email', 'webhook'],
            'emailTo' => 'owner@example.com',
            'webhookUrl' => 'https://hooks.example.com/forms',
        ], Page_Builder::EDITOR_VERSION_V20);

        $this->postJson('/pagebuilder-elementor/v2.3/form/contact-page/form-contact', [
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

    public function test_v23_form_route_keeps_the_submission_throttle(): void
    {
        $route = app('router')->getRoutes()->getByName('cms.core.pagebuilder_elementor_v23.form.submit');

        $this->assertNotNull($route);
        $this->assertContains('throttle:20,1', $route->gatherMiddleware());
    }

    public function test_v23_form_discards_an_unsafe_redirect_target(): void
    {
        $this->createPageWithForm([
            'submitActions' => ['message', 'redirect'],
            'redirectUrl' => 'javascript:alert(1)',
        ]);

        $this->postJson('/pagebuilder-elementor/v2.3/form/contact-page/form-contact', [
            'name' => 'Aruna',
            'email' => 'aruna@example.com',
        ])->assertOk()->assertJson([
            'success' => true,
            'redirect' => '',
        ]);
    }

    private function createPageWithForm(array $settings, string $editorVersion = Page_Builder::EDITOR_VERSION_V23): void
    {
        $form = [
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

        DB::table('page_builder')->insert([
            'user_id' => 1,
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
}
