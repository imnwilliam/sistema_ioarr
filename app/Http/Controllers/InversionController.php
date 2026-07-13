<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class InversionController extends Controller
{
    public function index()
    {
        $inversiones = DB::table('inversiones')
            ->leftJoin('financiera', 'inversiones.id', '=', 'financiera.id_inversion')
            ->select('inversiones.*', 'financiera.pim', 'financiera.certificado', 'financiera.devengado', 'financiera.girado')
            ->whereNull('inversiones.deleted_at')
            ->orderBy('inversiones.id', 'desc')
            ->get();

        return view('inversiones', compact('inversiones'));
    }

    public function store(Request $request)
    {
        // Validación estricta: el CUI solo debe ser único entre los IOARR
        // ACTIVOS (no eliminados). Un CUI de un proyecto eliminado queda libre.
        $request->validate([
            'cui' => [
                'required',
                'numeric',
                Rule::unique('inversiones', 'cui')->whereNull('deleted_at'),
            ],
            'nombre_inversion' => 'required',
            'tipo_ioarr' => 'required'
        ], [
            'cui.unique' => 'Ya existe un IOARR activo registrado con este CUI.',
        ]);

        try {
            // 1. Guardamos la Inversión incluyendo el nuevo Tipo de IOARR
            $id_inversion = DB::table('inversiones')->insertGetId([
                'cui' => $request->cui,
                'nombre_inversion' => $request->nombre_inversion,
                'tipo_ioarr' => $request->tipo_ioarr,
                'estado_pmi' => 'Activo',
                'fase' => $request->fase ?? 'Formulación'
            ]);

            // 2. Le creamos su "billetera" en la tabla financiera
            DB::table('financiera')->insert([
                'id_inversion' => $id_inversion,
                'pim' => $request->pim ?? 0,
                'certificado' => 0,
                'devengado' => 0,
                'girado' => 0
            ]);
        } catch (QueryException $e) {
            // Red de seguridad: si por alguna razón la BD aún tuviera la
            // restricción física (p. ej. no se corrió la migración todavía),
            // evitamos el error 500 feo y mostramos un mensaje claro.
            return redirect()->back()->withInput()->with('error', 'No se pudo guardar: el CUI ingresado ya está en uso por otro IOARR activo.');
        }

        return redirect('/inversiones')->with('success', 'Proyecto IOARR registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        // Misma validación, pero ignorando el propio registro que se está editando.
        $request->validate([
            'cui' => [
                'required',
                'numeric',
                Rule::unique('inversiones', 'cui')->whereNull('deleted_at')->ignore($id),
            ],
            'nombre_inversion' => 'required',
            'tipo_ioarr' => 'required'
        ], [
            'cui.unique' => 'Ya existe otro IOARR activo registrado con este CUI.',
        ]);

        try {
            // 1. Actualizamos datos básicos y el Tipo de IOARR
            DB::table('inversiones')->where('id', $id)->update([
                'cui' => $request->cui,
                'nombre_inversion' => $request->nombre_inversion,
                'tipo_ioarr' => $request->tipo_ioarr,
                'estado_pmi' => $request->estado_pmi,
                'fase' => $request->fase
            ]);

            // 2. Actualizamos la billetera
            DB::table('financiera')->updateOrInsert(
                ['id_inversion' => $id],
                [
                    'pim' => $request->pim ?? 0,
                    'certificado' => $request->certificado ?? 0,
                    'devengado' => $request->devengado ?? 0,
                    'girado' => $request->girado ?? 0
                ]
            );
        } catch (QueryException $e) {
            return redirect()->back()->withInput()->with('error', 'No se pudo actualizar: el CUI ingresado ya está en uso por otro IOARR activo.');
        }

        return redirect('/inversiones')->with('success', 'Datos actualizados correctamente.');
    }

    public function destroy($id)
    {
        DB::table('inversiones')->where('id', $id)->update(['deleted_at' => now()]);
        return redirect('/inversiones')->with('success', 'Proyecto IOARR eliminado de la vista.');
    }
}