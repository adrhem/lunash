<?php

use App\Http\Controllers\ContainerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::group(['prefix' => 'containers', 'as' => 'containers.'], function () {
        Route::post('refresh', [ContainerController::class, 'refresh'])->name('refresh');
    });
});
