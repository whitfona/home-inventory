<?php

use App\Http\Controllers\BoxController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::prefix('boxes')->group(function () {
    Route::get('/', [BoxController::class, 'index']);
    Route::get('/{box}', [BoxController::class, 'show']);
    Route::post('/', [BoxController::class, 'store']);
    Route::put('/{box}', [BoxController::class, 'update']);
    Route::delete('/{box}', [BoxController::class, 'destroy']);

    Route::get('/{box}/items', [ItemController::class, 'index']);
    Route::get('/{box}/items/{item}', [ItemController::class, 'show']);
    Route::post('/{box}/items', [ItemController::class, 'store']);
    Route::put('/{box}/items/{item}', [ItemController::class, 'update']);
    Route::delete('/{box}/items/{item}', [ItemController::class, 'destroy']);
});

Route::get('/search', SearchController::class);
