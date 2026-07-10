<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EquiposExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        // Misma consulta con filtros que usabas en el controlador
        $query = DB::table('equipos')
            ->leftJoin('inversiones', 'equipos.id_inversion', '=', 'inversiones.id')
            ->leftJoin('areas_upss', 'equipos.id_upss', '=', 'areas_upss.id')
            ->select('equipos.*', 'inversiones.cui', 'areas_upss.nombre_upss')
            ->whereNull('equipos.deleted_at');

        if ($this->request->filled('filtro_inversion')) $query->where('equipos.id_inversion', $this->request->filtro_inversion);
        if ($this->request->filled('filtro_upss')) $query->where('equipos.id_upss', $this->request->filtro_upss);
        if ($this->request->filled('filtro_tipo')) $query->where('equipos.tipo_equipo', $this->request->filtro_tipo);
        if ($this->request->filled('filtro_expediente')) $query->where('equipos.expediente', 'LIKE', '%' . $this->request->filtro_expediente . '%');

        return $query->get();
    }

    public function headings(): array
    {
        // Estructura de cabeceras (Filas 1, 2, 3 y 4 del Excel)
        return [
            ['REPORTE GENERAL DE EQUIPOS TÉCNICO-MÉDICOS Y MOBILIARIO - IOARR'], // Fila 1
            ['Fecha de generación: ' . date('d/m/Y h:i A')], // Fila 2
            [], // Fila 3 (Espacio en blanco)
            [   // Fila 4 (Columnas)
                'N° Expediente',
                'CUI (Inversión)',
                'Nombre del Equipo',
                'Tipo de Equipo',
                'Área / UPSS',
                'Servicio',
                'Ambiente',
                'Estado Situacional',
                'Cantidad',
                'Precio Unitario',
                'Costo Total'
            ]
        ];
    }

    public function map($equipo): array
    {
        // Mapeo de los datos fila por fila
        return [
            $equipo->expediente ?? 'N/A',
            $equipo->cui,
            $equipo->nombre_equipo,
            $equipo->tipo_equipo,
            $equipo->nombre_upss ?? 'Sin área',
            $equipo->servicio ?? '-',
            $equipo->ambiente ?? '-',
            $equipo->estado_situacional ?? '-',
            $equipo->cantidad,
            $equipo->precio_unitario,
            $equipo->precio_total,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Combinar celdas para el título principal
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');

        return [
            // Estilo Fila 1: Título principal (Fondo Púrpura, Letra Blanca, Centrado)
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF7C3AED']], 
                'alignment' => ['horizontal' => 'center', 'vertical' => 'center']
            ],
            // Estilo Fila 2: Fecha de generación (Gris oscuro, alineado a la derecha)
            2 => [
                'font' => ['italic' => true, 'color' => ['argb' => 'FF4B5563'], 'size' => 10],
                'alignment' => ['horizontal' => 'right']
            ],
            // Estilo Fila 4: Cabecera de tabla (Fondo Gris Oscuro, Letra Blanca)
            4 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF374151']], 
                'alignment' => ['horizontal' => 'center']
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            // Darle formato de Moneda (S/) Automático a las columnas J y K
            'J' => '"S/" #,##0.00',
            'K' => '"S/" #,##0.00',
            // Centrar la cantidad
            'I' => '0',
        ];
    }
}