<?php

namespace Tests\Feature;

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
}
