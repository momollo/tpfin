<?php

use App\Http\Controllers\Api\TicketApiController;
use Illuminate\Support\Facades\Route;

/*
 * API AlchiClick — endpoints avancés (Jour 7 du TP BTS)
 * Préfixe : /api
 */
Route::prefix('tickets')->group(function () {
    Route::get('open',           [TicketApiController::class, 'openTickets']);
    Route::get('closed',         [TicketApiController::class, 'closedTickets']);
    Route::get('stats',          [TicketApiController::class, 'stats']);
});

Route::get('users/{email}/tickets', [TicketApiController::class, 'userTickets']);
