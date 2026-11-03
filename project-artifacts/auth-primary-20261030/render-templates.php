<?php
if(time()>=strtotime('2026-10-30 09:50:00 UTC'))throw new RuntimeException('Deadline reached');
$root=dirname(__DIR__,2);
require $root.'/vendor/autoload.php';
$app=require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app->make('url')->forceRootUrl('https://laravel-13-phoenix.aruna');
$app->make('url')->forceScheme('https');
$dir=__DIR__.'/rendered';
if(!is_dir($dir))mkdir($dir);
foreach(['default','card','split_left','split_right'] as $template){
 foreach(['login','signup','forgotpassword','resetpassword'] as $page){
  $html=view('auth.'.$template.'.'.$page,['data'=>['recovery_code'=>'non-submittable-qa-fixture']])->render();
  file_put_contents($dir.'/'.$template.'-'.$page.'.html',$html);
  if(str_contains($html,'assets/css/themes/')||str_contains($html,'shared/phoenix-cms.css'))throw new RuntimeException('CMS CSS leak');
 }
}
echo "PASS: 16 Blade templates rendered without CMS theme CSS.\n";
