<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema IOARR Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>

    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .smooth-transition { transition: all 0.3s ease; }

        /* ---------------------------------------------------
           DISEÑO ELEGANTE (Borde suave tipo relieve)
           --------------------------------------------------- */
        .datatable-top, .dataTable-top {
            padding-bottom: 1rem;
        }
        
        /* Buscador */
        .datatable-input, .dataTable-input { 
            border-radius: 0.5rem !important; 
            border: 1.5px solid #d1d5db !important; /* Gris neutro suave */
            padding: 0.5rem 0.75rem !important; 
            outline: none !important; 
            color: #374151 !important;
            background-color: #ffffff !important;
            transition: all 0.25s ease;
        }
        .datatable-input:hover, .dataTable-input:hover {
            border-color: #9ca3af !important; /* Se oscurece ligeramente al pasar el mouse */
        }
        .datatable-input:focus, .dataTable-input:focus { 
            border-color: #3b82f6 !important; /* Azul sutil al enfocar */
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
        }

        /* Selector de cantidad (10, 25, 50...) */
        .datatable-selector, .dataTable-selector { 
            border-radius: 0.5rem !important; 
            border: 1.5px solid #d1d5db !important; /* Gris neutro suave */
            padding: 0.4rem 1.8rem 0.4rem 0.75rem !important; 
            outline: none !important;
            color: #374151 !important;
            background-color: #ffffff !important;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .datatable-selector:hover, .datatable-selector:hover {
            border-color: #9ca3af !important;
        }
        .datatable-selector:focus, .dataTable-selector:focus { 
            border-color: #3b82f6 !important; 
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
        }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    @include('includes.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto">
        
        <header class="bg-white border-b border-gray-200 px-8 py-5 sticky top-0 z-40 shadow-sm flex justify-between items-center">
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

                $pctCertificado = $totalPim > 0 ? ($totalCertificado / $totalPim) * 100 : 0;
                $pctDevengado = $totalPim > 0 ? ($totalDevengado / $totalPim) * 100 : 0;
                $pctGirado = $totalPim > 0 ? ($totalGirado / $totalPim) * 100 : 0;
                
                $avanceGeneral = $pctDevengado; 

                $getColoresAvance = function($pct) {
                    if ($pct <= 25) return ['bar' => 'bg-red-500', 'icon' => 'text-red-400'];
                    if ($pct <= 50) return ['bar' => 'bg-orange-500', 'icon' => 'text-orange-400'];
                    if ($pct <= 74) return ['bar' => 'bg-yellow-400', 'icon' => 'text-yellow-400'];
                    return ['bar' => 'bg-emerald-500', 'icon' => 'text-emerald-400'];
                };

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
                
                <div class="col-span-1 lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-4">
                        <div>
                            <h3 class="font-bold text-gray-800"><i class="fa-solid fa-chart-column text-blue-500 mr-2"></i> Valor total de las inversiones</h3>
                            <p class="text-xs text-gray-400 mt-1">Haz clic en una barra para ver el desglose de equipos.</p>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-search text-gray-400 text-xs"></i>
                                </div>
                                <input type="text" id="buscador-grafico" placeholder="Buscar CUI..." class="pl-8 pr-3 py-1.5 border border-gray-300 rounded-lg text-sm outline-none focus:border-blue-500 transition-colors w-40 md:w-48">
                            </div>
                            <div class="flex gap-1 bg-gray-50 p-1 rounded-lg border border-gray-200">
                                <button onclick="prevChartPage()" id="btn-prev-chart" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 text-gray-600 rounded shadow-sm hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-all" title="Anteriores"><i class="fa-solid fa-chevron-left text-xs"></i></button>
                                <button onclick="nextChartPage()" id="btn-next-chart" class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 text-gray-600 rounded shadow-sm hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-all" title="Siguientes"><i class="fa-solid fa-chevron-right text-xs"></i></button>
                            </div>
                        </div>
                    </div>
                    <div id="chart-inversiones" class="mt-auto"></div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 id="titulo-chart-areas" class="font-bold text-gray-800 mb-4"><i class="fa-solid fa-chart-donut text-purple-500 mr-2"></i> Distribución Global por Áreas</h3>
                    <div id="chart-areas" class="mt-4"></div>
                </div>
            </div>

            <div id="panel-detalle" class="hidden bg-slate-900 rounded-2xl shadow-xl overflow-hidden smooth-transition mt-6">
                <div class="p-4 bg-blue-600 flex justify-between items-center text-white">
                    <h3 class="font-bold"><i class="fa-solid fa-microscope mr-2"></i> Detalle de Equipos: <span id="lbl-cui" class="text-blue-200"></span></h3>
                    <button onclick="cerrarDetalle()" class="text-white hover:text-gray-200 transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="bg-white rounded-xl p-4 overflow-y-auto max-h-[350px]">
                            <h4 class="text-sm font-bold text-gray-600 text-center mb-2">Equipos</h4>
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
                
                <div class="p-4">
                    <table id="tabla-financiera" class="min-w-full text-sm text-left text-gray-600">
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
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <script>
        // --- 1. Inicializar la tabla financiera con Datatables ---
        document.addEventListener("DOMContentLoaded", function() {
            new simpleDatatables.DataTable("#tabla-financiera", {
                searchable: true,
                perPage: 10,
                perPageSelect: [10, 15, 25, 50, 100],
                labels: { 
                    placeholder: "Buscar CUI...", 
                    perPage: "filas por pág.", 
                    noRows: "No hay registros financieros.", 
                    info: "{start} a {end} de {rows}" 
                }
            });
        });

        const allInvDatos = @json($inversionesMontos);
        const areasDatos = @json($equiposPorArea);
        let chartTipos = null; 
        
        // --- 2. Lógica del Gráfico de Barras con Paginación ---
        let filteredInvDatos = [...allInvDatos];
        let currentChartPage = 0;
        const chartPageSize = 5; // Mostrar 5 barras máximo
        let currentChartItems = [];
        let chartInversionesObj = null;

        function renderChart() {
            const start = currentChartPage * chartPageSize;
            currentChartItems = filteredInvDatos.slice(start, start + chartPageSize);

            const invLabels = currentChartItems.map(i => i.cui);
            const invMontos = currentChartItems.map(i => i.monto_total);

            if(!chartInversionesObj) {
                const optionsInv = {
                    chart: { 
                        type: 'bar', height: 350, fontFamily: 'Inter, sans-serif', toolbar: { show: false },
                        events: {
                            dataPointSelection: function(event, chartContext, config) {
                                const seleccionados = config.selectedDataPoints[0];
                                if (seleccionados.length === 0) {
                                    cerrarDetalle();
                                } else {
                                    // Utilizamos currentChartItems para obtener el CUI correcto según la página actual
                                    const index = config.dataPointIndex;
                                    cargarDetalleEquipos(currentChartItems[index].id, currentChartItems[index].cui);
                                }
                            }
                        }
                    },
                    series: [{ name: 'Monto Equipamiento (S/)', data: invMontos }],
                    xaxis: { categories: invLabels, title: { text: 'Códigos CUI (IOARR)', style: { fontWeight: 600, color: '#64748b' } } },
                    colors: ['#3b82f6'],
                    plotOptions: { bar: { borderRadius: 4, horizontal: false, columnWidth: '40%', dataLabels: { position: 'top' } } },
                    dataLabels: { 
                        enabled: true, 
                        formatter: function (val) { return "S/ " + parseFloat(val).toLocaleString('en-US', {minimumFractionDigits: 2}); },
                        offsetY: -20, 
                        style: { fontSize: '12px', colors: ["#304758"] }
                    },
                    tooltip: { 
                        theme: 'light',
                        y: { formatter: function (val) { return "S/ " + parseFloat(val).toLocaleString('en-US', {minimumFractionDigits: 2}); } } 
                    }
                };
                chartInversionesObj = new ApexCharts(document.querySelector("#chart-inversiones"), optionsInv);
                chartInversionesObj.render();
            } else {
                chartInversionesObj.updateOptions({ xaxis: { categories: invLabels } });
                chartInversionesObj.updateSeries([{ data: invMontos }]);
            }
            updateChartControls();
        }

        // Control de los botones < y >
        function updateChartControls() {
            document.getElementById('btn-prev-chart').disabled = currentChartPage === 0;
            document.getElementById('btn-next-chart').disabled = (currentChartPage + 1) * chartPageSize >= filteredInvDatos.length;
        }
        function prevChartPage() { if(currentChartPage > 0) { currentChartPage--; renderChart(); } }
        function nextChartPage() { if((currentChartPage + 1) * chartPageSize < filteredInvDatos.length) { currentChartPage++; renderChart(); } }

        // Buscador interactivo del gráfico
        document.getElementById('buscador-grafico').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            filteredInvDatos = allInvDatos.filter(i => i.cui.toLowerCase().includes(term));
            currentChartPage = 0;
            renderChart();
        });

        // Llamada inicial para pintar el gráfico
        renderChart();


        // --- 3. Gráfico 2: Áreas (Pie Chart) ---
        let chartAreasObj = null; 
        const optionsAreas = {
            chart: { type: 'donut', height: 350, fontFamily: 'Inter, sans-serif' },
            series: areasDatos.map(a => a.cantidad),
            labels: areasDatos.map(a => a.nombre_upss),
            colors: ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1'],
            plotOptions: { pie: { donut: { size: '65%' } } },
            dataLabels: { 
                enabled: true,
                formatter: function (val) { return val.toFixed(1) + "%"; },
                dropShadow: { enabled: true, top: 1, left: 1, blur: 1, opacity: 0.5 }
            },
            legend: { position: 'bottom', markers: { radius: 12 } }
        };
        chartAreasObj = new ApexCharts(document.querySelector("#chart-areas"), optionsAreas);
        chartAreasObj.render();


        // --- 4. LÓGICA DEL DRILL-DOWN (AJAX) ---
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

                    const conteoEquipos = {};
                    equipos.forEach(eq => {
                        conteoEquipos[eq.nombre_equipo] = (conteoEquipos[eq.nombre_equipo] || 0) + eq.cantidad;
                    });

                    if(chartTipos) { chartTipos.destroy(); } 

                    const alturaDinamica = Math.max(260, Object.keys(conteoEquipos).length * 45);

                    const optionsTipos = {
                        chart: { type: 'bar', height: alturaDinamica, fontFamily: 'Inter, sans-serif', toolbar: { show: false } },
                        series: [{ name: 'Cantidad', data: Object.values(conteoEquipos) }],
                        xaxis: { categories: Object.keys(conteoEquipos) },
                        colors: ['#0ea5e9', '#8b5cf6', '#f59e0b', '#10b981', '#ef4444', '#14b8a6', '#f43f5e', '#84cc16'],
                        plotOptions: { bar: { borderRadius: 4, horizontal: true, distributed: true, barHeight: '60%' } },
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