<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadUsuario extends Model
{
    use HasFactory;
    protected $table = 'actividad_usuarios';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = [
        'descripcionActividad',
        'fecha',
        'idUsuario'
    ];
    protected $casts = [
        'fecha' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function usuario(){
        return $this->belongsTo(Usuario::class,'idUsuario','id');
    }
}
