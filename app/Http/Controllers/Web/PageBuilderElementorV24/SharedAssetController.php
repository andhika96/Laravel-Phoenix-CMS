<?php

namespace App\Http\Controllers\Web\PageBuilderElementorV24;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class SharedAssetController extends Controller
{
    private const ASSETS = [
        'advanced.vue' => 'AdvancedControls.vue',
        'typography.vue' => 'controls/TypographyControl.vue',
        'link.vue' => 'controls/LinkControl.vue',
        'dynamic-tag.vue' => 'controls/DynamicTagControl.vue',
        'css-filter.vue' => 'controls/CssFilterControl.vue',
        'text-stroke.vue' => 'controls/TextStrokeControl.vue',
        'text-shadow.vue' => 'controls/TextShadowControl.vue',
        'grid-column-style.vue' => 'controls/GridColumnStyleControls.vue',
    ];

    public function __invoke(string $assetFile): BinaryFileResponse
    {
        abort_unless(isset(self::ASSETS[$assetFile]), 404);

        $path = resource_path('pagebuilder_elementor_v24/shared/'.self::ASSETS[$assetFile]);
        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'private, max-age=0, must-revalidate',
            'ETag' => '"'.sha1_file($path).'"',
            'Last-Modified' => gmdate('D, d M Y H:i:s', filemtime($path)).' GMT',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
