<?php

/* New Page Builder (Legacy) */

Route::controller(App\Http\Controllers\Web\PageBuilder\PageBuilder_Controller::class)->group(function()
{
	Route::get('/pagebuilder', 'index')->name('cms.core.pagebuilder');
	
	Route::get('/pagebuilder/create', 'create')->name('cms.core.pagebuilder.create');
	Route::post('/pagebuilder/store', 'store')->name('cms.core.pagebuilder.store');

	Route::get('/pagebuilder/edit/{idOrSlug}', 'edit')->name('cms.core.pagebuilder.edit');
	Route::post('/pagebuilder/update/{idOrSlug}', 'update')->name('cms.core.pagebuilder.update');

	Route::get('/pagebuilder/data/{idOrSlug}', 'getData')->name('cms.core.pagebuilder.data');
	Route::get('/pagebuilder/ads', 'ads')->name('cms.core.pagebuilder.asd');
});

/* New Page Builder Elementor Style (Isolated) */

Route::controller(App\Http\Controllers\Web\PageBuilderElementor\PageBuilderElementor_Controller::class)->group(function()
{
	Route::get('/pagebuilder-elementor/create', 'create')->name('cms.core.pagebuilder_elementor.create');
	Route::post('/pagebuilder-elementor/store', 'store')->name('cms.core.pagebuilder_elementor.store');

	Route::get('/pagebuilder-elementor/edit/{idOrSlug}', 'edit')->name('cms.core.pagebuilder_elementor.edit');
	Route::post('/pagebuilder-elementor/update/{idOrSlug}', 'update')->name('cms.core.pagebuilder_elementor.update');

	Route::get('/pagebuilder-elementor/data/{idOrSlug}', 'getData')->name('cms.core.pagebuilder_elementor.data');
	Route::get('/pagebuilder-elementor/image-rendition', 'imageRendition')->name('cms.core.pagebuilder_elementor.image_rendition');

	Route::get('/pagebuilder-elementor/preview/{idOrSlug}', 'preview')->name('cms.core.pagebuilder_elementor.preview');
	Route::post('/pagebuilder-elementor/form/{idOrSlug}/{nodeId}', 'submitForm')
		->middleware('throttle:20,1')
		->name('cms.core.pagebuilder_elementor.form.submit');
});

Route::controller(App\Http\Controllers\Web\PageBuilderElementorV23\PageBuilderElementorV23Controller::class)
	->prefix('pagebuilder-elementor/v2.3')
	->group(function () {
		Route::middleware(['auth', 'checkSuspended'])->group(function () {
			Route::get('/create', 'create')->name('cms.core.pagebuilder_elementor_v23.create');
			Route::post('/store', 'store')->name('cms.core.pagebuilder_elementor_v23.store');
			Route::get('/edit/{idOrSlug}', 'edit')->name('cms.core.pagebuilder_elementor_v23.edit');
			Route::post('/update/{idOrSlug}', 'update')->name('cms.core.pagebuilder_elementor_v23.update');
			Route::get('/data/{idOrSlug}', 'getData')->name('cms.core.pagebuilder_elementor_v23.data');
			Route::get('/image-rendition', 'imageRendition')->name('cms.core.pagebuilder_elementor_v23.image_rendition');
			Route::get('/preview/{idOrSlug}', 'preview')->name('cms.core.pagebuilder_elementor_v23.preview');
			Route::post('/form/editor-draft', 'submitEditorDraftForm')
				->middleware('throttle:10,1')
				->name('cms.core.pagebuilder_elementor_v23.form.editor_draft');
		});
		Route::post('/form/{idOrSlug}/{nodeId}', 'submitForm')->middleware('throttle:20,1')->name('cms.core.pagebuilder_elementor_v23.form.submit');
	});

Route::controller(App\Http\Controllers\Web\PageBuilderElementorV23\FormDatasetController::class)
	->prefix('pagebuilder-elementor/v2.3/datasets')
	->middleware(['auth', 'checkSuspended'])
	->group(function () {
		Route::get('/', 'index')->name('cms.core.pagebuilder_elementor_v23.datasets.index');
		Route::post('/', 'store')->name('cms.core.pagebuilder_elementor_v23.datasets.store');
		Route::put('/{datasetId}', 'update')->name('cms.core.pagebuilder_elementor_v23.datasets.update');
		Route::delete('/{datasetId}', 'destroy')->name('cms.core.pagebuilder_elementor_v23.datasets.destroy');
	});

/* New Arunika Themes */

Route::controller(App\Http\Controllers\Web\Themes\Themes_Controller::class)->group(function()
{
	Route::get('/arunika', 'index')->name('cms.core.arunika_themes');
	Route::get('/arunika/v1', 'index')->name('cms.core.arunika_themes.v1');
	Route::get('/arunika/v1/gemini', 'gemini')->name('cms.core.arunika_themes.v1.gemini');
});


/* File Manager v2 */
// Bisa diakses tanpa session (standalone dengan API key)
// Session auth juga tetap bekerja untuk akses internal
Route::get('/filemanager', function () 
{
	return view('filemanager.filemanager');

})->name('filemanager');

Route::get('/filemanager/thumbnail', [\App\Http\Controllers\Api\V1\FileManagerController::class, 'imagePreview'])->middleware('auth')->name('filemanager.thumbnail');

/* Installation Setup */

Route::controller(App\Http\Controllers\Web\Setup\Setup_Controller::class)->group(function()
{
	Route::get('/setup', 'index')->name('cms.core.setup');
	Route::get('/setup/success', 'setup_success')->name('cms.core.setup_success');
	Route::post('setup/process', 'process')->name('cms.core.auth.setup.process');
	// Route::post('setup/process', 'process')->name('cms.core.auth.setup.process');
});


/* Testing */

Route::name('cms.core.')
	->prefix('testing')
	->namespace('App\Http\Controllers\Web\Testing')
	->group(function() 
	{
		Route::controller(\Testing_Controller::class)->group(function()
		{
			// Route::get('/', 'index')->name('testing')->middleware('auth', 'permission:read data');
			// Route::get('/add', 'add')->name('testing.add')->middleware('auth', 'permission:submit data');
			Route::get('/benchmark-db', 'benchmark')->name('testing.benchmark');
		});
	});
