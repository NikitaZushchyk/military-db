<?php

use App\Http\Controllers\LoggerController;
use Illuminate\Support\Facades\Route;

Route::post('/logs', [LoggerController::class, 'store']);

Route::get('/logs', [LoggerController::class, 'index']);
