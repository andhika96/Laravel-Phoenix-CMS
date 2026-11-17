<?php
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
foreach (['rendered', 'compiled'] as $folder) if (!is_dir(__DIR__.'/'.$folder)) mkdir(__DIR__.'/'.$folder);
$app['config']->set('view.compiled', __DIR__.'/compiled');
$request = Illuminate\Http\Request::create('http://laravel-13-phoenix.aruna/dashboard');
$request->setLaravelSession($app['session']->driver());
$app->instance('request', $request);
$app['url']->setRequest($request);
$app['url']->forceRootUrl('http://laravel-13-phoenix.aruna');
$app['url']->forceScheme('http');
$account = App\Models\Awesome_Admin\Account::where('email', 'admin@aruna-dev.com')->firstOrFail();
$app['auth']->guard()->setUser($account);
$request->setUserResolver(fn () => $account);
$content = '<main class="container py-4" id="consumer-fixture"><h1>Typography dan komponen Phoenix</h1><p class="text-small">Fixture regresi stylesheet shared.</p><div class="row g-3"><section class="col-md-6"><div class="card"><div class="card-body"><h4>Panel contoh</h4><label class="form-label" for="qa-input">Nama</label><input id="qa-input" class="form-control mb-3" value="Contoh"><button type="button" class="btn btn-primary">Simpan</button></div></div></section><section class="col-md-6"><table class="table"><thead><tr><th>Judul</th><th>Status</th></tr></thead><tbody><tr><td class="align-middle">Data contoh</td><td><span class="badge text-bg-success">Aktif</span></td></tr></tbody></table></section></div><div class="position-relative"><i class="position-absolute end-40">Offset</i></div></main>';
$count = 0;
foreach (['default', 'calm_green', 'arunika_aurora', 'arunika_equinox', 'arunika_prism', 'arunika_lucent'] as $theme) {
    foreach (['cms', 'frontend', 'auth'] as $area) {
        $source = "@extends('themes.{$theme}.{$area}.{$area}_layout')\n@section('title', 'CSS QA')\n@section('content'){$content}\n@endsection";
        $html = Illuminate\Support\Facades\Blade::render($source);
        $html = preg_replace('~<script\b[^>]*>.*?</script\s*>~is', '', $html);
        file_put_contents(__DIR__.'/rendered/'.$area.'-'.$theme.'.html', $html);
        $count++;
    }
}
echo "Rendered {$count} active layout fixtures; no database writes; scripts omitted.\n";
