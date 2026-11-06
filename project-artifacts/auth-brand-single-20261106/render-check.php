<?php
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->make('url')->forceRootUrl('http://laravel-13-phoenix.aruna');
$app->make('url')->forceScheme('http');
$phase = ($argv[1] ?? 'after') === 'before' ? 'before' : 'after';
$directory = __DIR__.'/'.$phase;
if (!is_dir($directory)) mkdir($directory);
$checks = [];
foreach (array_keys(App\Support\AuthThemeCatalog::themes()) as $theme) {
    foreach (['login', 'signup', 'forgotpassword', 'resetpassword'] as $page) {
        $html = view('auth.'.$theme.'.'.$page, ['data' => ['recovery_code' => 'non-submittable-qa-fixture']])->render();
        file_put_contents($directory.'/'.$theme.'-'.$page.'.html', $html);
        $brand = substr_count($html, 'class="ph-auth-brand"');
        $panel = substr_count($html, 'class="ph-auth-panel-brand"');
        $footer = substr_count($html, 'class="ph-auth-form-footer"');
        $checks[] = compact('theme', 'page', 'brand', 'panel', 'footer');
        if ($phase === 'after' && ($brand !== 1 || $panel !== 0 || $footer !== 1)) {
            throw new RuntimeException('Unexpected branding in '.$theme.'/'.$page);
        }
    }
}
file_put_contents(__DIR__.'/'.$phase.'-render-checks.json', json_encode($checks, JSON_PRETTY_PRINT));
echo count($checks).' views rendered; '.array_sum(array_column($checks, 'panel')).' duplicated panel brand elements ('.$phase.').'.PHP_EOL;
