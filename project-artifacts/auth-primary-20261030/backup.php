<?php
if (time() >= strtotime('2026-10-30 09:50:00 UTC')) { throw new RuntimeException('Deadline reached'); }
$root=dirname(__DIR__,2);
require $root.'/vendor/autoload.php';
$app=require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$files=[
 'app/Models/Awesome_Admin/Page_Theme_Setting.php',
 'app/Http/Controllers/Web/Awesome_Admin/Awesome_Admin_Appearance_Controller.php',
 'resources/views/awesome_admin/awesome_admin_appearance.blade.php',
 'public/assets/js/vue3/manage_appearance/vueV3-manage-appearance-2026.js',
];
foreach (['default','card','split_left','split_right'] as $template) {
 foreach (['login','signup','forgotpassword','resetpassword'] as $page) $files[]="resources/views/auth/{$template}/{$page}.blade.php";
}
$dir=__DIR__.'/backups_'.gmdate('Ymd_His',time()+7*3600);
$manifest=[];
foreach($files as $file){
 $source=$root.'/'.$file;$target=$dir.'/'.$file;
 if(!is_dir(dirname($target)))mkdir(dirname($target),0777,true);
 if(!copy($source,$target)||hash_file('sha256',$source)!==hash_file('sha256',$target))throw new RuntimeException('Backup failed: '.$file);
 $manifest[]=['file'=>$file,'sha256'=>hash_file('sha256',$source)];
}
$settings=App\Models\Awesome_Admin\Page_Theme_Setting::whereIn('uri',['login','signup','forgotPassword','resetPassword'])->get()->toArray();
file_put_contents($dir.'/auth-settings.json',json_encode($settings,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
file_put_contents(__DIR__.'/backup-manifest.json',json_encode(['backup'=>$dir,'files'=>$manifest,'settings'=>$settings,'deadline'=>'2026-10-30 16:50 WIB'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
echo 'Backed up '.count($files).' source files and '.count($settings).' auth settings rows.'.PHP_EOL;
