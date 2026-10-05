<?php

namespace App\Http\Controllers\Web\Workflow;

use App\Http\Controllers\Controller;
use App\Http\Middleware\WorkflowAccess;
use App\Models\Workflow\Workflow;
use App\Models\Workflow\WorkflowRun;
use App\Support\Workflow\WorkflowGraph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkflowController extends Controller
{
    public function __construct(private readonly WorkflowGraph $graphs) {}

    public function index(Request $request)
    {
        return view('workflow.index', ['bootstrap' => [
            'urls' => ['index' => route('cms.core.workflow.index'), 'data' => route('cms.core.workflow.data'), 'create' => route('cms.core.workflow.create'), 'base' => url('workflow')],
            'permissions' => ['write' => WorkflowAccess::allows($request->user(), 'add data')],
        ]]);
    }

    public function create(Request $request)
    {
        return $this->editor($request);
    }

    public function edit(Request $request, Workflow $workflow)
    {
        $this->authorizeOwner($request, $workflow);
        return $this->editor($request, $workflow);
    }

    private function editor(Request $request, ?Workflow $workflow = null)
    {
        $urls = ['index' => route('cms.core.workflow.index'), 'store' => route('cms.core.workflow.store')];
        foreach (['update', 'publish', 'duplicate', 'archive', 'simulate', 'runs'] as $action) {
            $urls[$action] = $workflow ? route('cms.core.workflow.'.$action, $workflow) : null;
        }
        return view('workflow.editor', ['bootstrap' => [
            'workflow' => $workflow, 'urls' => $urls,
            'permissions' => ['write' => WorkflowAccess::allows($request->user(), $workflow ? 'edit data' : 'add data'), 'publish' => WorkflowAccess::allows($request->user(), 'edit data'), 'archive' => WorkflowAccess::allows($request->user(), 'delete data'), 'duplicate' => WorkflowAccess::allows($request->user(), 'add data')],
        ]]);
    }

    public function data(Request $request)
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:160'], 'category' => ['nullable', 'string', 'max:24'], 'status' => ['nullable', 'in:draft,published,archived']]);
        $query = Workflow::query();
        if (! $request->user()->hasRole(['Super Admin', 'Administrator'])) {
            $query->where('created_by', $request->user()->getKey());
        }
        if (! empty($filters['search'])) {
            $query->where(fn ($search) => $search->where('name', 'like', '%'.$filters['search'].'%')->orWhere('description', 'like', '%'.$filters['search'].'%'));
        }
        foreach (['category', 'status'] as $key) {
            if (! empty($filters[$key])) { $query->where($key, $filters[$key]); }
        }
        return response()->json($query->orderByDesc('updated_at')->orderByDesc('id')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $this->graphs->input($request);
        unset($data['draft_revision']);
        $workflow = Workflow::create($data + ['created_by' => $request->user()->getKey(), 'status' => 'draft', 'draft_revision' => 1]);
        return $this->saved($workflow, 201);
    }

    public function update(Request $request, Workflow $workflow)
    {
        $this->authorizeOwner($request, $workflow);
        $data = $this->graphs->input($request, true);
        DB::transaction(function () use ($workflow, $data): void {
            $locked = Workflow::whereKey($workflow->id)->lockForUpdate()->firstOrFail();
            $this->checkRevision($locked, $data['draft_revision']);
            $locked->fill($data)->forceFill(['draft_revision' => $locked->draft_revision + 1])->save();
        });
        return $this->saved($workflow->refresh());
    }

    public function publish(Request $request, Workflow $workflow)
    {
        $this->authorizeOwner($request, $workflow);
        $revision = $request->validate(['draft_revision' => ['required', 'integer', 'min:1']])['draft_revision'];
        DB::transaction(function () use ($workflow, $request, $revision): void {
            $locked = Workflow::whereKey($workflow->id)->lockForUpdate()->firstOrFail();
            $this->checkRevision($locked, $revision);
            $this->graphs->validatePublish($locked->graph);
            $already = DB::table('workflow_versions')->where('workflow_id', $locked->id)->where('draft_revision', $revision)->first();
            if ($already) { return; }
            $version = ($locked->published_version ?? 0) + 1;
            DB::table('workflow_versions')->insert([
                'workflow_id' => $locked->id, 'version' => $version, 'draft_revision' => $revision,
                'name' => $locked->name, 'description' => $locked->description, 'category' => $locked->category,
                'graph' => json_encode($locked->graph, JSON_THROW_ON_ERROR), 'published_by' => $request->user()->getKey(), 'created_at' => now(), 'updated_at' => now(),
            ]);
            $locked->forceFill(['status' => 'published', 'published_version' => $version, 'published_revision' => $revision, 'published_at' => now()])->save();
        });
        return $this->saved($workflow->refresh());
    }

    public function duplicate(Request $request, Workflow $workflow)
    {
        $this->authorizeOwner($request, $workflow);
        $copy = Workflow::create([
            'created_by' => $request->user()->getKey(), 'name' => mb_substr($workflow->name, 0, 150).' (salinan)',
            'description' => $workflow->description, 'category' => $workflow->category, 'graph' => $workflow->graph,
            'status' => 'draft', 'draft_revision' => 1,
        ]);
        return $this->saved($copy, 201);
    }

    public function archive(Request $request, Workflow $workflow)
    {
        $this->authorizeOwner($request, $workflow);
        $revision = $request->validate(['draft_revision' => ['required', 'integer', 'min:1']])['draft_revision'];
        DB::transaction(function () use ($workflow, $revision): void {
            $locked = Workflow::whereKey($workflow->id)->lockForUpdate()->firstOrFail();
            $this->checkRevision($locked, $revision);
            $locked->forceFill(['status' => 'archived', 'draft_revision' => $locked->draft_revision + 1])->save();
        });
        return $this->saved($workflow->refresh());
    }

    public function simulate(Request $request, Workflow $workflow)
    {
        $this->authorizeOwner($request, $workflow);
        $input = $request->validate(['draft_revision' => ['required', 'integer', 'min:1'], 'context' => ['present', 'array', 'max:100']]);
        abort_if(strlen(json_encode($input['context'], JSON_THROW_ON_ERROR)) > 65536, 422, 'Data simulasi terlalu besar.');
        $run = DB::transaction(function () use ($request, $workflow, $input): WorkflowRun {
            $locked = Workflow::whereKey($workflow->id)->lockForUpdate()->firstOrFail();
            $this->checkRevision($locked, $input['draft_revision']);
            $result = $this->graphs->simulate($locked->graph, $input['context']);
            $run = WorkflowRun::create([
                'workflow_id' => $locked->id, 'started_by' => $request->user()->getKey(), 'mode' => 'simulation', 'status' => 'completed',
                'draft_revision' => $locked->draft_revision, 'graph_snapshot' => $locked->graph, 'context' => $result['context'], 'started_at' => now(), 'finished_at' => now(),
            ]);
            foreach ($result['logs'] as $sequence => $log) { $run->logs()->create($log + ['sequence' => $sequence + 1]); }
            return $run->load('logs');
        });
        return response()->json(['data' => $run], 201);
    }

    public function runs(Request $request, Workflow $workflow)
    {
        $this->authorizeOwner($request, $workflow);
        return response()->json(['data' => WorkflowRun::where('workflow_id', $workflow->id)->with('logs')->latest('id')->limit(20)->get()]);
    }

    private function authorizeOwner(Request $request, Workflow $workflow): void
    {
        abort_unless($request->user()->hasRole(['Super Admin', 'Administrator']) || (int) $workflow->created_by === (int) $request->user()->getKey(), 403);
    }

    private function checkRevision(Workflow $workflow, int $revision): void
    {
        abort_if($workflow->status === 'archived', 409, 'Workflow sudah diarsipkan.');
        abort_if($workflow->draft_revision !== $revision, 409, 'Workflow berubah di sesi lain. Muat ulang sebelum menyimpan.');
    }

    private function saved(Workflow $workflow, int $status = 200)
    {
        return response()->json(['data' => $workflow, 'edit_url' => route('cms.core.workflow.edit', $workflow)], $status);
    }
}
