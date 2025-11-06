<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('actividad_usuarios', function (Blueprint $table) {
            $table->string('tipo_accion', 50)->nullable()->after('descripcionActividad');
            $table->string('modulo', 50)->nullable()->after('tipo_accion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('actividad_usuarios', function (Blueprint $table) {
            $table->dropColumn(['tipo_accion', 'modulo']);
        });
    }
};

