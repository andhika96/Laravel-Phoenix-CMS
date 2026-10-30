<?php
// Read-only candidate lookup; record counts, never database content or credentials.
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$db = Illuminate\Support\Facades\DB::connection();
$db->statement('START TRANSACTION READ ONLY');
try {
    $columns = $db->select(
        "SELECT TABLE_NAME AS table_name, COLUMN_NAME AS column_name FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND DATA_TYPE IN ('char', 'varchar', 'tinytext', 'text', 'mediumtext', 'longtext', 'json')",
        [$db->getDatabaseName()],
    );
    $hits = [];
    foreach ($columns as $column) {
        $table = str_replace('`', '``', $column->table_name);
        $field = str_replace('`', '``', $column->column_name);
        $query = $db->getPdo()->prepare("SELECT COUNT(*) FROM `{$table}` WHERE `{$field}` LIKE ?");
        $query->execute(['%new.css%']);
        $count = (int) $query->fetchColumn();
        if ($count > 0) {
            $hits[] = ['table' => $column->table_name, 'column' => $column->column_name, 'count' => $count];
        }
    }
    $result = ['candidate' => 'public/assets/beta_css/new.css', 'mode' => 'READ ONLY transaction', 'text_json_columns_checked' => count($columns), 'hits' => $hits];
    file_put_contents(__DIR__.'/database-css-check.json', json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
} finally {
    $db->rollBack();
}
