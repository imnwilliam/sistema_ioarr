<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EquiposExport;

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

    /**
     * Reglas de validación compartidas entre store() y update().
     * Límite de archivo: 3MB (3072 KB) por evidencia.
     */
    private function reglasValidacion(): array
    {
        return [
            'id_inversion' => 'nullable|integer|exists:inversiones,id',
            'id_upss' => 'nullable|integer|exists:areas_upss,id',
            'expediente' => 'nullable|numeric',
            'nombre_equipo' => 'required|string|max:255',
            'tipo_equipo' => 'nullable|string|max:100',
            'servicio' => 'nullable|string|max:255',
            'ambiente' => 'nullable|string|max:255',
            'estado_situacional' => 'nullable|string',
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
            'archivos_evidencia' => 'nullable|array',
            'archivos_evidencia.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:3072', // 3MB por archivo
        ];
    }

    private function mensajesValidacion(): array
    {
        return [
            'nombre_equipo.required' => 'El nombre del equipo es obligatorio.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.min' => 'La cantidad debe ser al menos 1.',
            'precio_unitario.required' => 'El precio unitario es obligatorio.',
            'precio_unitario.min' => 'El precio unitario no puede ser negativo.',
            'id_inversion.exists' => 'La inversión seleccionada no existe.',
            'id_upss.exists' => 'El área/UPSS seleccionada no existe.',
            'archivos_evidencia.*.mimes' => 'Cada evidencia debe ser un archivo PDF, Word, Excel o imagen (jpg/png).',
            'archivos_evidencia.*.max' => 'Cada archivo de evidencia no puede superar los 3MB.',
        ];
    }

    public function store(Request $request)
    {
        $request->validate($this->reglasValidacion(), $this->mensajesValidacion());

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
        $request->validate($this->reglasValidacion(), $this->mensajesValidacion());

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
        // Generamos el nombre del archivo dinámicamente con la extensión .xlsx
        $filename = "Reporte_Equipos_IOARR_" . date('Y-m-d_H-i') . ".xlsx";
        
        // Retornamos la descarga del Excel usando nuestra nueva clase de Diseño
        return Excel::download(new EquiposExport($request), $filename);
    }
}