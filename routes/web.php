<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Http\Events\RequestHandled;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InversionController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\CronogramaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\RolController;
use App\Http\Middleware\AdminMiddleware;

Event::listen(RequestHandled::class, function (RequestHandled $event) {
    $request = $event->request;
    $response = $event->response;
    
    if (in_array($request->method(), ['POST', 'PUT', 'DELETE']) && !$request->is('logout') && auth()->check()) {
        if ($response->status() < 400) {
            Cache::forever('ultima_modificacion', [
                'usuario' => auth()->user()->name,
                'fecha' => now('America/Lima')->format('Y-m-d H:i:s')
            ]);
        }
    }
});

Route::get('/', function () { return redirect('/login'); });

// Volvemos a la estructura normal y limpia del Middleware 'auth'
Route::middleware(['auth'])->group(function () {
    
    // RUTAS PÚBLICAS (Para todos los roles)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/inversiones/{id}/equipos', [DashboardController::class, 'equiposPorInversion']);

    // MI PERFIL
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
    Route::get('/equipos/descargar/{id}', [EquipoController::class, 'descargarPDF'])->name('equipos.descargar');
    Route::get('/equipos/{id}/evidencia/{index}', [EquipoController::class, 'verEvidencia'])->name('equipos.evidencia');
    Route::put('/equipos/{id}', [EquipoController::class, 'update'])->name('equipos.update');
    Route::delete('/equipos/{id}', [EquipoController::class, 'destroy'])->name('equipos.destroy');
    Route::delete('/equipos/{id}/archivo/{index}', [EquipoController::class, 'destroyArchivo']);

    // CRONOGRAMAS SEACE
    Route::post('/cronogramas', [CronogramaController::class, 'store'])->name('cronogramas.store');
    Route::get('/cronogramas/equipo/{id_equipo}', [CronogramaController::class, 'show']);

    // RUTAS BLINDADAS (Solo Administradores)
    Route::middleware([AdminMiddleware::class])->group(function () {
        
        // CONFIGURACIÓN
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

        // PERFILES Y ROLES
        Route::get('/roles', [RolController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RolController::class, 'store'])->name('roles.store');
        Route::put('/roles/{id}', [RolController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{id}', [RolController::class, 'destroy'])->name('roles.destroy');
    });
});

require __DIR__.'/auth.php';