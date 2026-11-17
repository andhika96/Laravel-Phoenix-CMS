<?php
// Read-only audit inventory; no application source or database data is changed.
declare(strict_types=1);

$root = dirname(__DIR__, 2);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$out = __DIR__;
$routes = [];
foreach ($app['router']->getRoutes() as $route) {
    $action = $route->getActionName();
    $row = ['methods' => $route->methods(), 'uri' => $route->uri(), 'name' => $route->getName(),
        'action' => $action, 'middleware' => $route->gatherMiddleware()];
    try {
        if ($action !== 'Closure') {
            [$class, $method] = array_pad(explode('@', $action, 2), 2, '__invoke');
            $row['class_exists'] = class_exists($class);
            if ($row['class_exists']) {
                $ref = new ReflectionMethod($class, $method);
                $row += ['file' => str_replace('\\', '/', substr($ref->getFileName(), strlen($root)+1)),
                    'line' => $ref->getStartLine(), 'end_line' => $ref->getEndLine()];
            }
        } elseif ($route->getAction('uses') instanceof Closure) {
            $ref = new ReflectionFunction($route->getAction('uses'));
            $row += ['file' => str_replace('\\', '/', substr($ref->getFileName(), strlen($root)+1)),
                'line' => $ref->getStartLine(), 'end_line' => $ref->getEndLine()];
        }
    } catch (Throwable $e) {
        $row['reflection_error'] = get_class($e);
    }
    $routes[] = $row;
}
if (count($routes) < 10) { throw new RuntimeException('Route inventory unexpectedly empty'); }
file_put_contents($out.'/route-inventory.json', json_encode($routes, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));

$connection = Illuminate\Support\Facades\DB::connection();
$connection->getPdo()->exec('SET SESSION TRANSACTION READ ONLY');
$database = $connection->getDatabaseName();
$runtime = ['php' => PHP_VERSION, 'laravel' => $app->version(), 'app_env' => $app->environment(),
    'debug' => config('app.debug'), 'database' => $database,
    'db_is_loopback' => in_array(config('database.connections.'.config('database.default').'.host'), ['127.0.0.1','localhost','::1']),
    'cache_store' => config('cache.default'), 'session_driver' => config('session.driver'),
    'queue_connection' => config('queue.default'), 'mail_default' => config('mail.default'),
    'config_cached' => $app->configurationIsCached(), 'routes_cached' => $app->routesAreCached(),
    'sql_version' => $connection->selectOne('SELECT VERSION() AS version')->version,
    'variables' => $connection->select("SHOW VARIABLES WHERE Variable_name IN ('max_connections','innodb_buffer_pool_size','innodb_lock_wait_timeout','transaction_isolation','slow_query_log','long_query_time','performance_schema')"),
    'status' => $connection->select("SHOW GLOBAL STATUS WHERE Variable_name IN ('Threads_connected','Threads_running','Max_used_connections','Slow_queries','Uptime')")];
$tables = $connection->select('SELECT table_name, engine, table_rows, data_length, index_length FROM information_schema.tables WHERE table_schema = ? ORDER BY table_name', [$database]);
$indexes = $connection->select('SELECT table_name, index_name, non_unique, seq_in_index, column_name, sub_part, cardinality FROM information_schema.statistics WHERE table_schema = ? ORDER BY table_name, index_name, seq_in_index', [$database]);
$columns = $connection->select('SELECT table_name, column_name, column_type, is_nullable, column_key FROM information_schema.columns WHERE table_schema = ? ORDER BY table_name, ordinal_position', [$database]);
file_put_contents($out.'/runtime-schema.json', json_encode(compact('runtime','tables','indexes','columns'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
echo json_encode(['routes' => count($routes), 'tables' => count($tables), 'runtime' => $runtime], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES), PHP_EOL;
