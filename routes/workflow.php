<?php

use App\Http\Controllers\Web\Workflow\WorkflowController;
use App\Http\Middleware\WorkflowAccess;
use Illuminate\Support\Facades\Route;

Route::prefix('workflow')->name('cms.core.workflow.')->middleware(['auth', 'checkSuspended'])->controller(WorkflowController::class)->group(function (): void {
    Route::get('/', 'index')->name('index')->middleware(WorkflowAccess::class.':read data');
    Route::get('/data', 'data')->name('data')->middleware(WorkflowAccess::class.':read data');
    Route::get('/create', 'create')->name('create')->middleware(WorkflowAccess::class.':add data');
    Route::post('/', 'store')->name('store')->middleware(WorkflowAccess::class.':add data');
    Route::get('/{workflow}/edit', 'edit')->name('edit')->middleware(WorkflowAccess::class.':read data')->whereNumber('workflow');
    Route::put('/{workflow}', 'update')->name('update')->middleware(WorkflowAccess::class.':edit data')->whereNumber('workflow');
    Route::post('/{workflow}/publish', 'publish')->name('publish')->middleware(WorkflowAccess::class.':edit data')->whereNumber('workflow');
    Route::post('/{workflow}/duplicate', 'duplicate')->name('duplicate')->middleware(WorkflowAccess::class.':add data')->whereNumber('workflow');
    Route::post('/{workflow}/archive', 'archive')->name('archive')->middleware(WorkflowAccess::class.':delete data')->whereNumber('workflow');
    Route::post('/{workflow}/simulate', 'simulate')->name('simulate')->middleware(WorkflowAccess::class.':edit data')->whereNumber('workflow');
    Route::get('/{workflow}/runs', 'runs')->name('runs')->middleware(WorkflowAccess::class.':read data')->whereNumber('workflow');
});
