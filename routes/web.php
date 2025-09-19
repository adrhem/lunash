<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::group(['prefix' => 'applications', 'as' => 'applications.'], function () {
        Route::post('refresh', [ApplicationController::class, 'refresh'])->name('refresh');
        Route::post('start/{id}', [ApplicationController::class, 'start'])->name('start');
        Route::post('stop/{id}', [ApplicationController::class, 'stop'])->name('stop');
        Route::post('restart/{id}', [ApplicationController::class, 'restart'])->name('restart');
        Route::post('pull/{id}', [ApplicationController::class, 'pull'])->name('pull');
        Route::get('logs/{id}', [ApplicationController::class, 'logs'])->name('logs');
    });
});
