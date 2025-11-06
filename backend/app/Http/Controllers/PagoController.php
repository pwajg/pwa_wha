<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Encomienda;
use App\Models\ActividadUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $pagos = Pago::all();
            return response()->json([
                'message' => 'Pagos obtenidos exitosamente.',
                'pagos' => $pagos,
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener pagos.',
                'error' => $e->getMessage(),
            ],500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:20',
            'monto' => 'required|numeric|min:0.01',
            'fechaPago' => 'required|date',
            'modalidadPago' => 'required|in:Efectivo,Transferencia,Tarjeta,BilleteraDigital',
            'idEncomienda' => 'required|exists:encomiendas,idEncomienda'
        ]);
        try {
            DB::beginTransaction();
            $encomienda = Encomienda::where('idEncomienda',$validated['idEncomienda'])->first();
            $totalPrevio = DB::table('pagos')
                ->where('idEncomienda',$encomienda->idEncomienda)
                ->sum('monto');
            if($encomienda->estadoPago === 'Pagado') {
                DB::rollback();
                return response()->json([
                    'message' => 'La Encomienda ya está pagada completamente.',
                ], 422);
            }
            $totalNuevo = $totalPrevio + (float) $validated['monto'];
            if($totalNuevo > (float) $encomienda->costo) {
                DB::rollback();
                return response()->json([
                    'message' => 'El monto del pago excede el costo de la encomienda.',
                    'total_pagado' => $totalNuevo,
                    'costo_total' => (float) $encomienda->costo,
                    'exceso' => $totalNuevo - (float) $encomienda->costo,
                ], 422);
            }
            $pago = Pago::create([
                'codigo' => $validated['codigo'] ?? null,
                'monto' => $validated['monto'],
                'fechaPago' => Carbon::parse($validated['fechaPago']),
                'modalidadPago' => $validated['modalidadPago'],
                'idEncomienda' => $validated['idEncomienda']
            ]);  
            $nuevoEstadoPago = 'Pendiente';
            if($totalNuevo >= (float) $encomienda->costo) {
                $nuevoEstadoPago = 'Pagado';
            } elseif ($totalNuevo > 0) {
                $nuevoEstadoPago = 'Parcial';
            }
            $encomienda->update(['estadoPago' => $nuevoEstadoPago]);
            $usuarioId = $request->user_id;
            if($usuarioId) {
                ActividadUsuario::crearActividad(
                    $usuarioId,
                    "Pago creado para encomienda con código: {$encomienda->codigo}. Pago: {$pago->monto}",
                    'creacion',
                    'Pagos'
                );
            }
            DB::commit();
            return response()->json([
                'message' => 'Pago creado exitosamente',
                'pago' => $pago,
                'encomienda' => $encomienda->fresh(),
                'total_pagado' => $totalNuevo,
                'adeudo' => (float) $encomienda->costo - $totalNuevo
            ],201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Error al crear pago',
                'error' => $e->getMessage(),
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $idPago)
    {
        try {
            $pago = Pago::findOrFail($idPago);
            return response()->json([
                'message' => 'Pago encontrado exitosamente.',
                'pago' => $pago,
            ]);
        } catch(\Exception $e) {
            return response()->json([

            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pago $pago)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $idPago)
    {
        try {
            $validated = $request->validate([
                'codigo' => 'nullable|string|max:20',
                'monto' => 'required|numeric|min:0.01',
                'modalidadPago' => 'required|in:Efectivo,Transferencia,Tarjeta,BilleteraDigital',
            ]);
            $pago = Pago::find($idPago);
            if(!$pago) {
                return response()->json([
                    'message' => 'Pago no encontrado.',
                    'id_buscado' => $idPago
                ], 404);
            }
            $updateData = [
                'monto' => $validated['monto'],
                'modalidadPago' => $validated['modalidadPago'],
            ];
            if(array_key_exists('codigo',$validated)) {
                $updateData['codigo'] = $validated['codigo'];
            }
            $pago->update($updateData);
            $pago->load(['Encomienda']);
            $usuarioId = $request->user_id;
            if($usuarioId) {
                ActividadUsuario::crearActividad(
                    $usuarioId,
                    "Pago actualizado.",
                    'actualizacion',
                    'Pagos'
                );
            }
            return response()->json([
                'message' => 'Pago actualizado exitosamente.',
                'pago' => $pago,
                'encomienda' => $pago->encomienda,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar pago.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $idPago)
    {
        try {
            DB::beginTransaction();
            $pago = Pago::findOrFail($idPago);
            if(!$pago) {
                return response()->json([
                    'message' => 'Pago no encontrado.',
                    'id_buscado' => $idPago
                ], 404);
            }
            $encomienda = Encomienda::where('idEncomienda',$pago->idEncomienda)->first();
            $pago->delete();
            $totalPagado = DB::table('pagos')
                ->where('idEncomienda',$encomienda->idEncomienda)
                ->sum('monto');
            $nuevoEstado = 'Pendiente';
            if ($totalPagado >= (float) $encomienda->costo) {
                $nuevoEstado = 'Pagado';
            } elseif ($totalPagado > 0) {
                $nuevoEstado = 'Parcial';
            }
            $encomienda->update(['estadoPago' => $nuevoEstado]);
            $usuarioId = $request->user_id;
            if($usuarioId) {
                ActividadUsuario::crearActividad(
                    $usuarioId,
                    "Pago eliminado para encomienda con código {$encomienda->codigo}.",
                    'eliminacion',
                    'Pagos'
                );
            }
            DB::commit();
            return response()->json([
                'message' => 'Pago eliminado exitosamente',
                'encomienda' => $encomienda->fresh(),
                'total_pagado' => $totalPagado,
                'adeudo' => (float) $encomienda->costo - $totalPagado
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar Pago',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
