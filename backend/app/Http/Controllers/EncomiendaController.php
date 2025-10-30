<?php

namespace App\Http\Controllers;

use App\Models\Encomienda;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Flete;
use App\Models\EstadoEncomienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EncomiendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $encomiendas = Encomienda::with([
                'ClienteRemitente',
                'ClienteDestinatario',
                'Flete',
                'estadoEncomiendas'
            ])->get();
            return response()->json($encomiendas);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener encomiendas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function generarCodigoEncomienda(){
        $fecha = Carbon::now()->format('Ymd');
        $ultimaEncomienda = Encomienda::whereDate('created_at', Carbon::today())
            ->orderBy('idEncomienda', 'desc')
            ->first();
        if($ultimaEncomienda){
            $ultimoNumero = (int) substr($ultimaEncomienda->codigo,-3);
            $nuevoNumero = $ultimoNumero + 1;
        } else {
            $nuevoNumero = 1;
        }
        return 'ENC-' . $fecha . '-' . str_pad($nuevoNumero, 3, '0', STR_PAD_LEFT);
    }

    private function crearEstadoEncomienda($encomiendaId, $estado, $observaciones = ''){
        return DB::table('estado_encomiendas')->insert([
            'idEncomienda' => $encomiendaId,
            'descripcionEstado' => $estado,
            'fechaCambio' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
    //Crear encomienda
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validar datos
            $validated =$request->validate([
                'descripcion' => 'required|string|max:500',
                'estadoPago' => 'required|in:Pendiente,Parcial,Pagado',
                'costo' => 'required|numeric|min:0',
                'observaciones' => 'nullable|string|max:500',
                'idClienteRemitente' => 'required|exists:clientes,idCliente|different:idClienteDestinatario',
                'idClienteDestinatario' => 'required|exists:clientes,idCliente',
                'idFlete' => 'required|exists:fletes,idFlete'
            ]);
            if ($validated['idClienteRemitente'] === $validated['idClienteDestinatario']) {
                return response()->json([
                    'message' => 'El remitente y el destinatario deben ser diferentes'
                ], 422);
            }
            $codigo = $this->generarCodigoEncomienda();
            $validated['codigo'] = $codigo;
            DB::beginTransaction();
            try {
                $encomienda = Encomienda::create($validated);
                $this->crearEstadoEncomienda($encomienda->idEncomienda, 'Registrado', 'Encomienda creada');
                DB::commit();
                $encomienda->load(['ClienteRemitente','ClienteDestinatario','Flete']);
                return response()->json([
                    'message' => 'Encomienda creada exitosamente',
                    'data' => $encomienda
                ], 201);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear encomienda: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $encomienda = Encomienda::where('idEncomienda',$id)->with([
                'ClienteRemitente',
                'ClienteDestinatario',
                'Flete'
            ])->first();
            if(!$encomienda) {
                return response()->json([
                    'message' => 'Encomienda no encontrada.',
                    'id_buscado' => $id
                ],404);
            }
            $estados = EstadoEncomienda::where('idEncomienda',$id)->orderBy('fechaCambio','desc')->get();
            $estadoActual = $estados->first();
            return response()->json([
                'message' => 'Encomienda encontrada exitosamente.',
                'encomienda' => $encomienda,
                'estado_actual' => $estadoActual ? [
                    'idEstadoEncomienda' => $estadoActual->idEstadoEncomienda,
                    'descripcionEstado' => $estadoActual->descripcionEstado,
                    'fechaCambio' => $estadoActual->fechaCambio,
                ] : null,
                'historial_estados' => $estados->map(function($estado) {return [
                    'idEstadoEncomienda' => $estado->idEstadoEncomienda,
                    'descripcionEstado' => $estado->descripcionEstado,
                    'fechaCambio' => $estado->fechaCambio,
                ]; })
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Encomienda no encontrada',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar encomienda por código (público)
     */
    public function buscarPorCodigo(string $codigo)
    {
        try {
            $encomienda = Encomienda::where('codigo', $codigo)->with([
                'ClienteRemitente',
                'ClienteDestinatario', 
                'Flete',
                'estadoEncomiendas' => function($query) {
                    $query->orderBy('fechaCambio', 'desc');
                }
            ])
            ->first();
            if (!$encomienda) {
                return response()->json([
                    'message' => 'Encomienda no encontrada',
                    'codigo_buscado' => $codigo
                ], 404);
            }
            // Obtener el estado más reciente
            $estadoActual = $encomienda->estadoEncomiendas->first();
            
            return response()->json([
                'message' => 'Encomienda encontrada',
                'encomienda' => $encomienda,
                'estadoActual' => $estadoActual,
            ],200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al buscar encomienda',
                'encontrada' => false,
                'error' => $e->getMessage()
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
            $encomienda = Encomienda::find($id);
            if(!$encomienda) {
                return response()->json([
                    'message' => 'Encomienda no encontrada.',
                    'id_buscado' => $id
                ], 404);
            }
            if(isset($validated['observaciones'])) {
                $encomienda->update([
                    'observaciones' => $validated['observaciones']
                ]);
            }
            $encomienda->load(['ClienteRemitente','ClienteDestinatario','Flete']);
            return response()->json([
                'message' => 'Encmienda actualizada exitosamente.',
                'encomienda' => $encomienda,
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar encomienda    .',
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
            $encomienda = Encomienda::find($id);
            if(!$encomienda) {
                return response()->json([
                    'message' => 'Encomienda no encontrada.',
                    'id_buscado' => $id
                ], 404);
            }
            DB::beginTransaction();
            try {
                EstadoEncomienda::where('idEncomienda',$encomienda->idEncomienda)->delete();
                $encomienda->delete();
                DB::commit();
                return response()->json([
                    'message' => 'Encomienda eliminada exitosamente.',
                    'codigo' => $encomienda->codigo,
                ], 200);
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar encomienda.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validar datos de cliente
     */
    private function validarCliente($cliente, $tipo)
    {
        $rules = [
            'tipoCliente' => 'required|string|in:Persona Natural,Empresa,Extranjero',
            'dni' => 'required|string',
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20'
        ];

        // Validar longitud del DNI según el tipo
        $tipoCliente = $cliente['tipoCliente'] ?? '';
        $dni = $cliente['dni'] ?? '';
        
        switch($tipoCliente) {
            case 'Persona Natural':
                if (strlen($dni) !== 8) {
                    throw new \Exception("El DNI debe tener 8 dígitos para Persona Natural");
                }
                break;
            case 'Empresa':
                if (strlen($dni) !== 11) {
                    throw new \Exception("El DNI debe tener 11 dígitos para Empresa");
                }
                break;
            case 'Extranjero':
                if (strlen($dni) !== 12) {
                    throw new \Exception("El DNI debe tener 12 dígitos para Extranjero");
                }
                break;
        }
    }

    /**
     * Crear o actualizar cliente
     */
    private function crearOActualizarCliente($clienteData)
    {
        // Buscar cliente existente por número de documento
        $clienteExistente = Cliente::byDni($clienteData['numeroDocumento'])->first();

        if ($clienteExistente) {
            // Actualizar datos del cliente existente
            $clienteExistente->update([
                'nombre' => $clienteData['nombre'],
                'telefono' => $clienteData['telefono']
            ]);
            return $clienteExistente;
        } else {
            // Crear nuevo cliente
            return Cliente::create([
                'tipoCliente' => $clienteData['tipoCliente'],
                'numeroDocumento' => $clienteData['numeroDocumento'],
                'nombre' => $clienteData['nombre'],
                'telefono' => $clienteData['telefono']
            ]);
        }
    }

    public function pagosEncomienda(string $idEncomienda) {
        try {
            $encomienda = Encomienda::where('idEncomienda',$idEncomienda)->first();
            if (!$encomienda) {
                return response()->json([
                    'message' => 'Encomienda no encontrada.',
                    'id_buscado' => $idEncomienda
                ], 404);
            }
            $pagos = Pago::where('idEncomienda',$encomienda->idEncomienda)
                ->orderBy('fechaPago','desc')
                ->orderBy('idPago','asc')
                ->get();
            $totalPagado = $pagos->sum('monto');
            $adeudo = $encomienda->costo - $totalPagado;
            return response()->json([
                'message' => 'Pagos obtenidos exitosamente.',
                'encomienda' => [
                    'idEncomienda' => $encomienda->idEncomienda,
                    'codigo' => $encomienda->codigo,
                    'costo' => $encomienda->costo,
                    'estadoPago' => $encomienda->estadoPago,
                ],
                'total_pagado' => $totalPagado,
                'adeudo' => $adeudo,
                'pagos' => $pagos
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener pagos.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function adeudoEncomienda (string $idEncomienda){
        try {
            $encomienda = Encomienda::where('idEncomienda',$idEncomienda)->first();
            if (!$encomienda) {
                return response()->json([
                    'message' => 'Encomienda no encontrada.',
                    'id_buscado' => $idEncomienda
                ], 404);
            }
            $totalPagado = \DB::table('pagos')
                ->where('idEncomienda',$encomienda->idEncomienda)
                ->sum('monto');
            $costo = (float) $encomienda->costo;
            $adeudo = max(0, $costo - (float) $totalPagado);
            if ($adeudo == 0) {
                $mensaje = 'Encomienda pagada completamente.';
            } elseif ($adeudo > 0) {
                $mensaje = 'Encomienda pendiente de pago: ' . number_format($adeudo,2);
            }
            return response()->json([
                'idEncomienda' => $encomienda->idEncomienda,
                'codigo' => $encomienda->codigo,
                'costo' => $costo,
                'total_pagado' => (float) $totalPagado,
                'adeudo' => $adeudo,
                'message' => $mensaje,
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener adeudo.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
