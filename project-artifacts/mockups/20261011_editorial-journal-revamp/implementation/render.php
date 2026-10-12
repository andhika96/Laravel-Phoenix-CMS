<?php

// Translation persistence is outside this isolated renderer check.
function t($value, $one = '', $two = '', $three = '', $four = '')
{
    return str_replace(['{1}', '{2}', '{3}', '{4}'], [$one, $two, $three, $four], $value);
}
$root = dirname(__DIR__, 4);
putenv('APP_CONFIG_CACHE='.__DIR__.'/unused-config.php');
putenv('CACHE_STORE=array');
putenv('SESSION_DRIVER=array');
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['view.compiled' => __DIR__.'/compiled', 'cache.default' => 'array']);
if (!is_dir(__DIR__.'/compiled')) mkdir(__DIR__.'/compiled', 0777, true);
$app->instance('request', Illuminate\Http\Request::create('http://laravel-13-phoenix.aruna/article'));
$articles = app(App\Support\Article\ArticleTemplatePreviewFixture::class)->archivePaginator(1, '/article', 6);
$articles->getCollection()->each(fn ($article, $index) => $article->category->forceFill(['id' => 9200 + $index]));
$profiles = ['default' => []];
foreach (['boxed', 'underline', 'soft'] as $type) {
    $profiles[$type] = [
        'toolbar' => ['search' => ['enabled' => true, 'position' => 'left'], 'category' => ['enabled' => true, 'position' => 'right']],
        'pagination' => ['type' => $type],
        'editorial_journal' => ['read_more' => ['enabled' => true]],
    ];
}
$profiles['settings'] = [
    'toolbar' => ['search' => ['enabled' => true, 'position' => 'left', 'gap' => '18px', 'radius' => '16px'], 'category' => ['enabled' => true, 'position' => 'right']],
    'thumbnail' => ['height' => '240px', 'frame' => ['enabled' => true, 'radius' => '18px']],
    'pagination' => ['frame' => ['enabled' => true, 'radius' => '18px', 'border_width' => '3px'], 'show_total' => false],
    'editorial_journal' => [
        'lead_grid' => ['divider' => ['enabled' => false], 'spacing' => ['without_divider' => '36px']],
        'thumbnail' => ['edge_to_edge' => true],
        'card' => ['border' => ['enabled' => false], 'background' => ['type' => 'color', 'color' => '#f6f9fc'], 'height' => ['mode' => 'fixed', 'desktop' => '360px', 'tablet' => '340px', 'mobile' => '320px']],
        'read_more' => ['enabled' => true, 'position' => 'right', 'icon' => 'fas fa-chevron-right'],
    ],
];
$css = file_get_contents($root.'/public/assets/css/article/article-frontend-2026.css');
$typography = file_get_contents($root.'/public/assets/css/theme-responsive-typography.css');
foreach ($profiles as $name => $input) {
    $options = app(App\Support\Article\ArticleTemplateOptions::class)->archive('editorial-journal', $input);
    $body = view('article.templates.archive.editorial-journal', ['articles' => $articles, 'templateOptions' => $options, 'articleCategories' => $articles->getCollection()->pluck('category')->unique('name')->values()])->render();
    $body = str_replace('/storage/articles/template-preview-20260825/', '../assets/images/', $body);
    file_put_contents(__DIR__.'/'.$name.'.html', '<!doctype html><html lang="en" style="--ph-font-family:\'Noto Sans\',sans-serif;--ph-font-size:14px;--ph-theme-primary:#137e5e"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Editorial Journal · Active source</title><link rel="icon" href="data:,"><link rel="stylesheet" href="../assets/bootstrap.min.css"><link rel="stylesheet" href="../assets/noto_sans/fonts.css"><link rel="stylesheet" href="../assets/fontawesome/css/all.min.css"><style>'.$typography."\n".$css.'</style></head><body>'.$body.'</body></html>');
}
echo "Rendered Editorial source without preview overrides or database writes.\n";
