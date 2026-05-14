<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Manager\TicketController;
use App\Http\Controllers\WidgetController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/widget', WidgetController::class)->name('widget');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');


Route::prefix('manager')
    ->name('manager.')
    ->middleware(['auth', 'role:manager'])
    ->group(function () {

        Route::redirect('/', '/manager/tickets');

        Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');

        Route::get(
            '/tickets/{ticket}',
            [TicketController::class, 'show']
        )->name('tickets.show');

        Route::patch(
            '/tickets/{ticket}/status',
            [TicketController::class, 'updateStatus']
        )->name('tickets.update-status');

        Route::get(
            '/tickets/{ticket}/media/{media}',
            [TicketController::class, 'download']
        )->name('tickets.media.download');
    });
