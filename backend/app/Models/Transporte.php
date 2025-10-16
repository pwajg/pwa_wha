<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporte extends Model
{
    use HasFactory;
    protected $table = 'transportes';
    protected $primaryKey = 'idTransporte';
    public $timestamps = true;

    protected $fillable = [
        'placa',
        'caracteristicas',
        'idFlete'
    ];

}
