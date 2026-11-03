<?php
$root=dirname(__DIR__,2);
require $root.'/vendor/autoload.php';
$app=require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$manifest=json_decode(file_get_contents(__DIR__.'/backup-manifest.json'),true);
$results=[];
foreach($manifest['settings'] as $before){
 $current=App\Models\Awesome_Admin\Page_Theme_Setting::where('uri',$before['uri'])->first()->toArray();
 foreach($before as $key=>$value)if($current[$key]!==$value)throw new RuntimeException('Setting changed: '.$before['uri'].'.'.$key);
 if($current['page_primary_color']!=='#1FA675')throw new RuntimeException('Test primary not restored');
 $results[$before['uri']]=$current['page_primary_color'];
}
file_put_contents(__DIR__.'/restored-settings.json',json_encode($results,JSON_PRETTY_PRINT));
echo 'PASS: all original settings unchanged; four primary colors restored to #1FA675.'.PHP_EOL;
$app->make('url')->forceRootUrl('https://laravel-13-phoenix.aruna');
$app->make('url')->forceScheme('https');
view()->addNamespace('auth_baseline',$manifest['backup'].'/resources/views/auth');
$app->instance('request',Illuminate\Http\Request::create('https://laravel-13-phoenix.aruna/auth/signup'));
$baseline=view('auth_baseline::split_left.signup')->render();
file_put_contents(__DIR__.'/rendered/baseline-signup.html',$baseline);
echo 'Rendered original signup Blade with original CMS layout for baseline comparison.'.PHP_EOL;
