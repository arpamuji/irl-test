<?php

use App\Http\Controllers\Api\ClientController;
use App\Http\Middleware\CanManageClients;
use Illuminate\Support\Facades\Route;

Route::middleware(CanManageClients::class)->group(function () {
    Route::get('/clients/{client}/summary', [ClientController::class, 'summary'])->name('clients.summary');
});
