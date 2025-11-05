<?php

namespace App\Http\Controllers;

use App\Models\ActividadUsuario;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    public function index(Request $request) {
        try {
            $query = ActividadUsuario::with('usuario');
            if($request->has('idUsuario')) {
                $query->where('idUsuario',$request->idUsuario);
            }
            if($request->has('fechaDesde')) {
                $query->where('fecha','>=',$request->fechaDesde);
            }
            if($request->has('fechaHasta')) {
                $query->where('fecha','<=',$request->fechaHasta);
            }
            $actividades = $query->orderBy('fecha','desc')
                ->orderBy('id','desc')
                ->get();
            return response()->json([
                'message' => 'Actividades obtenidas correctamente',
                'actividades' => $actividades,
                'total' => $actividades->count()
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener actividades',
                'error' => $e->getMessage()
            ],500);
        }
    }
    public function actividadesUsuario(Request $request, string $idUsuario){
        try {
            $actividades = ActividadUsuario::where('idUsuario', $idUsuario)
                ->with('usuario')
                ->orderBy('fecha', 'desc')
                ->orderBy('id', 'desc')
                ->get();
            
            return response()->json([
                'message' => 'Actividades del usuario obtenidas exitosamente.',
                'actividades' => $actividades,
                'total' => $actividades->count()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener actividades del usuario.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function show(string $id){
        try {
            $actividad = ActividadUsuario::with('usuario')->findOrFail($id);
            
            return response()->json([
                'message' => 'Actividad encontrada exitosamente.',
                'actividad' => $actividad
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Actividad no encontrada.',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
