<?php

use App\Http\Controllers\Web\PageBuilderElementor\PageBuilderElementorPublishedPageController;
use Illuminate\Support\Facades\Route;

Route::get('/pages/{uri}', PageBuilderElementorPublishedPageController::class)
    // Compatibility alias retained for existing v2.3 callers.
    ->name('cms.public.pagebuilder_elementor_v23.show');
