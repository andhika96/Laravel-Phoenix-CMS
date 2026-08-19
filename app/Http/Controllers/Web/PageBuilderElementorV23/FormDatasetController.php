<?php

namespace App\Http\Controllers\Web\PageBuilderElementorV23;

use App\Http\Controllers\Controller;
use App\Models\PageBuilderElementorV23\FormDataset;
use App\Support\PageBuilderElementorV23\FormDatasetNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormDatasetController extends Controller
{
    public function __construct(private readonly FormDatasetNormalizer $normalizer)
    {
    }

    public function index(Request $request)
    {
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

    private function ownerId(Request $request): int
    {
        return (int) ($request->user()?->getAuthIdentifier() ?: 1);
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
}
