<?php
require dirname(__DIR__, 2).'/vendor/autoload.php';
$app = require dirname(__DIR__, 2).'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$settings = App\Models\Awesome_Admin\Page_Theme_Setting::get()->toArray();
file_put_contents(__DIR__.'/auth-settings-before_'.date('Ymd_His').'.json', json_encode($settings, JSON_PRETTY_PRINT));
foreach ($settings as $setting) echo json_encode(['page'=>$setting['uri'],'theme'=>$setting['page_theme'],'midnightTestimonialCount'=>count($setting['page_theme_options']['midnight_story']['testimonials']??[]),'midnightAvatarCount'=>count($setting['page_theme_options']['midnight_story']['avatars']??[])]).PHP_EOL;
