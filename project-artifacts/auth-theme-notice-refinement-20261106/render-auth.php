<?php
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->make('url')->forceRootUrl('http://laravel-13-phoenix.aruna');
$app->make('url')->forceScheme('http');
$directory=__DIR__.'/rendered_'.date('Ymd_His');
mkdir($directory);
$manifest=[];
foreach([...App\Support\AuthThemeCatalog::LEGACY,...array_keys(App\Support\AuthThemeCatalog::themes())] as $theme) {
 foreach(['login','signup','forgotpassword','resetpassword'] as $page) {
  $file=$theme.'-'.$page.'.html';
  $html=view('auth.'.$theme.'.'.$page,['data'=>['recovery_code'=>'non-submittable-qa-fixture']])->render();
  if(!str_contains($html,'ph-callout-no-border')||str_contains($html,'assets/css/cms/'))throw new RuntimeException('Auth notice/isolation check failed: '.$file);
  file_put_contents($directory.'/'.$file,$html);
  $manifest[]=$file;
 }
}
file_put_contents($directory.'/manifest.json',json_encode($manifest,JSON_PRETTY_PRINT));
echo $directory.PHP_EOL;
echo count($manifest).' auth views rendered with the CMS notice.'.PHP_EOL;
