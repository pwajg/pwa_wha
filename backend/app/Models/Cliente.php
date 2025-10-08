<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';
    protected $primaryKey = 'idCliente';
    public $timestamps = true;

    protected $fillable = [
        'tipoCliente',
        'dni',
        'nombre',
        'apellido',
        'telefono',
        'direccion',
        'email'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function encomiendasRemitente()
    {
        return $this->hasMany(Encomienda::class, 'idClienteRemitente', 'idCliente');
    }

    public function encomiendasDestinatario()
    {
        return $this->hasMany(Encomienda::class, 'idClienteDestinatario', 'idCliente');
    }

    // Scope para buscar por DNI
    public function scopeByDni($query, $dni)
    {
        return $query->where('dni', $dni);
    }
}