<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TrackingController;

/*
|--------------------------------------------------------------------------
| API Routes — HalconERP
|--------------------------------------------------------------------------
|
| Endpoints JSON para consumo desde la app Next.js en Vercel.
| Todas las rutas se prefijan automáticamente con /api
|
| Ejemplo: POST /api/login
|
*/

// ─── Rutas Públicas (sin autenticación) ───────────────────────────────
Route::post('/login', [AuthController::class, 'login']);
Route::post('/track', [TrackingController::class, 'search']);

// ─── Rutas Protegidas (requieren Bearer Token) ───────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Pedidos
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store'])
        ->middleware('role.api:sales,admin');
    Route::match(['patch', 'post'], '/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])
        ->middleware('role.api:sales,admin');

    // Papelera (solo admin)
    Route::get('/orders/trash', [OrderController::class, 'trash'])
        ->middleware('role.api:admin');
    Route::patch('/orders/{id}/restore', [OrderController::class, 'restore'])
        ->middleware('role.api:admin');

    // Inventario
    Route::get('/inventory', [InventoryController::class, 'index'])
        ->middleware('role.api:warehouse,purchasing,admin');
    Route::post('/inventory/restock', [InventoryController::class, 'restock'])
        ->middleware('role.api:purchasing,admin');

    // Usuarios (solo admin)
    Route::middleware('role.api:admin')->group(function () {
        Route::get('/users/roles', [UserController::class, 'roles']);
        Route::apiResource('users', UserController::class);
    });

    // Productos (lista para selects)
    Route::get('/products', function () {
        return response()->json([
            'data' => \App\Models\Product::all(),
        ]);
    });
});
