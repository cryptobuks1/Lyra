<?php

use SertxuDeveloper\Lyra\Http\Controllers\CRUD\CreateController;
use SertxuDeveloper\Lyra\Http\Controllers\CRUD\DestroyController;
use SertxuDeveloper\Lyra\Http\Controllers\CRUD\EditController;
use SertxuDeveloper\Lyra\Http\Controllers\CRUD\ShowController;
use SertxuDeveloper\Lyra\Http\Controllers\MediaManagerController;

Route::group(['middleware' => ['web', 'lyra-api']], function () {

  Route::prefix(config('lyra.routes.api.prefix'))->name(config('lyra.routes.api.name'))->group(function () {

    /** Media Manager routes */
    Route::get('media/disks', [MediaManagerController::class, 'disks']);
    Route::get('media/tree', [MediaManagerController::class, 'tree']);
    Route::get('media/files', [MediaManagerController::class, 'files']);
    Route::post('media/rename', [MediaManagerController::class, 'rename']);
    Route::post('media/move', [MediaManagerController::class, 'move']);
    Route::post('media/copy', [MediaManagerController::class ,'copy']);
    Route::post('media/delete', [MediaManagerController::class, 'delete']);

    /** Dynamic Resource routes */

    /** Create Controller */
    Route::get('{resource}/create', [CreateController::class, 'create']);
    Route::post('{resource}/create', [CreateController::class, 'store']);

    /** Show Controller */
    Route::get('{resource}', [ShowController::class, 'index']);
    Route::get('{resource}/{id}', [ShowController::class, 'show']);

    /** Edit Controller */
    Route::get('{resource}/{id}/edit', [EditController::class, 'edit']);
    Route::post('{resource}/{id}/edit', [EditController::class, 'update']);

    /** Destroy Controller */
    Route::post('{resource}/{id}/delete', [DestroyController::class, 'delete']);
    Route::post('{resource}/{id}/restore', [DestroyController::class, 'restore']);
    Route::post('{resource}/{id}/forceDelete', [DestroyController::class, 'forceDelete']);
  });

});
