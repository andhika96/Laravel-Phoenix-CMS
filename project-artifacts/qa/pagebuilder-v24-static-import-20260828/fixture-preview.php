<?php

use App\Models\Page_Builder\Page_Builder;
use App\Support\PageBuilderElementorV24\StaticImport\StaticPageImportService;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\UploadedFile;

require dirname(__DIR__, 3).'/vendor/autoload.php';
$app = require dirname(__DIR__, 3).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$result = $app->make(StaticPageImportService::class)->convert(new UploadedFile(
    'E:/Apps/Laragon/www/ceo-masters/index.html',
    'index.html',
    'text/html',
    null,
    true,
));

$page = (new Page_Builder())->forceFill([
    'page_name' => $result['pageName'],
    'custom_css' => $result['customCss'],
    'editor_version' => Page_Builder::EDITOR_VERSION_V24,
]);

echo view('pagebuilder_elementor_v24.frontend_renderer', [
    'page' => $page,
    'pageData' => $page,
    'nodes' => $result['layout'],
])->render();
