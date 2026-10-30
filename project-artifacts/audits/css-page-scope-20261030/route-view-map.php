<?php
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$input = json_decode(file_get_contents(__DIR__.'/theme-and-routes.json'), true);
$rows = [];
foreach ($input['routes'] as $route) {
    [$class, $method] = explode('@', $route['action'], 2);
    if (!class_exists($class) || !method_exists($class, $method)) {
        $rows[] = $route + ['controller_missing' => true];
        continue;
    }
    $reflection = new ReflectionMethod($class, $method);
    $file = $reflection->getFileName();
    $text = implode('', array_slice(file($file), $reflection->getStartLine()-1, $reflection->getEndLine()-$reflection->getStartLine()+1));
    preg_match_all('/\bview\(\s*[\'"]([a-zA-Z0-9_.\/-]+)[\'"]/', $text, $matches);
    $views = [];
    foreach (array_unique($matches[1]) as $view) {
        $path = 'resources/views/'.str_replace('.', '/', $view).'.blade.php';
        $views[] = ['view' => $view, 'file' => $path, 'exists' => is_file($root.'/'.$path)];
    }
    $rows[] = $route + ['source' => str_replace('\\', '/', substr($file, strlen($root)+1)), 'line' => $reflection->getStartLine(), 'literal_views' => $views];
}
file_put_contents(__DIR__.'/route-view-map.json', json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo 'Mapped '.count($rows).' GET route actions without calling controllers.'.PHP_EOL;
