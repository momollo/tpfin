<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Routes protégées
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/',               [GameController::class, 'index'])->name('game');
    Route::post('/game/save',     [GameController::class, 'save'])->name('game.save');
    Route::post('/game/upgrade',  [GameController::class, 'buyUpgrade'])->name('game.upgrade');
    Route::get('/leaderboard',    [GameController::class, 'leaderboard'])->name('game.leaderboard');

    Route::resource('tickets', TicketController::class);

    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
});