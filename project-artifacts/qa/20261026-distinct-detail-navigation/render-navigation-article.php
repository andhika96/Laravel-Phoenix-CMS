<?php

require __DIR__.'/../../../vendor/autoload.php';
$app = require __DIR__.'/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$article = App\Models\Article\Article::with(['category', 'author'])->whereKey(5059)->where('title', 'like', '[QA CKEditor]%')->firstOrFail();
$settings = App\Models\Article\ArticleTemplateSetting::current();
$typography = app(App\Support\SiteTypography::class)->resolve(site_config());
$controller = app(App\Http\Controllers\Web\Article\ArticleFrontendController::class);
$neighborsFor = new ReflectionMethod($controller, 'neighbors');
$neighbors = $neighborsFor->invoke($controller, $article);
$middle = $neighbors['previous'] ?? $article;
$first = app(App\Support\Article\PublicArticleQuery::class)->eligible()->orderBy('created_at')->orderBy('id')->firstOrFail();
$cases = ['qa' => $article, 'middle' => $middle, 'first' => $first];
$manifest = [];
foreach ($cases as $name => $current) {
    $neighbors = $neighborsFor->invoke($controller, $current);
    foreach (['editorial-feature', 'knowledge-toc'] as $key) {
        $options = app(App\Support\Article\ArticleTemplateOptions::class)->detail($key, (array) data_get($settings->detail_template_options, $key, []));
        $body = view('article.templates.detail.'.$key, ['article' => $current, 'templateOptions' => $options, 'previousArticle' => $neighbors['previous'], 'nextArticle' => $neighbors['next']])->render();
        $html = '<!doctype html><html lang="en" style="--ph-font-family:'.htmlspecialchars($typography['fontFamilyName']).',sans-serif;--ph-font-size:'.$typography['fontSize'].'"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
        foreach (['/assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css','/assets/plugins/fontawesome/5.15.3/css/all.min.css','/storage/fonts/'.$typography['fontFamilyCode'].'/fonts.css','/assets/css/theme-responsive-typography.css','/assets/css/article/article-frontend-2026.css','/assets/css/article/article-editor-content.css'] as $css) $html .= '<link rel="stylesheet" href="http://laravel-13-phoenix.aruna'.$css.'?v='.filemtime(public_path(ltrim($css,'/'))).'">';
        $html .= '</head><body><main>'.$body.'</main></body></html>';
        $filename = $name.'-'.$key.'.html';
        file_put_contents(__DIR__.'/'.$filename, $html);
        $manifest[] = ['file' => $filename, 'template' => $key, 'article_id' => $current->id, 'neighbors' => count(array_filter($neighbors)), 'hrefs' => array_values(array_map(fn ($a) => route('cms.core.article.detail',$a->uri), array_filter($neighbors)))];
    }
}
file_put_contents(__DIR__.'/navigation-article-manifest.json', json_encode($manifest, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
echo json_encode(['renders' => count($manifest), 'active_template' => $settings->detail_template]);
