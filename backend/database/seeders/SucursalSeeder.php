<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sucursales = [
            [
                'nombre' => 'Sucursal Lima Centro',
                'direccion' => 'Av. Javier Prado Este 1234',
                'ciudad' => 'Lima',
                'telefono' => '987654321'
            ],
            [
                'nombre' => 'Sucursal Lima Norte',
                'direccion' => 'Av. Túpac Amaru 5678',
                'ciudad' => 'Lima',
                'telefono' => '987654322'
            ],
            [
                'nombre' => 'Sucursal Arequipa',
                'direccion' => 'Av. Dolores 901',
                'ciudad' => 'Arequipa',
                'telefono' => '987654323'
            ],
            [
                'nombre' => 'Sucursal Cusco',
                'direccion' => 'Av. El Sol 234',
                'ciudad' => 'Cusco',
                'telefono' => '987654324'
            ],
            [
                'nombre' => 'Sucursal Piura',
                'direccion' => 'Av. Grau 345',
                'ciudad' => 'Piura',
                'telefono' => '987654325'
            ]
        ];

        foreach ($sucursales as $sucursal) {
            \App\Models\Sucursal::create($sucursal);
        }
    }
}
