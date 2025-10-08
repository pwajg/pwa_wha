<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\EncomiendaController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API funcionando']);
});

// Rutas de autenticación (públicas)
Route::post('/login', [UsuarioController::class, 'login']);
Route::post('/logout', [UsuarioController::class, 'logout']);

// Rutas públicas
Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::post('/usuarios', [UsuarioController::class, 'store']);

// Rutas protegidas (requieren autenticación)
Route::middleware('jwt.auth')->group(function () {
    Route::get('/me', [UsuarioController::class, 'me']); // Obtener información del usuario autenticado
    
    // Rutas de clientes
    Route::get('/clientes', [ClienteController::class, 'index']);
    Route::post('/clientes', [ClienteController::class, 'store']);
    Route::get('/clientes/buscar/{dni}', [ClienteController::class, 'buscarPorDni']);
    Route::get('/clientes/{id}', [ClienteController::class, 'show']);
    Route::put('/clientes/{id}', [ClienteController::class, 'update']);
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);

    // Rutas de sucursales
    Route::get('/sucursales', [SucursalController::class, 'index']);
    Route::post('/sucursales', [SucursalController::class, 'store']);
    Route::get('/sucursales/{id}', [SucursalController::class, 'show']);
    Route::put('/sucursales/{id}', [SucursalController::class, 'update']);
    Route::delete('/sucursales/{id}', [SucursalController::class, 'destroy']);

    // Rutas de encomiendas
    Route::get('/encomiendas', [EncomiendaController::class, 'index']);
    Route::post('/encomiendas', [EncomiendaController::class, 'store']);
    Route::get('/encomiendas/{id}', [EncomiendaController::class, 'show']);
    Route::put('/encomiendas/{id}', [EncomiendaController::class, 'update']);
    Route::delete('/encomiendas/{id}', [EncomiendaController::class, 'destroy']);
});