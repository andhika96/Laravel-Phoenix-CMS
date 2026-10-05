<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$connection = config('database.default');
$host = config('database.connections.'.$connection.'.host');
if ($connection !== 'mysql' || ! in_array($host, ['localhost', '127.0.0.1', '::1'], true) || app()->environment('production')) {
    throw new RuntimeException('Preflight requires the local non-production MySQL connection.');
}
$snapshot = ['database' => DB::connection()->getDatabaseName(), 'environment' => app()->environment(), 'created_at' => date(DATE_ATOM), 'tables' => []];
foreach (['workflows', 'workflow_versions', 'workflow_runs', 'workflow_run_logs', 'workflow_approval_tasks'] as $table) {
    $snapshot['tables'][$table] = Schema::hasTable($table) ? DB::table($table)->count() : null;
}
foreach (['menu_parentmenu_json', 'custom_permissions'] as $table) {
    $snapshot[$table] = Schema::hasTable($table) ? DB::table($table)->get()->toArray() : [];
}
$snapshot['workflow_roles'] = DB::table('roles')->whereIn('name', ['Administrator', 'Super Admin'])->pluck('name', 'id')->all();
$snapshot['routes'] = collect(app('router')->getRoutes())->filter(fn ($route) => str_starts_with($route->getName() ?? '', 'cms.core.workflow.'))->map(fn ($route) => ['name' => $route->getName(), 'uri' => $route->uri(), 'methods' => $route->methods(), 'middleware' => $route->gatherMiddleware()])->values()->all();
$backup = dirname(__DIR__).'/backups/20261005_095620_workflow-editor/database-navigation-before-migration.json';
if (file_exists($backup)) { throw new RuntimeException('Refusing to overwrite the pre-migration database snapshot.'); }
$bytes = file_put_contents($backup, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
if ($bytes === false) { throw new RuntimeException('Database snapshot write failed.'); }
echo json_encode(['database' => $snapshot['database'], 'environment' => $snapshot['environment'], 'tables' => $snapshot['tables'], 'routes' => count($snapshot['routes']), 'backup' => $backup, 'sha256' => hash_file('sha256', $backup)], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
