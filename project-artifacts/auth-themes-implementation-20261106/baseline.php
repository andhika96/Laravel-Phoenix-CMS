<?php
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$snapshot = [
    'date' => now()->toIso8601String(),
    'driver' => Illuminate\Support\Facades\DB::connection()->getDriverName(),
    'settings_columns' => Illuminate\Support\Facades\Schema::getColumnListing('page_theme_settings'),
    'theme_columns' => Illuminate\Support\Facades\Schema::getColumnListing('page_themes'),
    'settings' => Illuminate\Support\Facades\DB::table('page_theme_settings')->whereIn('uri', ['login', 'signup', 'forgotPassword', 'resetPassword'])->get()->all(),
    'themes' => Illuminate\Support\Facades\DB::table('page_themes')->whereIn('uri', ['login', 'signup', 'forgotpassword', 'resetpassword'])->get()->all(),
];
$path = __DIR__.'/database-before.json';
if (file_exists($path)) {
    throw new RuntimeException('The database snapshot already exists.');
}
file_put_contents($path, json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
echo json_encode(['driver' => $snapshot['driver'], 'settings_columns' => $snapshot['settings_columns'], 'theme_columns' => $snapshot['theme_columns'], 'pages' => array_column($snapshot['settings'], 'uri'), 'themes' => array_map(fn ($row) => [$row->uri, $row->theme_code, $row->theme_name], $snapshot['themes'])], JSON_PRETTY_PRINT);
