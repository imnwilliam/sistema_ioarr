<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('inversiones', function (Blueprint $table) {
            // Agregamos la columna después del nombre
            $table->string('tipo_ioarr', 100)->nullable()->after('nombre_inversion');
        });
    }
    public function down(): void {
        Schema::table('inversiones', function (Blueprint $table) {
            $table->dropColumn('tipo_ioarr');
        });
    }
};