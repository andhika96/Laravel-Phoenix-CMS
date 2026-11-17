<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class SharedStylesheetController extends Controller
{
    private const SECTIONS = [
        'extending-css-bootstrap-5' => 'BOOTSTRAP UTILITIES',
        'phoenix-cms' => 'PHOENIX COMPONENTS',
        'theme-responsive-typography' => 'RESPONSIVE TYPOGRAPHY',
    ];

    public function __invoke(Request $request, string $stylesheet): Response
    {
        abort_unless(isset(self::SECTIONS[$stylesheet]), 404);

        // Keep each section at its original cascade position around theme styles.
        $source = file_get_contents(public_path('assets/css/shared/phoenix-shared.css'));
        $section = self::SECTIONS[$stylesheet];
        $flag = preg_quote($section, '~');
        $matched = preg_match('~/\* START: '.$flag.' \*/\r?\n(.*?)\r?\n/\* END: '.$flag.' \*/~s', $source, $matches);
        abort_unless($matched === 1, 500);
        $css = $matches[1];

        $response = response($css, 200, [
            'Content-Type' => 'text/css; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600, must-revalidate',
            'X-Content-Type-Options' => 'nosniff',
        ]);
        $response->setEtag(hash('sha256', $css));
        $response->isNotModified($request);

        return $response;
    }
}
