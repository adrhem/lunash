<?php

use App\Http\Controllers\ContainerController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::post('/refresh-containers', [ContainerController::class, 'refresh'])->name('refresh-containers');
});
