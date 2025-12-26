<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\SoldierController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [LoginController::class, 'login'])->name('login');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('soldiers')->group(function () {
        Route::get('/', [SoldierController::class, 'index'])->name('soldiers.index');
        Route::get('/{soldier}', [SoldierController::class, 'show'])->name('soldiers.show');
        Route::put('/{soldier}', [SoldierController::class, 'update'])->name('soldiers.update');
        Route::delete('/{soldier}', [SoldierController::class, 'delete'])->name('soldiers.delete');
        Route::post('/', [SoldierController::class, 'store'])->name('soldiers.store');
    });
    Route::prefix('warehouse')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('warehouses.index');
        Route::post('/', [WarehouseController::class, 'store'])->name('warehouses.store');
        Route::put('/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
    });
});
