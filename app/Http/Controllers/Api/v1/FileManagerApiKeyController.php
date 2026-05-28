<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FmApiKey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * FileManagerApiKeyController
 * Endpoint untuk admin kelola API Keys
 */
class FileManagerApiKeyController extends Controller
{
    protected function ok(mixed $data = null, string $msg = 'OK', int $code = 200): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $msg, 'data' => $data], $code);
    }

    protected function fail(string $msg, int $code = 422, mixed $errors = null): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $msg, 'errors' => $errors], $code);
    }

    // ── List semua API Keys ────────────────────────────────

    public function index(): JsonResponse
    {
        $keys = FmApiKey::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($k) => $this->resource($k));

        return $this->ok($keys);
    }

    // ── Create API Key baru ────────────────────────────────

    public function store(Request $request): JsonResponse
    {
        $v = Validator::make($request->all(), [
            'name'              => 'required|string|max:100',
            'user_id'           => 'nullable|integer|exists:users,id',
            'allowed_origins'   => 'nullable|array',
            'allowed_origins.*' => 'string',
            'allowed_disks'     => 'nullable|array',
            'allowed_disks.*'   => 'in:local,public,s3',
            'can_upload'        => 'boolean',
            'can_delete'        => 'boolean',
            'can_rename'        => 'boolean',
            'can_move'          => 'boolean',
            'can_create_folder' => 'boolean',
            'expires_at'        => 'nullable|date',
        ]);
        if ($v->fails()) return $this->fail('Validasi gagal', 422, $v->errors());

        $apiKey = FmApiKey::create([
            'name'              => $request->input('name'),
            'user_id'           => $request->input('user_id'),
            'key'               => FmApiKey::generateKey(),
            'secret'            => FmApiKey::generateKey(),
            'allowed_origins'   => $request->input('allowed_origins'),
            'allowed_disks'     => $request->input('allowed_disks'),
            'can_upload'        => $request->boolean('can_upload', true),
            'can_delete'        => $request->boolean('can_delete', false),
            'can_rename'        => $request->boolean('can_rename', false),
            'can_move'          => $request->boolean('can_move', false),
            'can_create_folder' => $request->boolean('can_create_folder', true),
            'is_active'         => true,
            'expires_at'        => $request->input('expires_at'),
        ]);

        // Tampilkan key & secret sekali saja saat create
        $data = $this->resource($apiKey);
        $data['key']    = $apiKey->key;    // tampil plain di response create
        $data['secret'] = $apiKey->secret; // tampil plain di response create

        return $this->ok($data, 'API Key berhasil dibuat. Simpan key & secret ini, tidak akan ditampilkan lagi!', 201);
    }

    // ── Show single key ────────────────────────────────────

    public function show(int $id): JsonResponse
    {
        $apiKey = FmApiKey::with('user:id,name,email')->find($id);
        if (!$apiKey) return $this->fail('API Key tidak ditemukan', 404);
        return $this->ok($this->resource($apiKey));
    }

    // ── Update permissions / settings ─────────────────────

    public function update(Request $request, int $id): JsonResponse
    {
        $apiKey = FmApiKey::find($id);
        if (!$apiKey) return $this->fail('API Key tidak ditemukan', 404);

        $v = Validator::make($request->all(), [
            'name'              => 'sometimes|string|max:100',
            'allowed_origins'   => 'nullable|array',
            'allowed_disks'     => 'nullable|array',
            'allowed_disks.*'   => 'in:local,public,s3',
            'can_upload'        => 'boolean',
            'can_delete'        => 'boolean',
            'can_rename'        => 'boolean',
            'can_move'          => 'boolean',
            'can_create_folder' => 'boolean',
            'is_active'         => 'boolean',
            'expires_at'        => 'nullable|date',
        ]);
        if ($v->fails()) return $this->fail('Validasi gagal', 422, $v->errors());

        $apiKey->update($request->only([
            'name', 'allowed_origins', 'allowed_disks',
            'can_upload', 'can_delete', 'can_rename', 'can_move', 'can_create_folder',
            'is_active', 'expires_at',
        ]));

        return $this->ok($this->resource($apiKey), 'API Key berhasil diperbarui');
    }

    // ── Revoke / delete ────────────────────────────────────

    public function destroy(int $id): JsonResponse
    {
        $apiKey = FmApiKey::find($id);
        if (!$apiKey) return $this->fail('API Key tidak ditemukan', 404);
        $apiKey->delete();
        return $this->ok(null, 'API Key berhasil dihapus');
    }

    // ── Regenerate key ─────────────────────────────────────

    public function regenerate(int $id): JsonResponse
    {
        $apiKey = FmApiKey::find($id);
        if (!$apiKey) return $this->fail('API Key tidak ditemukan', 404);

        $newKey    = FmApiKey::generateKey();
        $newSecret = FmApiKey::generateKey();
        $apiKey->update(['key' => $newKey, 'secret' => $newSecret]);

        return $this->ok([
            'key'    => $newKey,
            'secret' => $newSecret,
        ], 'API Key berhasil di-regenerate. Simpan key & secret baru ini!');
    }

    // ── Helpers ────────────────────────────────────────────

    protected function resource(FmApiKey $k): array
    {
        return [
            'id'                => $k->id,
            'name'              => $k->name,
            'user_id'           => $k->user_id,
            'user_name'         => $k->user->name ?? 'Global',
            'key_preview'       => substr($k->key, 0, 8) . '...' . substr($k->key, -4), // jangan expose full key di list
            'allowed_origins'   => $k->allowed_origins,
            'allowed_disks'     => $k->allowed_disks,
            'can_upload'        => $k->can_upload,
            'can_delete'        => $k->can_delete,
            'can_rename'        => $k->can_rename,
            'can_move'          => $k->can_move,
            'can_create_folder' => $k->can_create_folder,
            'is_active'         => $k->is_active,
            'expires_at'        => $k->expires_at?->format('d M Y H:i'),
            'last_used_at'      => $k->last_used_at?->format('d M Y H:i'),
            'last_used_ip'      => $k->last_used_ip,
            'request_count'     => $k->request_count,
            'created_at'        => $k->created_at->format('d M Y H:i'),
        ];
    }
}
