<?php

namespace App\Http\Controllers\Web\PageBuilderElementor;

use App\Http\Controllers\Controller;
use App\Models\Page_Builder\Page_Builder;

class PageBuilderElementorPublishedPageController extends Controller
{
    public function __invoke(string $uri)
    {
        $pageData = Page_Builder::query()
            ->where('status', 'publish')
            ->where('uri', $uri)
            ->firstOrFail();

        $view = [
            Page_Builder::EDITOR_VERSION_V23 => 'pagebuilder_elementor_v23.frontend_renderer',
            Page_Builder::EDITOR_VERSION_V24 => 'pagebuilder_elementor_v24.frontend_renderer',
        ][(string) $pageData->editor_version] ?? null;

        abort_unless($view, 404);

        $nodes = is_array($pageData->vars)
            ? $pageData->vars
            : (json_decode($pageData->vars ?? '[]', true) ?? []);

        return view($view, [
            'pageData' => $pageData,
            'nodes' => $nodes,
        ]);
    }
}
