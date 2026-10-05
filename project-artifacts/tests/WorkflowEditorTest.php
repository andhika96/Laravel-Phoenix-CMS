<?php

namespace Tests\Feature;

use App\Models\Awesome_Admin\Account;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class WorkflowEditorTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'cache.default' => 'array']);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');
        Schema::create('accounts', function (Blueprint $table) {
            $table->id(); $table->string('email')->nullable(); $table->string('username')->nullable();
            $table->string('fullname')->nullable(); $table->string('password')->nullable();
            $table->timestamp('suspended_at')->nullable(); $table->tinyInteger('status')->default(2); $table->timestamps();
        });
        Schema::create('roles', function (Blueprint $table) { $table->id(); $table->string('name'); $table->string('guard_name'); $table->timestamps(); });
        Schema::create('model_has_roles', function (Blueprint $table) { $table->unsignedBigInteger('role_id'); $table->string('model_type'); $table->unsignedBigInteger('model_id'); });
        if (is_file(database_path('migrations/2026_10_05_000001_create_workflow_tables.php'))) {
            (require database_path('migrations/2026_10_05_000001_create_workflow_tables.php'))->up();
        }
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    private function actor(bool $admin = true): Account
    {
        $user = Account::create(['email' => uniqid().'@example.test', 'username' => uniqid(), 'fullname' => 'Workflow tester']);
        if ($admin) {
            $role = DB::table('roles')->insertGetId(['name' => 'Administrator', 'guard_name' => 'web']);
            DB::table('model_has_roles')->insert(['role_id' => $role, 'model_type' => Account::class, 'model_id' => $user->id]);
        }
        return $user;
    }

    private function graph(): array
    {
        return ['nodes' => [
            ['id' => 'start', 'type' => 'trigger', 'label' => 'Start', 'x' => 40, 'y' => 40, 'config' => ['event' => 'manual']],
            ['id' => 'finish', 'type' => 'end', 'label' => 'End', 'x' => 400, 'y' => 40, 'config' => []],
        ], 'edges' => [['id' => 'edge1', 'source' => 'start', 'target' => 'finish', 'port' => 'out']], 'viewport' => ['x' => 0, 'y' => 0, 'zoom' => 1]];
    }

    private function createWorkflow(array $graph = []): array
    {
        return $this->postJson('/workflow', ['name' => 'Pengajuan', 'category' => 'approval', 'graph' => $graph ?: $this->graph()])->assertCreated()->json('data');
    }

    public function test_guest_is_redirected_and_unprivileged_user_is_denied(): void
    {
        $this->get('/workflow')->assertRedirect('/auth/login');
        $this->actingAs($this->actor(false), 'web')->getJson('/workflow/data')->assertForbidden();
    }

    public function test_editor_breadcrumb_can_be_generated(): void
    {
        $items = \Diglactic\Breadcrumbs\Breadcrumbs::generate('workflow.editor');
        $this->assertCount(2, $items);
        $this->assertSame('Editor', $items->last()->title);
    }

    public function test_draft_save_conflict_publish_versions_duplicate_and_archive(): void
    {
        $this->actingAs($this->actor(), 'web');
        $workflow = $this->createWorkflow();
        $this->postJson('/workflow/'.$workflow['id'].'/publish', ['draft_revision' => 1])->assertOk()->assertJsonPath('data.published_version', 1)->assertJsonPath('data.published_revision', 1);
        $this->putJson('/workflow/'.$workflow['id'], ['name' => 'Revisi', 'category' => 'form', 'draft_revision' => 1, 'graph' => $this->graph()])->assertOk()->assertJsonPath('data.draft_revision', 2);
        $this->putJson('/workflow/'.$workflow['id'], ['name' => 'Stale', 'category' => 'form', 'draft_revision' => 1, 'graph' => $this->graph()])->assertConflict();
        $this->assertSame('Pengajuan', DB::table('workflow_versions')->first()->name);
        $copy = $this->postJson('/workflow/'.$workflow['id'].'/duplicate')->assertCreated()->assertJsonPath('data.status', 'draft')->json('data');
        $this->assertNotSame($workflow['id'], $copy['id']);
        $this->postJson('/workflow/'.$workflow['id'].'/archive', ['draft_revision' => 1])->assertConflict();
        $this->postJson('/workflow/'.$workflow['id'].'/archive', ['draft_revision' => 2])->assertOk()->assertJsonPath('data.status', 'archived');
        $this->postJson('/workflow/'.$workflow['id'].'/publish', ['draft_revision' => 2])->assertConflict();
    }

    public function test_draft_can_be_incomplete_but_publishing_rejects_cycles_and_orphans(): void
    {
        $this->actingAs($this->actor(), 'web');
        $workflow = $this->createWorkflow(['nodes' => [], 'edges' => []]);
        $this->postJson('/workflow/'.$workflow['id'].'/publish', ['draft_revision' => 1])->assertUnprocessable();
        $graph = $this->graph();
        $graph['nodes'][] = ['id' => 'orphan', 'type' => 'end', 'label' => 'Orphan', 'x' => 10, 'y' => 20, 'config' => []];
        $this->putJson('/workflow/'.$workflow['id'], ['name' => 'Orphan', 'category' => 'general', 'draft_revision' => 1, 'graph' => $graph])->assertOk();
        $this->postJson('/workflow/'.$workflow['id'].'/publish', ['draft_revision' => 2])->assertUnprocessable();
        $graph['edges'][] = ['id' => 'cycle', 'source' => 'finish', 'target' => 'start', 'port' => 'out'];
        $this->putJson('/workflow/'.$workflow['id'], ['name' => 'Cycle', 'category' => 'general', 'draft_revision' => 2, 'graph' => $graph])->assertOk();
        $this->postJson('/workflow/'.$workflow['id'].'/publish', ['draft_revision' => 3])->assertUnprocessable();
    }

    public function test_simulation_evaluates_conditions_records_snapshot_and_sends_nothing(): void
    {
        Http::preventStrayRequests(); Mail::fake();
        $this->actingAs($this->actor(), 'web');
        $graph = $this->graph();
        $graph['nodes'][] = ['id' => 'condition', 'type' => 'condition', 'label' => 'Nominal', 'x' => 200, 'y' => 0, 'config' => ['field' => 'amount', 'operator' => 'gte', 'value' => '100']];
        $graph['nodes'][] = ['id' => 'email', 'type' => 'email', 'label' => 'Email', 'x' => 400, 'y' => 0, 'config' => ['to' => 'test@example.test', 'subject' => 'Result']];
        $graph['edges'] = [
            ['id' => 'a', 'source' => 'start', 'target' => 'condition', 'port' => 'out'],
            ['id' => 'b', 'source' => 'condition', 'target' => 'email', 'port' => 'true'],
            ['id' => 'c', 'source' => 'condition', 'target' => 'finish', 'port' => 'false'],
            ['id' => 'd', 'source' => 'email', 'target' => 'finish', 'port' => 'out'],
        ];
        $workflow = $this->createWorkflow($graph);
        $run = $this->postJson('/workflow/'.$workflow['id'].'/simulate', ['draft_revision' => 1, 'context' => ['amount' => 150]])->assertCreated()->assertJsonPath('data.mode', 'simulation')->assertJsonPath('data.status', 'completed')->json('data');
        $this->assertContains('email', array_column($run['logs'], 'node_id'));
        $run2 = $this->postJson('/workflow/'.$workflow['id'].'/simulate', ['draft_revision' => 1, 'context' => ['amount' => 20]])->assertCreated()->json('data');
        $this->assertNotContains('email', array_column($run2['logs'], 'node_id'));
        $this->assertSame(2, DB::table('workflow_runs')->count());
        $this->assertSame(0, DB::table('workflow_approval_tasks')->count());
        $this->assertSame($graph['nodes'], json_decode(DB::table('workflow_runs')->first()->graph_snapshot, true)['nodes']);
        Mail::assertNothingSent(); Http::assertNothingSent();
    }

    public function test_shape_validation_blocks_dangling_edges_unknown_types_and_duplicate_ids(): void
    {
        $this->actingAs($this->actor(), 'web');
        $graph = $this->graph(); $graph['edges'][0]['target'] = 'missing';
        $this->postJson('/workflow', ['name' => 'Bad', 'category' => 'general', 'graph' => $graph])->assertUnprocessable();
        $graph = $this->graph(); $graph['nodes'][0]['type'] = 'php';
        $this->postJson('/workflow', ['name' => 'Bad', 'category' => 'general', 'graph' => $graph])->assertUnprocessable();
        $graph = $this->graph(); $graph['nodes'][1]['id'] = 'start';
        $this->postJson('/workflow', ['name' => 'Bad', 'category' => 'general', 'graph' => $graph])->assertUnprocessable();
    }

    public function test_schema_can_roll_back_without_touching_accounts(): void
    {
        $this->assertTrue(Schema::hasTable('workflow_approval_tasks'));
        (require database_path('migrations/2026_10_05_000001_create_workflow_tables.php'))->down();
        $this->assertFalse(Schema::hasTable('workflows'));
        $this->assertTrue(Schema::hasTable('accounts'));
    }

    public function test_custom_permissions_are_owner_scoped_and_suspended_accounts_are_denied(): void
    {
        Schema::create('custom_permissions', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('role_id'); $table->string('menu_link'); $table->text('permissions');
        });
        $owner = $this->actor(false);
        $other = $this->actor(false);
        $role = DB::table('roles')->insertGetId(['name' => 'Workflow Editor', 'guard_name' => 'web']);
        foreach ([$owner, $other] as $user) {
            DB::table('model_has_roles')->insert(['role_id' => $role, 'model_type' => Account::class, 'model_id' => $user->id]);
        }
        DB::table('custom_permissions')->insert(['role_id' => $role, 'menu_link' => 'workflow', 'permissions' => json_encode(['read data', 'add data', 'edit data'])]);
        $this->actingAs($owner, 'web');
        $workflow = $this->createWorkflow();
        $this->actingAs($other, 'web')->getJson('/workflow/data')->assertOk()->assertJsonCount(0, 'data');
        $this->putJson('/workflow/'.$workflow['id'], ['name' => 'Unauthorized', 'category' => 'general', 'draft_revision' => 1, 'graph' => $this->graph()])->assertForbidden();
        $this->getJson('/workflow/'.$workflow['id'].'/runs')->assertForbidden();
        $this->postJson('/workflow/'.$workflow['id'].'/duplicate')->assertForbidden();
        $owner->suspended_at = now(); $owner->save();
        $this->actingAs($owner, 'web')->getJson('/workflow/data')->assertForbidden();
    }

    public function test_simulation_requires_current_revision_and_valid_config_and_publish_is_idempotent(): void
    {
        $this->actingAs($this->actor(), 'web');
        $workflow = $this->createWorkflow();
        $this->postJson('/workflow/'.$workflow['id'].'/simulate', ['draft_revision' => 2, 'context' => []])->assertConflict();
        foreach ([1, 2] as $attempt) {
            $this->postJson('/workflow/'.$workflow['id'].'/publish', ['draft_revision' => 1])->assertOk();
        }
        $this->assertSame(1, DB::table('workflow_versions')->count());
        $this->getJson('/workflow/data?search=Pengajuan&status=published')->assertOk()->assertJsonCount(1, 'data');
        $graph = $this->graph(); $graph['nodes'][0]['config'] = ['event' => 'arbitrary.php'];
        $this->putJson('/workflow/'.$workflow['id'], ['name' => 'Invalid config', 'category' => 'general', 'draft_revision' => 1, 'graph' => $graph])->assertOk();
        $this->postJson('/workflow/'.$workflow['id'].'/publish', ['draft_revision' => 2])->assertUnprocessable();
        $this->postJson('/workflow/'.$workflow['id'].'/simulate', ['draft_revision' => 2, 'context' => []])->assertUnprocessable();
        $this->assertSame(0, DB::table('workflow_runs')->count());
    }

    public function test_list_search_includes_description(): void
    {
        $this->actingAs($this->actor(), 'web');
        $workflow = $this->createWorkflow();
        DB::table('workflows')->where('id', $workflow['id'])->update(['description' => 'Review pengadaan']);
        $this->getJson('/workflow/data?search=pengadaan')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_write_requires_csrf_token_outside_testing_environment(): void
    {
        $this->withMiddleware();
        app()->instance('env', 'local');
        $this->actingAs($this->actor(), 'web');
        $payload = ['name' => 'CSRF verified', 'category' => 'general', 'graph' => $this->graph()];
        $this->postJson('/workflow', $payload)->assertStatus(419);
        $this->withSession(['_token' => 'workflow-test-token'])->postJson('/workflow', $payload, ['X-CSRF-TOKEN' => 'workflow-test-token'])->assertCreated();
    }

    public function test_signed_and_far_canvas_coordinates_are_saved_without_clamping(): void
    {
        $this->actingAs($this->actor(), 'web');
        $graph = $this->graph();
        $graph['nodes'][0]['x'] = -800; $graph['nodes'][0]['y'] = -500;
        $graph['nodes'][1]['x'] = 3200; $graph['nodes'][1]['y'] = 2400;
        $graph['viewport']['zoom'] = 0.1;
        $workflow = $this->createWorkflow($graph);
        $this->assertSame($graph, $workflow['graph']);
    }

    private function visualNodes(): array
    {
        return [
            ['id'=>'table','type'=>'table','label'=>'Tabel tugas','x'=>-200,'y'=>-100,'config'=>[
                'rows'=>2,'columns'=>2,'cells'=>[['Nama','Status'],['<img src=x onerror=evil>','Pending']],
                'columnWidths'=>[120,180],'rowHeights'=>[42,60],'header'=>true,'fontSize'=>14,'align'=>'left',
                'textColor'=>'#243746','background'=>'#ffffff','headerBackground'=>'#e8f5ef','borderColor'=>'#b7c9c0',
            ]],
            ['id'=>'icon','type'=>'icon','label'=>'Bintang','x'=>200,'y'=>200,'config'=>['iconClass'=>'fal fa-star','color'=>'#1fa675','size'=>64,'width'=>160,'height'=>160]],
            ['id'=>'list','type'=>'icon-list','label'=>'Checklist','x'=>600,'y'=>200,'config'=>[
                'items'=>[['iconClass'=>'fas fa-check','text'=>'Pemeriksaan'],['iconClass'=>'fab fa-github','text'=>'Dokumentasi']],
                'color'=>'#1fa675','textColor'=>'#243746','fontSize'=>14,'gap'=>8,'width'=>280,
            ]],
        ];
    }

    public function test_visual_nodes_round_trip_through_save_publish_and_duplicate(): void
    {
        $this->actingAs($this->actor(),'web');
        $graph=$this->graph(); $graph['nodes']=array_merge($graph['nodes'],$this->visualNodes());
        $workflow=$this->createWorkflow($graph);
        $this->assertSame($graph,$workflow['graph']);
        $this->postJson('/workflow/'.$workflow['id'].'/publish',['draft_revision'=>1])->assertOk();
        $this->assertSame($graph,json_decode(DB::table('workflow_versions')->first()->graph,true));
        $copy=$this->postJson('/workflow/'.$workflow['id'].'/duplicate')->assertCreated()->json('data');
        $this->assertSame($graph,$copy['graph']);
        $run=$this->postJson('/workflow/'.$workflow['id'].'/simulate',['draft_revision'=>1,'context'=>[]])->assertCreated()->json('data');
        $this->assertSame(['start','finish'],array_column($run['logs'],'node_id'));
        $this->assertSame($graph,json_decode(DB::table('workflow_runs')->first()->graph_snapshot,true));
    }

    public function test_visual_only_diagram_can_publish_but_cannot_simulate(): void
    {
        $this->actingAs($this->actor(),'web');
        $workflow=$this->createWorkflow(['nodes'=>$this->visualNodes(),'edges'=>[]]);
        $this->postJson('/workflow/'.$workflow['id'].'/publish',['draft_revision'=>1])->assertOk();
        $this->postJson('/workflow/'.$workflow['id'].'/simulate',['draft_revision'=>1,'context'=>[]])->assertUnprocessable();
        $this->assertSame(0,DB::table('workflow_runs')->count());
    }

    public function test_empty_table_cells_and_icon_list_text_remain_strings_after_save(): void
    {
        $this->actingAs($this->actor(),'web');
        $nodes=$this->visualNodes();
        $nodes[0]['config']['cells'][1][0]='';
        $nodes[2]['config']['items'][0]['text']='';
        $workflow=$this->createWorkflow(['nodes'=>$nodes,'edges'=>[]]);
        $this->assertSame('', $workflow['graph']['nodes'][0]['config']['cells'][1][0]);
        $this->assertSame('', $workflow['graph']['nodes'][2]['config']['items'][0]['text']);
    }

    public function test_invalid_visual_config_and_visual_execution_edges_are_rejected(): void
    {
        $this->actingAs($this->actor(),'web');
        foreach (['count','cells','width','icon','color','edge','indexed-cells'] as $case) {
            $graph=$this->graph(); $graph['nodes']=array_merge($graph['nodes'],$this->visualNodes());
            if ($case==='count') $graph['nodes'][2]['config']['rows']=21;
            if ($case==='cells') $graph['nodes'][2]['config']['cells']=[['Wrong shape']];
            if ($case==='width') $graph['nodes'][2]['config']['columnWidths']=[10,180];
            if ($case==='icon') $graph['nodes'][3]['config']['iconClass']='fas fa-star onclick=evil';
            if ($case==='color') $graph['nodes'][4]['config']['color']='url(javascript:evil)';
            if ($case==='edge') $graph['edges'][]=['id'=>'bad','source'=>'start','target'=>'table','port'=>'out'];
            if ($case==='indexed-cells') $graph['nodes'][2]['config']['cells']=['wrong'=>['A','B'],'keys'=>['C','D']];
            $this->postJson('/workflow',['name'=>'Invalid','category'=>'diagram','graph'=>$graph])->assertUnprocessable();
        }
    }
}
