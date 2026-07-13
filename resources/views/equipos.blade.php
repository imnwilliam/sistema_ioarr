<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Equipos - Sistema IOARR Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>

    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .smooth-transition { transition: all 0.3s ease; }
        
        .ts-wrapper .ts-control {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            padding: 0.6rem 0.75rem;
            box-shadow: none;
            background-color: #ffffff;
            font-size: 0.875rem;
            min-height: 42px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .ts-wrapper.focus .ts-control {
            border-color: #2563eb;
            box-shadow: 0 0 0 1px #2563eb;
        }
        .ts-wrapper .ts-control > input {
            font-size: 0.875rem;
            color: #374151;
        }
        .ts-wrapper .ts-control .item {
            color: #1f2937;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 90%;
        }
        .ts-dropdown {
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            font-size: 0.875rem;
            padding: 0.25rem;
            background-color: #ffffff !important;
            z-index: 9999 !important; 
        }
        .ts-dropdown .option {
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            color: #374151;
        }
        .ts-dropdown .active {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
        }

        .datatable-top, .dataTable-top { padding-bottom: 1rem; }
        
        .datatable-input, .dataTable-input { 
            border-radius: 0.5rem !important; 
            border: 1.5px solid #d1d5db !important; 
            padding: 0.5rem 0.75rem !important; 
            outline: none !important; 
            color: #374151 !important;
            background-color: #ffffff !important;
            transition: all 0.25s ease;
        }
        .datatable-input:hover, .dataTable-input:hover { border-color: #9ca3af !important; }
        .datatable-input:focus, .dataTable-input:focus { 
            border-color: #3b82f6 !important; 
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
        }

        .datatable-selector, .dataTable-selector { 
            border-radius: 0.5rem !important; 
            border: 1.5px solid #d1d5db !important; 
            padding: 0.4rem 1.8rem 0.4rem 0.75rem !important; 
            outline: none !important;
            color: #374151 !important;
            background-color: #ffffff !important;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .datatable-selector:hover, .datatable-selector:hover { border-color: #9ca3af !important; }
        .datatable-selector:focus, .dataTable-selector:focus { 
            border-color: #3b82f6 !important; 
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
        }

        .btn-exportar:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    @include('includes.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto relative">
        
        <header class="h-20 shrink-0 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-30 shadow-sm">
            <h2 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-microscope text-purple-600 mr-2"></i> Gestión de Equipos</h2>
            <div class="flex items-center gap-3">
                <button onclick="exportarPDFEquipos(this)" 
                    class="btn-exportar bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm smooth-transition">
                    <i class="fa-solid fa-file-pdf mr-2"></i> Exportar PDF
                </button>
                <a href="{{ route('equipos.exportar', request()->all()) }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm smooth-transition">
                    <i class="fa-solid fa-file-excel mr-2"></i> Exportar
                </a>
                <button onclick="abrirModalEquipo()" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm smooth-transition">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo Equipo
                </button>
            </div>
        </header>

        <div class="p-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center rounded-t-2xl">
                    <h3 class="font-bold text-gray-700"><i class="fa-solid fa-filter mr-2 text-blue-500"></i> Filtros de Búsqueda</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('equipos.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-5 items-end">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Inversión (CUI)</label>
                            <select name="filtro_inversion" id="filtro_inversion" class="w-full">
                                <option value="">Todos los CUIs</option>
                                @foreach($inversiones as $inv)
                                    <option value="{{ $inv->id }}" {{ request('filtro_inversion') == $inv->id ? 'selected' : '' }}>
                                        {{ $inv->cui }} - {{ Str::limit($inv->nombre_inversion, 40) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Área / UPSS</label>
                            <select name="filtro_upss" id="filtro_upss" class="w-full">
                                <option value="">Todas las Áreas</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ request('filtro_upss') == $area->id ? 'selected' : '' }}>
                                        {{ $area->nombre_upss }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tipo de Equipo</label>
                            <select name="filtro_tipo" id="filtro_tipo" class="w-full">
                                <option value="">Todos los Tipos</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->nombre_tipo }}" {{ request('filtro_tipo') == $tipo->nombre_tipo ? 'selected' : '' }}>
                                        {{ $tipo->nombre_tipo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">N° Expediente</label>
                            <input type="text" name="filtro_expediente" id="filtro_expediente_inp" inputmode="numeric" pattern="\d*" value="{{ request('filtro_expediente') }}" placeholder="Buscar N°..." class="w-full border border-gray-300 rounded-lg px-3 outline-none focus:border-blue-500 text-sm h-[42px] text-gray-700">
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-lg smooth-transition shadow-md shadow-blue-500/20">
                                <i class="fa-solid fa-magnifying-glass mr-2"></i> Filtrar
                            </button>
                            <a href="{{ route('equipos.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-lg smooth-transition flex items-center justify-center" title="Limpiar Filtros">
                                <i class="fa-solid fa-eraser"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-4">
                <table id="tabla-equipos" class="min-w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th>Equipo / Tipo / Exp.</th>
                            <th>Inversión (CUI)</th>
                            <th>UPSS / Servicio / Ambiente</th>
                            <th>Estado Situacional</th>
                            <th>Costo Total</th>
                            <th data-sortable="false" class="text-center">Evidencias</th>
                            <th data-sortable="false" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($equipos as $eq)
                            <tr class="hover:bg-gray-50 smooth-transition border-b border-gray-100">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900">{{ $eq->nombre_equipo }}</div>
                                    <div class="text-xs text-purple-600 font-semibold mt-1">{{ $eq->tipo_equipo }}</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5"><span class="font-medium text-gray-600">Exp:</span> {{ $eq->expediente ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 font-medium text-blue-700">{{ $eq->cui }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-gray-700">{{ $eq->nombre_upss ?? 'Sin área' }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        @if($eq->servicio) <span class="font-medium text-gray-600">Serv:</span> {{ $eq->servicio }} <br> @endif
                                        @if($eq->ambiente) <span class="font-medium text-gray-600">Amb:</span> {{ $eq->ambiente }} @endif
                                    </div>
                                </td>
                                
                                <td class="px-4 py-3">
                                    <div class="text-xs text-gray-600 w-48 truncate" title="{{ $eq->estado_situacional }}">
                                        {{ $eq->estado_situacional ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 font-medium text-gray-800">S/ {{ number_format($eq->precio_total ?? 0, 2) }}<br><span class="text-xs text-gray-400">Cant: {{ $eq->cantidad }}</span></td>
                                
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $archivos = json_decode($eq->archivo_evidencia, true);
                                        if(!$archivos && !empty($eq->archivo_evidencia)) $archivos = [$eq->archivo_evidencia];
                                        $archivos = $archivos ?? [];
                                    @endphp

                                    @if(count($archivos) > 0)
                                        <div class="flex flex-col gap-1 items-center">
                                            @foreach($archivos as $idx => $ruta)
                                                @php
                                                    $ext = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));
                                                    $icon = 'fa-file text-gray-500';
                                                    if($ext == 'pdf') $icon = 'fa-file-pdf text-red-500';
                                                    elseif(in_array($ext, ['xls','xlsx'])) $icon = 'fa-file-excel text-green-500';
                                                    elseif(in_array($ext, ['doc','docx'])) $icon = 'fa-file-word text-blue-500';
                                                @endphp
                                                <a href="{{ route('equipos.evidencia', [$eq->id, $idx]) }}" target="_blank" rel="noopener" class="text-blue-600 hover:bg-blue-100 px-2 py-1 rounded smooth-transition text-xs font-semibold whitespace-nowrap" title="Ver Documento">
                                                    <i class="fa-solid {{ $icon }} mr-1"></i> Doc {{ $idx + 1 }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs italic">Sin archivos</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center flex justify-center gap-2">
                                    <button onclick="abrirCronograma({{ $eq->id }}, '{{ addslashes($eq->nombre_equipo) }}')" class="text-emerald-600 hover:bg-emerald-100 p-2 rounded-lg smooth-transition" title="Fechas">
                                        <i class="fa-regular fa-calendar-days"></i>
                                    </button>

                                    <button onclick="editarEquipo({{ json_encode($eq) }})" class="text-gray-600 hover:bg-gray-200 p-2 rounded-lg smooth-transition" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <form action="{{ route('equipos.destroy', $eq->id) }}" method="POST" class="inline form-eliminar">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmarEliminacion(this)" class="text-red-600 hover:bg-red-100 p-2 rounded-lg smooth-transition" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    
                    <tfoot>
                        <tr class="bg-blue-50/80 border-t border-blue-200">
                            <td colspan="4" class="px-4 py-4 text-right font-black text-slate-700 uppercase tracking-widest text-xs">
                                Total General:
                            </td>
                            <td class="px-4 py-4 font-black text-blue-700 text-base whitespace-nowrap">
                                S/ {{ number_format($sumaTotal ?? 0, 2) }}
                            </td>
                            <td colspan="2" class="px-4 py-4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </main>

    @include('equipos.modal-equipo')
    @include('equipos.modal-cronograma')
    @include('equipos.scripts')

</body>
</html>