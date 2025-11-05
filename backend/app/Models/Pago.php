<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pagos';
    protected $primaryKey = 'idPago';
    public $timestamps = true;

    protected $fillable = [
        'codigo',
        'monto',
        'fechaPago',
        'modalidadPago',
        'idEncomienda'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fechaPago' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function encomienda()
    {
        return $this->belongsTo(Encomienda::class, 'idEncomienda', 'idEncomienda');
    }
}