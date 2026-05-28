<?php

/**
 * routes/filemanager.php
 *
 * Cara include di routes/api.php:
 *
 *   Route::prefix('v1')->group(function () {
 *       require base_path('routes/filemanager.php');
 *   });
 */

use App\Http\Controllers\Api\V1\FileManagerController;
use App\Http\Controllers\Api\V1\FileManagerApiKeyController;
use App\Http\Controllers\Api\V1\FileManagerPermissionController;

Route::prefix('filemanager')->group(function () {

    // CORS preflight — tanpa auth
    Route::options('{any}', [FileManagerController::class, 'preflight'])->where('any', '.*');

    // Auth middleware FileManagerAuth
    Route::middleware(\App\Http\Middleware\FileManagerAuth::class)->group(function () {

        // Disk check & list
        Route::get('check-disk',  [FileManagerController::class, 'checkDisk']);
        Route::get('disks',       [FileManagerController::class, 'getDisks']);
        Route::get('disks/test',  [FileManagerController::class, 'testDisk']);

        // Browse (list folder + file dari storage langsung)
        Route::get('browse', [FileManagerController::class, 'browse']);

        // Info detail file atau folder
        Route::get('info', [FileManagerController::class, 'info']);

        // Image editor
        Route::post('image/edit', [FileManagerController::class, 'imageEdit']);

        // Quota
        Route::get('quota', [FileManagerController::class, 'quota']);

        // Folder operations
        Route::post  ('folder/create', [FileManagerController::class, 'createFolder']);
        Route::post  ('folder/rename', [FileManagerController::class, 'renameFolder']);
        Route::post  ('folder/move',   [FileManagerController::class, 'moveFolder']);
        Route::delete('folder/delete', [FileManagerController::class, 'deleteFolder']);

        // File operations
        Route::post  ('file/upload', [FileManagerController::class, 'upload']);
        Route::post  ('file/rename', [FileManagerController::class, 'renameFile']);
        Route::post  ('file/move',   [FileManagerController::class, 'moveFile']);
        Route::post  ('file/copy',   [FileManagerController::class, 'copyFile']);
        Route::delete('file/delete', [FileManagerController::class, 'deleteFile']);
        Route::get   ('file/serve',  [FileManagerController::class, 'serveFile'])->name('filemanager.serve');

        // Batch operations (multi-item)
        Route::post  ('batch/move',   [FileManagerController::class, 'batchMove']);
        Route::delete('batch/delete', [FileManagerController::class, 'batchDelete']);

        // Permission (ACL) — hanya untuk disk yang support per-object ACL
        Route::get ('permission',       [FileManagerPermissionController::class, 'getPermission']);
        Route::post('permission',       [FileManagerPermissionController::class, 'updatePermission']);
        Route::post('permission/bulk',  [FileManagerPermissionController::class, 'bulkUpdatePermission']);

        // Metadata (Content-Type, Cache-Control, dll) — via S3 CopyObject server-side
        Route::get ('metadata',         [FileManagerPermissionController::class, 'getMetadata']);
        Route::post('metadata',         [FileManagerPermissionController::class, 'updateMetadata']);
        Route::post('metadata/bulk',    [FileManagerPermissionController::class, 'bulkUpdateMetadata']);

        // Admin only — settings, quota management, API Key management
        Route::middleware(\App\Http\Middleware\FmAdminMiddleware::class)->group(function () {
            Route::get ('settings',                 [FileManagerController::class, 'getSettings']);
            Route::post('settings',                 [FileManagerController::class, 'updateSettings']);
            Route::post('quotas/user/{userId}',     [FileManagerController::class, 'updateUserQuota']);

            Route::get   ('api-keys',                    [FileManagerApiKeyController::class, 'index']);
            Route::post  ('api-keys',                    [FileManagerApiKeyController::class, 'store']);
            Route::get   ('api-keys/{id}',               [FileManagerApiKeyController::class, 'show']);
            Route::put   ('api-keys/{id}',               [FileManagerApiKeyController::class, 'update']);
            Route::delete('api-keys/{id}',               [FileManagerApiKeyController::class, 'destroy']);
            Route::post  ('api-keys/{id}/regenerate',    [FileManagerApiKeyController::class, 'regenerate']);
        });
    });
});
