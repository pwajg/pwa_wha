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
        'numeroDocumento',
        'nombre',
        'telefono'
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

    // Scope para buscar por número de documento
    public function scopeByDocumento($query, $numeroDocumento)
    {
        return $query->where('numeroDocumento', $numeroDocumento);
    }
}