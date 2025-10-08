<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $clientes = Cliente::all();
            return response()->json($clientes);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener clientes',
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
                'tipoCliente' => 'required|string|in:Persona Natural,Empresa,Extranjero',
                'dni' => 'required|string|unique:clientes,dni',
                'nombre' => 'required|string|max:255',
                'apellido' => 'nullable|string|max:255',
                'telefono' => 'required|string|max:20',
                'direccion' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255'
            ]);

            $cliente = Cliente::create($request->all());

            return response()->json([
                'message' => 'Cliente creado exitosamente',
                'data' => $cliente
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear cliente',
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
            $cliente = Cliente::findOrFail($id);
            return response()->json($cliente);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Cliente no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Buscar cliente por DNI
     */
    public function buscarPorDni(string $dni)
    {
        try {
            $cliente = Cliente::byDni($dni)->first();
            
            if (!$cliente) {
                return response()->json([
                    'message' => 'Cliente no encontrado'
                ], 404);
            }

            return response()->json($cliente);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al buscar cliente',
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
            $cliente = Cliente::findOrFail($id);
            
            $request->validate([
                'tipoCliente' => 'required|string|in:Persona Natural,Empresa,Extranjero',
                'dni' => 'required|string|unique:clientes,dni,' . $id . ',idCliente',
                'nombre' => 'required|string|max:255',
                'apellido' => 'nullable|string|max:255',
                'telefono' => 'required|string|max:20',
                'direccion' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255'
            ]);

            $cliente->update($request->all());

            return response()->json([
                'message' => 'Cliente actualizado exitosamente',
                'data' => $cliente
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar cliente',
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
            $cliente = Cliente::findOrFail($id);
            $cliente->delete();

            return response()->json([
                'message' => 'Cliente eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
