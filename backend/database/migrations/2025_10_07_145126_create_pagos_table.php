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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('idPago');
            $table->float('monto', 2);
            $table->string('estadoPago');
            $table->dateTime('fechaPago');
            $table->string('modalidadPago');
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
        Schema::dropIfExists('pagos');
    }
};
