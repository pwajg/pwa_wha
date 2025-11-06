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
     * Obtener mapa de calor por zonas (ciudades)
     */
    public function mapaCalorZonas(Request $request)
    {
        try {
            $fechaDesde = $request->input('fechaDesde');
            $fechaHasta = $request->input('fechaHasta');
            $provincia = $request->input('provincia');

            $query = Encomienda::query()
                ->with(['ClienteDestinatario', 'Flete.SucursalDestino']);

            // Aplicar filtros de fecha
            if ($fechaDesde) {
                $query->whereDate('created_at', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $query->whereDate('created_at', '<=', $fechaHasta);
            }

            $encomiendas = $query->get();

            // Zonas predefinidas
            $zonas = [
                // Pataz
                ['id' => 1, 'nombre' => 'Tayabamba', 'provincia' => 'Pataz'],
                ['id' => 2, 'nombre' => 'Chilia', 'provincia' => 'Pataz'],
                ['id' => 3, 'nombre' => 'Huancaspata', 'provincia' => 'Pataz'],
                ['id' => 4, 'nombre' => 'Huaylillas', 'provincia' => 'Pataz'],
                ['id' => 5, 'nombre' => 'Huayo', 'provincia' => 'Pataz'],
                ['id' => 6, 'nombre' => 'Ongón', 'provincia' => 'Pataz'],
                ['id' => 7, 'nombre' => 'Parcoy', 'provincia' => 'Pataz'],
                ['id' => 8, 'nombre' => 'Pataz', 'provincia' => 'Pataz'],
                ['id' => 9, 'nombre' => 'Pías', 'provincia' => 'Pataz'],
                ['id' => 10, 'nombre' => 'Santiago de Challas', 'provincia' => 'Pataz'],
                ['id' => 11, 'nombre' => 'Taurija', 'provincia' => 'Pataz'],
                ['id' => 12, 'nombre' => 'Urpay', 'provincia' => 'Pataz'],
                // Trujillo
                ['id' => 13, 'nombre' => 'Trujillo', 'provincia' => 'Trujillo'],
                ['id' => 14, 'nombre' => 'El Porvenir', 'provincia' => 'Trujillo'],
                ['id' => 15, 'nombre' => 'La Esperanza', 'provincia' => 'Trujillo'],
                ['id' => 16, 'nombre' => 'Huanchaco', 'provincia' => 'Trujillo'],
                ['id' => 17, 'nombre' => 'Moche', 'provincia' => 'Trujillo'],
                ['id' => 18, 'nombre' => 'Salaverry', 'provincia' => 'Trujillo'],
            ];

            // Agrupar encomiendas por ciudad del destinatario
            // Nota: Asumiendo que la ciudad está en algún campo del cliente destinatario
            // Si no existe, podemos usar la ciudad de la sucursal destino si está disponible
            $encomiendasPorZona = [];

            foreach ($encomiendas as $encomienda) {
                if ($encomienda->ClienteDestinatario) {
                    // Intentar obtener la ciudad del cliente destinatario
                    // Como no hay campo "ciudad" en clientes, usaremos un método alternativo
                    // Por ahora, agruparemos según un patrón o según la sucursal destino si está disponible
                    $zonaNombre = 'Trujillo'; // Por defecto
                    
                    // Si hay una sucursal destino, podemos obtener su ciudad
                    if ($encomienda->Flete && $encomienda->Flete->SucursalDestino) {
                        $zonaNombre = $encomienda->Flete->SucursalDestino->ciudad ?? 'Trujillo';
                    }
                    
                    // Normalizar nombres de ciudades
                    $zonaNombre = $this->normalizarNombreZona($zonaNombre);
                    
                    if (!isset($encomiendasPorZona[$zonaNombre])) {
                        $encomiendasPorZona[$zonaNombre] = [
                            'envios' => 0,
                            'ingresos' => 0
                        ];
                    }
                    
                    $encomiendasPorZona[$zonaNombre]['envios']++;
                    
                    // Obtener ingresos de los pagos
                    $pagos = Pago::where('idEncomienda', $encomienda->idEncomienda)->sum('monto');
                    $encomiendasPorZona[$zonaNombre]['ingresos'] += $pagos;
                }
            }

            // Mapear datos a las zonas predefinidas
            $resultado = [];
            foreach ($zonas as $zona) {
                $zonaNombre = $zona['nombre'];
                $envios = $encomiendasPorZona[$zonaNombre]['envios'] ?? 0;
                $ingresos = $encomiendasPorZona[$zonaNombre]['ingresos'] ?? 0;
                
                // Aplicar filtro de provincia si existe
                if ($provincia && $zona['provincia'] !== $provincia) {
                    continue;
                }
                
                $resultado[] = [
                    'id' => $zona['id'],
                    'nombre' => $zonaNombre,
                    'provincia' => $zona['provincia'],
                    'envios' => $envios,
                    'ingresos' => (float) $ingresos
                ];
            }

            return response()->json($resultado, 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener mapa de calor de zonas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Normalizar nombre de zona para agrupar correctamente
     */
    private function normalizarNombreZona($nombre)
    {
        $normalizaciones = [
            'trujillo' => 'Trujillo',
            'el porvenir' => 'El Porvenir',
            'la esperanza' => 'La Esperanza',
            'huanchaco' => 'Huanchaco',
            'moche' => 'Moche',
            'salaverry' => 'Salaverry',
            'tayabamba' => 'Tayabamba',
            'chilia' => 'Chilia',
            'huancaspata' => 'Huancaspata',
            'huaylillas' => 'Huaylillas',
            'huayo' => 'Huayo',
            'ongón' => 'Ongón',
            'parcoy' => 'Parcoy',
            'pataz' => 'Pataz',
            'pías' => 'Pías',
            'santiago de challas' => 'Santiago de Challas',
            'taurija' => 'Taurija',
            'urpay' => 'Urpay',
        ];
        
        $nombreLower = strtolower($nombre);
        return $normalizaciones[$nombreLower] ?? 'Trujillo';
    }
}

