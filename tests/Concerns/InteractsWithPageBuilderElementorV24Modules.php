<?php

namespace Tests\Concerns;

use App\Support\PageBuilderElementorV24\ModuleCatalog;
use Illuminate\Contracts\View\View;
use RuntimeException;

trait InteractsWithPageBuilderElementorV24Modules
{
    protected function pageBuilderV24Module(string $type): array
    {
        $module = (new ModuleCatalog)->find($type);

        if ($module === null) {
            throw new RuntimeException('Page Builder v2.4 module is inactive: '.$type);
        }

        return $module;
    }

    protected function pageBuilderV24ModuleView(array $module, array $data = []): View
    {
        return view()->file($module['assets']['view'], $data);
    }

    protected function pageBuilderV24ModuleViewByType(string $type, array $data = []): View
    {
        return $this->pageBuilderV24ModuleView($this->pageBuilderV24Module($type), $data);
    }
}
