<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema IOARR Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Librería de Gráficos ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .smooth-transition { transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    @include('includes.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto">
        
        <!-- ENCABEZADO MEJORADO -->
        <header class="bg-white border-b border-gray-200 px-8 py-5 sticky top-0 z-10 shadow-sm flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight flex items-center">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center mr-3 shadow-md shadow-blue-500/30 text-sm">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    Panel Gerencial de Inversiones
                </h2>
                <p class="text-sm text-slate-500 mt-1.5 font-medium ml-11">Visión global de ejecución financiera y distribución de equipamiento</p>
            </div>
            <div class="hidden md:flex items-center text-sm font-bold text-slate-500 bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200">
                <i class="fa-regular fa-calendar-check text-blue-500 mr-2 text-lg"></i>
                <span class="uppercase tracking-wider text-xs">{{ date('d M Y') }}</span>
            </div>
        </header>

        <div class="p-8 space-y-8">
            
            <!-- TARJETAS SUPERIORES (KPIs REDISEÑADOS) -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                
                <!-- Tarjeta PIM -->
                <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.08)] border border-gray-100 flex items-center gap-5 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-500/10 cursor-default">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 text-blue-600 flex items-center justify-center text-2xl shrink-0 shadow-inner">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">PIM Asignado</p>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tighter">S/ {{ number_format($financiera->total_pim ?? 0, 2) }}</h3>
                    </div>
                </div>

                <!-- Tarjeta Certificado -->
                <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(245,158,11,0.08)] border border-gray-100 flex items-center gap-5 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-500/10 cursor-default">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-50 to-amber-100 text-amber-500 flex items-center justify-center text-2xl shrink-0 shadow-inner">
                        <i class="fa-solid fa-file-contract"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Total Certificado</p>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tighter">S/ {{ number_format($financiera->total_certificado ?? 0, 2) }}</h3>
                    </div>
                </div>

                <!-- Tarjeta Devengado -->
                <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(16,185,129,0.08)] border border-gray-100 flex items-center gap-5 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/10 cursor-default">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 text-emerald-600 flex items-center justify-center text-2xl shrink-0 shadow-inner">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Total Devengado</p>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tighter">S/ {{ number_format($financiera->total_devengado ?? 0, 2) }}</h3>
                    </div>
                </div>

                <!-- Tarjeta Girado -->
                <div class="bg-white rounded-2xl p-6 shadow-[0_2px_10px_-3px_rgba(168,85,247,0.08)] border border-gray-100 flex items-center gap-5 transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-purple-500/10 cursor-default">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100 text-purple-600 flex items-center justify-center text-2xl shrink-0 shadow-inner">
                        <i class="fa-solid fa-money-bill-transfer"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Total Girado</p>
                        <h3 class="text-2xl font-black text-slate-800 tracking-tighter">S/ {{ number_format($financiera->total_girado ?? 0, 2) }}</h3>
                    </div>
                </div>
            </div>

            <!-- SECCIÓN PRINCIPAL: Gráficos -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- GRÁFICO 1: Inversiones y Montos -->
                <div class="col-span-1 lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-2"><i class="fa-solid fa-chart-column text-blue-500 mr-2"></i> Inversiones vs. Monto de Equipamiento</h3>
                    <p class="text-xs text-gray-400 mb-4">Haz clic en una barra para ver el desglose de equipos.</p>
                    <div id="chart-inversiones"></div>
                </div>

                <!-- GRÁFICO 2: Equipos por Área -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4"><i class="fa-solid fa-chart-donut text-purple-500 mr-2"></i> Distribución por Áreas</h3>
                    <div id="chart-areas"></div>
                </div>
            </div>

            <!-- PANEL DE DRILL-DOWN (Se muestra al hacer clic en Gráfico 1) -->
            <div id="panel-detalle" class="hidden bg-slate-900 rounded-2xl shadow-xl overflow-hidden smooth-transition">
                <div class="p-4 bg-blue-600 flex justify-between items-center text-white">
                    <h3 class="font-bold"><i class="fa-solid fa-microscope mr-2"></i> Detalle de Equipos: <span id="lbl-cui" class="text-blue-200"></span></h3>
                    <button onclick="cerrarDetalle()" class="text-white hover:text-gray-200 transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Sub-gráfico de Tipos -->
                        <div class="bg-white rounded-xl p-4">
                            <h4 class="text-sm font-bold text-gray-600 text-center mb-2">Tipos de Equipo</h4>
                            <div id="chart-detalle-tipos"></div>
                        </div>
                        <!-- Tabla de Información Adicional -->
                        <div class="col-span-1 lg:col-span-2 bg-white rounded-xl overflow-hidden">
                            <div class="max-h-64 overflow-y-auto">
                                <table class="min-w-full text-sm text-left text-gray-600">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 sticky top-0 shadow-sm">
                                        <tr>
                                            <th class="px-4 py-3">Equipo</th>
                                            <th class="px-4 py-3">Área</th>
                                            <th class="px-4 py-3 text-center">Cant.</th>
                                            <th class="px-4 py-3 text-right">Precio Unit.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-detalle-body" class="divide-y divide-gray-100">
                                        <!-- Se llena por JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRÁFICO 3: Cuadro de Ejecución Financiera -->
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
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-blue-700">{{ $ejec->cui }}</td>
                                    <td class="px-6 py-4 text-right font-medium text-slate-700">S/ {{ number_format($ejec->pim, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-medium text-amber-600">S/ {{ number_format($ejec->certificado, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-medium text-emerald-600">S/ {{ number_format($ejec->devengado, 2) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-emerald-500 h-2 rounded-full transition-all duration-1000" style="width: {{ $avance }}%"></div>
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

    <!-- SCRIPTS DE LOS GRÁFICOS -->
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
                    // EVENTO CLICK (Drill-down)
                    dataPointSelection: function(event, chartContext, config) {
                        const index = config.dataPointIndex;
                        const idInversion = invIds[index];
                        const cuiSeleccionado = invLabels[index];
                        cargarDetalleEquipos(idInversion, cuiSeleccionado);
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
        const optionsAreas = {
            chart: { type: 'donut', height: 350, fontFamily: 'Inter, sans-serif' },
            series: areasDatos.map(a => a.cantidad),
            labels: areasDatos.map(a => a.nombre_upss),
            colors: ['#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#6366f1'],
            plotOptions: { pie: { donut: { size: '65%' } } },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', markers: { radius: 12 } }
        };
        new ApexCharts(document.querySelector("#chart-areas"), optionsAreas).render();

        // --- LÓGICA DEL DRILL-DOWN (AJAX) ---
        function cargarDetalleEquipos(idInversion, cui) {
            document.getElementById('lbl-cui').innerText = cui;
            document.getElementById('panel-detalle').classList.remove('hidden');
            
            // Consumir el endpoint AJAX que creamos en web.php
            fetch(`/api/inversiones/${idInversion}/equipos`)
                .then(response => response.json())
                .then(equipos => {
                    
                    // 1. Llenar la Tabla
                    const tbody = document.getElementById('tabla-detalle-body');
                    tbody.innerHTML = '';
                    
                    if(equipos.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-500 font-medium">No hay equipos registrados en esta inversión.</td></tr>';
                    } else {
                        equipos.forEach(eq => {
                            tbody.innerHTML += `
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-slate-800">${eq.nombre_equipo}</td>
                                    <td class="px-4 py-3"><span class="bg-slate-200 text-slate-700 text-xs px-2 py-1 rounded font-medium">${eq.nombre_upss || 'Sin área'}</span></td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-700">${eq.cantidad}</td>
                                    <td class="px-4 py-3 text-right font-medium text-slate-700">S/ ${parseFloat(eq.precio_unitario).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                </tr>
                            `;
                        });
                    }

                    // 2. Dibujar el Sub-Gráfico (Tipos de Equipos)
                    // Agrupamos la cantidad por "tipo_equipo"
                    const conteoTipos = {};
                    equipos.forEach(eq => {
                        conteoTipos[eq.tipo_equipo] = (conteoTipos[eq.tipo_equipo] || 0) + eq.cantidad;
                    });

                    if(chartTipos) { chartTipos.destroy(); } // Destruir gráfico anterior si existe

                    const optionsTipos = {
                        chart: { type: 'pie', height: 260, fontFamily: 'Inter, sans-serif' },
                        series: Object.values(conteoTipos),
                        labels: Object.keys(conteoTipos),
                        legend: { position: 'bottom', markers: { radius: 12 } },
                        dataLabels: { dropShadow: { enabled: false } }
                    };
                    chartTipos = new ApexCharts(document.querySelector("#chart-detalle-tipos"), optionsTipos);
                    chartTipos.render();
                });
        }

        function cerrarDetalle() {
            document.getElementById('panel-detalle').classList.add('hidden');
        }
    </script>
</body>
</html>