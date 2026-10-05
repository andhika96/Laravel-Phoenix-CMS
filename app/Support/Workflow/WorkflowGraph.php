<?php

namespace App\Support\Workflow;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WorkflowGraph
{
    public const VISUAL_TYPES = ['table', 'icon', 'icon-list'];
    public const TYPES = ['trigger', 'condition', 'approval', 'delay', 'email', 'webhook', 'update', 'end', ...self::VISUAL_TYPES];
    public const OPERATORS = ['eq', 'neq', 'gt', 'gte', 'lt', 'lte', 'contains', 'exists'];

    public function input(Request $request, bool $updating = false): array
    {
        // ponytail: cap graphs at 200 nodes/400 edges; use iterative traversal and larger budgets if workflows grow beyond this.
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:4000'],
            'category' => ['required', Rule::in(['general', 'approval', 'form', 'automation', 'diagram'])],
            'draft_revision' => [$updating ? 'required' : 'sometimes', 'integer', 'min:1'],
            'graph' => ['required', 'array:nodes,edges,viewport'],
            'graph.nodes' => ['present', 'array', 'max:200'],
            'graph.nodes.*' => ['array:id,type,label,x,y,config'],
            'graph.nodes.*.id' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{1,80}$/', 'distinct:strict'],
            'graph.nodes.*.type' => ['required', Rule::in(self::TYPES)],
            'graph.nodes.*.label' => ['required', 'string', 'max:160'],
            'graph.nodes.*.x' => ['required', 'numeric', 'between:-100000,100000'],
            'graph.nodes.*.y' => ['required', 'numeric', 'between:-100000,100000'],
            'graph.nodes.*.config' => ['present', 'array', 'max:20'],
            'graph.edges' => ['present', 'array', 'max:400'],
            'graph.edges.*' => ['array:id,source,target,port'],
            'graph.edges.*.id' => ['required', 'string', 'regex:/^[A-Za-z0-9_-]{1,80}$/', 'distinct:strict'],
            'graph.edges.*.source' => ['required', 'string'],
            'graph.edges.*.target' => ['required', 'string'],
            'graph.edges.*.port' => ['required', Rule::in(['out', 'true', 'false', 'approved', 'rejected'])],
            'graph.viewport' => ['sometimes', 'array:x,y,zoom'],
            'graph.viewport.x' => ['required_with:graph.viewport', 'numeric', 'between:-100000,100000'],
            'graph.viewport.y' => ['required_with:graph.viewport', 'numeric', 'between:-100000,100000'],
            'graph.viewport.zoom' => ['required_with:graph.viewport', 'numeric', 'between:0.1,4'],
        ]);
        $ids = array_column($data['graph']['nodes'], 'id');
        $visualIds = array_column(array_filter($data['graph']['nodes'], fn ($node) => in_array($node['type'], self::VISUAL_TYPES, true)), 'id');
        foreach ($data['graph']['edges'] as $edge) {
            if (! in_array($edge['source'], $ids, true) || ! in_array($edge['target'], $ids, true)) {
                $this->invalid('Koneksi harus menunjuk node yang ada pada workflow.');
            }
            if (in_array($edge['source'], $visualIds, true) || in_array($edge['target'], $visualIds, true)) {
                $this->invalid('Elemen visual tidak dapat menjadi langkah eksekusi workflow.');
            }
        }
        foreach ($data['graph']['nodes'] as &$node) {
            $visual = in_array($node['type'], self::VISUAL_TYPES, true);
            if ($visual) { $this->validateConfig($node); }
            if (strlen(json_encode($node['config'], JSON_THROW_ON_ERROR)) > ($visual ? 262144 : 8192)) {
                $this->invalid('Konfigurasi node terlalu besar.');
            }
            if ($node['type'] === 'table') {
                $node['config']['cells'] = array_map(fn ($row) => array_map(fn ($cell) => $cell ?? '', $row), $node['config']['cells']);
                $node['config']['header'] = (bool) $node['config']['header'];
            } elseif ($node['type'] === 'icon-list') {
                $node['config']['items'] = array_map(fn ($item) => ['iconClass' => $item['iconClass'], 'text' => $item['text'] ?? ''], $node['config']['items']);
            }
        }
        unset($node);
        $data['graph']['nodes'] = array_values($data['graph']['nodes']);
        $data['graph']['edges'] = array_values($data['graph']['edges']);
        $data['graph']['viewport'] ??= ['x' => 0, 'y' => 0, 'zoom' => 1];
        return $data;
    }

    public function validatePublish(array $graph): void
    {
        $allNodes = array_column($graph['nodes'], null, 'id');
        foreach ($allNodes as $node) { $this->validateConfig($node); }
        $nodes = array_filter($allNodes, fn ($node) => ! in_array($node['type'], self::VISUAL_TYPES, true));
        if ($allNodes !== [] && $nodes === []) {
            if ($graph['edges'] !== []) { $this->invalid('Elemen visual tidak memiliki koneksi eksekusi.'); }
            return;
        }
        $triggers = array_filter($nodes, fn ($node) => $node['type'] === 'trigger');
        if (count($triggers) !== 1 || ! in_array('end', array_column($nodes, 'type'), true)) {
            $this->invalid('Workflow memerlukan tepat satu Trigger dan minimal satu End.');
        }
        $outgoing = [];
        foreach ($graph['edges'] as $edge) {
            if (! isset($nodes[$edge['source']], $nodes[$edge['target']])) { $this->invalid('Koneksi harus menunjuk langkah eksekusi workflow.'); }
            $source = $nodes[$edge['source']];
            $target = $nodes[$edge['target']];
            $ports = match ($source['type']) {
                'condition' => ['true', 'false'], 'approval' => ['approved', 'rejected'], 'end' => [], default => ['out'],
            };
            if ($target['type'] === 'trigger' || ! in_array($edge['port'], $ports, true) || $edge['source'] === $edge['target']) {
                $this->invalid('Arah atau port koneksi tidak sesuai jenis node.');
            }
            if (isset($outgoing[$edge['source']][$edge['port']])) {
                $this->invalid('Setiap port hanya boleh memiliki satu koneksi.');
            }
            $outgoing[$edge['source']][$edge['port']] = $edge['target'];
        }
        foreach ($nodes as $id => $node) {
            $required = match ($node['type']) {
                'end' => [], 'condition' => ['true', 'false'], 'approval' => ['approved', 'rejected'], default => ['out'],
            };
            if (array_diff($required, array_keys($outgoing[$id] ?? []))) {
                $this->invalid('Hubungkan seluruh output node: '.$node['label'].'.');
            }
        }
        $visited = [];
        $active = [];
        $walk = function (string $id) use (&$walk, &$visited, &$active, $outgoing): void {
            if (isset($active[$id])) {
                $this->invalid('Workflow tidak boleh memiliki koneksi berulang (cycle).');
            }
            if (isset($visited[$id])) {
                return;
            }
            $active[$id] = true;
            foreach ($outgoing[$id] ?? [] as $target) {
                $walk($target);
            }
            unset($active[$id]);
            $visited[$id] = true;
        };
        $walk(array_key_first($triggers));
        if (count($visited) !== count($nodes)) {
            $this->invalid('Semua node harus terhubung dari Trigger.');
        }
    }

    private function validateConfig(array $node): void
    {
        $config = $node['config'];
        $rules = match ($node['type']) {
            'trigger' => ['event' => ['required', Rule::in(['manual', 'form.submitted', 'data.created', 'data.updated'])], 'form_id' => ['nullable', 'string', 'max:160']],
            'condition' => ['field' => ['required', 'string', 'max:160'], 'operator' => ['required', Rule::in(self::OPERATORS)], 'value' => ['present', 'nullable', 'string', 'max:2000']],
            'approval' => ['assignee_id' => ['nullable', 'integer', 'min:1'], 'assignee_role' => ['nullable', 'string', 'max:120']],
            'delay' => ['minutes' => ['required', 'integer', 'between:0,43200']],
            'email' => ['to' => ['required', 'email:rfc', 'max:255'], 'subject' => ['required', 'string', 'max:255']],
            'webhook' => ['url' => ['required', 'url:http,https', 'max:2000'], 'method' => ['required', Rule::in(['POST'])]],
            'update' => ['field' => ['required', 'string', 'max:160'], 'value' => ['present', 'nullable', 'string', 'max:2000']],
            'table' => [
                'rows' => ['required','integer','between:1,20'], 'columns' => ['required','integer','between:1,20'],
                'cells' => ['required','array','list','min:1','max:20'], 'cells.*' => ['required','array','list','min:1','max:20'], 'cells.*.*' => ['present','nullable','string','max:500'],
                'columnWidths' => ['required','array','list','min:1','max:20'], 'columnWidths.*' => ['required','integer','between:60,600'],
                'rowHeights' => ['required','array','list','min:1','max:20'], 'rowHeights.*' => ['required','integer','between:28,240'],
                'header' => ['required','boolean'], 'fontSize' => ['required','integer','between:10,32'], 'align' => ['required',Rule::in(['left','center','right'])],
                'textColor' => ['required','regex:/^#[0-9a-fA-F]{6}$/'], 'background' => ['required','regex:/^#[0-9a-fA-F]{6}$/'],
                'headerBackground' => ['required','regex:/^#[0-9a-fA-F]{6}$/'], 'borderColor' => ['required','regex:/^#[0-9a-fA-F]{6}$/'],
            ],
            'icon' => [
                'iconClass' => ['required','string','max:120','regex:/^(?:fas|far|fab|fal|fad) fa-[a-z0-9-]+$/'],
                'color' => ['required','regex:/^#[0-9a-fA-F]{6}$/'], 'size' => ['required','integer','between:12,256'],
                'width' => ['required','integer','between:80,1000'], 'height' => ['required','integer','between:80,1000'],
            ],
            'icon-list' => [
                'items' => ['required','array','list','min:1','max:30'], 'items.*' => ['array:iconClass,text'],
                'items.*.iconClass' => ['required','string','max:120','regex:/^(?:fas|far|fab|fal|fad) fa-[a-z0-9-]+$/'],
                'items.*.text' => ['present','nullable','string','max:500'],
                'color' => ['required','regex:/^#[0-9a-fA-F]{6}$/'], 'textColor' => ['required','regex:/^#[0-9a-fA-F]{6}$/'],
                'fontSize' => ['required','integer','between:10,32'], 'gap' => ['required','integer','between:0,40'], 'width' => ['required','integer','between:160,1000'],
            ],
            default => [],
        };
        $validator = validator($config, $rules);
        if ($validator->fails()) {
            $this->invalid($node['label'].': '.$validator->errors()->first());
        }
        if ($node['type'] === 'table' && (
            count($config['cells']) !== (int) $config['rows'] || count($config['rowHeights']) !== (int) $config['rows']
            || count($config['columnWidths']) !== (int) $config['columns']
            || collect($config['cells'])->contains(fn ($row) => count($row) !== (int) $config['columns'])
        )) { $this->invalid($node['label'].': ukuran grid, sel, dan row/column tidak sesuai.'); }
        if ($node['type'] === 'approval' && empty($config['assignee_id']) && empty($config['assignee_role'])) {
            $this->invalid($node['label'].': tentukan pengguna atau role approver.');
        }
    }

    public function simulate(array $graph, array $context): array
    {
        $this->validatePublish($graph);
        $nodes = array_column(array_filter($graph['nodes'], fn ($node) => ! in_array($node['type'], self::VISUAL_TYPES, true)), null, 'id');
        if ($nodes === []) { $this->invalid('Diagram visual tidak memiliki Trigger untuk disimulasikan.'); }
        $current = array_values(array_filter($nodes, fn ($node) => $node['type'] === 'trigger'))[0]['id'];
        $logs = [];
        while (isset($nodes[$current])) {
            $node = $nodes[$current];
            $config = $node['config'];
            $port = 'out';
            $message = 'Simulasi: '.$node['label'];
            switch ($node['type']) {
                case 'condition':
                    $actual = Arr::get($context, $config['field']);
                    $expected = $config['value'] ?? '';
                    $scalar = is_scalar($actual) || $actual === null;
                    $result = match ($config['operator']) {
                        'exists' => Arr::has($context, $config['field']) && $actual !== null,
                        'eq' => $scalar && (string) $actual === (string) $expected,
                        'neq' => ! $scalar || (string) $actual !== (string) $expected,
                        'contains' => $scalar && str_contains((string) $actual, (string) $expected),
                        'gt' => is_numeric($actual) && is_numeric($expected) && $actual > $expected,
                        'gte' => is_numeric($actual) && is_numeric($expected) && $actual >= $expected,
                        'lt' => is_numeric($actual) && is_numeric($expected) && $actual < $expected,
                        'lte' => is_numeric($actual) && is_numeric($expected) && $actual <= $expected,
                    };
                    $port = $result ? 'true' : 'false';
                    $message = 'Kondisi '.$config['field'].' → '.strtoupper($port);
                    break;
                case 'approval':
                    $port = 'approved';
                    $message = 'Simulasi approval: diasumsikan disetujui; tidak membuat tugas approver.';
                    break;
                case 'delay':
                    $message = 'Simulasi delay '.$config['minutes'].' menit: waktu tunggu dilewati.';
                    break;
                case 'email':
                    $message = 'Simulasi email ke '.$config['to'].'; email tidak dikirim.';
                    break;
                case 'webhook':
                    $message = 'Simulasi webhook '.$config['url'].'; request tidak dikirim.';
                    break;
                case 'update':
                    Arr::set($context, $config['field'], $config['value']);
                    $message = 'Data simulasi '.$config['field'].' diperbarui; database modul tidak diubah.';
                    break;
                case 'end':
                    $message = 'Simulasi selesai.';
                    break;
            }
            $logs[] = ['node_id' => $current, 'level' => 'info', 'message' => $message, 'payload' => ['port' => $port]];
            if ($node['type'] === 'end') {
                break;
            }
            $edge = collect($graph['edges'])->first(fn ($edge) => $edge['source'] === $current && $edge['port'] === $port);
            $current = $edge['target'];
        }
        return ['logs' => $logs, 'context' => $context];
    }

    private function invalid(string $message): never
    {
        throw ValidationException::withMessages(['graph' => $message]);
    }
}
