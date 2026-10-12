<?php

function t($value, $one = '', $two = '', $three = '', $four = '')
{
    return str_replace(['{1}', '{2}', '{3}', '{4}'], [$one, $two, $three, $four], $value);
}
$root = dirname(__DIR__, 3);
putenv('APP_CONFIG_CACHE='.__DIR__.'/qa/unused-config.php');
putenv('CACHE_STORE=array');
putenv('SESSION_DRIVER=array');
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['view.compiled' => __DIR__.'/qa/compiled', 'cache.default' => 'array']);
if (!is_dir(__DIR__.'/qa/compiled')) mkdir(__DIR__.'/qa/compiled', 0777, true);
$app->instance('request', Illuminate\Http\Request::create('http://laravel-13-phoenix.aruna/article'));
$css = file_get_contents($root.'/public/assets/css/article/article-frontend-2026.css');
$typography = file_get_contents($root.'/public/assets/css/theme-responsive-typography.css');
foreach (['magazine' => 'mosaic-magazine', 'classic' => 'mosaic-classic'] as $name => $template) {
    $articles = app(App\Support\Article\ArticleTemplatePreviewFixture::class)->archivePaginator(1, '/article', $name === 'classic' ? 12 : 6);
    $articles->getCollection()->each(fn ($article, $index) => $article->category->forceFill(['id' => 9200 + $index]));
    $input = ['pagination' => ['frame' => ['enabled' => false]]];
    if ($name === 'magazine') $input['mosaic_magazine'] = ['proportion' => 'asymmetric'];
    else $input['classic_layout'] = ['lead_ratio' => '67-33', 'show_author' => false, 'show_read_more' => true, 'side_height' => ['desktop' => '7rem', 'tablet' => '7rem', 'mobile' => '14rem']];
    $options = app(App\Support\Article\ArticleTemplateOptions::class)->archive($template, $input);
    $body = view('article.templates.archive.'.$template, ['articles' => $articles, 'templateOptions' => $options, 'articleCategories' => $articles->getCollection()->pluck('category')->unique('name')->values()])->render();
    $body = str_replace('/storage/articles/template-preview-20260825/', 'assets/images/', $body);
    $html = '<!doctype html><html lang="en" style="--ph-font-family:\'Noto Sans\',sans-serif;--ph-font-size:14px;--ph-theme-primary:#137e5e"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.($name === 'classic' ? 'Mosaic Classic' : 'Mosaic Magazine').' · Revamp preview</title><link rel="icon" href="data:,"><link rel="stylesheet" href="assets/bootstrap.min.css"><link rel="stylesheet" href="assets/noto_sans/fonts.css"><link rel="stylesheet" href="assets/fontawesome/css/all.min.css"><style>'.$typography."\n".$css.'</style><link rel="stylesheet" href="common.css"><link rel="stylesheet" href="'.$name.'.css"></head><body>'.$body.'<dialog id="article-dialog" aria-labelledby="dialog-title"><form method="dialog"><span>Sample editorial content</span><button>Close</button></form><h2 id="dialog-title"></h2><p id="dialog-copy"></p></dialog><script src="preview.js"></script></body></html>';
    file_put_contents(__DIR__.'/'.$name.'.html', $html);
}
echo "Rendered both Mosaic previews from active templates; application source/settings unchanged.\n";
