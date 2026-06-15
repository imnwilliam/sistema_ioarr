<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InversionController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\CronogramaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PerfilController; // <-- NUEVO
use App\Http\Middleware\AdminMiddleware;

Route::get('/', function () { return redirect('/login'); });

Route::middleware(['auth'])->group(function () {
    
    // RUTAS PÚBLICAS (Para todos los roles)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/inversiones/{id}/equipos', [DashboardController::class, 'equiposPorInversion']);
    Route::get('/cronograma', [CronogramaController::class, 'index'])->name('cronograma.index');

    // MI PERFIL (Cualquier usuario puede cambiar su propia clave)
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::put('/perfil', [PerfilController::class, 'update'])->name('perfil.update');

    // INVERSIONES
    Route::get('/inversiones', [InversionController::class, 'index'])->name('inversiones.index');
    Route::post('/inversiones', [InversionController::class, 'store'])->name('inversiones.store');
    Route::put('/inversiones/{id}', [InversionController::class, 'update'])->name('inversiones.update');
    Route::delete('/inversiones/{id}', [InversionController::class, 'destroy'])->name('inversiones.destroy');

    // EQUIPOS
    Route::get('/equipos', [EquipoController::class, 'index'])->name('equipos.index');
    Route::post('/equipos', [EquipoController::class, 'store'])->name('equipos.store');
    Route::get('/equipos/exportar', [EquipoController::class, 'exportarCSV'])->name('equipos.exportar');
    Route::put('/equipos/{id}', [EquipoController::class, 'update'])->name('equipos.update');
    Route::delete('/equipos/{id}', [EquipoController::class, 'destroy'])->name('equipos.destroy');
    
    // NUEVA RUTA PARA BORRAR UN ARCHIVO ESPECÍFICO
    Route::delete('/equipos/{id}/archivo/{index}', [EquipoController::class, 'destroyArchivo']);

    Route::post('/cronogramas', [CronogramaController::class, 'store'])->name('cronogramas.store');

    // RUTAS BLINDADAS (Solo Administradores)
    Route::middleware([AdminMiddleware::class])->group(function () {
        
        // CONFIGURACIÓN (CRUD de Catálogos)
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
        Route::post('/configuracion/area', [ConfiguracionController::class, 'storeArea'])->name('configuracion.area.store');
        Route::put('/configuracion/area/{id}', [ConfiguracionController::class, 'updateArea'])->name('configuracion.area.update');
        Route::delete('/configuracion/area/{id}', [ConfiguracionController::class, 'destroyArea'])->name('configuracion.area.destroy');
        
        Route::post('/configuracion/tipo', [ConfiguracionController::class, 'storeTipo'])->name('configuracion.tipo.store');
        Route::put('/configuracion/tipo/{id}', [ConfiguracionController::class, 'updateTipo'])->name('configuracion.tipo.update');
        Route::delete('/configuracion/tipo/{id}', [ConfiguracionController::class, 'destroyTipo'])->name('configuracion.tipo.destroy');

        // USUARIOS
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    });
});

require __DIR__.'/auth.php';