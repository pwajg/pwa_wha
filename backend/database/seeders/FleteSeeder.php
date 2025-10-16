<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las sucursales
        $sucursales = DB::table('sucursales')->get();
        
        if ($sucursales->isEmpty()) {
            $this->command->warn('No hay sucursales disponibles. Ejecuta primero el EncomiendaSeeder.');
            return;
        }

        // Sucursal principal (origen por defecto)
        $sucursalPrincipal = $sucursales->first();
        
        // Crear un transporte por defecto si no existe
        $transporteId = DB::table('transportes')->insertGetId([
            'placa' => 'ABC-123',
            'caracteristicas' => 'Camión de carga - Capacidad: 1000kg',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $fletesCreados = 0;
        $fechaHoy = Carbon::today();

        // Crear un flete por cada sucursal (excepto la principal)
        foreach ($sucursales as $sucursal) {
            if ($sucursal->id == $sucursalPrincipal->id) {
                continue; // Saltar la sucursal principal
            }

            // Generar código único para el flete
            $codigoFlete = 'FLT-' . $fechaHoy->format('ymd') . '-' . str_pad($fletesCreados + 1, 3, '0', STR_PAD_LEFT);

            // Crear el flete
            $fleteId = DB::table('fletes')->insertGetId([
                'codigo' => $codigoFlete,
                'observaciones' => "Flete automático del {$fechaHoy->format('d/m/Y')} hacia {$sucursal->nombre}",
                'idTransporte' => $transporteId,
                'idSucursalOrigen' => $sucursalPrincipal->id,
                'idSucursalDestino' => $sucursal->id,
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
        }

        $this->command->info('✅ Fletes creados exitosamente:');
        $this->command->info("   - {$fletesCreados} fletes generados para el {$fechaHoy->format('d/m/Y')}");
        $this->command->info('   - Cada flete tiene origen en: ' . $sucursalPrincipal->nombre);
        $this->command->info('   - Estados iniciales: "En origen"');
        $this->command->info('');
        $this->command->info('📦 Códigos de fletes generados:');
        
        // Mostrar los códigos generados
        $fletesGenerados = DB::table('fletes')
            ->join('sucursales as origen', 'fletes.idSucursalOrigen', '=', 'origen.id')
            ->join('sucursales as destino', 'fletes.idSucursalDestino', '=', 'destino.id')
            ->whereDate('fletes.created_at', $fechaHoy)
            ->select('fletes.codigo', 'origen.nombre as origen', 'destino.nombre as destino')
            ->get();

        foreach ($fletesGenerados as $flete) {
            $this->command->info("   - {$flete->codigo} ({$flete->origen} → {$flete->destino})");
        }
    }
}
