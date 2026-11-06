<?php
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->make('url')->forceRootUrl('http://laravel-13-phoenix.aruna');
$app->make('url')->forceScheme('http');
$directory = __DIR__.'/rendered_'.date('Ymd_His');
mkdir($directory);
$files = [];
foreach ([...App\Support\AuthThemeCatalog::LEGACY, ...array_keys(App\Support\AuthThemeCatalog::themes())] as $theme) {
    foreach (['login', 'signup', 'forgotpassword', 'resetpassword'] as $page) {
        $filename = $theme.'-'.$page.'.html';
        $html = view('auth.'.$theme.'.'.$page, ['data' => ['recovery_code' => 'non-submittable-qa-fixture']])->render();
        if (str_contains($html, 'assets/css/cms/') || str_contains($html, 'css/shared/phoenix-cms.css')) {
            throw new RuntimeException('CMS CSS leaked into authentication.');
        }
        file_put_contents($directory.'/'.$filename, $html);
        $files[] = $filename;
    }
}
file_put_contents($directory.'/manifest.json', json_encode($files, JSON_PRETTY_PRINT));
echo $directory.PHP_EOL;
echo count($files).' auth views rendered without changing selected themes.'.PHP_EOL;
