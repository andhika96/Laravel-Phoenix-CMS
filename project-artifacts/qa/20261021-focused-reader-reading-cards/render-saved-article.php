<?php

require __DIR__.'/../../../vendor/autoload.php';
$app = require __DIR__.'/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$article = App\Models\Article\Article::with(['category', 'author'])->whereKey(5059)->where('title', 'like', '[QA CKEditor]%')->firstOrFail();
$settings = App\Models\Article\ArticleTemplateSetting::current();
$typography = app(App\Support\SiteTypography::class)->resolve(site_config());
$catalog = app(App\Support\Article\ArticleTemplateCatalog::class);
$base = 'http://laravel-13-phoenix.aruna';
$manifest = ['id' => $article->id, 'uri' => $article->uri, 'saved_template' => $settings->detail_template, 'content_sha256' => hash('sha256', $article->content), 'font' => $typography];
foreach ($catalog->detail() as $key => $definition) {
    $templateOptions = app(App\Support\Article\ArticleTemplateOptions::class)->detail($key, (array) data_get($settings->detail_template_options, $key, []));
    $body = view($definition['view'], ['article' => $article, 'templateOptions' => $templateOptions, 'previousArticle' => null, 'nextArticle' => null])->render();
    $html = '<!doctype html><html lang="id" style="--ph-font-family:'.htmlspecialchars($typography['fontFamilyName']).',-apple-system,BlinkMacSystemFont,Segoe UI,sans-serif;--ph-font-size:'.$typography['fontSize'].';--ph-theme-primary:#1fa675"><head><base href="'.$base.'/"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
    foreach (['/assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css', '/storage/fonts/'.$typography['fontFamilyCode'].'/fonts.css', '/assets/css/theme-responsive-typography.css', '/assets/css/article/article-frontend-2026.css', '/assets/css/article/article-editor-content.css'] as $css) $html .= '<link rel="stylesheet" href="'.$base.$css.'?v='.filemtime(public_path(ltrim($css, '/'))).'">';
    $html .= '</head><body style="margin:0"><main>'.$body.'</main></body></html>';
    file_put_contents(__DIR__.'/saved-'.$key.'.html', $html);
}
file_put_contents(__DIR__.'/saved-article-manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo json_encode(['id' => $article->id, 'templates' => array_keys($catalog->detail()), 'saved_template' => $settings->detail_template]);
