<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
use App\Models\ActividadUsuario;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $sucursales = Sucursal::all();
            return response()->json($sucursales);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener sucursales',
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
            $request->validate([
                'nombre' => 'required|string|max:255',
                'direccion' => 'required|string|max:255',
                'ciudad' => 'required|string|max:255',
                'telefono' => 'required|string|max:9'
            ]);

            $sucursal = Sucursal::create($request->all());
            $usuarioId = $request->user_id;
            if($usuarioId) {
                ActividadUsuario::create([
                    'descripcionActividad' => "Sucursal creada: \n--> " . "{$sucursal->nombre}",
                    'fecha' => now(),
                    'idUsuario' => $usuarioId
                ]);
            }
            return response()->json($sucursal, 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear sucursal',
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
            $sucursal = Sucursal::findOrFail($id);
            return response()->json($sucursal);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Sucursal no encontrada',
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
            $sucursal = Sucursal::findOrFail($id);
            $validated = $request->validate([
                'nombre' => 'required|string|max:255',
                'telefono' => 'required|string|max:20',
            ]);
            $sucursal->update($request->only(['nombre','direccion','telefono']));
            $usuarioId = $request->user_id;
            if($usuarioId) {
                ActividadUsuario::create([
                    'descripcionActividad' => "Sucursal actualizada: \n--> " . "{$sucursal->nombre}",
                    'fecha' => now(),
                    'idUsuario' => $usuarioId
                ]);
            }
            return response()->json([
                'message' => 'Sucursal actualizado exitosamente',
                'data' => $sucursal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar sucursal.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $sucursal = Sucursal::findOrFail($id);
            $usuarioId = $request->user_id;
            if($usuarioId) {
                ActividadUsuario::create([
                    'descripcionActividad' => "Sucursal eliminada: \n--> " . "{$sucursal->nombre}",
                    'fecha' => now(),
                    'idUsuario' => $usuarioId
                ]);
            }
            $sucursal->delete();

            return response()->json([
                'message' => 'Sucursal eliminada correctamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar sucursal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
