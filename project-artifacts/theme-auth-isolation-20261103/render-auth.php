<?php
$root=dirname(__DIR__,2);
require $root.'/vendor/autoload.php';
$app=require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$app['config']->set('view.compiled',__DIR__.'/compiled');
$app['url']->forceRootUrl('https://laravel-13-phoenix.aruna');
$app['url']->forceScheme('https');
foreach(['default','card','split_left','split_right'] as $template){
 foreach(['login','signup','forgotpassword','resetpassword'] as $page){
  $html=view('auth.'.$template.'.'.$page,['data'=>['recovery_code'=>'non-submittable-qa-fixture']])->render();
  file_put_contents(__DIR__.'/rendered/auth-'.$template.'-'.$page.'.html',$html);
  if(str_contains($html,'assets/css/themes/')||str_contains($html,'shared/phoenix-cms.css')||str_contains($html,'cms-bootstrap-theme.css'))throw new RuntimeException('CMS CSS leaked to auth');
 }
}
echo "PASS: 16 auth views rendered without CMS theme assets.\n";
