<?php

namespace App\Http\Controllers;

use App\Models\ActividadUsuario;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    public function index(Request $request) {
        try {
            $query = ActividadUsuario::with('usuario');
            
            // Filtros
            if($request->has('idUsuario') && $request->idUsuario) {
                $query->where('idUsuario', $request->idUsuario);
            }
            if($request->has('fechaDesde') && $request->fechaDesde) {
                $query->where('fecha', '>=', $request->fechaDesde);
            }
            if($request->has('fechaHasta') && $request->fechaHasta) {
                $query->where('fecha', '<=', $request->fechaHasta . ' 23:59:59');
            }
            
            // Filtro por rol
            if($request->has('rol') && $request->rol) {
                $query->whereHas('usuario', function($q) use ($request) {
                    $q->where('rol', $request->rol);
                });
            }
            
            // Filtro por búsqueda de texto
            if($request->has('busqueda') && $request->busqueda) {
                $busqueda = $request->busqueda;
                $query->where(function($q) use ($busqueda) {
                    $q->where('descripcionActividad', 'like', "%{$busqueda}%")
                      ->orWhereHas('usuario', function($subQ) use ($busqueda) {
                          $subQ->where('nombre', 'like', "%{$busqueda}%")
                               ->orWhere('email', 'like', "%{$busqueda}%");
                      });
                });
            }
            
            // Filtro por módulo
            if($request->has('modulo') && $request->modulo) {
                $query->where('modulo', $request->modulo);
            }
            
            // Filtro por tipo de acción (usar campo de BD si existe, sino buscar en descripción)
            if($request->has('tipoAccion') && $request->tipoAccion) {
                $tipoAccion = $request->tipoAccion;
                $query->where(function($q) use ($tipoAccion) {
                    // Primero buscar en el campo tipo_accion
                    $q->where('tipo_accion', $tipoAccion);
                    
                    // Si no hay resultados, buscar en descripción (para actividades antiguas)
                    if($tipoAccion !== 'otra') {
                        $tipos = [
                            'creacion' => ['creado', 'creada', 'creó', 'creó', 'registrado', 'registrada'],
                            'actualizacion' => ['actualizado', 'actualizada', 'modificado', 'modificada', 'editado', 'editada'],
                            'eliminacion' => ['eliminado', 'eliminada', 'borrado', 'borrada', 'anulado', 'anulada'],
                            'login' => ['inicio de sesión', 'login', 'ingresó'],
                            'logout' => ['cierre de sesión', 'logout', 'salió'],
                            'descarga' => ['descargado', 'descargada', 'descarga'],
                            'exportacion' => ['exportado', 'exportada', 'exportación'],
                            'visualizacion' => ['visualizado', 'visualizada', 'consultado', 'consultada']
                        ];
                        
                        if(isset($tipos[$tipoAccion])) {
                            $q->orWhere(function($subQ) use ($tipos, $tipoAccion) {
                                foreach($tipos[$tipoAccion] as $palabra) {
                                    $subQ->orWhere('descripcionActividad', 'like', "%{$palabra}%");
                                }
                            });
                        }
                    }
                });
            }
            
            $actividades = $query->orderBy('fecha','desc')
                ->orderBy('id','desc')
                ->get();
            
            // Formatear actividades para el frontend
            $actividadesFormateadas = $actividades->map(function($actividad) {
                return $this->formatearActividad($actividad);
            });
            
            return response()->json([
                'message' => 'Actividades obtenidas correctamente',
                'data' => $actividadesFormateadas,
                'total' => $actividadesFormateadas->count()
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener actividades',
                'error' => $e->getMessage()
            ],500);
        }
    }
    
    /**
     * Formatea una actividad para el frontend
     */
    private function formatearActividad($actividad) {
        $descripcion = $actividad->descripcionActividad;
        $usuario = $actividad->usuario;
        
        // Usar tipo_accion y modulo de la base de datos, o detectarlos si son null
        $tipoAccion = $actividad->tipo_accion ?: $this->detectarTipoAccion($descripcion);
        $modulo = $actividad->modulo ?: $this->detectarModulo($descripcion);
        
        // Formatear fecha correctamente
        $fecha = $actividad->fecha instanceof \Carbon\Carbon 
            ? $actividad->fecha 
            : \Carbon\Carbon::parse($actividad->fecha);
        
        return [
            'id' => $actividad->id,
            'usuario_id' => $actividad->idUsuario,
            'usuario_nombre' => $usuario ? $usuario->nombre : 'Usuario desconocido',
            'usuario_rol' => $usuario ? $usuario->rol : 'N/A',
            'tipo_accion' => $tipoAccion,
            'modulo' => $modulo,
            'descripcion' => $descripcion,
            'fecha_hora' => $fecha->toISOString(),
            'created_at' => $actividad->created_at ? $actividad->created_at->toISOString() : null,
            'updated_at' => $actividad->updated_at ? $actividad->updated_at->toISOString() : null
        ];
    }
    
    /**
     * Detecta el tipo de acción basado en la descripción
     */
    private function detectarTipoAccion($descripcion) {
        $descripcionLower = strtolower($descripcion);
        
        if (strpos($descripcionLower, 'creado') !== false || strpos($descripcionLower, 'creada') !== false || 
            strpos($descripcionLower, 'registrado') !== false || strpos($descripcionLower, 'registrada') !== false) {
            return 'creacion';
        }
        if (strpos($descripcionLower, 'actualizado') !== false || strpos($descripcionLower, 'actualizada') !== false ||
            strpos($descripcionLower, 'modificado') !== false || strpos($descripcionLower, 'modificada') !== false ||
            strpos($descripcionLower, 'editado') !== false || strpos($descripcionLower, 'editada') !== false) {
            return 'actualizacion';
        }
        if (strpos($descripcionLower, 'eliminado') !== false || strpos($descripcionLower, 'eliminada') !== false ||
            strpos($descripcionLower, 'borrado') !== false || strpos($descripcionLower, 'borrada') !== false ||
            strpos($descripcionLower, 'anulado') !== false || strpos($descripcionLower, 'anulada') !== false) {
            return 'eliminacion';
        }
        if (strpos($descripcionLower, 'inicio de sesión') !== false || strpos($descripcionLower, 'login') !== false ||
            strpos($descripcionLower, 'ingresó') !== false) {
            return 'login';
        }
        if (strpos($descripcionLower, 'cierre de sesión') !== false || strpos($descripcionLower, 'logout') !== false ||
            strpos($descripcionLower, 'salió') !== false) {
            return 'logout';
        }
        if (strpos($descripcionLower, 'descargado') !== false || strpos($descripcionLower, 'descargada') !== false ||
            strpos($descripcionLower, 'descarga') !== false) {
            return 'descarga';
        }
        if (strpos($descripcionLower, 'exportado') !== false || strpos($descripcionLower, 'exportada') !== false ||
            strpos($descripcionLower, 'exportación') !== false) {
            return 'exportacion';
        }
        if (strpos($descripcionLower, 'visualizado') !== false || strpos($descripcionLower, 'visualizada') !== false ||
            strpos($descripcionLower, 'consultado') !== false || strpos($descripcionLower, 'consultada') !== false) {
            return 'visualizacion';
        }
        
        return 'otra';
    }
    
    /**
     * Detecta el módulo basado en la descripción
     */
    private function detectarModulo($descripcion) {
        $descripcionLower = strtolower($descripcion);
        
        if (strpos($descripcionLower, 'usuario') !== false) {
            return 'Usuarios';
        }
        if (strpos($descripcionLower, 'encomienda') !== false) {
            return 'Encomiendas';
        }
        if (strpos($descripcionLower, 'flete') !== false) {
            return 'Fletes';
        }
        if (strpos($descripcionLower, 'sucursal') !== false) {
            return 'Sucursales';
        }
        if (strpos($descripcionLower, 'cliente') !== false) {
            return 'Clientes';
        }
        if (strpos($descripcionLower, 'pago') !== false) {
            return 'Pagos';
        }
        if (strpos($descripcionLower, 'reporte') !== false) {
            return 'Reportes';
        }
        if (strpos($descripcionLower, 'configuración') !== false || strpos($descripcionLower, 'configuracion') !== false) {
            return 'Configuración';
        }
        
        return 'Sistema';
    }
    public function actividadesUsuario(Request $request, string $idUsuario){
        try {
            $actividades = ActividadUsuario::where('idUsuario', $idUsuario)
                ->with('usuario')
                ->orderBy('fecha', 'desc')
                ->orderBy('id', 'desc')
                ->get();
            
            // Formatear actividades para el frontend
            $actividadesFormateadas = $actividades->map(function($actividad) {
                return $this->formatearActividad($actividad);
            });
            
            return response()->json([
                'message' => 'Actividades del usuario obtenidas exitosamente.',
                'data' => $actividadesFormateadas,
                'total' => $actividadesFormateadas->count()
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
                'data' => $this->formatearActividad($actividad)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Actividad no encontrada.',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
