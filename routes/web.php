<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/boxes', fn () => Inertia::render('Boxes/Index'))->name('dashboard');
    Route::get('/boxes/{box}', fn (string $box) => Inertia::render('Boxes/Show', ['id' => $box]))->name('boxes.show');
    Route::get('/boxes/{box}/items/{item}', fn (string $box, string $item) => Inertia::render('Items/Show', ['boxId' => $box, 'itemId' => $item]))->name('items.show');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
