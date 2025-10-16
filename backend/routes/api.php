<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\EncomiendaController;
use App\Http\Controllers\FleteController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API funcionando']);
});

// Rutas de autenticación (públicas)
Route::post('/login', [UsuarioController::class, 'login']);
Route::post('/logout', [UsuarioController::class, 'logout']);

// Rutas públicas
Route::get('/usuarios', [UsuarioController::class, 'index']);
Route::post('/usuarios', [UsuarioController::class, 'store']);
Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

// Rutas públicas de sucursales para el frontend
Route::get('/sucursales', [SucursalController::class, 'index']);

// Búsqueda pública de encomiendas por código
Route::get('/encomiendas/buscar/{codigo}', [EncomiendaController::class, 'buscarPorCodigo']);

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

    // Rutas de sucursales (CRUD completo)
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

    // Rutas: Flete
    Route::post('/fletes', [FleteController::class, 'store']);
    Route::get('/fletes', [FleteController::class, 'index']);
    Route::get('/fletes/{id}',[FleteController::class,'show']);
    Route::put('/fletes/{id}',[FleteController::class,'update']);
    Route::delete('/fletes/{id}',[FleteController::class,'destroy']);
    
    // Rutas adicionales para fletes
    Route::post('/fletes/{id}/asignar-encomiendas', [FleteController::class, 'asignarEncomiendas']);
    Route::post('/fletes/{id}/enviar', [FleteController::class, 'enviarFlete']);
});