<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Authenticatable
{
    use Notifiable;
    protected $table = 'usuarios';
    protected $fillable = ['nombre', 'email', 'password', 'rol', 'idSucursal', 'theme_preference'];
    protected $hidden = ['password'];

    public function sucursal() {
        return $this->belongsTo(Sucursal::class, 'idSucursal');
    }
}
