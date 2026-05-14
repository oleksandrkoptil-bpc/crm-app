<?php

use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\TicketStatisticsController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.token')->group(function () {
    Route::post('/tickets', [TicketController::class, 'store'])->middleware('ticket.limit');
    Route::get('/tickets/statistics', TicketStatisticsController::class);
});
