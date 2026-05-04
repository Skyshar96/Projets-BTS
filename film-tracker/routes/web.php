<?php

use App\Http\Controllers\MovieController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WatchItemController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register'])->name('register.store');
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::get('/ma-liste', [WatchItemController::class, 'index'])->name('watch-items.index');
    Route::post('/films/{imdbId}/avis', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/watch-items', [WatchItemController::class, 'store'])->name('watch-items.store');
    Route::patch('/watch-items/{watchItem}', [WatchItemController::class, 'update'])->name('watch-items.update');
});

Route::get('/', [MovieController::class, 'index'])->name('movies.index');
Route::get('/films/{imdbId}', [MovieController::class, 'show'])->name('movies.show');
