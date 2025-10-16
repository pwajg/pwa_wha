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
        Schema::create('estado_fletes', function (Blueprint $table) {
            $table->id('idEstadoFlete');
            $table->string('descripcionEstado');
            $table->dateTime('fechaCambio');
            $table->timestamps();

            //Claves foraneas
            $table->foreignId('idFlete')->constrained('fletes', 'idFlete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estado_fletes');
    }
};
