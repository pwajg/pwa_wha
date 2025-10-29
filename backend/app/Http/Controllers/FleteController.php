<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flete;
use App\Models\Transporte;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\EstadoFlete;
use App\Models\Encomienda;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FleteController extends Controller
{
    /**
     * Display a listing of the resource.
     * Retorna todos los fletes con sus relaciones completas
     * Soporta filtrado por fecha (año, mes, día)
     */
    public function index(Request $request)
    {
        try {
            // Primero, obtener el conteo total de fletes en la BD para debugging
            $totalFletesEnBD = Flete::count();
            \Log::info('Total fletes en BD (sin relaciones): ' . $totalFletesEnBD);
            
            // Construir query base - cargar relaciones sin fallar si no existen
            try {
                $query = Flete::with([
                    'SucursalOrigen:id,nombre',
                    'SucursalDestino:id,nombre',
                    'Transporte:idTransporte,placa,marca',
                    'estadoFletes' => function($query) {
                        $query->orderBy('fechaCambio', 'desc');
                    },
                    'Encomiendas' => function($query) {
                        $query->select('idEncomienda', 'codigo', 'idFlete');
                    }
                ]);
            } catch (\Exception $e) {
                \Log::error('Error al cargar relaciones: ' . $e->getMessage());
                // Si falla el with(), intentar sin relaciones
                $query = Flete::query();
            }

            // Filtros opcionales por fecha
            if ($request->has('anio') && $request->anio) {
                $query->whereNotNull('created_at')->whereYear('created_at', $request->anio);
            }

            if ($request->has('mes') && $request->mes) {
                $query->whereNotNull('created_at')->whereMonth('created_at', $request->mes);
            }

            if ($request->has('dia') && $request->dia) {
                $query->whereNotNull('created_at')->whereDay('created_at', $request->dia);
            }

            // Ordenar por idFlete para asegurar un ordenamiento consistente
            // El ordenamiento por created_at se puede hacer después si es necesario
            $query->orderBy('idFlete', 'desc');

            // Obtener todos los fletes
            try {
                $fletes = $query->get();
            } catch (\Exception $e) {
                \Log::error('Error obteniendo fletes: ' . $e->getMessage());
                // Si falla, obtener sin relaciones complejas
                $fletes = Flete::orderBy('idFlete', 'desc')->get();
            }
            
            // Ordenar la colección en memoria si es necesario (solo si hay muchos fletes)
            // Por ahora, mantener el orden del query

            // Log para debugging
            \Log::info('Fletes obtenidos con relaciones: ' . $fletes->count());
            
            // Si no se obtuvieron fletes pero hay en la BD, intentar sin filtros de fecha
            if ($fletes->isEmpty() && $totalFletesEnBD > 0) {
                \Log::warning('No se obtuvieron fletes con el query, intentando sin filtros de fecha...');
                $fletes = Flete::with([
                    'SucursalOrigen:id,nombre',
                    'SucursalDestino:id,nombre',
                    'Transporte:idTransporte,placa,marca',
                    'estadoFletes' => function($query) {
                        $query->orderBy('fechaCambio', 'desc');
                    },
                    'Encomiendas' => function($query) {
                        $query->select('idEncomienda', 'codigo', 'idFlete');
                    }
                ])
                ->orderBy('idFlete', 'desc')
                ->get();
                \Log::info('Fletes obtenidos sin filtros de fecha: ' . $fletes->count());
            }

            // Formatear los datos para el frontend
            $estadoFiltro = $request->has('estado') && $request->estado ? $request->estado : null;
            
            $fletesFormateados = $fletes->map(function($flete) {
                try {
                    // Obtener el estado actual (último estado registrado)
                    $estadoActual = null;
                    if ($flete->relationLoaded('estadoFletes')) {
                        $estadoActual = $flete->estadoFletes->first();
                    } else {
                        // Cargar si no se cargó antes
                        $estadoActual = $flete->estadoFletes()->orderBy('fechaCambio', 'desc')->first();
                    }
                    return [
                        'flete' => $flete,
                        'estadoActual' => $estadoActual ? $estadoActual->descripcionEstado : 'Sin estado'
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error procesando flete ' . $flete->idFlete . ': ' . $e->getMessage());
                    return [
                        'flete' => $flete,
                        'estadoActual' => 'Sin estado'
                    ];
                }
            })
            ->filter(function($item) use ($estadoFiltro) {
                // Filtrar por estado si se especificó
                if ($estadoFiltro) {
                    return $item['estadoActual'] === $estadoFiltro;
                }
                return true;
            })
            ->map(function($item) {
                try {
                    $flete = $item['flete'];
                    $estadoString = $item['estadoActual'];
                    
                    // Obtener el objeto estadoActual para la información completa
                    $estadoActualObj = null;
                    try {
                        if ($flete->relationLoaded('estadoFletes')) {
                            $estadoActualObj = $flete->estadoFletes->first();
                        } else {
                            $estadoActualObj = $flete->estadoFletes()->orderBy('fechaCambio', 'desc')->first();
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Error obteniendo estado del flete ' . $flete->idFlete);
                    }
                    
                    // Contar encomiendas asociadas
                    $totalEncomiendas = 0;
                    try {
                        if ($flete->relationLoaded('Encomiendas')) {
                            $totalEncomiendas = $flete->Encomiendas->count();
                        } else {
                            $totalEncomiendas = $flete->Encomiendas()->count();
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Error contando encomiendas del flete ' . $flete->idFlete);
                    }
                    
                    // Obtener sucursales de forma segura
                    $sucursalOrigen = null;
                    try {
                        if ($flete->relationLoaded('SucursalOrigen') && $flete->SucursalOrigen) {
                            $sucursalOrigen = [
                                'id' => $flete->SucursalOrigen->id,
                                'nombre' => $flete->SucursalOrigen->nombre
                            ];
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Error obteniendo SucursalOrigen del flete ' . $flete->idFlete);
                    }
                    
                    $sucursalDestino = null;
                    try {
                        if ($flete->relationLoaded('SucursalDestino') && $flete->SucursalDestino) {
                            $sucursalDestino = [
                                'id' => $flete->SucursalDestino->id,
                                'nombre' => $flete->SucursalDestino->nombre
                            ];
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Error obteniendo SucursalDestino del flete ' . $flete->idFlete);
                    }
                    
                    $transporte = null;
                    try {
                        if ($flete->relationLoaded('Transporte') && $flete->Transporte) {
                            $transporte = [
                                'id' => $flete->Transporte->idTransporte,
                                'placa' => $flete->Transporte->placa,
                                'marca' => $flete->Transporte->marca
                            ];
                        }
                    } catch (\Exception $e) {
                        \Log::warning('Error obteniendo Transporte del flete ' . $flete->idFlete);
                    }
                    
                    // Formatear fechas de forma segura
                    $createdAt = null;
                    if ($flete->created_at) {
                        if (is_string($flete->created_at)) {
                            try {
                                $createdAt = \Carbon\Carbon::parse($flete->created_at)->format('Y-m-d');
                            } catch (\Exception $e) {
                                $createdAt = $flete->created_at;
                            }
                        } else {
                            $createdAt = $flete->created_at->format('Y-m-d');
                        }
                    }
                    
                    $updatedAt = null;
                    if ($flete->updated_at) {
                        if (is_string($flete->updated_at)) {
                            try {
                                $updatedAt = \Carbon\Carbon::parse($flete->updated_at)->format('Y-m-d H:i:s');
                            } catch (\Exception $e) {
                                $updatedAt = $flete->updated_at;
                            }
                        } else {
                            $updatedAt = $flete->updated_at->format('Y-m-d H:i:s');
                        }
                    }
                    
                    // Formatear fechaCambio del estado de forma segura
                    $fechaCambioEstado = null;
                    if ($estadoActualObj && $estadoActualObj->fechaCambio) {
                        if (is_string($estadoActualObj->fechaCambio)) {
                            try {
                                $fechaCambioEstado = \Carbon\Carbon::parse($estadoActualObj->fechaCambio)->format('Y-m-d H:i:s');
                            } catch (\Exception $e) {
                                $fechaCambioEstado = $estadoActualObj->fechaCambio;
                            }
                        } else {
                            $fechaCambioEstado = $estadoActualObj->fechaCambio->format('Y-m-d H:i:s');
                        }
                    }
                    
                    return [
                        'id' => $flete->idFlete,
                        'codigo' => $flete->codigo ?? '',
                        'sucursalOrigen' => $sucursalOrigen,
                        'sucursalDestino' => $sucursalDestino,
                        'estado' => $estadoString,
                        'totalEncomiendas' => $totalEncomiendas,
                        'observaciones' => $flete->observaciones ?? '',
                        'transporte' => $transporte,
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt,
                        // Información adicional del estado
                        'estadoInfo' => $estadoActualObj ? [
                            'descripcionEstado' => $estadoActualObj->descripcionEstado,
                            'fechaCambio' => $fechaCambioEstado
                        ] : null
                    ];
                } catch (\Exception $e) {
                    \Log::error('Error formateando flete ' . ($item['flete']->idFlete ?? 'desconocido') . ': ' . $e->getMessage());
                    // Retornar estructura mínima si hay error
                    return [
                        'id' => $item['flete']->idFlete ?? 0,
                        'codigo' => $item['flete']->codigo ?? 'Error',
                        'sucursalOrigen' => null,
                        'sucursalDestino' => null,
                        'estado' => 'Error',
                        'totalEncomiendas' => 0,
                        'observaciones' => '',
                        'transporte' => null,
                        'created_at' => null,
                        'updated_at' => null,
                        'estadoInfo' => null
                    ];
                }
            })
            ->filter(function($item) {
                // Filtrar items con errores críticos
                return $item['id'] > 0 && $item['codigo'] !== 'Error';
            });

            \Log::info('Fletes formateados: ' . $fletesFormateados->count());

            return response()->json([
                'message' => 'Fletes obtenidos exitosamente',
                'data' => $fletesFormateados->values(), // Usar values() para reiniciar índices
                'total' => $fletesFormateados->count()
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error al obtener fletes: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Intentar devolver fletes básicos sin relaciones si hay un error
            try {
                $fletesBasicos = Flete::select('idFlete', 'codigo', 'observaciones', 'idSucursalOrigen', 'idSucursalDestino', 'created_at', 'updated_at')
                    ->orderBy('idFlete', 'desc')
                    ->get()
                    ->map(function($flete) {
                        // Formatear fechas de forma segura
                        $createdAt = null;
                        if ($flete->created_at) {
                            if (is_string($flete->created_at)) {
                                try {
                                    $createdAt = \Carbon\Carbon::parse($flete->created_at)->format('Y-m-d');
                                } catch (\Exception $e) {
                                    $createdAt = $flete->created_at;
                                }
                            } else {
                                $createdAt = $flete->created_at->format('Y-m-d');
                            }
                        }
                        
                        $updatedAt = null;
                        if ($flete->updated_at) {
                            if (is_string($flete->updated_at)) {
                                try {
                                    $updatedAt = \Carbon\Carbon::parse($flete->updated_at)->format('Y-m-d H:i:s');
                                } catch (\Exception $e) {
                                    $updatedAt = $flete->updated_at;
                                }
                            } else {
                                $updatedAt = $flete->updated_at->format('Y-m-d H:i:s');
                            }
                        }
                        
                        return [
                            'id' => $flete->idFlete,
                            'codigo' => $flete->codigo ?? '',
                            'sucursalOrigen' => null,
                            'sucursalDestino' => null,
                            'estado' => 'Sin estado',
                            'totalEncomiendas' => 0,
                            'observaciones' => $flete->observaciones ?? '',
                            'transporte' => null,
                            'created_at' => $createdAt,
                            'updated_at' => $updatedAt,
                            'estadoInfo' => null
                        ];
                    });
                
                return response()->json([
                    'message' => 'Fletes obtenidos (modo básico debido a errores en relaciones)',
                    'data' => $fletesBasicos,
                    'total' => $fletesBasicos->count(),
                    'warning' => 'Algunos datos pueden estar incompletos debido a relaciones faltantes'
                ], 200);
            } catch (\Exception $e2) {
                // Si incluso esto falla, devolver error
                return response()->json([
                    'message' => 'Error al obtener fletes: ' . $e->getMessage(),
                    'error' => config('app.debug') ? $e->getTraceAsString() : null,
                    'data' => [],
                    'total' => 0
                ], 500);
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     * Crear fletes automáticamente para todas las sucursales de destino
     */
    public function store(Request $request)
    {
        try {
            $usuarioId = $request->user_id;
            if (!$usuarioId) {
                return response()->json([
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            $usuario = Usuario::find($usuarioId);
            if (!$usuario) {
                return response()->json([
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Obtener la sucursal del usuario autenticado
            $sucursalOrigen = null;
            if ($usuario->idSucursal) {
                $sucursalOrigen = Sucursal::find($usuario->idSucursal);
            }

            // Obtener todas las sucursales de destino
            $sucursalesDestino = Sucursal::all();
            
            if (!$sucursalOrigen) {
                return response()->json([
                    'message' => 'Usuario no tiene sucursal asignada'
                ], 400);
            }

            $fletesCreados = [];
            $fletesNoCreados = [];
            $fechaHoy = Carbon::today();

            // Obtener IDs de fletes que están en estado "Registrado" (sin importar el día)
            $fletesRegistrados = EstadoFlete::where('descripcionEstado', 'Registrado')
                ->whereHas('Flete', function($query) use ($sucursalOrigen) {
                    $query->where('idSucursalOrigen', $sucursalOrigen->id);
                })
                ->pluck('idFlete')
                ->toArray();

            // Obtener sucursales de destino que ya tienen fletes registrados
            $sucursalesConFletesRegistrados = Flete::whereIn('idFlete', $fletesRegistrados)
                ->pluck('idSucursalDestino')
                ->toArray();

            DB::beginTransaction();

            try {
                foreach ($sucursalesDestino as $sucursalDestino) {
                    // Saltar si es la misma sucursal origen
                    if ($sucursalDestino->id === $sucursalOrigen->id) {
                        continue;
                    }

                    // Verificar si esta sucursal ya tiene un flete registrado hoy
                    if (in_array($sucursalDestino->id, $sucursalesConFletesRegistrados)) {
                        $fleteExistente = Flete::where('idSucursalOrigen', $sucursalOrigen->id)
                            ->where('idSucursalDestino', $sucursalDestino->id)
                            ->whereIn('idFlete', $fletesRegistrados)
                            ->first();

                        $fletesNoCreados[] = [
                            'sucursal' => $sucursalDestino->nombre,
                            'razon' => 'Ya existe un flete registrado para esta ruta hoy',
                            'flete_existente' => $fleteExistente ? $fleteExistente->codigo : 'N/A'
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
     * Actualizar estado de un flete específico
     */
    public function actualizarEstado(Request $request, $fleteId)
    {
        try {
            $validated = $request->validate([
                'descripcionEstado' => 'required|string|max:255',
                'observaciones' => 'nullable|string|max:500'
            ]);

            $flete = Flete::find($fleteId);
            if (!$flete) {
                return response()->json(['message' => 'Flete no encontrado'], 404);
            }

            // Obtener el estado actual del flete
            $estadoActual = $flete->estadoFletes()->latest()->first();
            
            // Verificar que no se esté duplicando el mismo estado
            if ($estadoActual && $estadoActual->descripcionEstado === $validated['descripcionEstado']) {
                return response()->json([
                    'message' => 'El flete ya se encuentra en el estado especificado'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Crear nuevo estado
                $nuevoEstado = $this->crearEstadoFlete(
                    $flete->idFlete, 
                    $validated['descripcionEstado'], 
                    $validated['observaciones'] ?? 'Estado actualizado'
                );

                DB::commit();

                return response()->json([
                    'message' => 'Estado del flete actualizado exitosamente',
                    'flete' => $flete->load(['SucursalOrigen', 'SucursalDestino', 'Transporte']),
                    'estado_anterior' => $estadoActual ? $estadoActual->descripcionEstado : 'Sin estado previo',
                    'estado_nuevo' => $validated['descripcionEstado']
                ], 200);

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar estado del flete: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener historial de estados de un flete
     */
    public function obtenerHistorialEstados($fleteId)
    {
        try {
            $flete = Flete::find($fleteId);
            if (!$flete) {
                return response()->json(['message' => 'Flete no encontrado'], 404);
            }

            $historial = $flete->estadoFletes()
                ->orderBy('fechaCambio', 'desc')
                ->get();

            return response()->json([
                'flete' => $flete->load(['SucursalOrigen', 'SucursalDestino', 'Transporte']),
                'historial_estados' => $historial
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener historial de estados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear fletes automáticamente para todas las sucursales (endpoint manual)
     */
    public function crearFletesAutomaticos(Request $request)
    {
        try {
            // Obtener usuario autenticado desde el middleware JWT
            $usuarioId = $request->user_id;
            if (!$usuarioId) {
                return response()->json([
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            // Obtener el usuario completo desde la base de datos
            $usuario = Usuario::find($usuarioId);
            if (!$usuario) {
                return response()->json([
                    'message' => 'Usuario no encontrado'
                ], 404);
            }

            // Obtener la sucursal del usuario autenticado
            $sucursalOrigen = null;
            if ($usuario->idSucursal) {
                $sucursalOrigen = Sucursal::find($usuario->idSucursal);
            }
            
            if (!$sucursalOrigen) {
                return response()->json([
                    'message' => 'Usuario no tiene sucursal asignada'
                ], 400);
            }

            // Obtener todas las sucursales de destino
            $sucursalesDestino = Sucursal::where('id', '!=', $sucursalOrigen->id)->get();
            
            if ($sucursalesDestino->isEmpty()) {
                return response()->json([
                    'message' => 'No hay sucursales de destino disponibles'
                ], 400);
            }

            $fletesCreados = [];
            $fletesNoCreados = [];
            $fechaHoy = Carbon::today();

            // Obtener IDs de fletes que están en estado "Registrado" (sin importar el día)
            $fletesRegistrados = EstadoFlete::where('descripcionEstado', 'Registrado')
                ->whereHas('Flete', function($query) use ($sucursalOrigen) {
                    $query->where('idSucursalOrigen', $sucursalOrigen->id);
                })
                ->pluck('idFlete')
                ->toArray();

            // Obtener sucursales de destino que ya tienen fletes registrados
            $sucursalesConFletesRegistrados = Flete::whereIn('idFlete', $fletesRegistrados)
                ->pluck('idSucursalDestino')
                ->toArray();

            DB::beginTransaction();

            try {
                foreach ($sucursalesDestino as $sucursalDestino) {
                    // Verificar si esta sucursal ya tiene un flete registrado hoy
                    if (in_array($sucursalDestino->id, $sucursalesConFletesRegistrados)) {
                        $fleteExistente = Flete::where('idSucursalOrigen', $sucursalOrigen->id)
                            ->where('idSucursalDestino', $sucursalDestino->id)
                            ->whereIn('idFlete', $fletesRegistrados)
                            ->first();

                        $fletesNoCreados[] = [
                            'sucursal' => $sucursalDestino->nombre,
                            'razon' => 'Ya existe un flete registrado para esta ruta hoy',
                            'flete_existente' => $fleteExistente ? $fleteExistente->codigo : 'N/A'
                        ];
                        continue;
                    }

                    // Crear el flete
                    $codigo = $this->generarCodigoFlete();
                    $flete = Flete::create([
                        'codigo' => $codigo,
                        'observaciones' => 'Flete automático creado para ' . $sucursalDestino->nombre . ' - ' . $fechaHoy->format('d/m/Y'),
                        'idTransporte' => null, // Se asignará cuando se envíe
                        'idSucursalOrigen' => $sucursalOrigen->id,
                        'idSucursalDestino' => $sucursalDestino->id
                    ]);

                    // Crear estado inicial "Registrado"
                    $this->crearEstadoFlete($flete->idFlete, 'Registrado', 'Flete creado automáticamente');

                    $fletesCreados[] = [
                        'flete' => $flete,
                        'sucursal_destino' => $sucursalDestino->nombre,
                        'codigo' => $flete->codigo
                    ];
                }

                DB::commit();

                return response()->json([
                    'message' => 'Proceso de creación automática de fletes completado',
                    'fecha' => $fechaHoy->format('d/m/Y'),
                    'sucursal_origen' => $sucursalOrigen->nombre,
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
                'message' => 'Error al crear fletes automáticamente: ' . $e->getMessage()
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
