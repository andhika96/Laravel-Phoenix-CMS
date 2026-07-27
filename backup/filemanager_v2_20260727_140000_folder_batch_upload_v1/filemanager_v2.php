<?php

use App\Http\Controllers\Api\V2\FileManagerV2Controller;
use App\Http\Controllers\Web\FileManagerV2\FileManagerV2PageController;
use Illuminate\Support\Facades\Route;

/*
| This file is registered before the legacy generic /storage/{path} fallback.
| It prevents every direct URL into storage/app/public/filemanager_v2, including
| runtime chunks and cache files, while V2 serves files through authenticated API
| routes below.
*/
Route::any('/storage/filemanager_v2/{path?}', static function () {
    abort(404);
})->where('path', '.*')->name('filemanager_v2.storage_guard');

Route::middleware(['auth', 'checkSuspended'])->group(function (): void {
    Route::get('/admin/file-manager-v2', FileManagerV2PageController::class)
        ->name('filemanager_v2.index');

    Route::prefix('/api/v2/file-manager')->name('filemanager_v2.')->group(function (): void {
        Route::get('/bootstrap', [FileManagerV2Controller::class, 'bootstrap'])->name('bootstrap');
        Route::get('/settings', [FileManagerV2Controller::class, 'settings'])->name('settings.show');
        Route::put('/settings', [FileManagerV2Controller::class, 'saveSettings'])->name('settings.save');
        Route::post('/settings/test', [FileManagerV2Controller::class, 'testSettingsConnection'])->name('settings.test');
        Route::get('/assets', [FileManagerV2Controller::class, 'browse'])->name('assets.browse');
        Route::get('/folders', [FileManagerV2Controller::class, 'folders'])->name('folders.index');
        Route::get('/assets/details', [FileManagerV2Controller::class, 'details'])->name('assets.details');
        Route::patch('/assets', [FileManagerV2Controller::class, 'rename'])->name('assets.rename');

        Route::post('/folders', [FileManagerV2Controller::class, 'createFolder'])->name('folders.create');
        Route::post('/assets/upload', [FileManagerV2Controller::class, 'upload'])->name('assets.upload');
        Route::post('/assets/move', [FileManagerV2Controller::class, 'move'])->name('assets.move');
        Route::post('/assets/star', [FileManagerV2Controller::class, 'toggleStar'])->name('assets.star');
        Route::delete('/assets', [FileManagerV2Controller::class, 'delete'])->name('assets.delete');
        Route::post('/assets/delete-preview', [FileManagerV2Controller::class, 'deletePreview'])->name('assets.delete_preview');
        Route::post('/assets/delete-batch', [FileManagerV2Controller::class, 'deleteMany'])->name('assets.delete_many');
        Route::get('/assets/preview', [FileManagerV2Controller::class, 'preview'])->name('assets.preview');
        Route::get('/assets/download', [FileManagerV2Controller::class, 'download'])->name('assets.download');

        Route::post('/uploads', [FileManagerV2Controller::class, 'startUpload'])->name('uploads.start');
        Route::post('/uploads/{upload}/chunks/{part}', [FileManagerV2Controller::class, 'uploadChunk'])->whereNumber('part')->name('uploads.chunk');
        Route::post('/uploads/{upload}/complete', [FileManagerV2Controller::class, 'completeUpload'])->name('uploads.complete');
        Route::delete('/uploads/{upload}', [FileManagerV2Controller::class, 'cancelUpload'])->name('uploads.cancel');
    });
});
