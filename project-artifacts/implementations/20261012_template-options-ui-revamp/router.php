<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$projectRoot = dirname(__DIR__, 3);
$fixtureRoot = realpath(dirname(__DIR__, 2).'/mockups/20261012_template-options-ui-revamp');
if ($path === '/') { readfile(__DIR__.'/index.html'); exit; }
$sourceAssets = [
    '/assets/manager.css' => $projectRoot.'/public/assets/css/article/article-template-manager-2026.css',
    '/assets/manager.js' => $projectRoot.'/public/assets/js/vue3/manage_article_templates/vueV3-manage-article-templates-2026.js',
];
if (isset($sourceAssets[$path])) {
    header('Content-Type: '.(str_ends_with($path, '.css') ? 'text/css' : 'application/javascript'));
    readfile($sourceAssets[$path]);
    exit;
}
if (str_starts_with($path, '/preview/') || $path === '/save-demo') { require $fixtureRoot.'/router.php'; exit; }
$resolved = realpath($fixtureRoot.$path);
$types = ['css' => 'text/css', 'js' => 'application/javascript', 'png' => 'image/png', 'woff2' => 'font/woff2'];
$extension = pathinfo($resolved ?: '', PATHINFO_EXTENSION);
if (str_starts_with($path, '/assets/') && $resolved && str_starts_with($resolved, $fixtureRoot.DIRECTORY_SEPARATOR) && is_file($resolved) && isset($types[$extension])) {
    header('Content-Type: '.$types[$extension]);
    readfile($resolved);
    exit;
}
http_response_code(404);
