<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $areas = DB::table('areas_upss')->orderBy('nombre_upss', 'asc')->get();
        $tipos = DB::table('tipos_equipo')->orderBy('nombre_tipo', 'asc')->get();
        return view('configuracion', compact('areas', 'tipos'));
    }

    public function storeArea(Request $request)
    {
        DB::table('areas_upss')->insert(['nombre_upss' => $request->nombre_upss]);
        return redirect('/configuracion')->with('success', 'Área UPSS registrada con éxito.');
    }

    public function updateArea(Request $request, $id)
    {
        DB::table('areas_upss')->where('id', $id)->update(['nombre_upss' => $request->nombre_upss]);
        return redirect('/configuracion')->with('success', 'Área UPSS actualizada.');
    }

    public function destroyArea($id)
    {
        try {
            DB::table('areas_upss')->where('id', $id)->delete();
            return redirect('/configuracion')->with('success', 'Área UPSS eliminada del catálogo.');
        } catch (QueryException $e) {
            return redirect('/configuracion')->with('error', 'No se puede eliminar esta área porque actualmente está asignada a equipos activos.');
        }
    }

    public function storeTipo(Request $request)
    {
        DB::table('tipos_equipo')->insert(['nombre_tipo' => $request->nombre_tipo]);
        return redirect('/configuracion')->with('success', 'Tipo de equipo registrado.');
    }

    public function updateTipo(Request $request, $id)
    {
        DB::table('tipos_equipo')->where('id', $id)->update(['nombre_tipo' => $request->nombre_tipo]);
        return redirect('/configuracion')->with('success', 'Tipo de equipo actualizado.');
    }

    public function destroyTipo($id)
    {
        try {
            DB::table('tipos_equipo')->where('id', $id)->delete();
            return redirect('/configuracion')->with('success', 'Tipo de equipo eliminado del catálogo.');
        } catch (QueryException $e) {
            return redirect('/configuracion')->with('error', 'No se puede eliminar este tipo porque existen equipos registrados bajo esta categoría.');
        }
    }
}