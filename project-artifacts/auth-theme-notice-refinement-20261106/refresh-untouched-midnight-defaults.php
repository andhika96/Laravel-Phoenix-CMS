<?php
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app=require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$before=json_decode(file_get_contents(__DIR__.'/midnight-defaults-before.json'),true);
$new=App\Support\AuthThemeCatalog::defaults('midnight_story','login');
$keys=['page_background','form_surface','panel_color','testimonials','info_text'];
$report=[];
Illuminate\Support\Facades\DB::transaction(function() use($before,$new,$keys,&$report) {
 foreach(App\Models\Awesome_Admin\Page_Theme_Setting::lockForUpdate()->get() as $setting) {
  $options=$setting->page_theme_options??[];
  $changed=[];
  foreach($keys as $key) {
   if(array_key_exists($key,$options['midnight_story']??[])&&$options['midnight_story'][$key]===$before[$key]&&$before[$key]!==$new[$key]) {
    unset($options['midnight_story'][$key]);
    $changed[]=$key;
   }
  }
  if($changed) $setting->update(['page_theme_options'=>$options]);
  $report[]=['page'=>$setting->uri,'selectedThemePreserved'=>$setting->page_theme,'defaultKeysRefreshed'=>$changed];
 }
});
file_put_contents(__DIR__.'/midnight-default-refresh.json',json_encode($report,JSON_PRETTY_PRINT));
echo json_encode($report,JSON_PRETTY_PRINT).PHP_EOL;
