<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\SucursalController;
use App\Http\Controllers\EncomiendaController;
use App\Http\Controllers\FleteController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ActividadController;

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
Route::get('/encomiendas/buscar/{codigo}', [EncomiendaController::class, 'buscarPorCodigo']); //Funciona

// Rutas públicas de sucursales para el frontend
Route::get('/sucursales', [SucursalController::class, 'index']);

// Rutas protegidas (requieren autenticación)
Route::middleware('jwt.auth')->group(function () {
    Route::get('/me', [UsuarioController::class, 'me']); // Obtener información del usuario autenticado
    Route::get('/user/theme', [UsuarioController::class, 'getTheme']); // Obtener preferencia de tema
    Route::put('/user/theme', [UsuarioController::class, 'updateTheme']); // Actualizar preferencia de tema
    
    // Rutas de usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index']); // Funciona
    Route::post('/usuarios', [UsuarioController::class, 'store']); // Funciona
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show']); // Funciona
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update']); // Funciona
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']); // Funciona

    // Rutas de clientes
    Route::get('/clientes', [ClienteController::class, 'index']); // Funciona
    Route::post('/clientes', [ClienteController::class, 'store']); //Funciona
    Route::get('/clientes/buscar/{documento}', [ClienteController::class, 'buscarPorDocumento']); // Funciona
    Route::get('/clientes/{id}', [ClienteController::class, 'show']); // Funciona
    Route::put('/clientes/{id}', [ClienteController::class, 'update']); // Funciona
    Route::delete('/clientes/{id}', [ClienteController::class, 'destroy']); // Funciona

    // Rutas de sucursales (CRUD completo)
    Route::post('/sucursales', [SucursalController::class, 'store']); // Funciona
    Route::get('/sucursales/{id}', [SucursalController::class, 'show']); // Funciona
    Route::put('/sucursales/{id}', [SucursalController::class, 'update']); // Funciona
    Route::delete('/sucursales/{id}', [SucursalController::class, 'destroy']); // Funciona | Warning: FK de 'fletes'

    // Rutas de encomiendas
    Route::get('/encomiendas', [EncomiendaController::class, 'index']); //Funciona
    Route::post('/encomiendas', [EncomiendaController::class, 'store']); //Funciona
    Route::get('/encomiendas/{id}', [EncomiendaController::class, 'show']); //Funciona
    Route::put('/encomiendas/{id}', [EncomiendaController::class, 'update']); //Funciona | Agrega observaciones
    Route::delete('/encomiendas/{id}', [EncomiendaController::class, 'destroy']); // Funciona
    // Búsqueda pública de encomiendas por código
    Route::get('/encomiendas/{id}/pagos', [EncomiendaController::class, 'pagosEncomienda']); // Funciona  | Muestra los pagos de la encomienda
    Route::get('/encomiendas/{id}/adeudo',[EncomiendaController::class, 'adeudoEncomienda']); // Funciona | Muestra adeudo
    Route::post('/encomiendas/{id}/entregado',[EncomiendaController::class, 'encomiendaEntregado']);

    // Rutas: Flete
    Route::post('/fletes', [FleteController::class, 'store']); //funciona
    Route::get('/fletes', [FleteController::class, 'index']); // Funciona
    Route::get('/fletes/por-enviar', [FleteController::class, 'fletesPorEnviar']); // Funciona | Fletes registrados del usuario autenticado
    Route::get('/fletes/mi-sucursal', [FleteController::class, 'fletesPorSucursal']); // Funciona | Fletes registrados de la sucursal del usuario autenticado
    Route::get('/fletes/{id}',[FleteController::class,'show']); // Funciona
    Route::put('/fletes/{id}',[FleteController::class,'update']); // Funciona | Cambia observaciones
    Route::delete('/fletes/{id}',[FleteController::class,'destroy']); // Funciona

    // Rutas adicionales para fletes
    Route::post('/fletes/{id}/enviar', [FleteController::class, 'enviarFlete']); // Funciona | Envia automaticamente las encomiendas asignadas
    Route::post('/fletes/{id}/reprogramar', [FleteController::class, 'reprogramarFlete']); // Funciona | Reprograma automaticamente las encomiendas asignadas
    Route::put('/fletes/{id}/cambiar-transporte', [FleteController::class, 'cambiarTransporte']); // Funciona
    Route::post('/fletes/{id}/en-destino', [FleteController::class, 'fleteEnDestino']); // Funciona | Actualiza el estado de las encomiendas asignadas
    Route::get('/fletes/{id}/encomiendas', [FleteController::class,'encomiendasAsignadas']); // Funciona
    //Route::get('/fletes/{id}/historial-estados', [FleteController::class, 'obtenerHistorialEstados']);
    Route::post('/fletes/crear-automaticos', [FleteController::class, 'crearFletesAutomaticos']); //Crea fletes automaticamente

    // Rutas Pagos
    Route::post('/pagos',[PagoController::class,'store']); // Funciona
    Route::get('/pagos',[PagoController::class,'index']); // Funciona
    Route::get('/pagos/{id}',[PagoController::class,'show']); // Funciona
    Route::put('/pagos/{id}',[PagoController::class,'update']); // Funciona | Actualiza: codigo, monto, modalidadPago
    Route::delete('/pagos/{id}',[PagoController::class,'destroy']); // Funciona | Anular un pago

    // Rutas de actividades
    Route::get('/actividades',[ActividadController::class, 'index']);
    Route::get('/actividades/usuario/{idUsuario}',[ActividadController::class, 'actividadesUsuario']);
    Route::get('/actividades/{id}',[ActividadController::class, 'show']);
});