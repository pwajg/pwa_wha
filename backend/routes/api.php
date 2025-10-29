<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\EncomiendaController;
use App\Http\Controllers\FleteController;

Route::get('/ping', function () {
    return response()->json(['message' => 'API funcionando']);
});

// Endpoint de prueba para verificar autenticación
Route::middleware('jwt.auth')->get('/test-auth', function (Request $request) {
    return response()->json([
        'message' => 'Autenticación exitosa',
        'user_id' => $request->user_id,
        'user_email' => $request->user_email,
        'user_rol' => $request->user_rol
    ]);
});

// Rutas de autenticación (públicas)
Route::post('/login', [UsuarioController::class, 'login']); // Funciona
Route::post('/logout', [UsuarioController::class, 'logout']); // Funciona

Route::get('/usuarios/{id}', [UsuarioController::class, 'show']);
Route::put('/usuarios/{id}', [UsuarioController::class, 'update']);
Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

// Rutas públicas de sucursales para el frontend
Route::get('/sucursales', [SucursalController::class, 'index']);

// Rutas protegidas (requieren autenticación)
Route::middleware('jwt.auth')->group(function () {
    Route::get('/me', [UsuarioController::class, 'me']); // Obtener información del usuario autenticado
    
    // Rutas de usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index']); // Funciona
    Route::post('/usuarios', [UsuarioController::class, 'store']); // Funciona

    // Rutas de clientes
    Route::get('/clientes', [ClienteController::class, 'index']); // Funciona
    Route::post('/clientes', [ClienteController::class, 'store']); //Funciona
    Route::get('/clientes/buscar/{documento}', [ClienteController::class, 'buscarPorDocumento']); // Funciona
    Route::get('/clientes/{id}', [ClienteController::class, 'show']);
    Route::put('/clientes/{id}', [ClienteController::class, 'update']);
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']);

    // Rutas de sucursales (CRUD completo)
    Route::post('/sucursales', [SucursalController::class, 'store']);
    Route::get('/sucursales/{id}', [SucursalController::class, 'show']);
    Route::put('/sucursales/{id}', [SucursalController::class, 'update']);
    Route::delete('/sucursales/{id}', [SucursalController::class, 'destroy']);

    // Rutas de encomiendas
    Route::get('/encomiendas', [EncomiendaController::class, 'index']); //Funciona
    Route::post('/encomiendas', [EncomiendaController::class, 'store']); //Funciona
    Route::get('/encomiendas/{id}', [EncomiendaController::class, 'show']); //Funciona
    Route::put('/encomiendas/{id}', [EncomiendaController::class, 'update']); //Funciona | Agrega observaciones
    Route::delete('/encomiendas/{id}', [EncomiendaController::class, 'destroy']);
    // Búsqueda pública de encomiendas por código
    Route::get('/encomiendas/buscar/{codigo}', [EncomiendaController::class, 'buscarPorCodigo']); //Funciona

    // Rutas: Flete
    Route::post('/fletes', [FleteController::class, 'store']); //funciona
    Route::get('/fletes', [FleteController::class, 'index']); // Funciona
    Route::get('/fletes/{id}',[FleteController::class,'show']); // Funciona
    Route::put('/fletes/{id}',[FleteController::class,'update']); // Funciona | Cambia observaciones
    Route::delete('/fletes/{id}',[FleteController::class,'destroy']); // Funciona
    
    // Rutas adicionales para fletes
    Route::post('/fletes/{id}/enviar', [FleteController::class, 'enviarFlete']); // Funciona | Envia automaticamente las encomiendas asignadas
    Route::post('/fletes/{id}/reprogramar', [FleteController::class, 'reprogramarFlete']); // Funciona
    Route::put('/fletes/{id}/cambiar-transporte', [FleteController::class, 'cambiarTransporte']); // Funciona
    Route::post('/fletes/{id}/en-destino', [FleteController::class, 'fleteEnDestino']); // Funciona
    Route::get('/fletes/{id}/encomiendas', [FleteController::class,'encomiendasAsignadas']);
    //Route::get('/fletes/{id}/historial-estados', [FleteController::class, 'obtenerHistorialEstados']);
    Route::post('/fletes/crear-automaticos', [FleteController::class, 'crearFletesAutomaticos']); //Crea fletes automaticamente
});