<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'direccion',
        'ciudad',
        'telefono'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'idSucursal', 'id');
    }

    public function encomiendasOrigen()
    {
        return $this->hasMany(Encomienda::class, 'idSucursalOrigen', 'id');
    }

    public function encomiendasDestino()
    {
        return $this->hasMany(Encomienda::class, 'idSucursalDestino', 'id');
    }
}