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
        Schema::create('fletes', function (Blueprint $table) {
            $table->id('idFlete');
            $table->string('codigo')->unique();
            $table->string('observaciones');

            //Claves foraneas
            $table->foreignId('idTransporte')->constrained('transportes', 'idTransporte');
            $table->foreignId('idSucursalOrigen')->constrained('sucursales', 'id');
            $table->foreignId('idSucursalDestino')->constrained('sucursales', 'id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fletes');
    }
};
