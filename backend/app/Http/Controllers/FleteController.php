<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Flete;
use App\Models\Transporte;
use App\Models\Sucursal;
use App\Models\Usuario;
use App\Models\EstadoFlete;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FleteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $fletes = Flete::with([
                'SucursalOrigen',
                'SucursalDestino',
                'Transporte',
                'estadoFletes'
            ])->get();
            $fletesEstado = $fletes->map(function($flete){
                $estadoActual = $flete->estadoFletes()->orderBy('fechaCambio','desc')->first();
                return [
                    'idFlete' => $flete->idFlete,
                    'codigo' => $flete->codigo,
                    'observaciones' => $flete->observaciones,
                    'idTransporte' => $flete->idTransporte,
                    'idSucursalOrigen' => $flete->idSucursalOrigen,
                    'idSucursalDestino' => $flete->idSucursalDestino,
                    'created_at' => $flete->created_at,
                    'updated_at' => $flete->updated_at,
                    'SucursalOrigen' => $flete->SucursalOrigen,
                    'SucursalDestino' => $flete->SucursalDestino,
                    'Transporte' => $flete->Transporte,
                    'estado_actual' => $estadoActual ? [
                        'idEstadoFlete' => $estadoActual->idEstadoFlete,
                        'descripcionEstado' => $estadoActual->descripcionEstado,
                        'fechaCambio' => $estadoActual->fechaCambio
                    ] : null,
                    'historial_estados' => $flete->estadoFletes
                ];
            });
            return response()->json([
                'message' => 'Fletes obtenidos exitosamente.',
                'fletes' => $fletesEstado,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener Fletes.',
                'error' => $e->getMessage()
            ], 500);
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
    public function EncomiendasAsignadas(Request $request, $fleteId)
    {
        try {
            $flete = Flete::where('idFlete',$fleteId)->first();
            if (!$flete) {
                return response()->json([
                    'message' => 'Flete no encontrado',
                    'id_buscado' => $fleteId
                ],404);
            }
            $encomiendas = $flete->encomiendas()
                ->with([
                    'ClienteRemitente',
                    'ClienteDestinatario',
                    'estadoEncomiendas' => function($query) {
                        $query->orderBy('fechaCambio','desc');
                    }
                ])
                ->get()
                ->map(function($enc) {
                    $estadoActual = $enc->estadoEncomiendas->first();
                    return [
                        'idEncomienda' => $enc->idEncomienda,
                        'codigo' => $enc->codigo,
                        'descripcion' => $enc->descripcion,
                        'costo' => $enc->costo,
                        'estadoPago' => $enc->estadoPago,
                        'ClienteRemitente' => $enc->ClienteRemitente,
                        'ClienteDestinatario' => $enc->ClienteDestinatario,
                        'estado_actual' => $estadoActual ? [
                            'idEstadoEncomienda' => $estadoActual->idEstadoEncomienda,
                            'descripcionEstado' => $estadoActual->descripcionEstado,
                            'fechaCambio' => $estadoActual->fechaCambio
                        ] : null
                    ];
                });
            return response()->json([
                'message' => 'Encomiendas encontradas exitosamente.',
                'flete' => [
                    'idFlete' => $flete->idFlete,
                    'codigo' => $flete->codigo,
                ],
                'encomiendas' => $encomiendas
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener encomiendas asignadas al flete.',
                'error' => $e->getMessage()
            ],500);
        }
    }

    private function crearEstadoEncomienda(int $encomiendaId, string $estado, string $obs = ''){
        return \DB::table('estado_encomiendas')->insert([
            'idEncomienda' => $encomiendaId,
            'descripcionEstado' => $estado,
            'fechaCambio' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
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
            };

            $transporteEnUso = Flete::where('idTransporte', $validated['idTransporte'])
                ->whereHas('estadoFletes',function($query){
                    $query->where('descripcionEstado','Enviado')
                        ->whereRaw('fechaCambio = (SELECT MAX(fechaCambio) FROM estado_fletes WHERE idFlete = fletes.idFlete)');    
                })
                ->exists();
            if($transporteEnUso){
                return response()->json([
                    'message' => 'El transporte ya está en uso'
                ], 400);
            }
            // Verificar que el flete esté en estado "Registrado"
            $estadoActual = $flete->estadoFletes()->orderBy('fechaCambio','desc')->first();
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
                $encomiendaIds = \DB::table('encomiendas')
                    ->where('idFlete',$flete->idFlete)
                    ->pluck('idEncomienda');
                if($encomiendaIds->isNotEmpty()) {
                    $now = now();
                    $rows = $encomiendaIds->map(fn($id) => [
                        'idEncomienda' => $id,
                        'descripcionEstado' => 'Enviado',
                        'fechaCambio' => $now,
                        'created_at' => $now,
                        'updated_at' => $now
                    ])->all();
                    \DB::table('estado_encomiendas')->insert($rows);
                }
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
    public function fleteEnDestino(Request $request, $fleteId)
    {
        try {
            $validated = $request->validate([
                'observaciones' => 'nullable|string|max:500',
            ]);
            $flete = Flete::find($fleteId);
            if(!$flete) {
                return response()->json([
                    'message' => 'Flete no encontrado.',
                    'id_buscado' => $fleteId
                ], 404);
            }
            $estadoActual = $flete->estadoFletes()
                ->orderBy('fechaCambio', 'desc')
                ->orderBy('idEstadoFlete','desc')
                ->first();
            $descripcionActual = $estadoActual ? trim($estadoActual->descripcionEstado) : null;
            if(!$estadoActual || !in_array($descripcionActual,['Enviado','Reprogramado'])) {
                return response()->json([
                    'message' => 'Solo se pueden actualizar fletes en estado "Enviado" o "Reprogramado".',
                    'estado_actual' => $estadoActual
                ], 400);
            }
            DB::beginTransaction();
            try {
                $this->crearEstadoFlete(
                    $flete->idFlete,
                    'EnDestino',
                    $validated['observaciones'] ?? 'Flete en destino'
                );
                $encomiendaIds = \DB::table('encomiendas')
                    ->where('idFlete',$flete->idFlete)
                    ->pluck('idEncomienda');
                if($encomiendaIds->isNotEmpty()) {
                    $now = now();
                    $rows = $encomiendaIds->map(fn($id) => [
                        'idEncomienda' => $id,
                        'descripcionEstado' => 'EnDestino',
                        'fechaCambio' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all();
                    \DB::table('estado_encomiendas')->insert($rows);
                }
                DB::commit();
                return response()->json([
                    'message' => 'Flete actualizado exitosamente.',
                    'flete' => $flete->load(['SucursalOrigen', 'SucursalDestino', 'Transporte']),
                ],200);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar flete.',
                'error' => $e->getMessage(),
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
                        'observaciones' => '',
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
        try {
            $flete = Flete::where('idFlete', $id)->with([
                'SucursalOrigen',
                'SucursalDestino',
                'Transporte',
            ])->first();
            if(!$flete) {
                return response()->json([
                    'message' => 'Flete no encontrado.',
                    'id_buscado' => $id
                ], 404);
            }
            $estados = EstadoFlete::where('idFlete',$id)->orderBy('fechaCambio','desc')->get();
            $estadoActual = $estados->first();
            return response()->json([
                'message' => 'Flete encontrado exitosamente.',
                'flete' => $flete,
                'estado_actual' => $estadoActual ? [
                    'idEstadoFlete' => $estadoActual->idEstadoFlete,
                    'descripcionEstado' => $estadoActual->descripcionEstado,
                    'fechaCambio' => $estadoActual->fechaCambio,
                ] : null,
                'historial_estados' => $estados->map(function($estado) {return [
                    'idEstadoFlete' => $estado->idEstadoFlete,
                    'descripcionEstado' => $estado->descripcionEstado,
                    'fechaCambio' => $estado->fechaCambio,
                ]; })
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener flete.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function reprogramarFlete(Request $request, $id) {
        try {
            $validated = $request->validate([
                'observaciones' => 'nullable|string|max:500',
            ]);
            $flete = Flete::where('idFlete', $id)->first();
            if(!$flete) {
                return response()->json([
                    'message' => 'Flete no encontrado.',
                    'id_buscado' => $id
                ], 404);
            }
            DB::beginTransaction();
            try {
                $this->crearEstadoFlete(
                    $flete->idFlete,
                    'Reprogramado',
                    $validated['observaciones'] ?? 'Flete reprogramado'
                );
                $encomiendaIds = \DB::table('encomiendas')
                    ->where('idFlete',$flete->idFlete)
                    ->pluck('idEncomienda');
                if($encomiendaIds->isNotEmpty()) {
                    $now = now();
                    $rows = $encomiendaIds->map(fn($id) => [
                        'idEncomienda' => $id,
                        'descripcionEstado' => 'Reprogramado',
                        'fechaCambio' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ])->all();
                    \DB::table('estado_encomiendas')->insert($rows);
                }
                DB::commit();
                $estadoReprogramado = EstadoFlete::where('idFlete',$flete->idFlete)
                    ->where('descripcionEstado','Reprogramado')
                    ->orderBy('fechaCambio', 'desc')
                    ->first();
                $flete->load(['SucursalOrigen', 'SucursalDestino', 'Transporte']);
                return response()->json([
                    'message' => 'Flete reprogramado exitosamente.',
                    'flete' => $flete,
                    'estado_reprogramado' => $estadoReprogramado,
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al reprogramar flete.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function cambiarTransporte(Request $request, $id){
        try {
            $validated = $request->validate([
                'idTransporte' => 'required|exists:transportes,idTransporte',
                'observaciones' => 'nullable|string|max:500',
            ]);
            $flete = Flete::where('idFlete',$id)->first();
            if(!$flete) {
                return response()->json([
                    'message' => 'Flete no encontrado.',
                    'id_buscado' => $id
                ], 404);
            }
            $nuevoTransporte = Transporte::find($validated['idTransporte']);
            if(!$nuevoTransporte) {
                return response()->json([
                    'message' => 'Transporte no encontrado.',
                ], 404);
            }
            // Verificar que el transporte no esté en uso por otro flete cuyo estado más reciente sea "Enviado"
            // Usamos una subconsulta para obtener el último estado por flete y filtramos por "Enviado"
            $latestEstadoSub = \DB::table('estado_fletes as ef_max')
                ->select('ef_max.idFlete', \DB::raw('MAX(ef_max.fechaCambio) as max_fecha'))
                ->groupBy('ef_max.idFlete');

            $transporteEnUso = \DB::table('fletes as f')
                ->joinSub($latestEstadoSub, 'm', function($join) {
                    $join->on('m.idFlete', '=', 'f.idFlete');
                })
                ->join('estado_fletes as ef', function($join) {
                    $join->on('ef.idFlete', '=', 'f.idFlete')
                        ->on('ef.fechaCambio', '=', 'm.max_fecha');
                })
                ->where('f.idTransporte', $validated['idTransporte'])
                ->where('f.idFlete', '!=', $flete->idFlete)
                ->where('ef.descripcionEstado', 'Enviado')
                ->exists();

            if ($transporteEnUso) {
                return response()->json([
                    'message' => 'El transporte ya está en uso por otro flete.'
                ], 400);
            }
            DB::beginTransaction();
            try {
                $transporteAnterior = $flete->idTransporte ? Transporte::find($flete->idTransporte) : null;
                $flete->update([
                    'idTransporte' => $validated['idTransporte']
                ]);
                DB::commit();
                $flete->load(['SucursalOrigen', 'SucursalDestino', 'Transporte']);
                return response()->json([
                    'message' => 'Transporte cambiado exitosamente.',
                    'flete' => $flete,
                    'transporte_anterior' => $transporteAnterior ? [
                        'idTransporte' => $transporteAnterior->idTransporte,
                        'placa' => $transporteAnterior->placa
                    ] : null,
                    'transporte_nuevo' => [
                        'idTransporte' => $nuevoTransporte->idTransporte,
                        'placa' => $nuevoTransporte->placa,
                    ]
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al cambiar transporte.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validated = $request->validate([
                'observaciones' => 'nullable|string|max:500'
            ]);
            $flete = Flete::find($id);
            if(!$flete) {
                return response()->json([
                    'message' => 'Flete no encontrado.',
                    'id_buscado' => $id
                ], 404);
            }
            if(isset($validated['observaciones'])) {
                $flete->update([
                    'observaciones' => $validated['observaciones']
                ]);
            }
            $flete->load(['SucursalOrigen', 'SucursalDestino', 'Transporte']);
            return response()->json([
                'message' => 'Flete actualizado exitosamente.',
                'flete' => $flete,
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar flete.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $flete = Flete::find($id);
            if(!$flete) {
                return response()->json([
                    'message' => 'Flete no encontrado.',
                    'id_buscado' => $id
                ], 404);
            }
            DB::beginTransaction();
            try {
                EstadoFlete::where('idFlete',$flete->idFlete)->delete();
                $flete->delete();
                DB::commit();
                return response()->json([
                    'message' => 'Flete eliminado exitosamente.',
                    'codigo' => $flete->codigo,
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar flete.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
