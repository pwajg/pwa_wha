<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerarFletesDiarios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fletes:generar-diarios {--sucursal-origen=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Genera automáticamente un flete por cada sucursal de destino para el día actual';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚛 Iniciando generación automática de fletes...');
        
        // Obtener sucursal origen (por defecto la primera, o la especificada)
        $sucursalOrigenId = $this->option('sucursal-origen');
        
        if ($sucursalOrigenId) {
            $sucursalOrigen = DB::table('sucursales')->where('id', $sucursalOrigenId)->first();
            if (!$sucursalOrigen) {
                $this->error("❌ No se encontró la sucursal origen con ID: {$sucursalOrigenId}");
                return 1;
            }
        } else {
            $sucursalOrigen = DB::table('sucursales')->first();
            if (!$sucursalOrigen) {
                $this->error('❌ No hay sucursales disponibles en el sistema');
                return 1;
            }
        }

        $this->info("📍 Sucursal origen: {$sucursalOrigen->nombre}");

        // Obtener todas las sucursales de destino (excluyendo la origen)
        $sucursalesDestino = DB::table('sucursales')
            ->where('id', '!=', $sucursalOrigen->id)
            ->get();

        if ($sucursalesDestino->isEmpty()) {
            $this->warn('⚠️  No hay sucursales de destino disponibles');
            return 0;
        }

        // Verificar si ya existen fletes para hoy
        $fechaHoy = Carbon::today();
        $fletesExistentes = DB::table('fletes')
            ->whereDate('created_at', $fechaHoy)
            ->where('idSucursalOrigen', $sucursalOrigen->id)
            ->count();

        if ($fletesExistentes > 0) {
            $this->warn("⚠️  Ya existen {$fletesExistentes} fletes para hoy desde {$sucursalOrigen->nombre}");
            
            if (!$this->confirm('¿Deseas continuar y crear fletes adicionales?')) {
                $this->info('❌ Operación cancelada');
                return 0;
            }
        }

        // Obtener o crear un transporte disponible
        $transporte = DB::table('transportes')->first();
        if (!$transporte) {
            $transporteId = DB::table('transportes')->insertGetId([
                'placa' => 'AUTO-' . $fechaHoy->format('ymd'),
                'caracteristicas' => 'Transporte automático - Generado el ' . $fechaHoy->format('d/m/Y'),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->info("🚚 Transporte automático creado: {$transporteId}");
        } else {
            $transporteId = $transporte->idTransporte;
            $this->info("🚚 Usando transporte existente: {$transporte->placa}");
        }

        $fletesCreados = 0;
        $bar = $this->output->createProgressBar($sucursalesDestino->count());

        foreach ($sucursalesDestino as $sucursalDestino) {
            // Generar código único para el flete
            $codigoFlete = 'FLT-' . $fechaHoy->format('ymd') . '-' . str_pad($fletesCreados + 1, 3, '0', STR_PAD_LEFT);

            // Crear el flete
            $fleteId = DB::table('fletes')->insertGetId([
                'codigo' => $codigoFlete,
                'observaciones' => "Flete automático del {$fechaHoy->format('d/m/Y')} hacia {$sucursalDestino->nombre}",
                'idTransporte' => $transporteId,
                'idSucursalOrigen' => $sucursalOrigen->id,
                'idSucursalDestino' => $sucursalDestino->id,
                'created_at' => $fechaHoy,
                'updated_at' => $fechaHoy
            ]);

            // Crear estado inicial del flete
            DB::table('estado_fletes')->insert([
                'descripcionEstado' => 'En origen',
                'fechaCambio' => $fechaHoy,
                'idFlete' => $fleteId,
                'created_at' => $fechaHoy,
                'updated_at' => $fechaHoy
            ]);

            $fletesCreados++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("✅ Generación completada exitosamente:");
        $this->info("   📦 {$fletesCreados} fletes creados para el {$fechaHoy->format('d/m/Y')}");
        $this->info("   🏢 Origen: {$sucursalOrigen->nombre}");
        $this->info("   🎯 Destinos: " . $sucursalesDestino->pluck('nombre')->join(', '));
        $this->info("   📊 Estado inicial: 'En origen'");

        // Mostrar resumen de fletes creados
        $this->newLine();
        $this->info('📋 Resumen de fletes creados:');
        
        $fletesGenerados = DB::table('fletes')
            ->join('sucursales as origen', 'fletes.idSucursalOrigen', '=', 'origen.id')
            ->join('sucursales as destino', 'fletes.idSucursalDestino', '=', 'destino.id')
            ->whereDate('fletes.created_at', $fechaHoy)
            ->where('fletes.idSucursalOrigen', $sucursalOrigen->id)
            ->select('fletes.codigo', 'origen.nombre as origen', 'destino.nombre as destino')
            ->orderBy('fletes.codigo')
            ->get();

        $headers = ['Código', 'Origen', 'Destino'];
        $rows = $fletesGenerados->map(function ($flete) {
            return [$flete->codigo, $flete->origen, $flete->destino];
        })->toArray();

        $this->table($headers, $rows);

        return 0;
    }
}