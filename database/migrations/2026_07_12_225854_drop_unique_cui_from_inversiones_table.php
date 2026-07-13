<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inversiones', function (Blueprint $table) {
            // Quitamos el UNIQUE físico sobre 'cui': con soft-delete (deleted_at),
            // un CUI eliminado seguía "ocupando" el valor a nivel de BD y bloqueaba
            // reutilizarlo. Ahora la unicidad se valida en la aplicación, solo
            // contra los IOARR activos (deleted_at IS NULL).
            $table->dropUnique('cui');
        });
    }

    public function down(): void
    {
        Schema::table('inversiones', function (Blueprint $table) {
            $table->unique('cui');
        });
    }
};