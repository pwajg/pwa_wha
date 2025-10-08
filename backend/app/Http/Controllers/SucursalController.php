<?php

namespace App\Http\Controllers;

use App\Models\Sucursal;
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
                'nombreSucursal' => 'required|string|max:255',
                'direccion' => 'required|string|max:255',
                'telefono' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
                'responsable' => 'nullable|string|max:255'
            ]);

            $sucursal = Sucursal::create($request->all());

            return response()->json([
                'message' => 'Sucursal creada exitosamente',
                'data' => $sucursal
            ], 201);
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
            
            $request->validate([
                'nombreSucursal' => 'required|string|max:255',
                'direccion' => 'required|string|max:255',
                'telefono' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
                'responsable' => 'nullable|string|max:255'
            ]);

            $sucursal->update($request->all());

            return response()->json([
                'message' => 'Sucursal actualizada exitosamente',
                'data' => $sucursal
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar sucursal',
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
            $sucursal = Sucursal::findOrFail($id);
            $sucursal->delete();

            return response()->json([
                'message' => 'Sucursal eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar sucursal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
