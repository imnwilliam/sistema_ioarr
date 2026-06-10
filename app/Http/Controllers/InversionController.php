<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        // 1. Guardamos la Inversión y obtenemos su ID
        $id_inversion = DB::table('inversiones')->insertGetId([
            'cui' => $request->cui,
            'nombre_inversion' => $request->nombre_inversion,
            'estado_pmi' => 'Activo'
        ]);

        // 2. Le creamos su "billetera" en la tabla financiera
        DB::table('financiera')->insert([
            'id_inversion' => $id_inversion,
            'pim' => $request->pim ?? 0,
            'certificado' => 0,
            'devengado' => 0,
            'girado' => 0
        ]);

        return redirect('/inversiones')->with('success', 'Proyecto IOARR registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        // 1. Actualizamos datos básicos
        DB::table('inversiones')->where('id', $id)->update([
            'cui' => $request->cui,
            'nombre_inversion' => $request->nombre_inversion,
            'estado_pmi' => $request->estado_pmi
        ]);

        // 2. Actualizamos la billetera (Usamos updateOrInsert por si el registro no existía)
        DB::table('financiera')->updateOrInsert(
            ['id_inversion' => $id],
            [
                'pim' => $request->pim ?? 0,
                'certificado' => $request->certificado ?? 0,
                'devengado' => $request->devengado ?? 0,
                'girado' => $request->girado ?? 0
            ]
        );

        return redirect('/inversiones')->with('success', 'Datos financieros actualizados correctamente.');
    }

    public function destroy($id)
    {
        DB::table('inversiones')->where('id', $id)->update(['deleted_at' => now()]);
        return redirect('/inversiones')->with('success', 'Proyecto IOARR eliminado de la vista.');
    }
}