<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;

    protected $table = 'sucursales';
    protected $primaryKey = 'idSucursal';
    public $timestamps = true;

    protected $fillable = [
        'nombreSucursal',
        'direccion',
        'telefono',
        'email',
        'responsable'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function encomiendasOrigen()
    {
        return $this->hasMany(Encomienda::class, 'idSucursalOrigen', 'idSucursal');
    }

    public function encomiendasDestino()
    {
        return $this->hasMany(Encomienda::class, 'idSucursalDestino', 'idSucursal');
    }
}