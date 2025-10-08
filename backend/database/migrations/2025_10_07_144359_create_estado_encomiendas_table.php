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
        Schema::create('estado_encomiendas', function (Blueprint $table) {
            $table->id('idEstadoEncomienda');
            $table->string('descripcionEstado');
            $table->dateTime('fechaCambio');
            $table->timestamps();

            //Claves foraneas
            $table->foreignId('idEncomienda')->constrained('encomiendas', 'idEncomienda');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estado_encomiendas');
    }
};
