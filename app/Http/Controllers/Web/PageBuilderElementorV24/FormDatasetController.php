<?php

namespace App\Http\Controllers\Web\PageBuilderElementorV24;

use App\Http\Controllers\Controller;
use App\Models\Page_Builder\Page_Builder;
use App\Models\PageBuilderElementorV24\FormDataset;
use App\Support\PageBuilderElementorV24\FormDatasetNormalizer;
use App\Support\PageBuilderElementorV24\ModuleCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class FormDatasetController extends Controller
{
    public function __construct(
        private readonly FormDatasetNormalizer $normalizer,
        private readonly ModuleCatalog $moduleCatalog,
    ) {
    }

    public function index(Request $request)
    {
        $this->requireFormModule();

        $datasets = FormDataset::query()
            ->where('user_id', $this->ownerId($request))
            ->orderBy('name')
            ->get()
            ->map(fn (FormDataset $dataset): array => $this->present($dataset))
            ->values();

        return response()->json(['success' => true, 'data' => $datasets]);
    }

    public function store(Request $request)
    {
        $this->requireFormModule();

        $request->validate(['name' => ['required', 'string', 'max:120']]);
        $validation = $this->normalizer->validate($request->all());

        if (! $validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => 'The dataset hierarchy is invalid.',
                'errors' => $validation['errors'],
            ], 422);
        }

        $dataset = FormDataset::create([
            'user_id' => $this->ownerId($request),
            'name' => trim((string) $request->input('name')),
            'slug' => $this->uniqueSlug($request, (string) $request->input('name')),
            'schema_version' => $validation['dataset']['schemaVersion'],
            'nodes' => $validation['dataset']['nodes'],
        ]);

        return response()->json(['success' => true, 'data' => $this->present($dataset)], 201);
    }

    public function update(Request $request, int $datasetId)
    {
        $this->requireFormModule();

        $dataset = FormDataset::query()
            ->where('user_id', $this->ownerId($request))
            ->findOrFail($datasetId);

        $request->validate(['name' => ['required', 'string', 'max:120']]);
        $payload = $request->all();
        if (! array_key_exists('nodes', $payload)) {
            $payload['nodes'] = $dataset->nodes;
        }
        $validation = $this->normalizer->validate($payload);

        if (! $validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => 'The dataset hierarchy is invalid.',
                'errors' => $validation['errors'],
            ], 422);
        }

        $dataset->fill([
            'name' => trim((string) $request->input('name')),
            'schema_version' => $validation['dataset']['schemaVersion'],
            'nodes' => $validation['dataset']['nodes'],
        ]);
        $dataset->save();

        return response()->json(['success' => true, 'data' => $this->present($dataset)]);
    }

    public function destroy(Request $request, int $datasetId)
    {
        $this->requireFormModule();

        $dataset = FormDataset::query()
            ->where('user_id', $this->ownerId($request))
            ->findOrFail($datasetId);

        $disconnectedFields = DB::transaction(function () use ($dataset, $request, $datasetId): int {
            // ponytail: one owner-scoped layout scan keeps references consistent; add a registry if deletion becomes high-volume.
            $disconnectedFields = $this->disconnectDatasetReferences($this->ownerId($request), $datasetId);
            $dataset->delete();

            return $disconnectedFields;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $datasetId,
                'disconnectedFields' => $disconnectedFields,
            ],
        ]);
    }

    private function present(FormDataset $dataset): array
    {
        return [
            'id' => $dataset->getKey(),
            'name' => $dataset->name,
            'slug' => $dataset->slug,
            'schemaVersion' => (int) $dataset->schema_version,
            'nodes' => $dataset->nodes ?: [],
        ];
    }

    private function requireFormModule(): void
    {
        $module = $this->moduleCatalog->find('form');
        abort_unless(
            is_array($module) && in_array('form-submission', $module['capabilities'] ?? [], true),
            404,
        );
    }

    private function ownerId(Request $request): int
    {
        return (int) $request->user()->getAuthIdentifier();
    }

    private function uniqueSlug(Request $request, string $name): string
    {
        $base = Str::slug($name) ?: 'dataset';
        $slug = $base;
        $counter = 2;
        while (FormDataset::query()->where('user_id', $this->ownerId($request))->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }

    private function disconnectDatasetReferences(int $ownerId, int $datasetId): int
    {
        $disconnectedFields = 0;

        Page_Builder::query()
            ->where('user_id', $ownerId)
            ->where('editor_version', Page_Builder::EDITOR_VERSION_V24)
            ->select(['id', 'vars'])
            ->chunkById(100, function ($pages) use ($datasetId, &$disconnectedFields): void {
                foreach ($pages as $page) {
                    $layout = is_array($page->vars)
                        ? $page->vars
                        : json_decode((string) $page->vars, true);

                    if (! is_array($layout)) {
                        throw ValidationException::withMessages([
                            'dataset' => 'The dataset references could not be reconciled safely.',
                        ]);
                    }

                    $changedFields = $this->disconnectDatasetInValue($layout, $datasetId);
                    if ($changedFields === 0) {
                        continue;
                    }

                    $page->vars = json_encode($layout, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
                    $page->save();
                    $disconnectedFields += $changedFields;
                }
            }, 'id');

        return $disconnectedFields;
    }

    private function disconnectDatasetInValue(mixed &$value, int $datasetId): int
    {
        if (! is_array($value)) {
            return 0;
        }

        $disconnectedFields = 0;
        if (($value['datasetMode'] ?? '') === 'dataset' && (int) ($value['datasetId'] ?? 0) === $datasetId) {
            $value['datasetMode'] = 'static';
            $value['datasetId'] = '';
            $value['datasetParentFieldId'] = '';
            $disconnectedFields++;
        }

        foreach ($value as &$child) {
            $disconnectedFields += $this->disconnectDatasetInValue($child, $datasetId);
        }
        unset($child);

        return $disconnectedFields;
    }
}
