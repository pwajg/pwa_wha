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
        Schema::create('encomiendas', function (Blueprint $table) {
            $table->id('idEncomienda');
            $table->string('codigo')->unique();
            $table->string('descripcion');
            $table->float('costo', 2);
            $table->enum('estadoPago', ['Pendiente', 'Parcial', 'Pagado']);
            $table->text('observaciones')->nullable();
            
            //Claves foraneas
            $table->foreignId('idClienteRemitente')->constrained('clientes', 'idCliente');
            $table->foreignId('idClienteDestinatario')->constrained('clientes', 'idCliente');
            $table->foreignId('idFlete')->constrained('fletes', 'idFlete');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encomiendas');
    }
};
