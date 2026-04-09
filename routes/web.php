<?php

use App\Http\Controllers\ClientController;
use App\Http\Middleware\CanManageClients;
use Illuminate\Support\Facades\Route;

Route::middleware(CanManageClients::class)->group(function () {
    Route::get('/clients', [ClientController::class, 'lists'])->name('clients.index');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
});

Route::get('/', function () {
    return view('welcome');
});
