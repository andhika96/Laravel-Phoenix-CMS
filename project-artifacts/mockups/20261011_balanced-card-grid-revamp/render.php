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
$articles = app(App\Support\Article\ArticleTemplatePreviewFixture::class)->archivePaginator(1, '/article', 6);
$articles->getCollection()->each(fn ($article, $index) => $article->category->forceFill(['id' => 9200 + $index]));
$profiles = ['default' => []];
foreach (['boxed', 'underline', 'soft'] as $type) $profiles[$type] = ['pagination' => ['type' => $type]];
foreach (['flat', 'outline', 'elevated'] as $style) $profiles[$style] = ['balanced_grid' => ['card_style' => $style]];
$profiles['custom'] = [
    'grid' => ['desktop' => 2, 'tablet' => 2, 'mobile' => 2],
    'toolbar' => ['search' => ['position' => 'left', 'gap' => '18px', 'radius' => '16px', 'input_background_color' => '#eef7f2'], 'category' => ['position' => 'right']],
    'thumbnail' => ['height' => '180px', 'fit' => 'contain', 'frame' => ['enabled' => true, 'radius' => '18px', 'border_width' => '2px']],
    'balanced_grid' => ['text_align' => 'center', 'card_padding' => '24px', 'card_radius' => '20px', 'excerpt_lines' => 4, 'gap' => ['desktop' => '40px', 'tablet' => '28px', 'mobile' => '24px']],
    'article_title' => ['tag' => 'h3'],
    'pagination' => ['type' => 'soft', 'frame' => ['enabled' => true, 'radius' => '18px', 'border_width' => '3px'], 'show_total' => false, 'item_gap' => '12px'],
    'shell' => ['padding' => ['enabled' => true, 'desktop' => ['top' => '26px'], 'tablet' => ['top' => '22px'], 'mobile' => ['top' => '18px']]],
];
$profiles['details-off'] = ['balanced_grid' => ['show_category' => false, 'show_excerpt' => false, 'show_date' => false, 'show_read_more' => false]];
$profiles['asset'] = ['thumbnail' => ['mode' => 'asset', 'fit' => 'contain']];
$profiles['empty'] = [];
$profiles['filtered'] = [];
$css = file_get_contents($root.'/public/assets/css/article/article-frontend-2026.css');
$typography = file_get_contents($root.'/public/assets/css/theme-responsive-typography.css');
$demoStyle = '<style>[hidden]{display:none!important}.preview-empty{grid-column:1/-1;padding:2rem;color:#667085}dialog{width:min(660px,calc(100% - 32px));max-height:85dvh;padding:1.5rem;border:1px solid #e6e9ef;border-radius:1rem;color:#172033;font:inherit}dialog::backdrop{background:rgb(23 32 51 / .5)}dialog form{display:flex;justify-content:space-between;gap:1rem}dialog button{min-height:44px;padding:.4rem .8rem;border:1px solid #d8dce5;border-radius:.5rem;background:#fff;font:inherit}dialog h2{font-size:var(--ph-fmv2-rfs-h3,1.5em);line-height:1.4;margin-top:1rem}dialog p{color:#667085;line-height:1.75}</style>';
foreach ($profiles as $name => $input) {
    $app->instance('request', Illuminate\Http\Request::create('http://laravel-13-phoenix.aruna/article'.($name === 'filtered' ? '?search=systems' : '')));
    $options = app(App\Support\Article\ArticleTemplateOptions::class)->archive('balanced-card-grid', $input);
    $posts = $name === 'empty' ? new Illuminate\Pagination\LengthAwarePaginator([], 0, 6, 1, ['path' => '/article']) : $articles;
    $body = view('article.templates.archive.balanced-card-grid', ['articles' => $posts, 'templateOptions' => $options, 'articleCategories' => $articles->getCollection()->pluck('category')->unique('name')->values()])->render();
    $body = str_replace('/storage/articles/template-preview-20260825/', ($name === 'default' ? '' : '../').'assets/images/', $body);
    $prefix = $name === 'default' ? '' : '../';
    $demo = $name === 'default' ? '<dialog id="article-dialog" aria-labelledby="dialog-title"><form method="dialog"><span>Sample editorial content</span><button>Close</button></form><h2 id="dialog-title"></h2><p id="dialog-copy"></p></dialog><script src="preview.js"></script>' : '';
    $html = '<!doctype html><html lang="en" style="--ph-font-family:\'Noto Sans\',sans-serif;--ph-font-size:14px;--ph-theme-primary:#137e5e"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Balanced Card Grid · Revamp</title><link rel="icon" href="data:,"><link rel="stylesheet" href="'.$prefix.'assets/bootstrap.min.css"><link rel="stylesheet" href="'.$prefix.'assets/noto_sans/fonts.css"><link rel="stylesheet" href="'.$prefix.'assets/fontawesome/css/all.min.css"><style>'.$typography."\n".$css.'</style>'.($name === 'default' ? $demoStyle : '').'</head><body>'.$body.$demo.'</body></html>';
    file_put_contents(__DIR__.'/'.($name === 'default' ? 'index.html' : 'implementation/'.$name.'.html'), $html);
}
file_put_contents(__DIR__.'/implementation/normalized-profiles.json', json_encode(array_map(fn ($input) => app(App\Support\Article\ArticleTemplateOptions::class)->archive('balanced-card-grid', $input), $profiles), JSON_PRETTY_PRINT));
echo count($profiles)." Balanced profiles rendered from active Blade, normalizer, and CSS; no CMS settings changed.\n";
