<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema IOARR Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .smooth-transition { transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    @include('includes.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto">
        
        <header class="bg-white border-b border-gray-200 px-8 py-5 sticky top-0 z-10 shadow-sm flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center mr-3 shadow-md shadow-blue-500/30 text-sm">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    Seguimiento de Inversiones
                </h2>
            </div>
            
            @php
                $ultimaMod = \Illuminate\Support\Facades\Cache::get('ultima_modificacion');
            @endphp

            <div class="hidden md:flex bg-blue-50/80 border border-blue-100 rounded-xl px-5 py-2.5 items-center shadow-sm">
                <div class="bg-blue-600 text-white rounded-lg w-10 h-10 flex items-center justify-center mr-3 shadow-md">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-0.5">Última Modificación</span>
                    @if($ultimaMod)
                        <span class="text-sm font-black text-slate-800 leading-none">
                            {{ \Carbon\Carbon::parse($ultimaMod['fecha'])->format('d/m/Y - h:i A') }}
                        </span>
                        <span class="text-[10px] text-slate-500 font-medium mt-1 truncate max-w-[150px]" title="{{ $ultimaMod['usuario'] }}">
                            Por: <span class="font-bold text-blue-700">{{ $ultimaMod['usuario'] }}</span>
                        </span>
                    @else
                        <span class="text-sm font-bold text-slate-400">Sin actividad reciente</span>
                    @endif
                </div>
            </div>
        </header>

        <div class="p-8 space-y-8">
            
            @php
                $totalPim = $financiera->total_pim ?? 0;
                $totalCertificado = $financiera->total_certificado ?? 0;
                $totalDevengado = $financiera->total_devengado ?? 0;
                $totalGirado = $financiera->total_girado ?? 0;

                // Evitar división por cero
                $pctCertificado = $totalPim > 0 ? ($totalCertificado / $totalPim) * 100 : 0;
                $pctDevengado = $totalPim > 0 ? ($totalDevengado / $totalPim) * 100 : 0;
                $pctGirado = $totalPim > 0 ? ($totalGirado / $totalPim) * 100 : 0;
                
                // Avance general de ejecución
                $avanceGeneral = $pctDevengado; 

                // Función para determinar colores del semáforo según porcentaje
                $getColoresAvance = function($pct) {
                    if ($pct <= 25) return ['bar' => 'bg-red-500', 'icon' => 'text-red-400'];
                    if ($pct <= 50) return ['bar' => 'bg-orange-500', 'icon' => 'text-orange-400'];
                    if ($pct <= 74) return ['bar' => 'bg-yellow-400', 'icon' => 'text-yellow-400'];
                    return ['bar' => 'bg-emerald-500', 'icon' => 'text-emerald-400'];
                };

                // Asignar colores para la tarjeta general
                $colorGen = $getColoresAvance($avanceGeneral);
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-5 gap-4 2xl:gap-6">
                
                <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-4 2xl:p-5 shadow-xl border border-slate-700 flex items-center gap-3 2xl:gap-4 transform transition-all duration-300 hover:-translate-y-1">
                    <div class="w-12 h-12 rounded-xl bg-white/10 {{ $colorGen['icon'] }} flex items-center justify-center text-xl shrink-0 shadow-inner transition-colors duration-500">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div class="w-full min-w-0">
                        <p class="text-[10px] 2xl:text-[11px] font-extrabold text-slate-400 uppercase tracking-widest mb-1 truncate">Avance General</p>
                        <h3 class="text-2xl 2xl:text-3xl font-black text-white tracking-tighter whitespace-nowrap">{{ number_format($avanceGeneral, 1) }}%</h3>
                        <div class="w-full bg-slate-700 rounded-full h-1.5 mt-2">
                            <div class="{{ $colorGen['bar'] }} h-1.5 rounded-full transition-all duration-1000" style="width: {{ $avanceGeneral }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 2xl:p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-gray-100 flex items-center gap-3 2xl:gap-4 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-500/10 cursor-default">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600 flex items-center justify-center text-xl shrink-0 shadow-inner">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] 2xl:text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mb-1 truncate">PIM Asignado</p>
                        <h3 class="text-lg 2xl:text-xl font-black text-slate-800 tracking-tighter whitespace-nowrap">S/ {{ number_format($totalPim, 2) }}</h3>
                        <span class="inline-flex items-center gap-1 text-[9px] 2xl:text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md mt-1 whitespace-nowrap">100% Base</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 2xl:p-5 shadow-[0_2px_10px_-3px_rgba(245,158,11,0.08)] border border-gray-100 flex items-center gap-3 2xl:gap-4 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-500/10 cursor-default">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-50 to-amber-100 text-amber-500 flex items-center justify-center text-xl shrink-0 shadow-inner">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] 2xl:text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mb-1 truncate">Certificado</p>
                        <h3 class="text-lg 2xl:text-xl font-black text-slate-800 tracking-tighter whitespace-nowrap">S/ {{ number_format($totalCertificado, 2) }}</h3>
                        <span class="inline-flex items-center gap-1 text-[9px] 2xl:text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md mt-1 whitespace-nowrap">{{ number_format($pctCertificado, 1) }}% del PIM</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 2xl:p-5 shadow-[0_2px_10px_-3px_rgba(16,185,129,0.08)] border border-gray-100 flex items-center gap-3 2xl:gap-4 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/10 cursor-default">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 text-emerald-600 flex items-center justify-center text-xl shrink-0 shadow-inner">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] 2xl:text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mb-1 truncate">Devengado</p>
                        <h3 class="text-lg 2xl:text-xl font-black text-slate-800 tracking-tighter whitespace-nowrap">S/ {{ number_format($totalDevengado, 2) }}</h3>
                        <span class="inline-flex items-center gap-1 text-[9px] 2xl:text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md mt-1 whitespace-nowrap">{{ number_format($pctDevengado, 1) }}% del PIM</span>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-4 2xl:p-5 shadow-[0_2px_10px_-3px_rgba(168,85,247,0.08)] border border-gray-100 flex items-center gap-3 2xl:gap-4 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-purple-500/10 cursor-default">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 text-purple-600 flex items-center justify-center text-xl shrink-0 shadow-inner">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] 2xl:text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mb-1 truncate">Girado</p>
                        <h3 class="text-lg 2xl:text-xl font-black text-slate-800 tracking-tighter whitespace-nowrap">S/ {{ number_format($totalGirado, 2) }}</h3>
                        <span class="inline-flex items-center gap-1 text-[9px] 2xl:text-[10px] font-bold text-purple-600 bg-purple-50 px-2 py-0.5 rounded-md mt-1 whitespace-nowrap">{{ number_format($pctGirado, 1) }}% del PIM</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="col-span-1 lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-2"><i class="fa-solid fa-chart-column text-blue-500 mr-2"></i> Total de Inversiones activas</h3>
                    <p class="text-xs text-gray-400 mb-4">Haz clic en una barra para ver el desglose de equipos.</p>
                    <div id="chart-inversiones"></div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 id="titulo-chart-areas" class="font-bold text-gray-800 mb-4"><i class="fa-solid fa-chart-donut text-purple-500 mr-2"></i> Distribución Global por Áreas</h3>
                    <div id="chart-areas"></div>
                </div>
            </div>

            <div id="panel-detalle" class="hidden bg-slate-900 rounded-2xl shadow-xl overflow-hidden smooth-transition">
                <div class="p-4 bg-blue-600 flex justify-between items-center text-white">
                    <h3 class="font-bold"><i class="fa-solid fa-microscope mr-2"></i> Detalle de Equipos: <span id="lbl-cui" class="text-blue-200"></span></h3>
                    <button onclick="cerrarDetalle()" class="text-white hover:text-gray-200 transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="bg-white rounded-xl p-4 overflow-y-auto max-h-[350px]">
                            <h4 class="text-sm font-bold text-gray-600 text-center mb-2">Tipos de Equipo</h4>
                            <div id="chart-detalle-tipos"></div>
                        </div>
                        <div class="col-span-1 lg:col-span-2 bg-white rounded-xl overflow-hidden">
                            <div class="max-h-[350px] overflow-y-auto">
                                <table class="min-w-full text-sm text-left text-gray-600">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0 shadow-sm z-10">
                                        <tr>
                                            <th class="px-4 py-3">Equipo</th>
                                            <th class="px-4 py-3">Área</th>
                                            <th class="px-4 py-3 text-center">Cant.</th>
                                            <th class="px-4 py-3 text-right">Precio Unit.</th>
                                            <th class="px-4 py-3 text-right text-blue-600 font-bold">Precio Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-detalle-body" class="divide-y divide-gray-100">
                                        </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800"><i class="fa-solid fa-file-invoice-dollar text-emerald-500 mr-2"></i> Cuadro de Ejecución Financiera por Inversión</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-6 py-4">Inversión (CUI)</th>
                                <th class="px-6 py-4 text-right">PIM Aprobado</th>
                                <th class="px-6 py-4 text-right">Monto Certificado</th>
                                <th class="px-6 py-4 text-right">Monto Devengado</th>
                                <th class="px-6 py-4 text-center min-w-[150px]">Avance (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($ejecucionTabla as $ejec)
                                @php
                                    $avance = $ejec->pim > 0 ? ($ejec->devengado / $ejec->pim) * 100 : 0;
                                    $colorTab = $getColoresAvance($avance);
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-blue-700">{{ $ejec->cui }}</td>
                                    <td class="px-6 py-4 text-right font-medium text-slate-700">S/ {{ number_format($ejec->pim, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-medium text-amber-600">S/ {{ number_format($ejec->certificado, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-medium text-emerald-600">S/ {{ number_format($ejec->devengado, 2) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="{{ $colorTab['bar'] }} h-2 rounded-full transition-all duration-1000" style="width: {{ $avance }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold text-slate-600 w-10 text-right">{{ number_format($avance, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            @if(count($ejecucionTabla) == 0)
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400 font-medium">No hay registros financieros.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        // Datos enviados desde el Controlador
        const invDatos = @json($inversionesMontos);
        const areasDatos = @json($equiposPorArea);
        
        let chartTipos = null; // Variable global para el sub-gráfico

        // --- GRÁFICO 1: Barras de Inversiones ---
        const invLabels = invDatos.map(i => i.cui);
        const invMontos = invDatos.map(i => i.monto_total);
        const invIds = invDatos.map(i => i.id);

        const optionsInv = {
            chart: { 
                type: 'bar', height: 350, fontFamily: 'Inter, sans-serif',
                toolbar: { show: false },
                events: {
                    // EVENTO CLICK (Drill-down y Deselección)
                    dataPointSelection: function(event, chartContext, config) {
                        const seleccionados = config.selectedDataPoints[0];
                        if (seleccionados.length === 0) {
                            cerrarDetalle();
                        } else {
                            const index = config.dataPointIndex;
                            const idInversion = invIds[index];
                            const cuiSeleccionado = invLabels[index];
                            cargarDetalleEquipos(idInversion, cuiSeleccionado);
                        }
                    }
                }
            },
            series: [{ name: 'Monto Equipamiento (S/)', data: invMontos }],
            xaxis: { categories: invLabels, title: { text: 'Códigos CUI (IOARR)', style: { fontWeight: 600, color: '#64748b' } } },
            colors: ['#3b82f6'],
            plotOptions: { bar: { borderRadius: 4, horizontal: false, columnWidth: '40%' } },
            dataLabels: { enabled: false },
            tooltip: { 
                theme: 'light',
                y: { formatter: function (val) { return "S/ " + parseFloat(val).toLocaleString('en-US', {minimumFractionDigits: 2}); } } 
            }
        };
        new ApexCharts(document.querySelector("#chart-inversiones"), optionsInv).render();

        // --- GRÁFICO 2: Áreas (Pie Chart) ---
        let chartAreasObj = null; 
        const optionsAreas = {
            chart: { type: 'donut', height: 350, fontFamily: 'Inter, sans-serif' },
            series: areasDatos.map(a => a.cantidad),
            labels: areasDatos.map(a => a.nombre_upss),
            colors: ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1'],
            plotOptions: { pie: { donut: { size: '65%' } } },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', markers: { radius: 12 } }
        };
        chartAreasObj = new ApexCharts(document.querySelector("#chart-areas"), optionsAreas);
        chartAreasObj.render();

        // --- LÓGICA DEL DRILL-DOWN (AJAX) ---
        function cargarDetalleEquipos(idInversion, cui) {
            document.getElementById('lbl-cui').innerText = cui;
            document.getElementById('panel-detalle').classList.remove('hidden');
            
            fetch(`/api/inversiones/${idInversion}/equipos`)
                .then(response => response.json())
                .then(equipos => {
                    const tbody = document.getElementById('tabla-detalle-body');
                    tbody.innerHTML = '';
                    
                    if(equipos.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 font-medium">No hay equipos registrados en esta inversión.</td></tr>';
                    } else {
                        equipos.forEach(eq => {
                            const precioUnitario = parseFloat(eq.precio_unitario);
                            const precioTotal = eq.cantidad * precioUnitario;
                            
                            tbody.innerHTML += `
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-slate-800">${eq.nombre_equipo}</td>
                                    <td class="px-4 py-3"><span class="bg-slate-200 text-slate-700 text-xs px-2 py-1 rounded font-medium">${eq.nombre_upss || 'Sin área'}</span></td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-700">${eq.cantidad}</td>
                                    <td class="px-4 py-3 text-right font-medium text-slate-700">S/ ${precioUnitario.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                    <td class="px-4 py-3 text-right font-bold text-blue-600">S/ ${precioTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                </tr>
                            `;
                        });
                    }

                    const conteoTipos = {};
                    equipos.forEach(eq => {
                        conteoTipos[eq.tipo_equipo] = (conteoTipos[eq.tipo_equipo] || 0) + eq.cantidad;
                    });

                    if(chartTipos) { chartTipos.destroy(); } 

                    const alturaDinamica = Math.max(260, Object.keys(conteoTipos).length * 35);

                    const optionsTipos = {
                        chart: { type: 'bar', height: alturaDinamica, fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
                        series: [{ name: 'Cantidad', data: Object.values(conteoTipos) }],
                        xaxis: { categories: Object.keys(conteoTipos) },
                        colors: ['#0ea5e9', '#8b5cf6', '#f59e0b', '#10b981', '#ef4444', '#14b8a6', '#f43f5e', '#84cc16'],
                        plotOptions: { bar: { borderRadius: 4, horizontal: true, distributed: true, barHeight: '70%' } },
                        dataLabels: { enabled: true, textAnchor: 'start', style: { fontSize: '12px', colors: ['#fff'] }, offsetX: 10 },
                        legend: { show: false }, 
                        tooltip: { theme: 'light' }
                    };
                    
                    chartTipos = new ApexCharts(document.querySelector("#chart-detalle-tipos"), optionsTipos);
                    chartTipos.render();

                    const conteoAreasCUI = {};
                    equipos.forEach(eq => {
                        const area = eq.nombre_upss || 'Sin área';
                        conteoAreasCUI[area] = (conteoAreasCUI[area] || 0) + eq.cantidad;
                    });

                    if(Object.keys(conteoAreasCUI).length > 0) {
                        chartAreasObj.updateOptions({ labels: Object.keys(conteoAreasCUI) });
                        chartAreasObj.updateSeries(Object.values(conteoAreasCUI));
                        document.getElementById('titulo-chart-areas').innerHTML = `<i class="fa-solid fa-filter text-purple-500 mr-2"></i> Áreas del CUI: ${cui}`;
                    } else {
                        chartAreasObj.updateOptions({ labels: ['Sin Equipos'] });
                        chartAreasObj.updateSeries([1]); 
                    }
                });
        }

        function cerrarDetalle() {
            document.getElementById('panel-detalle').classList.add('hidden');
            chartAreasObj.updateOptions({ labels: areasDatos.map(a => a.nombre_upss) });
            chartAreasObj.updateSeries(areasDatos.map(a => a.cantidad));
            document.getElementById('titulo-chart-areas').innerHTML = `<i class="fa-solid fa-chart-donut text-purple-500 mr-2"></i> Distribución Global por Áreas`;
        }
    </script>
</body>
</html>