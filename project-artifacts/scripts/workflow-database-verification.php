<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$result = ['database' => DB::connection()->getDatabaseName(), 'tables' => []];
foreach (['workflows', 'workflow_versions', 'workflow_runs', 'workflow_run_logs', 'workflow_approval_tasks'] as $table) {
    if (! Schema::hasTable($table)) { throw new RuntimeException('Missing table: '.$table); }
    $result['tables'][DB::connection()->getTablePrefix().$table] = DB::table($table)->count();
}
$result['migrations'] = DB::table('migrations')->whereIn('migration', ['2026_10_05_000001_create_workflow_tables', '2026_10_05_000002_add_workflow_navigation'])->pluck('migration')->all();
$result['menu_entries'] = 0;
foreach (DB::table('menu_parentmenu_json')->where('menu_page', 'awesome_admin')->get() as $row) {
    $result['menu_entries'] += collect(json_decode($row->menu_vars, true))->where('parent_link', 'workflow')->count();
}
$result['permission_rows'] = DB::table('custom_permissions')->where('menu_link', 'workflow')->count();
if (count($result['migrations']) !== 2 || $result['menu_entries'] !== 1) { throw new RuntimeException('Migration/navigation verification failed.'); }
echo json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR).PHP_EOL;
