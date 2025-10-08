<?php

namespace App\Http\Controllers;

use App\Models\Encomienda;
use App\Models\Cliente;
use App\Models\EstadoEncomienda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EncomiendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $encomiendas = Encomienda::with([
                'clienteRemitente',
                'clienteDestinatario',
                'sucursalOrigen',
                'sucursalDestino',
                'estados' => function($query) {
                    $query->orderBy('fechaCambio', 'desc');
                }
            ])->get();

            return response()->json($encomiendas);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener encomiendas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validar datos básicos de la encomienda
            $request->validate([
                'codigo' => 'required|string|unique:encomiendas,codigo',
                'descripcion' => 'required|string',
                'costo' => 'required|numeric|min:0',
                'observaciones' => 'nullable|string',
                'clienteRemitente' => 'required|array',
                'clienteDestinatario' => 'required|array',
                'idSucursalOrigen' => 'required|exists:sucursales,idSucursal',
                'idSucursalDestino' => 'required|exists:sucursales,idSucursal'
            ]);

            // Validar datos de clientes
            $this->validarCliente($request->clienteRemitente, 'Cliente Remitente');
            $this->validarCliente($request->clienteDestinatario, 'Cliente Destinatario');

            // Crear o buscar cliente remitente
            $clienteRemitente = $this->crearOActualizarCliente($request->clienteRemitente);

            // Crear o buscar cliente destinatario
            $clienteDestinatario = $this->crearOActualizarCliente($request->clienteDestinatario);

            // Crear la encomienda
            $encomienda = Encomienda::create([
                'codigo' => $request->codigo,
                'descripcion' => $request->descripcion,
                'costo' => $request->costo,
                'observaciones' => $request->observaciones,
                'estadoPago' => 'Pendiente',
                'idClienteRemitente' => $clienteRemitente->idCliente,
                'idClienteDestinatario' => $clienteDestinatario->idCliente,
                'idSucursalOrigen' => $request->idSucursalOrigen,
                'idSucursalDestino' => $request->idSucursalDestino
            ]);

            // Crear el estado inicial
            EstadoEncomienda::create([
                'descripcionEstado' => 'Registrado',
                'fechaCambio' => now(),
                'idEncomienda' => $encomienda->idEncomienda
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Encomienda creada exitosamente',
                'data' => $encomienda->load([
                    'clienteRemitente',
                    'clienteDestinatario',
                    'sucursalOrigen',
                    'sucursalDestino',
                    'estados'
                ])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al crear encomienda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $encomienda = Encomienda::with([
                'clienteRemitente',
                'clienteDestinatario',
                'sucursalOrigen',
                'sucursalDestino',
                'estados' => function($query) {
                    $query->orderBy('fechaCambio', 'desc');
                },
                'pagos'
            ])->findOrFail($id);

            return response()->json($encomienda);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Encomienda no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $encomienda = Encomienda::findOrFail($id);
            
            $request->validate([
                'descripcion' => 'required|string',
                'costo' => 'required|numeric|min:0',
                'observaciones' => 'nullable|string',
                'estadoPago' => 'required|in:Pendiente,Parcial,Pagado'
            ]);

            $encomienda->update($request->only([
                'descripcion',
                'costo',
                'observaciones',
                'estadoPago'
            ]));

            return response()->json([
                'message' => 'Encomienda actualizada exitosamente',
                'data' => $encomienda
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar encomienda',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $encomienda = Encomienda::findOrFail($id);
            $encomienda->delete();

            return response()->json([
                'message' => 'Encomienda eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar encomienda',
                'error' => $e->getMessage()
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
        // Buscar cliente existente por DNI
        $clienteExistente = Cliente::byDni($clienteData['dni'])->first();

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
                'dni' => $clienteData['dni'],
                'nombre' => $clienteData['nombre'],
                'telefono' => $clienteData['telefono']
            ]);
        }
    }
}