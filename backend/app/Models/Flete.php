<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flete extends Model
{
    use HasFactory;
    protected $table = 'fletes';
    protected $primaryKey = 'idFlete';
    public $timestamps = true;

    protected $fillable = [
        'codigo',
        'observaciones',
        'idTransporte',
        'idSucursalOrigen',
        'idSucursalDestino'
    ];
    public function encomiendas() {
        return $this->hasMany(\App\Models\Encomienda::class,'idFlete','idFlete');
    }
    public function Transporte(){
        return $this->belongsTo(Transporte::class, 'idTransporte', 'idTransporte');
    }
    public function SucursalOrigen(){
        return $this->belongsTo(Sucursal::class,'idSucursalOrigen', 'id');
    }
    public function SucursalDestino(){
        return $this->belongsTo(Sucursal::class,'idSucursalDestino','id');
    }        
    public function estadoFletes(){
        return $this->hasMany(EstadoFlete::class, 'idFlete', 'idFlete');
    }
    
    public function Encomiendas(){
        return $this->hasMany(Encomienda::class, 'idFlete', 'idFlete');
    }
}
