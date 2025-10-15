<?php

namespace Database\Seeders;

use App\Models\Usuario;
use App\Models\Sucursal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear una sucursal principal si no existe
        $sucursalPrincipal = Sucursal::firstOrCreate(
            ['nombre' => 'Sucursal Principal'],
            [
                'direccion' => 'Dirección Principal',
                'ciudad' => 'Ciudad Principal',
                'telefono' => '123456789'
            ]
        );

        // Crear usuario administrador
        Usuario::firstOrCreate(
            ['email' => 'admin@empresa.com'],
            [
                'nombre' => 'Administrador',
                'password' => Hash::make('admin123'),
                'rol' => 'Administrador',
                'idSucursal' => $sucursalPrincipal->id
            ]
        );

        // Crear usuario colaborador de prueba
        Usuario::firstOrCreate(
            ['email' => 'colaborador@empresa.com'],
            [
                'nombre' => 'Colaborador',
                'password' => Hash::make('colaborador123'),
                'rol' => 'Colaborador',
                'idSucursal' => $sucursalPrincipal->id
            ]
        );

        $this->command->info('Usuarios de prueba creados:');
        $this->command->info('Administrador: admin@empresa.com / admin123');
        $this->command->info('Colaborador: colaborador@empresa.com / colaborador123');
    }
}
