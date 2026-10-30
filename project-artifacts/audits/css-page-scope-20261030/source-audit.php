<?php
$root = dirname(__DIR__, 3);
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$theme = ['auth' => custom_theme('auth'), 'cms' => custom_theme('cms'), 'frontend' => custom_theme('frontend'), 'login_template' => custom_page_theme('login')];
$routes = [];
foreach (Illuminate\Support\Facades\Route::getRoutes() as $route) {
    $action = $route->getActionName();
    if (!in_array('GET', $route->methods(), true) || !str_contains($action, 'Controllers\\Web\\')) continue;
    if (preg_match('/PageBuilder|FileManager|Experimental/i', $action)) continue;
    $routes[] = ['uri' => $route->uri(), 'name' => $route->getName(), 'action' => $action];
}
file_put_contents(__DIR__.'/theme-and-routes.json', json_encode(['theme' => $theme, 'routes' => $routes], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo json_encode(['theme' => $theme, 'get_routes_in_scope' => count($routes)], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
