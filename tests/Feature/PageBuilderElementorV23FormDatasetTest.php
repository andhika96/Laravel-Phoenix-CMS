<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PageBuilderElementorV23FormDatasetTest extends TestCase
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

        Schema::create('page_builder', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(1);
            $table->string('uri')->unique();
            $table->string('page_name')->nullable();
            $table->text('custom_css')->nullable();
            $table->text('vars');
            $table->string('status')->default('publish');
            $table->string('editor_version', 10)->default('2.0');
            $table->timestamps();
        });

        $this->actingAsEditor();
    }

    protected function tearDown(): void
    {
        DB::purge('sqlite');
        config(['database.default' => $this->originalConnection]);
        DB::setDefaultConnection($this->originalConnection);

        parent::tearDown();
    }

    public function test_dataset_api_lists_creates_and_updates_shared_parent_child_data(): void
    {
        $this->getJson('/pagebuilder-elementor/v2.3/datasets')
            ->assertOk()
            ->assertJsonPath('data', []);

        $created = $this->postJson('/pagebuilder-elementor/v2.3/datasets', [
            'name' => 'Location Starter',
            'schemaVersion' => 1,
            'nodes' => [
                ['id' => 'id', 'parentId' => null, 'label' => 'Indonesia', 'code' => 'ID'],
                ['id' => 'id-jb', 'parentId' => 'id', 'label' => 'Jawa Barat', 'code' => 'ID-JB'],
            ],
        ]);

        $created->assertCreated()
            ->assertJsonPath('data.name', 'Location Starter')
            ->assertJsonPath('data.nodes.0.value', 'ID')
            ->assertJsonPath('data.nodes.1.parentId', 'id');

        $datasetId = $created->json('data.id');

        $this->getJson('/pagebuilder-elementor/v2.3/datasets')
            ->assertOk()
            ->assertJsonCount(1, 'data');

        $this->putJson('/pagebuilder-elementor/v2.3/datasets/'.$datasetId, [
            'name' => 'Updated Locations',
            'nodes' => [
                ['id' => 'id', 'parentId' => null, 'label' => 'Indonesia', 'value' => 'ID'],
            ],
        ])->assertOk()->assertJsonPath('data.name', 'Updated Locations');

        $this->assertSame('Updated Locations', DB::table('pagebuilder_elementor_v23_form_datasets')->value('name'));
    }

    public function test_dataset_api_rejects_invalid_hierarchy_without_persisting_it(): void
    {
        $this->postJson('/pagebuilder-elementor/v2.3/datasets', [
            'name' => 'Broken Dataset',
            'nodes' => [
                ['id' => 'same', 'parentId' => 'same', 'label' => 'Broken'],
                ['id' => 'same', 'parentId' => 'missing', 'label' => 'Duplicate'],
            ],
        ])->assertUnprocessable()->assertJsonPath('success', false);

        $this->assertSame(0, DB::table('pagebuilder_elementor_v23_form_datasets')->count());
    }

    public function test_delete_removes_dataset_and_disconnects_only_owned_v23_page_fields(): void
    {
        $now = now();
        $datasetId = DB::table('pagebuilder_elementor_v23_form_datasets')->insertGetId([
            'user_id' => 1,
            'name' => 'Location Dataset',
            'slug' => 'location-dataset',
            'schema_version' => 1,
            'nodes' => json_encode([['id' => 'id', 'parentId' => null, 'label' => 'Indonesia', 'value' => 'ID']]),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $v23Layout = [[
            'id' => 'form-root',
            'type' => 'form',
            'settings' => [
                'fields' => [
                    ['id' => 'country', 'type' => 'select', 'datasetMode' => 'dataset', 'datasetId' => $datasetId],
                    ['id' => 'province', 'type' => 'select', 'datasetMode' => 'dataset', 'datasetId' => (string) $datasetId, 'datasetParentFieldId' => 'country'],
                ],
            ],
        ]];
        $legacyLayout = [[
            'id' => 'legacy-form',
            'type' => 'form',
            'settings' => ['fields' => [['id' => 'legacy', 'datasetMode' => 'dataset', 'datasetId' => $datasetId]],],
        ]];

        DB::table('page_builder')->insert([
            ['user_id' => 1, 'uri' => 'owned-v23', 'page_name' => 'Owned V23', 'vars' => json_encode($v23Layout), 'editor_version' => '2.3', 'status' => 'publish', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => 1, 'uri' => 'owned-v20', 'page_name' => 'Owned V20', 'vars' => json_encode($legacyLayout), 'editor_version' => '2.0', 'status' => 'publish', 'created_at' => $now, 'updated_at' => $now],
            ['user_id' => 2, 'uri' => 'other-v23', 'page_name' => 'Other V23', 'vars' => json_encode($legacyLayout), 'editor_version' => '2.3', 'status' => 'publish', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $this->deleteJson('/pagebuilder-elementor/v2.3/datasets/'.$datasetId)
            ->assertOk()
            ->assertJsonPath('data.id', $datasetId)
            ->assertJsonPath('data.disconnectedFields', 2);

        $this->assertDatabaseMissing('pagebuilder_elementor_v23_form_datasets', ['id' => $datasetId]);

        $owned = json_decode((string) DB::table('page_builder')->where('uri', 'owned-v23')->value('vars'), true, flags: JSON_THROW_ON_ERROR);
        $this->assertSame('static', $owned[0]['settings']['fields'][0]['datasetMode']);
        $this->assertSame('', $owned[0]['settings']['fields'][0]['datasetId']);
        $this->assertSame('static', $owned[0]['settings']['fields'][1]['datasetMode']);
        $this->assertSame('', $owned[0]['settings']['fields'][1]['datasetParentFieldId']);

        $this->assertStringContainsString('"datasetMode":"dataset"', (string) DB::table('page_builder')->where('uri', 'owned-v20')->value('vars'));
        $this->assertStringContainsString('"datasetMode":"dataset"', (string) DB::table('page_builder')->where('uri', 'other-v23')->value('vars'));
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
