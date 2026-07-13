<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Filtro visible de Estado PMI: todos | activos | cerrados
        // Por defecto "todos", para no ocultar información de forma silenciosa
        // (así el usuario decide explícitamente qué quiere ver).
        $filtroEstado = $request->query('estado', 'todos');
        if (!in_array($filtroEstado, ['todos', 'activos', 'cerrados'])) {
            $filtroEstado = 'todos';
        }

        $estadoActivo = 'Activo';
        $estadoCerrado = 'Cerrado / Liquidado';

        // 1. Gráfico 1: Inversiones (CUI) vs Monto Total de sus Equipos
        $inversionesMontos = DB::table('inversiones')
            ->leftJoin('equipos', 'inversiones.id', '=', 'equipos.id_inversion')
            ->select('inversiones.id', 'inversiones.cui', 'inversiones.nombre_inversion', DB::raw('COALESCE(SUM(equipos.precio_total), 0) as monto_total'))
            ->whereNull('inversiones.deleted_at')
            ->whereNull('equipos.deleted_at')
            ->when($filtroEstado === 'activos', function ($q) use ($estadoActivo) {
                $q->where('inversiones.estado_pmi', $estadoActivo);
            })
            ->when($filtroEstado === 'cerrados', function ($q) use ($estadoCerrado) {
                $q->where('inversiones.estado_pmi', $estadoCerrado);
            })
            ->groupBy('inversiones.id', 'inversiones.cui', 'inversiones.nombre_inversion')
            ->get();

        // 2. Gráfico 2: Cantidad de Equipos divididos por Área (UPSS)
        // Se une con "inversiones" para poder respetar el mismo filtro de Estado PMI
        // y también excluir equipos de inversiones eliminadas (soft delete).
        $equiposPorArea = DB::table('areas_upss')
            ->leftJoin('equipos', 'areas_upss.id', '=', 'equipos.id_upss')
            ->leftJoin('inversiones', 'equipos.id_inversion', '=', 'inversiones.id')
            ->select('areas_upss.nombre_upss', DB::raw('COUNT(equipos.id) as cantidad'))
            ->whereNull('equipos.deleted_at')
            ->whereNull('inversiones.deleted_at')
            ->when($filtroEstado === 'activos', function ($q) use ($estadoActivo) {
                $q->where('inversiones.estado_pmi', $estadoActivo);
            })
            ->when($filtroEstado === 'cerrados', function ($q) use ($estadoCerrado) {
                $q->where('inversiones.estado_pmi', $estadoCerrado);
            })
            ->groupBy('areas_upss.id', 'areas_upss.nombre_upss')
            ->having('cantidad', '>', 0) // Solo áreas que tengan equipos
            ->get();

        // --- FIX: Ejecución Financiera Consolidada ---
        // Se une con "inversiones" y se excluyen las que tienen deleted_at,
        // y ahora también se puede filtrar por Estado PMI (Activo / Cerrado).
        $financiera = DB::table('financiera')
            ->join('inversiones', 'financiera.id_inversion', '=', 'inversiones.id')
            ->whereNull('inversiones.deleted_at')
            ->when($filtroEstado === 'activos', function ($q) use ($estadoActivo) {
                $q->where('inversiones.estado_pmi', $estadoActivo);
            })
            ->when($filtroEstado === 'cerrados', function ($q) use ($estadoCerrado) {
                $q->where('inversiones.estado_pmi', $estadoCerrado);
            })
            ->select(
                DB::raw('COALESCE(SUM(financiera.pim), 0) as total_pim'),
                DB::raw('COALESCE(SUM(financiera.certificado), 0) as total_certificado'),
                DB::raw('COALESCE(SUM(financiera.devengado), 0) as total_devengado'),
                DB::raw('COALESCE(SUM(financiera.girado), 0) as total_girado')
            )->first();

        // 4. Tabla de Ejecución Financiera Desglosada
        $ejecucionTabla = DB::table('inversiones')
            ->leftJoin('financiera', 'inversiones.id', '=', 'financiera.id_inversion')
            ->select('inversiones.cui', 'inversiones.estado_pmi', 'financiera.pim', 'financiera.certificado', 'financiera.devengado')
            ->whereNull('inversiones.deleted_at')
            ->when($filtroEstado === 'activos', function ($q) use ($estadoActivo) {
                $q->where('inversiones.estado_pmi', $estadoActivo);
            })
            ->when($filtroEstado === 'cerrados', function ($q) use ($estadoCerrado) {
                $q->where('inversiones.estado_pmi', $estadoCerrado);
            })
            ->get();

        return view('dashboard', compact('inversionesMontos', 'equiposPorArea', 'financiera', 'ejecucionTabla', 'filtroEstado'));
    }

    // NUEVA FUNCIÓN: Devuelve los equipos de una inversión específica al hacer clic en el Gráfico 1
    public function equiposPorInversion($id)
    {
        $equipos = DB::table('equipos')
            ->leftJoin('areas_upss', 'equipos.id_upss', '=', 'areas_upss.id')
            ->select('equipos.nombre_equipo', 'equipos.tipo_equipo', 'equipos.cantidad', 'equipos.precio_unitario', 'equipos.precio_total', 'areas_upss.nombre_upss')
            ->where('equipos.id_inversion', $id)
            ->whereNull('equipos.deleted_at')
            ->get();

        return response()->json($equipos);
    }
}