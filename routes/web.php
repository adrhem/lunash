<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::group(['prefix' => 'applications', 'as' => 'applications.'], function () {
        Route::post('refresh', [ApplicationController::class, 'refresh'])->name('refresh');
    });
});
