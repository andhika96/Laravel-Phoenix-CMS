<?php
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
foreach (['site_config', 'page_theme_settings', 'accounts', 'model_has_roles'] as $table) {
    $row = Illuminate\Support\Facades\DB::selectOne('SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?', [Illuminate\Support\Facades\DB::getTablePrefix().$table]);
    echo $table.':'.($row->ENGINE ?? 'missing').PHP_EOL;
}
$before = json_decode(file_get_contents(__DIR__.'/database-before.json'), true)['settings'];
foreach ($before as $row) {
    $after = App\Models\Awesome_Admin\Page_Theme_Setting::where('uri', $row['uri'])->first()->toArray();
    $differences = [];
    foreach (['page_theme', 'page_color_nuances', 'page_background_image', 'page_primary_color'] as $key) {
        if ($row[$key] !== $after[$key]) $differences[] = $key;
    }
    echo json_encode(['page' => $row['uri'], 'changedLegacyFields' => $differences, 'themeOptions' => array_keys($after['page_theme_options'] ?? [])]).PHP_EOL;
}
echo 'binlog='.Illuminate\Support\Facades\DB::selectOne('SELECT @@log_bin AS value')->value.PHP_EOL;
echo json_encode(Illuminate\Support\Facades\DB::selectOne('SELECT @@log_bin_basename AS binlog, @@version AS version')).PHP_EOL;
echo json_encode(Illuminate\Support\Facades\DB::select('SHOW BINARY LOGS')).PHP_EOL;
