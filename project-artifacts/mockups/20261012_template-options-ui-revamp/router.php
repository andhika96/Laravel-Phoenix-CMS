<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if (str_starts_with($path, '/preview/')) {
    require __DIR__.'/bootstrap.php';
    $parts = explode('/', trim($path, '/'));
    $surface = $parts[1] ?? '';
    $key = $parts[2] ?? '';
    $catalog = app(App\Support\Article\ArticleTemplateCatalog::class);
    if (!in_array($surface, ['archive', 'detail'], true) || !$catalog->isAllowed($surface, $key)) { http_response_code(404); exit; }
    $input = json_decode($_GET['template_options'] ?? '{}', true);
    if (!is_array($input)) { http_response_code(400); exit; }
    $app->instance('request', Illuminate\Http\Request::create('http://127.0.0.1:8780'.$_SERVER['REQUEST_URI']));
    $options = app(App\Support\Article\ArticleTemplateOptions::class)->{$surface}($key, $input);
    $fixture = app(App\Support\Article\ArticleTemplatePreviewFixture::class);
    $posts = $fixture->archivePaginator(1, '/article', $key === 'mosaic-classic' ? 12 : 6);
    $neighbors = $fixture->neighbors();
    $body = view($catalog->view($surface, $key), ['templateOptions' => $options, 'articles' => $posts, 'popularArticles' => $posts->getCollection()->take(4), 'articleCategories' => $posts->getCollection()->pluck('category')->unique('id'), 'article' => $fixture->detailArticle(), 'previousArticle' => $neighbors['previous'] ?? null, 'nextArticle' => $neighbors['next'] ?? null, 'relatedArticles' => collect(), 'isPreviewFixture' => true, 'previewDevice' => $_GET['preview_device'] ?? 'desktop'])->render();
    $body = str_replace('/storage/articles/template-preview-20260825/', '/assets/images/', $body);
    $typography = file_get_contents($root.'/public/assets/css/theme-responsive-typography.css');
    $css = file_get_contents($root.'/public/assets/css/article/article-frontend-2026.css');
    echo '<!doctype html><html lang="en" style="--ph-font-family:\'Noto Sans\',sans-serif;--ph-font-size:14px;--ph-theme-primary:#1aa579"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="icon" href="data:,"><link rel="stylesheet" href="/assets/bootstrap.min.css"><link rel="stylesheet" href="/assets/noto_sans/fonts.css"><link rel="stylesheet" href="/assets/fontawesome/css/all.min.css"><style>'.$typography."\n".$css.'</style></head><body>'.$body.'<script>document.addEventListener("click",e=>{if(e.target.closest("a"))e.preventDefault()});document.addEventListener("submit",e=>e.preventDefault());</script></body></html>';
    exit;
}
if ($path === '/save-demo') { http_response_code(403); header('Content-Type: application/json'); echo json_encode(['message' => 'Prototype tidak menyimpan setting CMS.']); exit; }
if ($path === '/') { readfile(__DIR__.'/index.html'); exit; }
$resolved = realpath(__DIR__.$path);
if ($resolved && str_starts_with($resolved, __DIR__.DIRECTORY_SEPARATOR) && is_file($resolved) && preg_match('/\.(html|css|js|png|woff2)$/', $resolved)) return false;
http_response_code(404);
