<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('soldiers')->group(function () {
        Route::get('/',[SoldierController::class, 'index'])->name('soldiers.index');
        Route::get('/{soldier_id}',[SoldierController::class, 'show'])->name('soldiers.show');
        Route::put('/{soldier_id}',[SoldierController::class, 'update'])->name('soldiers.update');
        Route::delete('/{soldier_id}',[SoldierController::class, 'delete'])->name('soldiers.delete');
        Route::post('/',[SoldierController::class, 'store'])->name('soldiers.store');
    });
});
