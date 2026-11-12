<?php
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$directory = __DIR__.'/rendered';
$compiled = __DIR__.'/compiled';
foreach ([$directory, $compiled] as $path) if (!is_dir($path)) mkdir($path);
$app['config']->set('view.compiled', $compiled);
$request = Illuminate\Http\Request::create('http://laravel-13-phoenix.aruna/dashboard');
$request->setLaravelSession($app['session']->driver());
$app->instance('request', $request);
$app['url']->setRequest($request);
$app['url']->forceRootUrl('http://laravel-13-phoenix.aruna');
$app['url']->forceScheme('http');
$account = App\Models\Awesome_Admin\Account::where('email', 'admin@aruna-dev.com')->firstOrFail();
$app['auth']->guard()->setUser($account);
$request->setUserResolver(fn () => $account);
function writeFixture(string $file, string $html): void {
    $html = preg_replace('~<script\b[^>]*>.*?</script\s*>~is', '', $html);
    file_put_contents(__DIR__.'/rendered/'.$file.'.html', $html);
}
foreach (['default', 'calm_green'] as $theme) {
    $source = file_get_contents(resource_path('views/dashboard/dashboard.blade.php'));
    $source = str_replace("@extends('themes.'.custom_theme('cms'))", "@extends('themes.{$theme}.cms.cms_layout')", $source);
    writeFixture('cms-'.$theme, Illuminate\Support\Facades\Blade::render($source));
}
foreach (['calm_green', 'arunika_aurora', 'arunika_equinox', 'arunika_prism', 'arunika_lucent'] as $theme) {
    $source = "@extends('themes.{$theme}.frontend.frontend_layout')\n@section('title','Utility compatibility QA')\n@section('content')";
    $source .= '<main class="container py-4" id="consumer-fixture"><h1>Typography dan komponen Phoenix</h1><p class="text-small">Fixture regresi stylesheet shared.</p><div class="row g-3"><section class="col-md-6"><div class="card"><div class="card-body"><h4>Panel contoh</h4><label class="form-label" for="qa-input">Nama</label><input id="qa-input" class="form-control mb-3" value="Contoh"><button type="button" class="btn btn-primary">Simpan</button></div></div></section><section class="col-md-6"><table class="table"><thead><tr><th>Judul</th><th>Status</th></tr></thead><tbody><tr><td class="align-middle">Data contoh</td><td><span class="badge text-bg-success">Aktif</span></td></tr></tbody></table></section></div></main>';
    $source .= '\n@endsection';
    writeFixture('frontend-'.$theme, Illuminate\Support\Facades\Blade::render($source));
}
echo 'Rendered 7 consumer fixtures with active layouts/fonts; no settings changed. Scripts omitted for CSS-only comparison.'.PHP_EOL;
