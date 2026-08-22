<?php

namespace App\Http\Controllers\Web\PageBuilderElementorV24;

use App\Http\Controllers\Controller;
use App\Support\PageBuilderElementorV24\ModuleCatalog;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ModuleAssetController extends Controller
{
    private const ASSET_KEYS = [
        'definition.js' => 'definition',
        'canvas.vue' => 'canvas',
        'settings.vue' => 'settings',
        'runtime.js' => 'runtime',
        'styles.css' => 'styles',
    ];

    private const MIME = [
        'definition' => 'text/javascript; charset=UTF-8',
        'canvas' => 'text/plain; charset=UTF-8',
        'settings' => 'text/plain; charset=UTF-8',
        'runtime' => 'text/javascript; charset=UTF-8',
        'styles' => 'text/css; charset=UTF-8',
    ];

    public function __invoke(string $type, string $assetFile, ModuleCatalog $catalog): BinaryFileResponse
    {
        $assetKey = self::ASSET_KEYS[$assetFile] ?? null;
        abort_if($assetKey === null, 404);

        $module = $catalog->find($type);
        abort_if($module === null || ! isset($module['assets'][$assetKey]), 404);

        $path = $module['assets'][$assetKey];
        abort_unless(is_string($path) && is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => self::MIME[$assetKey],
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'ETag' => '"'.sha1_file($path).'"',
            'Last-Modified' => gmdate('D, d M Y H:i:s', filemtime($path)).' GMT',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
