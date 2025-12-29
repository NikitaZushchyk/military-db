<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DutyRosterController;
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

    Route::prefix('assignments')->group(function () {
        Route::post('/issue', [AssignmentController::class, 'issue'])->name('assignments.issue');
        Route::post('/return', [AssignmentController::class, 'return'])->name('assignments.return');
        Route::get('/', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/active', [AssignmentController::class, 'active'])->name('assignments.active');
    });

    Route::get('/roster', [DutyRosterController::class, 'index'])->name('roster.index');
    Route::post('/roster', [DutyRosterController::class, 'store'])->name('roster.store');
});
