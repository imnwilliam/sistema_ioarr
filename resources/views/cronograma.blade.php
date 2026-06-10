<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cronograma SEACE - Sistema IOARR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } .smooth-transition { transition: all 0.3s ease; } </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    @include('includes.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-10">
            <h2 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-calendar-check text-emerald-600 mr-2"></i> Control de Cronogramas SEACE</h2>
        </header>

        <div class="p-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-700">Línea de Tiempo de Adquisiciones</h3>
                </div>
                
                <table class="min-w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4">Inversión (CUI)</th>
                            <th class="px-6 py-4">Equipo / Expediente</th>
                            <th class="px-6 py-4">Etapa SEACE</th>
                            <th class="px-6 py-4 text-center">Fecha Inicio</th>
                            <th class="px-6 py-4 text-center">Fecha Fin</th>
                            <th class="px-6 py-4 text-center">Estado Alerta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cronogramas as $crono)
                            @php
                                $hoy = date('Y-m-d');
                                $estado = 'Sin Fecha';
                                $color = 'bg-gray-100 text-gray-700';
                                
                                if($crono->fecha_inicio && $crono->fecha_fin) {
                                    $fecha_fin = date('Y-m-d', strtotime($crono->fecha_fin));
                                    $fecha_inicio = date('Y-m-d', strtotime($crono->fecha_inicio));
                                    
                                    if($hoy > $fecha_fin) {
                                        $estado = 'Vencido / Finalizado';
                                        $color = 'bg-red-100 text-red-800 border border-red-200';
                                    } elseif($hoy >= $fecha_inicio && $hoy <= $fecha_fin) {
                                        $estado = 'En Curso';
                                        $color = 'bg-blue-100 text-blue-800 border border-blue-200 animate-pulse';
                                    } else {
                                        $estado = 'Pendiente (Futuro)';
                                        $color = 'bg-amber-100 text-amber-800 border border-amber-200';
                                    }
                                }
                            @endphp

                            <tr class="border-b hover:bg-gray-50 smooth-transition">
                                <td class="px-6 py-4 font-bold text-blue-700">{{ $crono->cui }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $crono->nombre_equipo }}</div>
                                    <div class="text-xs text-gray-500 mt-1">Exp: {{ $crono->expediente ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 font-medium">{{ $crono->etapa }}</td>
                                <td class="px-6 py-4 text-center">{{ $crono->fecha_inicio ? date('d/m/Y', strtotime($crono->fecha_inicio)) : '-' }}</td>
                                <td class="px-6 py-4 text-center font-bold">{{ $crono->fecha_fin ? date('d/m/Y', strtotime($crono->fecha_fin)) : '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs font-bold px-3 py-1.5 rounded-full {{ $color }}">
                                        {{ $estado }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                    <i class="fa-regular fa-folder-open text-4xl mb-3 block"></i>
                                    Aún no hay fechas programadas. Registra el cronograma desde el módulo de Equipos.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>