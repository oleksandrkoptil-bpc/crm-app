<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Manager\TicketController;
use App\Http\Controllers\WidgetController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('manager.tickets.index');
    }

    return redirect()->route('login');
});

Route::get('/docs/api-docs.json', function () {
    abort_unless(file_exists(storage_path('api-docs/api-docs.json')), 404);

    return response()->file(storage_path('api-docs/api-docs.json'), [
        'Content-Type' => 'application/json',
    ]);
})->name('swagger.docs.json');

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
