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
        $query = DB::table('equipos')
            ->leftJoin('inversiones', 'equipos.id_inversion', '=', 'inversiones.id')
            ->leftJoin('areas_upss', 'equipos.id_upss', '=', 'areas_upss.id')
            ->select('equipos.*', 'inversiones.cui', 'areas_upss.nombre_upss')
            ->whereNull('equipos.deleted_at');

        // --- SISTEMA DE FILTROS ---
        if ($request->filled('filtro_inversion')) {
            $query->where('equipos.id_inversion', $request->filtro_inversion);
        }
        if ($request->filled('filtro_upss')) {
            $query->where('equipos.id_upss', $request->filtro_upss);
        }
        if ($request->filled('filtro_tipo')) {
            $query->where('equipos.tipo_equipo', $request->filtro_tipo);
        }
        // NUEVO FILTRO: Número de Expediente
        if ($request->filled('filtro_expediente')) {
            $query->where('equipos.expediente', 'LIKE', '%' . $request->filtro_expediente . '%');
        }

        $sumaTotal = $query->sum('equipos.precio_total');

        $equipos = $query->orderBy('equipos.id', 'desc')->get();
        
        $inversiones = DB::table('inversiones')->where('estado_pmi', 'Activo')->get();
        $areas = DB::table('areas_upss')->get();
        $tipos = DB::table('tipos_equipo')->get();

        return view('equipos', compact('equipos', 'inversiones', 'areas', 'tipos', 'sumaTotal'));
    }

    public function store(Request $request)
    {
        $equipo = new Equipo();
        $equipo->id_inversion = $request->id_inversion;
        $equipo->id_upss = $request->id_upss;
        $equipo->expediente = $request->expediente;
        $equipo->nombre_equipo = $request->nombre_equipo;
        $equipo->tipo_equipo = $request->tipo_equipo;
        $equipo->servicio = $request->servicio; 
        $equipo->ambiente = $request->ambiente;
        $equipo->estado_situacional = $request->estado_situacional;
        $equipo->cantidad = $request->cantidad;
        $equipo->precio_unitario = $request->precio_unitario;
        $equipo->precio_total = $request->cantidad * $request->precio_unitario;
        
        $archivos = [];
        if ($request->hasFile('archivos_evidencia')) {
            foreach ($request->file('archivos_evidencia') as $file) {
                $archivos[] = $file->store('evidencias', 'public');
            }
        }
        $equipo->archivo_evidencia = json_encode($archivos);
        
        $equipo->save();

        return redirect('/equipos')->with('success', 'Equipo y evidencias registrados correctamente.');
    }

    public function update(Request $request, $id)
    {
        $equipoExistente = DB::table('equipos')->where('id', $id)->first();
        
        $archivosExistentes = json_decode($equipoExistente->archivo_evidencia, true);
        if (!$archivosExistentes && !empty($equipoExistente->archivo_evidencia)) {
            $archivosExistentes = [$equipoExistente->archivo_evidencia];
        }
        $archivosExistentes = $archivosExistentes ?? [];

        if ($request->hasFile('archivos_evidencia')) {
            foreach ($request->file('archivos_evidencia') as $file) {
                $archivosExistentes[] = $file->store('evidencias', 'public');
            }
        }

        $datos = [
            'id_inversion' => $request->id_inversion,
            'id_upss' => $request->id_upss,
            'expediente' => $request->expediente,
            'nombre_equipo' => $request->nombre_equipo,
            'tipo_equipo' => $request->tipo_equipo,
            'servicio' => $request->servicio, 
            'ambiente' => $request->ambiente,
            'estado_situacional' => $request->estado_situacional,
            'cantidad' => $request->cantidad,
            'precio_unitario' => $request->precio_unitario,
            'precio_total' => $request->cantidad * $request->precio_unitario,
            'archivo_evidencia' => json_encode($archivosExistentes)
        ];

        DB::table('equipos')->where('id', $id)->update($datos);

        return redirect('/equipos')->with('success', 'Equipo actualizado correctamente.');
    }

    public function destroyArchivo($id, $index)
    {
        $equipo = DB::table('equipos')->where('id', $id)->first();
        $archivos = json_decode($equipo->archivo_evidencia, true) ?? [];

        if(isset($archivos[$index])) {
            Storage::disk('public')->delete($archivos[$index]); 
            unset($archivos[$index]); 
            $archivos = array_values($archivos); 
            
            DB::table('equipos')->where('id', $id)->update(['archivo_evidencia' => json_encode($archivos)]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        DB::table('equipos')->where('id', $id)->update(['deleted_at' => now()]);
        return redirect('/equipos')->with('success', 'Equipo eliminado del sistema.');
    }

    public function descargarPDF($id)
    {
        $equipo = DB::table('equipos')->where('id', $id)->first();
        
        if ($equipo && $equipo->archivo_evidencia && Storage::disk('local')->exists($equipo->archivo_evidencia)) {
            return Storage::disk('local')->download($equipo->archivo_evidencia);
        }

        return back()->with('error', 'El archivo no se encuentra disponible.');
    }

    public function exportarCSV(Request $request)
    {
        $query = DB::table('equipos')
            ->leftJoin('inversiones', 'equipos.id_inversion', '=', 'inversiones.id')
            ->leftJoin('areas_upss', 'equipos.id_upss', '=', 'areas_upss.id')
            ->select('equipos.nombre_equipo', 'equipos.tipo_equipo', 'equipos.expediente', 'inversiones.cui', 'areas_upss.nombre_upss', 'equipos.servicio', 'equipos.ambiente', 'equipos.estado_situacional', 'equipos.cantidad', 'equipos.precio_unitario', 'equipos.precio_total')
            ->whereNull('equipos.deleted_at');

        if ($request->filled('filtro_inversion')) $query->where('equipos.id_inversion', $request->filtro_inversion);
        if ($request->filled('filtro_upss')) $query->where('equipos.id_upss', $request->filtro_upss);
        if ($request->filled('filtro_tipo')) $query->where('equipos.tipo_equipo', $request->filtro_tipo);
        if ($request->filled('filtro_expediente')) $query->where('equipos.expediente', 'LIKE', '%' . $request->filtro_expediente . '%');

        $equipos = $query->get();

        $filename = "Reporte_Equipos_IOARR_" . date('Y-m-d') . ".csv";
        $headers = [ "Content-type" => "text/csv; charset=UTF-8", "Content-Disposition" => "attachment; filename=$filename", "Pragma" => "no-cache", "Cache-Control" => "must-revalidate, post-check=0, pre-check=0", "Expires" => "0" ];
        
        // Se añade Expediente a las columnas del Excel
        $columns = ['Nombre del Equipo', 'Tipo', 'Expediente', 'CUI (Inversion)', 'Area UPSS', 'Servicio', 'Ambiente', 'Estado Situacional', 'Cantidad', 'Precio Unitario', 'Precio Total'];

        $callback = function() use($equipos, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); 
            fputcsv($file, $columns, ';'); 
            foreach ($equipos as $eq) { fputcsv($file, [$eq->nombre_equipo, $eq->tipo_equipo, $eq->expediente ?? 'N/A', $eq->cui, $eq->nombre_upss ?? 'Sin área', $eq->servicio, $eq->ambiente, $eq->estado_situacional, $eq->cantidad, $eq->precio_unitario, $eq->precio_total], ';'); }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}