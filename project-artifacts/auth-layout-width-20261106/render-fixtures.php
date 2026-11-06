<?php
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->make('url')->forceRootUrl('http://laravel-13-phoenix.aruna');
$app->make('url')->forceScheme('http');
$snapshot = App\Models\Awesome_Admin\Page_Theme_Setting::whereIn('uri', App\Support\AuthThemeCatalog::PAGES)->orderBy('uri')->get()->toJson(JSON_PRETTY_PRINT);
if (($argv[1] ?? '') === 'snapshot-after') {
    file_put_contents(__DIR__.'/settings-after.json', $snapshot);
    if ($snapshot !== file_get_contents(__DIR__.'/settings-before.json')) throw new RuntimeException('Stored auth settings changed during QA; inspect without restoring.');
    echo 'Stored auth settings are identical before and after QA.'.PHP_EOL;
    exit;
}
file_put_contents(__DIR__.'/settings-before.json', $snapshot);
$directory = __DIR__.'/rendered';
if (!is_dir($directory)) mkdir($directory);
$fixtureTheme = $fixtureMode = '';
Illuminate\Support\Facades\Event::listen('eloquent.retrieved: '.App\Models\Awesome_Admin\Page_Theme_Setting::class, function ($setting) use (&$fixtureTheme, &$fixtureMode) {
    $options = $setting->page_theme_options ?? [];
    $options[$fixtureTheme]['layout_width'] = $fixtureMode;
    $setting->page_theme_options = $options;
});
$count = 0;
foreach (['ocean_split', 'verdant_split'] as $fixtureTheme) {
    foreach (['bounded', 'fluid'] as $fixtureMode) {
        foreach (['login', 'signup', 'forgotpassword', 'resetpassword'] as $page) {
            $html = view('auth.'.$fixtureTheme.'.'.$page, ['data' => ['recovery_code' => 'non-submittable-qa-fixture']])->render();
            file_put_contents($directory.'/'.$fixtureTheme.'-'.$fixtureMode.'-'.$page.'.html', $html);
            $count++;
        }
    }
}
echo $count.' Blade fixtures rendered using in-memory mode overrides; no settings saved.'.PHP_EOL;
