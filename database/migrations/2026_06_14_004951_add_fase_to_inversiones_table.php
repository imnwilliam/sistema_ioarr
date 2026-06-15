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
        Schema::table('inversiones', function (Blueprint $table) {
            // Agregamos la columna 'fase' después de 'estado_pmi'
            $table->string('fase')->default('Formulación')->after('estado_pmi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inversiones', function (Blueprint $table) {
            // Eliminamos la columna si revertimos la migración
            $table->dropColumn('fase');
        });
    }
};