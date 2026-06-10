<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CronogramaController extends Controller
{
    // 1. Mostrar la tabla general del cronograma
    public function index()
    {
        $cronogramas = DB::table('cronogramas')
            ->join('equipos', 'cronogramas.id_equipo', '=', 'equipos.id')
            ->join('inversiones', 'equipos.id_inversion', '=', 'inversiones.id')
            ->select('cronogramas.*', 'equipos.nombre_equipo', 'inversiones.cui', 'equipos.expediente')
            ->orderBy('cronogramas.fecha_inicio', 'asc') // Ordenar por los más próximos
            ->get();

        return view('cronograma', compact('cronogramas'));
    }

    // 2. Guardar cronograma desde el modal de Equipos
    public function store(Request $request)
    {
        $id_equipo = $request->id_equipo;

        DB::table('cronogramas')->where('id_equipo', $id_equipo)->delete();

        if ($request->convocatoria_inicio || $request->convocatoria_fin) {
            DB::table('cronogramas')->insert([
                'id_equipo' => $id_equipo,
                'etapa' => 'Convocatoria',
                'fecha_inicio' => $request->convocatoria_inicio,
                'fecha_fin' => $request->convocatoria_fin
            ]);
        }

        if ($request->buenapro_inicio || $request->buenapro_fin) {
            DB::table('cronogramas')->insert([
                'id_equipo' => $id_equipo,
                'etapa' => 'Otorgamiento de la Buena Pro',
                'fecha_inicio' => $request->buenapro_inicio,
                'fecha_fin' => $request->buenapro_fin
            ]);
        }

        return redirect('/equipos')->with('success', 'Cronograma SEACE actualizado correctamente.');
    }
}