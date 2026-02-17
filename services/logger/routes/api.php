<?php

use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::post('/logs', [LogController::class, 'store']);

Route::get('/logs', [LogController::class, 'index']);
