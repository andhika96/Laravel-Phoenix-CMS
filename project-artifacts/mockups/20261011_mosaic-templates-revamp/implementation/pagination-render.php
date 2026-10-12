<?php

require __DIR__.'/render.php';
$articleFixture = app(App\Support\Article\ArticleTemplatePreviewFixture::class)->archivePaginator(1, '/article', 6);
$paginationFiles = [];
foreach (app(App\Support\Article\ArticleTemplateCatalog::class)->archive() as $template => $entry) {
    foreach (['boxed', 'underline', 'soft'] as $type) {
        $app->instance('request', Illuminate\Http\Request::create('http://laravel-13-phoenix.aruna/article'));
        $options = app(App\Support\Article\ArticleTemplateOptions::class)->archive($template, ['pagination' => ['type' => $type]]);
        $body = view($entry['view'], ['articles' => $articleFixture, 'templateOptions' => $options, 'articleCategories' => collect()])->render();
        $body = str_replace('/storage/articles/template-preview-20260825/', '../assets/images/', $body);
        $file = 'pagination-'.$template.'-'.$type.'.html';
        $html = '<!doctype html><html lang="en" style="--ph-font-family:\'Noto Sans\',sans-serif;--ph-font-size:14px;--ph-theme-primary:#137e5e"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Pagination parity</title><link rel="icon" href="data:,"><link rel="stylesheet" href="../assets/bootstrap.min.css"><link rel="stylesheet" href="../assets/noto_sans/fonts.css"><link rel="stylesheet" href="../assets/fontawesome/css/all.min.css"><style>'.$typography."\n".$css.'</style></head><body>'.$body.'</body></html>';
        file_put_contents(__DIR__.'/'.$file, $html);
        $paginationFiles[] = ['template' => $template, 'type' => $type, 'file' => $file];
    }
}
file_put_contents(__DIR__.'/pagination-files.json', json_encode($paginationFiles, JSON_PRETTY_PRINT));
echo count($paginationFiles)." pagination fixtures rendered from all active archive templates.\n";
