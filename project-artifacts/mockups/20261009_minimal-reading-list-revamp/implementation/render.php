<?php

// The CMS translation helper inserts missing strings. Keep this visual fixture DB-free.
function t($value, $one = '', $two = '', $three = '', $four = '')
{
    return str_replace(['{1}', '{2}', '{3}', '{4}'], [$one, $two, $three, $four], $value);
}

$root = dirname(__DIR__, 4);
$output = __DIR__;
putenv('APP_CONFIG_CACHE='.$output.'/unused-config.php');
putenv('CACHE_STORE=array');
putenv('SESSION_DRIVER=array');
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['view.compiled' => $output.'/compiled', 'cache.default' => 'array']);
if (!is_dir($output.'/compiled')) mkdir($output.'/compiled', 0777, true);
$request = Illuminate\Http\Request::create('http://laravel-13-phoenix.aruna/article');
$app->instance('request', $request);
$fixture = app(App\Support\Article\ArticleTemplatePreviewFixture::class);
$articles = $fixture->archivePaginator(1, '/article', 6);
$articles->getCollection()->each(function ($article, $index) {
    $article->category->forceFill(['id' => 9200 + $index]);
});
$categories = $articles->getCollection()->pluck('category')->filter()->unique('name')->values();
$css = file_get_contents($root.'/public/assets/css/article/article-frontend-2026.css');
$typography = file_get_contents($root.'/public/assets/css/theme-responsive-typography.css');
$profiles = [];
foreach (['boxed', 'underline', 'soft'] as $model) $profiles[$model] = ['pagination' => ['type' => $model]];
$profiles['settings'] = [
    'header' => ['title' => ['text' => 'Reading room'], 'description' => ['enabled' => false]],
    'toolbar' => ['search' => ['gap' => '18px', 'radius' => '16px']],
    'post_list' => ['item_gap' => '24px'],
    'sidebar' => ['enabled' => false],
    'thumbnail' => ['mode' => 'background', 'height' => '280px', 'frame' => ['enabled' => true, 'radius' => '18px']],
    'article_title' => ['tag' => 'h2'],
    'pagination' => ['type' => 'boxed', 'show_total' => false, 'frame' => ['enabled' => true, 'border_width' => '3px', 'radius' => '18px']],
];
foreach ($profiles as $model => $input) {
    $options = app(App\Support\Article\ArticleTemplateOptions::class)->archive('minimal-reading-list', $input);
    $body = view('article.templates.archive.minimal-reading-list', ['articles' => $articles, 'templateOptions' => $options, 'articleCategories' => $categories, 'popularArticles' => $articles->getCollection()->take(4)])->render();
    $body = str_replace('/storage/articles/template-preview-20260825/', '../assets/', $body);
    $html = '<!doctype html><html lang="en" style="--ph-font-family:\'Noto Sans\',sans-serif;--ph-font-size:14px;--ph-theme-primary:#19a779"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Minimal Reading List · Active source</title><link rel="icon" href="data:,"><link rel="stylesheet" href="assets/bootstrap.min.css"><link rel="stylesheet" href="assets/noto_sans/fonts.css"><link rel="stylesheet" href="../assets/fontawesome/css/all.min.css"><style>'.$typography."\n".$css.'</style></head><body style="margin:0">'.$body.'</body></html>';
    file_put_contents($output.'/'.$model.'.html', $html);
}
echo "Rendered active Blade/CSS models and explicit settings profile; no database writes.\n";
