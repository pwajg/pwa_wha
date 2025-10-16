<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoFlete extends Model
{
    use HasFactory;
    protected $table = 'estado_fletes';
    protected $primaryKey = 'idEstadoFlete';
    public $timestamps = true;

    protected $fillable = [
        'descripcionEstado',
        'fechaCambio',
        'idFlete'
    ];

    // Relaciones
    public function Flete(){
        return $this->belongsTo(Flete::class, 'idFlete', 'idFlete');
    }
}
