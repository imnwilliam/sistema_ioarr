<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EquipoController extends Controller
{
    public function index(Request $request)
    {
        // 1. Iniciamos la consulta base
        $query = DB::table('equipos')
            ->leftJoin('inversiones', 'equipos.id_inversion', '=', 'inversiones.id')
            ->leftJoin('areas_upss', 'equipos.id_upss', '=', 'areas_upss.id')
            ->select('equipos.*', 'inversiones.cui', 'areas_upss.nombre_upss')
            ->whereNull('equipos.deleted_at');

        // 2. ¡NUEVO! Aplicamos filtros si el usuario los seleccionó
        if ($request->filled('filtro_inversion')) {
            $query->where('equipos.id_inversion', $request->filtro_inversion);
        }
        if ($request->filled('filtro_upss')) {
            $query->where('equipos.id_upss', $request->filtro_upss);
        }

        $equipos = $query->orderBy('equipos.id', 'desc')->get();

        // 3. Cargamos los catálogos para los selectores
        $inversiones = DB::table('inversiones')->where('estado_pmi', 'Activo')->get();
        $areas = DB::table('areas_upss')->get();
        $tipos = DB::table('tipos_equipo')->get();

        return view('equipos', compact('equipos', 'inversiones', 'areas', 'tipos'));
    }

    public function store(Request $request)
    {
        $equipo = new Equipo();
        $equipo->id_inversion = $request->id_inversion;
        $equipo->id_upss = $request->id_upss;
        $equipo->expediente = $request->expediente;
        $equipo->nombre_equipo = $request->nombre_equipo;
        $equipo->tipo_equipo = $request->tipo_equipo;
        $equipo->ambiente = $request->ambiente;
        $equipo->cantidad = $request->cantidad;
        $equipo->precio_unitario = $request->precio_unitario;
        $equipo->precio_total = $request->cantidad * $request->precio_unitario;
        
        // ¡NUEVO! Guardar archivo PDF si existe
        if ($request->hasFile('archivo_evidencia')) {
            $ruta = $request->file('archivo_evidencia')->store('evidencias', 'local');
            $equipo->archivo_evidencia = $ruta;
        }

        $equipo->save();

        return redirect('/equipos')->with('success', 'Equipo y evidencia registrados correctamente.');
    }

    public function update(Request $request, $id)
    {
        $datos = [
            'id_inversion' => $request->id_inversion,
            'id_upss' => $request->id_upss,
            'expediente' => $request->expediente,
            'nombre_equipo' => $request->nombre_equipo,
            'tipo_equipo' => $request->tipo_equipo,
            'ambiente' => $request->ambiente,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $request->precio_unitario,
            'precio_total' => $request->cantidad * $request->precio_unitario,
        ];

        // ¡NUEVO! Actualizar archivo PDF si se sube uno nuevo
        if ($request->hasFile('archivo_evidencia')) {
            $datos['archivo_evidencia'] = $request->file('archivo_evidencia')->store('evidencias', 'local');
        }

        DB::table('equipos')->where('id', $id)->update($datos);

        return redirect('/equipos')->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroy($id)
    {
        DB::table('equipos')->where('id', $id)->update(['deleted_at' => now()]);
        return redirect('/equipos')->with('success', 'Equipo eliminado del sistema.');
    }

    // ¡NUEVO! Función para descargar el documento de forma segura
    public function descargarPDF($id)
    {
        $equipo = DB::table('equipos')->where('id', $id)->first();
        
        if ($equipo && $equipo->archivo_evidencia && Storage::disk('local')->exists($equipo->archivo_evidencia)) {
            return Storage::disk('local')->download($equipo->archivo_evidencia);
        }

        return back()->with('error', 'El archivo no se encuentra disponible.');
    }

    public function exportarCSV()
    {
        $equipos = DB::table('equipos')
            ->leftJoin('inversiones', 'equipos.id_inversion', '=', 'inversiones.id')
            ->leftJoin('areas_upss', 'equipos.id_upss', '=', 'areas_upss.id')
            ->select('equipos.nombre_equipo', 'equipos.tipo_equipo', 'inversiones.cui', 'areas_upss.nombre_upss', 'equipos.cantidad', 'equipos.precio_unitario', 'equipos.precio_total')
            ->whereNull('equipos.deleted_at')
            ->get();

        $filename = "Reporte_Equipos_IOARR_" . date('Y-m-d') . ".csv";
        $headers = [ "Content-type" => "text/csv; charset=UTF-8", "Content-Disposition" => "attachment; filename=$filename", "Pragma" => "no-cache", "Cache-Control" => "must-revalidate, post-check=0, pre-check=0", "Expires" => "0" ];
        $columns = ['Nombre del Equipo', 'Tipo', 'CUI (Inversion)', 'Area UPSS', 'Cantidad', 'Precio Unitario', 'Precio Total'];

        $callback = function() use($equipos, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); 
            fputcsv($file, $columns, ';'); 
            foreach ($equipos as $eq) { fputcsv($file, [$eq->nombre_equipo, $eq->tipo_equipo, $eq->cui, $eq->nombre_upss ?? 'Sin área', $eq->cantidad, $eq->precio_unitario, $eq->precio_total], ';'); }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}