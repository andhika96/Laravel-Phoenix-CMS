<?php

use App\Http\Controllers\Web\PageBuilderElementorV24\FormDatasetController;
use App\Http\Controllers\Web\PageBuilderElementorV24\ModuleAssetController;
use App\Http\Controllers\Web\PageBuilderElementorV24\PageBuilderElementorV24Controller;
use App\Http\Controllers\Web\PageBuilderElementorV24\SharedAssetController;
use Illuminate\Support\Facades\Route;

Route::get(
    'pagebuilder-elementor/v2.4/module-assets/{type}/{assetFile}',
    ModuleAssetController::class,
)
    ->middleware(['auth', 'checkSuspended'])
    ->where('type', '[a-z][a-z0-9_]*')
    ->where('assetFile', '(?:definition|runtime)\.js|(?:canvas|settings)\.vue|styles\.css')
    ->name('cms.core.pagebuilder_elementor_v24.module_asset');

Route::get(
    'pagebuilder-elementor/v2.4/shared-assets/{assetFile}',
    SharedAssetController::class,
)
    ->middleware(['auth', 'checkSuspended'])
    ->where('assetFile', '[a-z][a-z0-9-]*\.vue')
    ->name('cms.core.pagebuilder_elementor_v24.shared_asset');

Route::controller(PageBuilderElementorV24Controller::class)
    ->prefix('pagebuilder-elementor/v2.4')
    ->group(function () {
        Route::middleware(['auth', 'checkSuspended'])->group(function () {
            Route::get('/create', 'create')->name('cms.core.pagebuilder_elementor_v24.create');
            Route::post('/store', 'store')->name('cms.core.pagebuilder_elementor_v24.store');
            Route::get('/edit/{idOrSlug}', 'edit')->name('cms.core.pagebuilder_elementor_v24.edit');
            Route::post('/update/{idOrSlug}', 'update')->name('cms.core.pagebuilder_elementor_v24.update');
            Route::get('/data/{idOrSlug}', 'getData')->name('cms.core.pagebuilder_elementor_v24.data');
            Route::get('/image-rendition', 'imageRendition')->name('cms.core.pagebuilder_elementor_v24.image_rendition');
            Route::get('/preview/{idOrSlug}', 'preview')->name('cms.core.pagebuilder_elementor_v24.preview');
            Route::post('/import/static', 'importStatic')->name('cms.core.pagebuilder_elementor_v24.import_static');
            Route::post('/form/editor-draft', 'submitEditorDraftForm')
                ->middleware('throttle:10,1')
                ->name('cms.core.pagebuilder_elementor_v24.form.editor_draft');
        });

        Route::post('/form/{idOrSlug}/{nodeId}', 'submitForm')
            ->middleware('throttle:20,1')
            ->name('cms.core.pagebuilder_elementor_v24.form.submit');
    });

Route::controller(FormDatasetController::class)
    ->prefix('pagebuilder-elementor/v2.4/datasets')
    ->middleware(['auth', 'checkSuspended'])
    ->group(function () {
        Route::get('/', 'index')->name('cms.core.pagebuilder_elementor_v24.datasets.index');
        Route::post('/', 'store')->name('cms.core.pagebuilder_elementor_v24.datasets.store');
        Route::put('/{datasetId}', 'update')->name('cms.core.pagebuilder_elementor_v24.datasets.update');
        Route::delete('/{datasetId}', 'destroy')->name('cms.core.pagebuilder_elementor_v24.datasets.destroy');
    });
