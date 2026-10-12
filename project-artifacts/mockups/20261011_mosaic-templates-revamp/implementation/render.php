<?php

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
$css = file_get_contents($root.'/public/assets/css/article/article-frontend-2026.css');
$typography = file_get_contents($root.'/public/assets/css/theme-responsive-typography.css');
foreach (['magazine' => 'mosaic-magazine', 'classic' => 'mosaic-classic'] as $name => $template) {
    $articles = app(App\Support\Article\ArticleTemplatePreviewFixture::class)->archivePaginator(1, '/article', $name === 'classic' ? 12 : 6);
    $articles->getCollection()->each(fn ($article, $index) => $article->category->forceFill(['id' => 9200 + $index]));
    $profiles = ['default' => []];
    foreach (['boxed', 'underline', 'soft'] as $type) $profiles[$type] = ['pagination' => ['type' => $type]];
    $profiles['settings'] = [
        'toolbar' => ['search' => ['position' => 'left', 'gap' => '18px', 'radius' => '16px', 'input_background_color' => '#eef7f2', 'button_background_color' => '#285d44'], 'category' => ['position' => 'right']],
        'thumbnail' => ['mode' => 'background', 'height' => '180px', 'fit' => 'contain', 'frame' => ['enabled' => true, 'radius' => '18px', 'border_width' => '2px']],
        'pagination' => ['type' => 'soft', 'frame' => ['enabled' => true, 'radius' => '18px', 'border_width' => '3px'], 'show_total' => false, 'item_gap' => '12px'],
        'grid' => ['desktop' => 2, 'tablet' => 2, 'mobile' => 2],
        'shell' => ['padding' => ['enabled' => true, 'desktop' => ['top' => '26px'], 'tablet' => ['top' => '22px'], 'mobile' => ['top' => '18px']]],
        'mosaic_magazine' => ['supporting_count' => 1, 'proportion' => 'asymmetric'],
        'mosaic_classic' => [
            'side_position' => 'left', 'lead_ratio' => '60-40', 'side_layout' => 'stacked', 'side_image_position' => 'left', 'divider' => false, 'side_numbers' => true, 'lead_layout' => 'image-first',
            'show_category' => false, 'show_excerpt' => false, 'show_author' => false, 'show_reading_time' => false, 'show_date' => false, 'show_read_more' => true,
            'gap' => ['desktop' => '40px', 'tablet' => '28px', 'mobile' => '24px'],
            'lead_height' => ['desktop' => '300px', 'tablet' => '240px', 'mobile' => '200px'],
            'side_height' => ['desktop' => '180px', 'tablet' => '140px', 'mobile' => '200px'],
            'highlight_height' => ['desktop' => '180px', 'tablet' => '160px', 'mobile' => '150px'],
            'highlight_columns' => ['desktop' => 1, 'tablet' => 1, 'mobile' => 1],
        ],
    ];
    $profiles['asset'] = ['thumbnail' => ['mode' => 'asset', 'fit' => 'contain']];
    $profiles['filtered'] = [];
    foreach ($profiles as $profile => $input) {
        $app->instance('request', Illuminate\Http\Request::create('http://laravel-13-phoenix.aruna/article'.($profile === 'filtered' ? '?search=systems' : '')));
        $options = app(App\Support\Article\ArticleTemplateOptions::class)->archive($template, $input);
        $body = view('article.templates.archive.'.$template, ['articles' => $articles, 'templateOptions' => $options, 'articleCategories' => $articles->getCollection()->pluck('category')->unique('name')->values()])->render();
        $body = str_replace('/storage/articles/template-preview-20260825/', '../assets/images/', $body);
        $html = '<!doctype html><html lang="en" style="--ph-font-family:\'Noto Sans\',sans-serif;--ph-font-size:14px;--ph-theme-primary:#137e5e"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.$template.' · Active source</title><link rel="icon" href="data:,"><link rel="stylesheet" href="../assets/bootstrap.min.css"><link rel="stylesheet" href="../assets/noto_sans/fonts.css"><link rel="stylesheet" href="../assets/fontawesome/css/all.min.css"><style>'.$typography."\n".$css.'</style></head><body>'.$body.'</body></html>';
        file_put_contents(__DIR__.'/'.$name.'-'.$profile.'.html', $html);
    }
}
echo "Rendered 14 source fixtures without preview CSS or database writes.\n";
