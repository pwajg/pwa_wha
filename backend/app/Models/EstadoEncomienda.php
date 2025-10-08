<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoEncomienda extends Model
{
    use HasFactory;

    protected $table = 'estado_encomiendas';
    protected $primaryKey = 'idEstadoEncomienda';
    public $timestamps = true;

    protected $fillable = [
        'descripcionEstado',
        'fechaCambio',
        'idEncomienda'
    ];

    protected $casts = [
        'fechaCambio' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function encomienda()
    {
        return $this->belongsTo(Encomienda::class, 'idEncomienda', 'idEncomienda');
    }
}