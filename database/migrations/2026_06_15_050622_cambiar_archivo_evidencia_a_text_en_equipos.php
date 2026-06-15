<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Usamos DB::statement directo para evitar problemas con librerías faltantes
        DB::statement('ALTER TABLE equipos MODIFY archivo_evidencia TEXT');
    }
    public function down(): void {
        DB::statement('ALTER TABLE equipos MODIFY archivo_evidencia VARCHAR(255)');
    }
};