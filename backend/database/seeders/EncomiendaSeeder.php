<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Sucursal;
use App\Models\Cliente;
use App\Models\Encomienda;
use App\Models\EstadoEncomienda;
use Carbon\Carbon;

class EncomiendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear sucursales usando DB::table para evitar problemas con el modelo
        $sucursalLimaId = DB::table('sucursales')->insertGetId([
            'nombre' => 'Lima Centro',
            'direccion' => 'Av. Jirón de la Unión 123',
            'ciudad' => 'Lima',
            'telefono' => '2345678',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $sucursalArequipaId = DB::table('sucursales')->insertGetId([
            'nombre' => 'Arequipa Centro',
            'direccion' => 'Plaza de Armas 456',
            'ciudad' => 'Arequipa',
            'telefono' => '123456',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $sucursalCuscoId = DB::table('sucursales')->insertGetId([
            'nombre' => 'Cusco Centro',
            'direccion' => 'Plaza San Francisco 789',
            'ciudad' => 'Cusco',
            'telefono' => '987654',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Crear clientes
        $cliente1 = Cliente::create([
            'tipoCliente' => 'PersonaNatural',
            'numeroDocumento' => '12345678',
            'nombre' => 'Juan Carlos Pérez García',
            'telefono' => '987654321'
        ]);

        $cliente2 = Cliente::create([
            'tipoCliente' => 'PersonaNatural',
            'numeroDocumento' => '87654321',
            'nombre' => 'María Elena Rodríguez López',
            'telefono' => '987654322'
        ]);

        $cliente3 = Cliente::create([
            'tipoCliente' => 'Empresa',
            'numeroDocumento' => '20123456789',
            'nombre' => 'Comercial Los Andes S.A.C.',
            'telefono' => '987654323'
        ]);

        $cliente4 = Cliente::create([
            'tipoCliente' => 'PersonaNatural',
            'numeroDocumento' => '11223344',
            'nombre' => 'Carlos Alberto Mendoza Silva',
            'telefono' => '987654324'
        ]);

        // Crear encomiendas
        $encomienda1 = Encomienda::create([
            'codigo' => 'ENC-251008-001',
            'descripcion' => 'Paquete pequeño con documentos',
            'costo' => 25.50,
            'estadoPago' => 'Pagado',
            'observaciones' => 'Fragil - Manejar con cuidado',
            'idClienteRemitente' => $cliente1->idCliente,
            'idClienteDestinatario' => $cliente2->idCliente,
            'idSucursalOrigen' => $sucursalLimaId,
            'idSucursalDestino' => $sucursalArequipaId,
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()->subDays(3)
        ]);

        $encomienda2 = Encomienda::create([
            'codigo' => 'ENC-251008-002',
            'descripcion' => 'Caja mediana con productos electrónicos',
            'costo' => 45.00,
            'estadoPago' => 'Parcial',
            'observaciones' => 'Valor declarado: S/ 500',
            'idClienteRemitente' => $cliente3->idCliente,
            'idClienteDestinatario' => $cliente4->idCliente,
            'idSucursalOrigen' => $sucursalArequipaId,
            'idSucursalDestino' => $sucursalCuscoId,
            'created_at' => Carbon::now()->subDays(2),
            'updated_at' => Carbon::now()->subDays(2)
        ]);

        $encomienda3 = Encomienda::create([
            'codigo' => 'ENC-251008-003',
            'descripcion' => 'Sobre con documentos legales',
            'costo' => 15.00,
            'estadoPago' => 'Pendiente',
            'observaciones' => 'Urgente - Entregar en 24 horas',
            'idClienteRemitente' => $cliente2->idCliente,
            'idClienteDestinatario' => $cliente1->idCliente,
            'idSucursalOrigen' => $sucursalCuscoId,
            'idSucursalDestino' => $sucursalLimaId,
            'created_at' => Carbon::now()->subDay(),
            'updated_at' => Carbon::now()->subDay()
        ]);

        $encomienda4 = Encomienda::create([
            'codigo' => 'ENC-251008-004',
            'descripcion' => 'Paquete grande con ropa',
            'costo' => 35.75,
            'estadoPago' => 'Pagado',
            'observaciones' => 'Peso: 8 kg',
            'idClienteRemitente' => $cliente4->idCliente,
            'idClienteDestinatario' => $cliente3->idCliente,
            'idSucursalOrigen' => $sucursalLimaId,
            'idSucursalDestino' => $sucursalArequipaId,
            'created_at' => Carbon::now()->subHours(6),
            'updated_at' => Carbon::now()->subHours(6)
        ]);

        // Crear estados para las encomiendas
        // Encomienda 1 - En tránsito
        EstadoEncomienda::create([
            'descripcionEstado' => 'Registrado',
            'fechaCambio' => $encomienda1->created_at,
            'idEncomienda' => $encomienda1->idEncomienda
        ]);
        EstadoEncomienda::create([
            'descripcionEstado' => 'En tránsito',
            'fechaCambio' => Carbon::now()->subDays(2),
            'idEncomienda' => $encomienda1->idEncomienda
        ]);

        // Encomienda 2 - Entregada
        EstadoEncomienda::create([
            'descripcionEstado' => 'Registrado',
            'fechaCambio' => $encomienda2->created_at,
            'idEncomienda' => $encomienda2->idEncomienda
        ]);
        EstadoEncomienda::create([
            'descripcionEstado' => 'En tránsito',
            'fechaCambio' => Carbon::now()->subDays(1),
            'idEncomienda' => $encomienda2->idEncomienda
        ]);
        EstadoEncomienda::create([
            'descripcionEstado' => 'Entregada',
            'fechaCambio' => Carbon::now()->subHours(2),
            'idEncomienda' => $encomienda2->idEncomienda
        ]);

        // Encomienda 3 - En origen
        EstadoEncomienda::create([
            'descripcionEstado' => 'Registrado',
            'fechaCambio' => $encomienda3->created_at,
            'idEncomienda' => $encomienda3->idEncomienda
        ]);
        EstadoEncomienda::create([
            'descripcionEstado' => 'En origen',
            'fechaCambio' => Carbon::now()->subHours(12),
            'idEncomienda' => $encomienda3->idEncomienda
        ]);

        // Encomienda 4 - En tránsito
        EstadoEncomienda::create([
            'descripcionEstado' => 'Registrado',
            'fechaCambio' => $encomienda4->created_at,
            'idEncomienda' => $encomienda4->idEncomienda
        ]);
        EstadoEncomienda::create([
            'descripcionEstado' => 'En tránsito',
            'fechaCambio' => Carbon::now()->subHours(3),
            'idEncomienda' => $encomienda4->idEncomienda
        ]);

        $this->command->info('✅ Datos de prueba creados exitosamente:');
        $this->command->info('   - 3 Sucursales');
        $this->command->info('   - 4 Clientes');
        $this->command->info('   - 4 Encomiendas');
        $this->command->info('   - Estados de encomiendas');
        $this->command->info('');
        $this->command->info('📦 Códigos de prueba disponibles:');
        $this->command->info('   - ENC-251008-001 (En tránsito)');
        $this->command->info('   - ENC-251008-002 (Entregada)');
        $this->command->info('   - ENC-251008-003 (En origen)');
        $this->command->info('   - ENC-251008-004 (En tránsito)');
    }
}