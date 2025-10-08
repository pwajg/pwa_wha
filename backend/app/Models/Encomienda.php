<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encomienda extends Model
{
    use HasFactory;

    protected $table = 'encomiendas';
    protected $primaryKey = 'idEncomienda';
    public $timestamps = true;

    protected $fillable = [
        'codigo',
        'descripcion',
        'costo',
        'observaciones',
        'estadoPago',
        'idClienteRemitente',
        'idClienteDestinatario',
        'idSucursalOrigen',
        'idSucursalDestino'
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function clienteRemitente()
    {
        return $this->belongsTo(Cliente::class, 'idClienteRemitente', 'idCliente');
    }

    public function clienteDestinatario()
    {
        return $this->belongsTo(Cliente::class, 'idClienteDestinatario', 'idCliente');
    }

    public function sucursalOrigen()
    {
        return $this->belongsTo(Sucursal::class, 'idSucursalOrigen', 'idSucursal');
    }

    public function sucursalDestino()
    {
        return $this->belongsTo(Sucursal::class, 'idSucursalDestino', 'idSucursal');
    }

    public function estados()
    {
        return $this->hasMany(EstadoEncomienda::class, 'idEncomienda', 'idEncomienda');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'idEncomienda', 'idEncomienda');
    }

    // Scope para buscar por código
    public function scopeByCodigo($query, $codigo)
    {
        return $query->where('codigo', $codigo);
    }
}