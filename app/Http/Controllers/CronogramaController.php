<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CronogramaController extends Controller
{
    // Devuelve los datos en formato JSON para el Modal de la tabla Equipos
    public function show($id_equipo)
    {
        $cronogramas = DB::table('cronogramas')->where('id_equipo', $id_equipo)->get();
        return response()->json($cronogramas);
    }

    // Guarda el cronograma desde el modal de Equipos
    public function store(Request $request)
    {
        $id_equipo = $request->id_equipo;
        $etapas = $request->etapas;

        // Limpiamos cronograma anterior para insertar el nuevo
        DB::table('cronogramas')->where('id_equipo', $id_equipo)->delete();

        $inserts = [];
        
        if ($etapas) {
            foreach ($etapas as $key => $datos) {
                if (!empty($datos['inicio']) || !empty($datos['fin'])) {
                    $inserts[] = [
                        'id_equipo' => $id_equipo,
                        'etapa' => $datos['nombre'], 
                        'fecha_inicio' => !empty($datos['inicio']) ? str_replace('T', ' ', $datos['inicio']) : null,
                        'fecha_fin' => !empty($datos['fin']) ? str_replace('T', ' ', $datos['fin']) : null,
                    ];
                }
            }
        }

        if (count($inserts) > 0) {
            DB::table('cronogramas')->insert($inserts);
        }

        return redirect('/equipos')->with('success', 'Cronograma SEACE actualizado correctamente.');
    }
}