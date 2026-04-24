<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;

Route::get('/', [HomeController::class, 'index'])->middleware('auth');
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

Auth::routes(['register' => false]);

Route::get('/rastreo', [OrderController::class, 'trackForm'])->name('orders.track');
Route::post('/rastreo', [OrderController::class, 'trackSearch'])->name('orders.search');


Route::middleware(['auth'])->group(function () {

    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/orders/trash', [OrderController::class, 'trash'])->name('orders.trash');
        Route::patch('/orders/{id}/restore', [OrderController::class, 'restore'])->name('orders.restore');
    });

    Route::middleware(['role:sales,admin'])->group(function () {
        // Apunta al controlador para cargar la lista de los 5 productos
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    });

    Route::middleware(['role:warehouse,purchasing,admin'])->group(function () {
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    });

    Route::middleware(['role:purchasing,admin'])->group(function () {
        Route::post('/inventory/restock', [InventoryController::class, 'restock'])->name('inventory.restock');
    });

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    
    Route::middleware(['role:sales,admin'])->group(function () {
        Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });
});