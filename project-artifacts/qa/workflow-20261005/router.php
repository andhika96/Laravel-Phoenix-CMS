<?php

// Local browser QA: no CMS authentication bypass and no application database writes.
$project = dirname(__DIR__, 3);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (str_starts_with($path, '/assets/')) {
    $file = realpath($project.'/public'.$path);
    $public = realpath($project.'/public/assets').DIRECTORY_SEPARATOR;
    if (! $file || ! str_starts_with($file, $public) || ! is_file($file)) { http_response_code(404); exit; }
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    $types = ['css' => 'text/css', 'js' => 'application/javascript', 'json' => 'application/json', 'woff2' => 'font/woff2', 'woff' => 'font/woff', 'ttf' => 'font/ttf', 'svg' => 'image/svg+xml'];
    if (! isset($types[$extension])) { http_response_code(404); exit; }
    header('Content-Type: '.$types[$extension]); readfile($file); exit;
}
require $project.'/vendor/autoload.php';
$app = require $project.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
Illuminate\Support\Facades\URL::forceRootUrl('http://127.0.0.1:8015');
Illuminate\Support\Facades\URL::forceScheme('http');
config(['view.compiled' => __DIR__.'/compiled', 'session.driver' => 'array']);
if (! is_dir(__DIR__.'/compiled')) { mkdir(__DIR__.'/compiled'); }
$app['view']->addNamespace('workflow_qa', __DIR__);
$file = __DIR__.'/fixture.json';
$graph = ['nodes' => [
    ['id' => 'trigger-1', 'type' => 'trigger', 'label' => 'Trigger', 'x' => 72, 'y' => 168, 'config' => ['event' => 'manual']],
    ['id' => 'end-1', 'type' => 'end', 'label' => 'End', 'x' => 708, 'y' => 168, 'config' => (object) []],
], 'edges' => [['id' => 'edge-1', 'source' => 'trigger-1', 'target' => 'end-1', 'port' => 'out']], 'viewport' => ['x' => 0, 'y' => 0, 'zoom' => 1]];
$workflow = is_file($file) ? json_decode(file_get_contents($file), true) : ['id' => 42, 'name' => 'QA Workflow', 'description' => 'Fixture terisolasi', 'category' => 'general', 'status' => 'draft', 'draft_revision' => 1, 'published_version' => null, 'published_revision' => null, 'graph' => $graph];
if (str_starts_with($path, '/fixture/') && ! str_ends_with($path, '/edit')) {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    if ($path === '/fixture/data') {
        echo json_encode(['data' => [$workflow], 'total' => 1, 'current_page' => 1, 'last_page' => 1]); exit;
    }
    if (str_ends_with($path, '/runs')) { echo '{"data":[]}'; exit; }
    if (str_ends_with($path, '/simulate')) {
        try {
            $graphArray = json_decode(json_encode($workflow['graph']), true);
            $result = app(App\Support\Workflow\WorkflowGraph::class)->simulate($graphArray, $input['context'] ?? []);
            http_response_code(201);
            echo json_encode(['data' => ['id' => 1, 'status' => 'completed', 'mode' => 'simulation', 'logs' => $result['logs'], 'context' => $result['context']]]);
        } catch (Illuminate\Validation\ValidationException $e) { http_response_code(422); echo json_encode(['message' => $e->getMessage(), 'errors' => $e->errors()]); }
        exit;
    }
    if (str_ends_with($path, '/publish')) {
        try { app(App\Support\Workflow\WorkflowGraph::class)->validatePublish(json_decode(json_encode($workflow['graph']), true)); }
        catch (Illuminate\Validation\ValidationException $e) { http_response_code(422); echo json_encode(['message' => $e->getMessage(), 'errors' => $e->errors()]); exit; }
        $workflow['status'] = 'published'; $workflow['published_version'] = ($workflow['published_version'] ?? 0) + 1; $workflow['published_revision'] = $workflow['draft_revision'];
    } elseif (str_ends_with($path, '/archive')) { $workflow['status'] = 'archived'; }
    elseif ($_SERVER['REQUEST_METHOD'] === 'PUT' || $path === '/fixture/workflow') {
        $workflow = array_replace($workflow, array_intersect_key($input, array_flip(['name', 'description', 'category', 'graph'])));
        $workflow['draft_revision']++;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { http_response_code(201); }
    }
    file_put_contents($file, json_encode($workflow, JSON_PRETTY_PRINT));
    echo json_encode(['data' => $workflow, 'edit_url' => 'http://127.0.0.1:8015/fixture/workflow/42/edit']); exit;
}
$editor = $path !== '/list';
$base = 'http://127.0.0.1:8015';
$urls = ['index' => $base.'/list', 'store' => $base.'/fixture/workflow', 'data' => $base.'/fixture/data', 'create' => $base.'/editor', 'base' => $base.'/fixture/workflow'];
foreach (['update', 'publish', 'archive', 'duplicate', 'simulate', 'runs'] as $action) { $urls[$action] = $base.'/fixture/workflow/42'.($action === 'update' ? '' : '/'.$action); }
$bootstrap = ['workflow' => $path === '/new' ? null : $workflow, 'urls' => $urls, 'permissions' => ['write' => true, 'publish' => true, 'archive' => true, 'duplicate' => true]];
$source = file_get_contents($project.'/resources/views/workflow/'.($editor ? 'editor' : 'index').'.blade.php');
$source = str_replace("@extends('themes.'.custom_theme('cms'))", "@extends('workflow_qa::layout')", $source);
$source = preg_replace('/\bt\(/', '$qaTranslate(', $source);
echo Illuminate\Support\Facades\Blade::render($source, ['bootstrap' => $bootstrap, 'qaTranslate' => fn (string $label): string => $label]);
