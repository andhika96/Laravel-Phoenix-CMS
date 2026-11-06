<?php
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app=require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$defaults=App\Support\AuthThemeCatalog::fields('midnight_story');
file_put_contents(__DIR__.'/midnight-defaults-before.json',json_encode(array_map(fn($field)=>$field['default'],$defaults),JSON_PRETTY_PRINT));
foreach(App\Models\Awesome_Admin\Page_Theme_Setting::get() as $setting) {
 $saved=$setting->page_theme_options['midnight_story']??[];
 $changed=[];
 foreach($saved as $key=>$value) if(isset($defaults[$key])&&!in_array($defaults[$key]['type'],['image','images'],true)&&$value!==$defaults[$key]['default']&&!in_array($key,['heading','description','button_text'],true)) $changed[]=$key;
 echo json_encode(['page'=>$setting->uri,'customMidnightFields'=>$changed,'hasExplicitAvatarSetting'=>array_key_exists('avatars',$saved)]).PHP_EOL;
}
