<?php

namespace App\Http\Controllers;

use App\Models\Encomienda;
use App\Models\Cliente;
use App\Models\Pago;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    /**
     * Obtener métricas resumen para el dashboard
     */
    public function metricasResumen(Request $request)
    {
        try {
            $fechaDesde = $request->input('fechaDesde');
            $fechaHasta = $request->input('fechaHasta');

            // Construir query base para encomiendas
            $queryEncomiendas = Encomienda::query();
            $queryPagos = Pago::query();

            // Aplicar filtros de fecha si están presentes
            if ($fechaDesde) {
                $queryEncomiendas->whereDate('created_at', '>=', $fechaDesde);
                $queryPagos->whereDate('fechaPago', '>=', $fechaDesde);
            }

            if ($fechaHasta) {
                $queryEncomiendas->whereDate('created_at', '<=', $fechaHasta);
                $queryPagos->whereDate('fechaPago', '<=', $fechaHasta);
            }

            // Total de encomiendas
            $totalEncomiendas = $queryEncomiendas->count();

            // Ingresos totales (suma de todos los pagos)
            $ingresosTotales = $queryPagos->sum('monto');

            // Clientes activos (clientes que tienen encomiendas en el periodo)
            $queryClientesActivos = Cliente::query();
            
            if ($fechaDesde || $fechaHasta) {
                $queryClientesActivos->where(function($q) use ($fechaDesde, $fechaHasta) {
                    $q->whereHas('encomiendasRemitente', function($query) use ($fechaDesde, $fechaHasta) {
                        if ($fechaDesde) {
                            $query->whereDate('created_at', '>=', $fechaDesde);
                        }
                        if ($fechaHasta) {
                            $query->whereDate('created_at', '<=', $fechaHasta);
                        }
                    })->orWhereHas('encomiendasDestinatario', function($query) use ($fechaDesde, $fechaHasta) {
                        if ($fechaDesde) {
                            $query->whereDate('created_at', '>=', $fechaDesde);
                        }
                        if ($fechaHasta) {
                            $query->whereDate('created_at', '<=', $fechaHasta);
                        }
                    });
                });
            } else {
                // Si no hay filtros de fecha, contar todos los clientes que tienen encomiendas
                $queryClientesActivos->where(function($q) {
                    $q->whereHas('encomiendasRemitente')
                      ->orWhereHas('encomiendasDestinatario');
                });
            }
            
            $clientesActivos = $queryClientesActivos->distinct()->count('idCliente');

            // Total de sucursales
            $totalSucursales = Sucursal::count();

            return response()->json([
                'totalEncomiendas' => $totalEncomiendas,
                'ingresosTotales' => (float) $ingresosTotales,
                'clientesActivos' => $clientesActivos,
                'totalSucursales' => $totalSucursales
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener métricas resumen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener volumen de envíos por período
     */
    public function volumenEnvios(Request $request)
    {
        try {
            $periodo = $request->input('periodo', 'semana'); // 'dia', 'semana', 'mes'
            $fechaDesde = $request->input('fechaDesde');
            $fechaHasta = $request->input('fechaHasta');

            $query = Encomienda::query();

            // Aplicar filtros de fecha
            if ($fechaDesde) {
                $query->whereDate('created_at', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $query->whereDate('created_at', '<=', $fechaHasta);
            }

            // Obtener encomiendas con su estado actual
            $encomiendas = $query->with(['estadoEncomiendas' => function($q) {
                $q->orderBy('fechaCambio', 'desc');
            }])->get();

            // Determinar el estado actual de cada encomienda
            $encomiendasConEstado = $encomiendas->map(function($encomienda) {
                $estadoActual = $encomienda->estadoEncomiendas->first();
                return [
                    'id' => $encomienda->idEncomienda,
                    'created_at' => $encomienda->created_at,
                    'estado' => $estadoActual ? $estadoActual->descripcionEstado : 'Registrado'
                ];
            });

            // Agrupar por período según el tipo
            $datos = [];
            
            if ($periodo === 'dia') {
                // Agrupar por hora del día
                $grupos = $encomiendasConEstado->groupBy(function($item) {
                    return Carbon::parse($item['created_at'])->format('H:00');
                });
                
                foreach ($grupos as $hora => $items) {
                    $porEstado = $items->groupBy('estado');
                    $estadoPrincipal = $porEstado->sortByDesc(function($estadoItems) {
                        return $estadoItems->count();
                    })->keys()->first() ?? 'Registrado';
                    
                    $datos[] = [
                        'label' => $hora,
                        'cantidad' => $items->count(),
                        'estado' => $estadoPrincipal
                    ];
                }
                
                // Ordenar por hora
                usort($datos, function($a, $b) {
                    return strcmp($a['label'], $b['label']);
                });
                
            } elseif ($periodo === 'semana') {
                // Agrupar por día de la semana
                $diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                
                for ($i = 6; $i >= 0; $i--) {
                    $fecha = Carbon::now()->subDays($i);
                    $fechaInicio = $fecha->copy()->startOfDay();
                    $fechaFin = $fecha->copy()->endOfDay();
                    
                    $itemsDelDia = $encomiendasConEstado->filter(function($item) use ($fechaInicio, $fechaFin) {
                        $fechaItem = Carbon::parse($item['created_at']);
                        return $fechaItem->gte($fechaInicio) && $fechaItem->lte($fechaFin);
                    });
                    
                    if ($itemsDelDia->count() > 0 || !$fechaDesde || !$fechaHasta) {
                        $porEstado = $itemsDelDia->groupBy('estado');
                        $estadoPrincipal = $porEstado->sortByDesc(function($estadoItems) {
                            return $estadoItems->count();
                        })->keys()->first() ?? 'Registrado';
                        
                        $datos[] = [
                            'label' => $diasSemana[$fecha->dayOfWeek],
                            'cantidad' => $itemsDelDia->count(),
                            'estado' => $estadoPrincipal
                        ];
                    }
                }
                
            } else { // mes
                // Agrupar por semana del mes
                $inicio = $fechaDesde ? Carbon::parse($fechaDesde) : Carbon::now()->startOfMonth();
                $fin = $fechaHasta ? Carbon::parse($fechaHasta) : Carbon::now();
                
                $semanaActual = $inicio->copy();
                $numeroSemana = 1;
                
                while ($semanaActual->lte($fin)) {
                    $semanaInicio = $semanaActual->copy()->startOfWeek();
                    $semanaFin = $semanaActual->copy()->endOfWeek();
                    
                    if ($semanaFin->gt($fin)) {
                        $semanaFin = $fin->copy();
                    }
                    
                    $itemsSemana = $encomiendasConEstado->filter(function($item) use ($semanaInicio, $semanaFin) {
                        $fechaItem = Carbon::parse($item['created_at']);
                        return $fechaItem->gte($semanaInicio) && $fechaItem->lte($semanaFin);
                    });
                    
                    $porEstado = $itemsSemana->groupBy('estado');
                    $estadoPrincipal = $porEstado->sortByDesc(function($estadoItems) {
                        return $estadoItems->count();
                    })->keys()->first() ?? 'Registrado';
                    
                    $datos[] = [
                        'label' => 'Sem ' . $numeroSemana,
                        'cantidad' => $itemsSemana->count(),
                        'estado' => $estadoPrincipal
                    ];
                    
                    $semanaActual->addWeek();
                    $numeroSemana++;
                }
            }

            return response()->json($datos, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener volumen de envíos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener tendencia de crecimiento (envíos y ganancias por mes)
     */
    public function tendenciaCrecimiento(Request $request)
    {
        try {
            $fechaDesde = $request->input('fechaDesde');
            $fechaHasta = $request->input('fechaHasta');

            $query = Encomienda::query();

            // Aplicar filtros de fecha
            if ($fechaDesde) {
                $query->whereDate('created_at', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $query->whereDate('created_at', '<=', $fechaHasta);
            }

            // Obtener encomiendas
            $encomiendas = $query->get();

            // Agrupar por mes
            $gruposPorMes = $encomiendas->groupBy(function($encomienda) {
                return Carbon::parse($encomienda->created_at)->format('Y-m');
            });

            $datos = [];
            $meses = [
                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
            ];

            foreach ($gruposPorMes->sortKeys() as $mesAnio => $items) {
                [$anio, $mes] = explode('-', $mesAnio);
                $totalEnvios = $items->count();
                $totalGanancias = $items->sum(function($encomienda) {
                    return $encomienda->Pago ? $encomienda->Pago->sum('monto') : 0;
                });

                // Obtener pagos reales de cada encomienda
                $gananciasReales = 0;
                foreach ($items as $encomienda) {
                    $pagos = Pago::where('idEncomienda', $encomienda->idEncomienda)->sum('monto');
                    $gananciasReales += $pagos;
                }

                $datos[] = [
                    'mes' => $meses[$mes] . ' ' . $anio,
                    'envios' => $totalEnvios,
                    'ganancias' => (float) $gananciasReales
                ];
            }

            return response()->json($datos, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener tendencia de crecimiento',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener mapa de calor por zonas (sucursales registradas)
     */
    public function mapaCalorZonas(Request $request)
    {
        try {
            $fechaDesde = $request->input('fechaDesde');
            $fechaHasta = $request->input('fechaHasta');
            $provincia = $request->input('provincia');

            // Obtener todas las sucursales registradas
            $sucursales = Sucursal::all();

            // Query base para encomiendas
            $queryEncomiendas = Encomienda::query()
                ->with(['Flete.SucursalDestino', 'Flete.SucursalOrigen']);

            // Aplicar filtros de fecha
            if ($fechaDesde) {
                $queryEncomiendas->whereDate('created_at', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $queryEncomiendas->whereDate('created_at', '<=', $fechaHasta);
            }

            $encomiendas = $queryEncomiendas->get();

            // Calcular estadísticas por sucursal
            $resultado = [];
            
            foreach ($sucursales as $sucursal) {
                // Contar encomiendas donde esta sucursal es destino
                $enviosDestino = $encomiendas->filter(function($encomienda) use ($sucursal) {
                    return $encomienda->Flete && 
                           $encomienda->Flete->idSucursalDestino == $sucursal->id;
                })->count();

                // Contar encomiendas donde esta sucursal es origen
                $enviosOrigen = $encomiendas->filter(function($encomienda) use ($sucursal) {
                    return $encomienda->Flete && 
                           $encomienda->Flete->idSucursalOrigen == $sucursal->id;
                })->count();

                // Total de envíos (suma de origen y destino)
                $totalEnvios = $enviosDestino + $enviosOrigen;

                // Calcular ingresos de encomiendas conectadas a esta sucursal
                $ingresos = 0;
                foreach ($encomiendas as $encomienda) {
                    $sucursalRelacionada = false;
                    
                    // Verificar si la encomienda está relacionada con esta sucursal
                    if ($encomienda->Flete) {
                        if ($encomienda->Flete->idSucursalDestino == $sucursal->id ||
                            $encomienda->Flete->idSucursalOrigen == $sucursal->id) {
                            $sucursalRelacionada = true;
                        }
                    }
                    
                    if ($sucursalRelacionada) {
                        // Sumar pagos de esta encomienda
                        $pagos = Pago::where('idEncomienda', $encomienda->idEncomienda)->sum('monto');
                        $ingresos += $pagos;
                    }
                }

                // Aplicar filtro de provincia si existe
                if ($provincia) {
                    // Si la ciudad de la sucursal no coincide con la provincia, omitir
                    $ciudadLower = strtolower($sucursal->ciudad ?? '');
                    $provinciaLower = strtolower($provincia);
                    
                    // Si no hay coincidencia, continuar
                    if (strpos($ciudadLower, $provinciaLower) === false && 
                        strpos($provinciaLower, $ciudadLower) === false) {
                        continue;
                    }
                }

                $resultado[] = [
                    'id' => $sucursal->id,
                    'nombre' => $sucursal->nombre,
                    'ciudad' => $sucursal->ciudad ?? '',
                    'direccion' => $sucursal->direccion ?? '',
                    'telefono' => $sucursal->telefono ?? '',
                    'envios' => $totalEnvios,
                    'enviosOrigen' => $enviosOrigen,
                    'enviosDestino' => $enviosDestino,
                    'ingresos' => (float) $ingresos
                ];
            }

            // Ordenar por número de envíos (mayor a menor)
            usort($resultado, function($a, $b) {
                return $b['envios'] - $a['envios'];
            });

            return response()->json($resultado, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener mapa de calor de zonas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

