<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('cards.index')
        : redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/cards', [CardController::class, 'index'])->name('cards.index');
    Route::post('/cards', [CardController::class, 'store'])->name('cards.store');
    Route::put('/cards/{card}', [CardController::class, 'update'])->name('cards.update');
    Route::delete('/cards/{card}', [CardController::class, 'destroy'])->name('cards.destroy');

    Route::get('/decks', [TagController::class, 'index'])->name('tags.index');
    Route::get('/decks/{tag}', [TagController::class, 'show'])->name('tags.show');

    Route::get('/review', [ReviewController::class, 'show'])->name('review');
    Route::get('/review/{tag}', [ReviewController::class, 'show'])->name('review.deck');
});

require __DIR__.'/auth.php';
