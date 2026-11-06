<?php
require dirname(__DIR__,2).'/vendor/autoload.php';
$app=require dirname(__DIR__,2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->make('url')->forceRootUrl('http://laravel-13-phoenix.aruna');
$app->make('url')->forceScheme('http');
$directory=__DIR__.'/rendered';
if(!is_dir($directory))mkdir($directory);
file_put_contents($directory.'/login.html',view('auth.midnight_story.login')->render());
echo 'Rendered Midnight Story without changing the selected theme or settings.'.PHP_EOL;
