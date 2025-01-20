<?php

use App\Http\Controllers\BoxController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

Route::get('/boxes', [BoxController::class, 'index']);
Route::get('/boxes/{box}', [BoxController::class, 'show']);
Route::post('/boxes', [BoxController::class, 'store']);
Route::put('/boxes/{box}', [BoxController::class, 'update']);
Route::delete('/boxes/{box}', [BoxController::class, 'destroy']);

Route::get('/items', [ItemController::class, 'index']);
Route::get('/items/{item}', [ItemController::class, 'show']);
Route::post('/items', [ItemController::class, 'store']);
Route::put('/items/{item}', [ItemController::class, 'update']);
Route::delete('/items/{item}', [ItemController::class, 'destroy']);

Route::get('/search', SearchController::class);
