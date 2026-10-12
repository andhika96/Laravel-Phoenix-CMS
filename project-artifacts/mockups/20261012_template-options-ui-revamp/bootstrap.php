<?php

function t($value, $one = '', $two = '', $three = '', $four = '')
{
    return str_replace(['{1}', '{2}', '{3}', '{4}'], [$one, $two, $three, $four], $value);
}
$root = dirname(__DIR__, 3);
putenv('APP_CONFIG_CACHE='.__DIR__.'/qa/unused-config.php');
putenv('CACHE_STORE=array');
putenv('SESSION_DRIVER=array');
putenv('VIEW_COMPILED_PATH='.__DIR__.'/qa/compiled');
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['view.compiled' => __DIR__.'/qa/compiled', 'cache.default' => 'array']);
if (!is_dir(__DIR__.'/qa/compiled')) mkdir(__DIR__.'/qa/compiled', 0777, true);
