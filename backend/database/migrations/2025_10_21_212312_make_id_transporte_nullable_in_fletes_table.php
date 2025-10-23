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
        Schema::table('fletes', function (Blueprint $table) {
            // Hacer la columna idTransporte nullable
            $table->foreignId('idTransporte')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fletes', function (Blueprint $table) {
            // Revertir: hacer la columna idTransporte NOT NULL nuevamente
            $table->foreignId('idTransporte')->nullable(false)->change();
        });
    }
};
