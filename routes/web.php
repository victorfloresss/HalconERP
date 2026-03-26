<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

// 1. Ruta Raíz e Inicio
Route::get('/', [HomeController::class, 'index'])->middleware('auth');
Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware('auth');

// 2. Rutas de Autenticación
Auth::routes(['register' => false]);

// 3. RUTAS PÚBLICAS (Clientes externos)
Route::get('/rastreo', [OrderController::class, 'trackForm'])->name('orders.track');
Route::post('/rastreo', [OrderController::class, 'trackSearch'])->name('orders.search');


// 4. RUTAS PRIVADAS (Gestión Interna)
Route::middleware(['auth'])->group(function () {

    // --- NIVEL: SOLO ADMINISTRADOR ---
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/orders/trash', [OrderController::class, 'trash'])->name('orders.trash');
        Route::patch('/orders/{id}/restore', [OrderController::class, 'restore'])->name('orders.restore');
    });

    // --- NIVEL: VENTAS Y ADMIN (Crear Pedidos) ---
    Route::middleware(['role:sales,admin'])->group(function () {
        Route::get('/orders/create', function () {
            return view('orders.create');
        })->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    });

    // --- NIVEL: COMPRAS Y ADMIN ---
    // Aquí podrías agregar rutas específicas para Purchasing en el futuro
    Route::middleware(['role:purchasing,admin'])->group(function () {
        // Ejemplo: Route::get('/purchasing/low-stock', [OrderController::class, 'lowStock']);
    });

    // --- NIVEL: GENERAL (Todos los empleados logueados) ---
    // Todos pueden ver la lista y el Borrado Lógico (el Admin controla la papelera arriba)
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    
    // Solo permitimos eliminar de la lista principal a Admin o Ventas
    Route::middleware(['role:sales,admin'])->group(function () {
        Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });
});