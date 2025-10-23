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
        'costo',
        'descripcion',
        'observaciones',
        'estadoPago',
        'idClienteRemitente',
        'idClienteDestinatario',
        'idFlete'
    ];

    protected $casts = [
        'costo' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function ClienteRemitente()
    {
        return $this->belongsTo(Cliente::class, 'idClienteRemitente', 'idCliente');
    }
    public function ClienteDestinatario() 
    {
        return $this->belongsTo(Cliente::class, 'idClienteDestinatario', 'idCliente');
    }
    public function Flete()
    {
        return $this->belongsTo(Flete::class, 'idFlete', 'idFlete');
    }
    public function estadoEncomiendas() {
        return $this->hasMany(EstadoEncomienda::class, 'idEncomienda', 'idEncomienda');
    }
}