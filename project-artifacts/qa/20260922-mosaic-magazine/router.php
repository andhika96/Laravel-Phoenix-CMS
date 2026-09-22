<?php
// Local, read-only QA harness. Renders active Blade/JS with sample content; never saves CMS settings.
$root = dirname(__DIR__, 3);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($_SERVER['REQUEST_METHOD'] !== 'GET') { http_response_code(405); exit; }
if (preg_match('#^/(assets/|storage/articles/template-preview-20260825/|storage/fonts/)#', $path)) {
    $file = realpath($root.'/public'.$path);
    $public = realpath($root.'/public');
    $storage = realpath($root.'/storage/app/public');
    if (!$file || (!str_starts_with($file, $public.DIRECTORY_SEPARATOR) && !str_starts_with($file, $storage.DIRECTORY_SEPARATOR)) || !is_file($file) || !in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['css', 'js', 'png', 'jpg', 'jpeg', 'svg', 'woff', 'woff2', 'ttf'])) { http_response_code(404); exit; }
    $type = ['css' => 'text/css', 'js' => 'application/javascript', 'svg' => 'image/svg+xml', 'woff2' => 'font/woff2', 'woff' => 'font/woff', 'ttf' => 'font/ttf'][pathinfo($file, PATHINFO_EXTENSION)] ?? mime_content_type($file);
    header('Content-Type: '.$type);
    readfile($file); exit;
}
if ($path === '/qa-vue.js') { header('Content-Type: application/javascript'); readfile($root.'/node_modules/vue/dist/vue.global.prod.js'); exit; }
require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$request = Illuminate\Http\Request::capture();
$app->instance('request', $request);
Illuminate\Support\Facades\URL::forceRootUrl('http://127.0.0.1:8769');
Illuminate\Support\Facades\URL::forceScheme('http');
$catalog = app(App\Support\Article\ArticleTemplateCatalog::class);
$normalizer = app(App\Support\Article\ArticleTemplateOptions::class);
$fixture = app(App\Support\Article\ArticleTemplatePreviewFixture::class);
$settings = new App\Models\Article\ArticleTemplateSetting($catalog->defaultSettings());
$settings->archive_template = 'mosaic-magazine';
$draft = json_decode((string) $request->query('template_options', ''), true);
$options = $normalizer->archive('mosaic-magazine', is_array($draft) ? $draft : []);
$articles = $fixture->archivePaginator();
$categories = $articles->getCollection()->map(fn ($article, $index) => (object) ['id' => $index + 1, 'name' => $article->category->name]);
if ($path === '/article' || $path === '/article/listdata') {
    $items = $articles->getCollection()->filter(function ($article, $index) use ($request) {
        return (!$request->filled('category') || (int) $request->query('category') === $index + 1)
            && (!$request->filled('search') || str_contains(strtolower($article->title), strtolower($request->query('search'))));
    })->values();
    $articles = new Illuminate\Pagination\LengthAwarePaginator($items, $items->count(), 12, 1, ['path' => route('cms.core.article')]);
    $data = ['articles' => $articles, 'templateSettings' => $settings, 'templateOptions' => $options, 'articleCategories' => $categories, 'archiveView' => 'article.templates.archive.mosaic-magazine'];
    if ($path === '/article/listdata') {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'html' => view($data['archiveView'], $data)->render(), 'total' => $items->count(), 'total_page' => 1, 'current_page' => 1, 'limit' => 12]);
        exit;
    }
    $source = file_get_contents($root.'/resources/views/article/archive.blade.php');
    preg_match("/@section\('content'\)(.*?)@endsection/s", $source, $match);
    echo '<!doctype html><html lang="en" style="--ph-theme-primary:#1aa879"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="/assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css"><link rel="stylesheet" href="/assets/plugins/fontawesome/5.15.3/css/all.min.css"><link rel="stylesheet" href="/assets/css/article/article-frontend-2026.css"></head><body>';
    echo Illuminate\Support\Facades\Blade::render($match[1], $data);
    echo '<script src="/qa-vue.js"></script><script src="/assets/plugins/vue/plugins/vuejs-paginate-next/js/vuejs-paginate-next.1.0.2.umd.js"></script><script src="/assets/js/vue3/article/vueV3-article-frontend-2026.js"></script></body></html>';
    exit;
}
if (str_starts_with($path, '/manage_article/templates/preview/') || $path === '/preview') {
    echo view('manage_article.templates.preview', [
        'surface' => 'archive', 'templateView' => 'article.templates.archive.mosaic-magazine',
        'articles' => $articles, 'templateSettings' => $settings, 'templateOptions' => $options,
        'previousArticle' => null, 'nextArticle' => null, 'articleCategories' => $categories,
        'isPreviewFixture' => true, 'previewDevice' => $request->query('device', 'desktop'),
    ])->render();
    exit;
}
if ($path !== '/manager') { http_response_code(404); exit; }
$archiveTemplates = $catalog->archive();
$detailTemplates = $catalog->detail();
foreach ($archiveTemplates as &$template) $template['preview_image'] = asset($template['preview_image']);
unset($template);
foreach ($detailTemplates as &$template) $template['preview_image'] = asset($template['preview_image']);
unset($template);
$source = file_get_contents($root.'/resources/views/manage_article/templates/index.blade.php');
preg_match("/@section\('content'\)(.*?)@endsection/s", $source, $match);
$content = Illuminate\Support\Facades\Blade::render($match[1], [
    'settings' => $settings, 'archiveTemplates' => $archiveTemplates, 'detailTemplates' => $detailTemplates,
    'archiveTemplateOptions' => $normalizer->archives(), 'detailTemplateOptions' => $normalizer->details(),
]);
echo '<!doctype html><html lang="en" style="--ph-theme-primary:#1aa879;--ph-font-family:Arial,sans-serif"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="/assets/plugins/bootstrap/5.3.6_custom/bootstrap.min.css"><link rel="stylesheet" href="/assets/plugins/fontawesome/5.15.3/css/all.min.css"><link rel="stylesheet" href="/assets/css/article/article-template-manager-2026.css"><style>body{padding:24px;background:#f6f8fb}.ph-btn-theme{background:var(--ph-theme-primary);color:white}.ph-btn-theme-outline{color:var(--ph-theme-primary);border-color:var(--ph-theme-primary)}</style></head><body>';
echo '<p>Local QA · sample content · Save is disabled</p>'.$content;
echo '<script src="/qa-vue.js"></script><script src="/assets/plugins/bootstrap/5.3.6/js/bootstrap.bundle.min.js"></script><script src="/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js"></script></body></html>';
