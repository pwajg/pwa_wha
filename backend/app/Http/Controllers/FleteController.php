<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flete;
use App\Models\Transporte;
use App\Models\Sucursal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FleteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * Crear fletes automáticamente para todas las sucursales de destino
     */
    public function store(Request $request)
    {
        try {
            // Obtener usuario autenticado
            $usuario = auth()->user();
            if (!$usuario) {
                return response()->json([
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Obtener todas las sucursales de destino
            $sucursalesDestino = Sucursal::all();
            $sucursalOrigen = Sucursal::find($usuario->idSucursal);
            
            if (!$sucursalOrigen) {
                return response()->json([
                    'message' => 'Usuario no tiene sucursal asignada'
                ], 400);
            }

            $fletesCreados = [];
            $fletesNoCreados = [];

            DB::beginTransaction();

            try {
                foreach ($sucursalesDestino as $sucursalDestino) {
                    // Saltar si es la misma sucursal origen
                    if ($sucursalDestino->id === $sucursalOrigen->id) {
                        continue;
                    }

                    // Verificar si ya existe un flete registrado (no enviado) para esta ruta hoy
                    $fleteExistente = Flete::where('idSucursalOrigen', $sucursalOrigen->id)
                        ->where('idSucursalDestino', $sucursalDestino->id)
                        ->whereDate('created_at', Carbon::today())
                        ->whereDoesntHave('estadoFletes', function($query) {
                            $query->where('descripcionEstado', 'Enviado');
                        })
                        ->first();

                    if ($fleteExistente) {
                        $fletesNoCreados[] = [
                            'sucursal' => $sucursalDestino->nombre,
                            'razon' => 'Ya existe un flete registrado para esta ruta hoy'
                        ];
                        continue;
                    }

                    // Crear el flete
                    $codigo = $this->generarCodigoFlete();
                    $flete = Flete::create([
                        'codigo' => $codigo,
                        'observaciones' => 'Flete automático creado para ' . $sucursalDestino->nombre,
                        'idTransporte' => null, // Se asignará cuando se envíe
                        'idSucursalOrigen' => $sucursalOrigen->id,
                        'idSucursalDestino' => $sucursalDestino->id
                    ]);

                    // Crear estado inicial "Registrado"
                    $this->crearEstadoFlete($flete->idFlete, 'Registrado', 'Flete creado automáticamente');

                    $fletesCreados[] = [
                        'flete' => $flete,
                        'sucursal_destino' => $sucursalDestino->nombre
                    ];
                }

                DB::commit();

                return response()->json([
                    'message' => 'Proceso de creación de fletes completado',
                    'fletes_creados' => count($fletesCreados),
                    'fletes_no_creados' => count($fletesNoCreados),
                    'detalle_creados' => $fletesCreados,
                    'detalle_no_creados' => $fletesNoCreados
                ], 201);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear fletes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asignar encomiendas a un flete específico
     */
    public function asignarEncomiendas(Request $request, $fleteId)
    {
        try {
            $validated = $request->validate([
                'encomiendas' => 'required|array|min:1',
                'encomiendas.*' => 'required|exists:encomiendas,id'
            ]);

            $flete = Flete::find($fleteId);
            if (!$flete) {
                return response()->json(['message' => 'Flete no encontrado'], 404);
            }

            // Verificar que el flete esté en estado "Registrado"
            $estadoActual = $flete->estadoFletes()->latest()->first();
            if (!$estadoActual || $estadoActual->descripcionEstado !== 'Registrado') {
                return response()->json([
                    'message' => 'Solo se pueden asignar encomiendas a fletes en estado "Registrado"'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Asignar encomiendas al flete
                foreach ($validated['encomiendas'] as $encomiendaId) {
                    DB::table('encomiendas')
                        ->where('id', $encomiendaId)
                        ->update(['flete_id' => $flete->idFlete]);
                }

                DB::commit();

                return response()->json([
                    'message' => 'Encomiendas asignadas exitosamente al flete',
                    'flete' => $flete->load(['SucursalOrigen', 'SucursalDestino'])
                ], 200);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al asignar encomiendas: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enviar flete (asignar transporte y cambiar estado)
     */
    public function enviarFlete(Request $request, $fleteId)
    {
        try {
            $validated = $request->validate([
                'idTransporte' => 'required|exists:transportes,idTransporte'
            ]);

            $flete = Flete::find($fleteId);
            if (!$flete) {
                return response()->json(['message' => 'Flete no encontrado'], 404);
            }

            // Verificar que el flete esté en estado "Registrado"
            $estadoActual = $flete->estadoFletes()->latest()->first();
            if (!$estadoActual || $estadoActual->descripcionEstado !== 'Registrado') {
                return response()->json([
                    'message' => 'Solo se pueden enviar fletes en estado "Registrado"'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Asignar transporte al flete
                $flete->update(['idTransporte' => $validated['idTransporte']]);

                // Cambiar estado a "Enviado"
                $this->crearEstadoFlete($flete->idFlete, 'Enviado', 'Flete enviado con transporte asignado');

                DB::commit();

                return response()->json([
                    'message' => 'Flete enviado exitosamente',
                    'flete' => $flete->load(['Transporte', 'SucursalOrigen', 'SucursalDestino'])
                ], 200);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al enviar flete: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar código único para el flete
     */
    private function generarCodigoFlete()
    {
        $fecha = Carbon::now()->format('Ymd');
        $ultimoFlete = Flete::whereDate('created_at', Carbon::today())
            ->orderBy('idFlete', 'desc')
            ->first();

        if ($ultimoFlete) {
            $ultimoNumero = (int) substr($ultimoFlete->codigo, -3);
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }

        return 'FLT-' . $fecha . '-' . str_pad($nuevoNumero, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Crear estado de flete
     */
    private function crearEstadoFlete($fleteId, $estado, $observaciones = '')
    {
        return DB::table('estado_fletes')->insert([
            'idFlete' => $fleteId,
            'descripcionEstado' => $estado,
            'fechaCambio' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
