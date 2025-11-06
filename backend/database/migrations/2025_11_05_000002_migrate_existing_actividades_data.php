<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\ActividadUsuario;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrar datos existentes: detectar tipo_accion y modulo para registros antiguos
        $actividades = ActividadUsuario::whereNull('tipo_accion')
            ->orWhereNull('modulo')
            ->get();
        
        foreach ($actividades as $actividad) {
            $descripcion = strtolower($actividad->descripcionActividad);
            
            // Detectar tipo de acción
            $tipoAccion = null;
            if (strpos($descripcion, 'creado') !== false || strpos($descripcion, 'creada') !== false || 
                strpos($descripcion, 'registrado') !== false || strpos($descripcion, 'registrada') !== false) {
                $tipoAccion = 'creacion';
            } elseif (strpos($descripcion, 'actualizado') !== false || strpos($descripcion, 'actualizada') !== false ||
                strpos($descripcion, 'modificado') !== false || strpos($descripcion, 'modificada') !== false ||
                strpos($descripcion, 'editado') !== false || strpos($descripcion, 'editada') !== false) {
                $tipoAccion = 'actualizacion';
            } elseif (strpos($descripcion, 'eliminado') !== false || strpos($descripcion, 'eliminada') !== false ||
                strpos($descripcion, 'borrado') !== false || strpos($descripcion, 'borrada') !== false ||
                strpos($descripcion, 'anulado') !== false || strpos($descripcion, 'anulada') !== false) {
                $tipoAccion = 'eliminacion';
            } else {
                $tipoAccion = 'otra';
            }
            
            // Detectar módulo
            $modulo = null;
            if (strpos($descripcion, 'usuario') !== false) {
                $modulo = 'Usuarios';
            } elseif (strpos($descripcion, 'encomienda') !== false) {
                $modulo = 'Encomiendas';
            } elseif (strpos($descripcion, 'flete') !== false) {
                $modulo = 'Fletes';
            } elseif (strpos($descripcion, 'sucursal') !== false) {
                $modulo = 'Sucursales';
            } elseif (strpos($descripcion, 'cliente') !== false) {
                $modulo = 'Clientes';
            } elseif (strpos($descripcion, 'pago') !== false) {
                $modulo = 'Pagos';
            } else {
                $modulo = 'Sistema';
            }
            
            $actividad->update([
                'tipo_accion' => $tipoAccion,
                'modulo' => $modulo
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hay necesidad de revertir la migración de datos
    }
};

