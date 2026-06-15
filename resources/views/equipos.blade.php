<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Equipos - Sistema IOARR Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.default.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>

    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .smooth-transition { transition: all 0.3s ease; }
        .ts-control { border-radius: 0.5rem; border-color: #d1d5db; padding: 0.5rem; box-shadow: none; }
        .ts-control.focus { border-color: #a855f7; box-shadow: 0 0 0 1px #a855f7; }
        .dataTable-input { border-radius: 0.5rem; border: 1px solid #d1d5db; padding: 0.5rem; outline: none; }
        .dataTable-input:focus { border-color: #a855f7; }
        .dataTable-selector { border-radius: 0.5rem; border: 1px solid #d1d5db; padding: 0.3rem; }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    @include('includes.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto relative">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-10 shadow-sm">
            <h2 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-microscope text-purple-600 mr-2"></i> Gestión de Equipos</h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('equipos.exportar') }}" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm smooth-transition">
                    <i class="fa-solid fa-file-excel mr-2"></i> Exportar
                </a>
                <button onclick="abrirModalEquipo()" class="bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm smooth-transition">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo Equipo
                </button>
            </div>
        </header>

        <div class="p-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-4">
                <table id="tabla-equipos" class="min-w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th>Equipo / Tipo</th>
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
                            <tr class="hover:bg-gray-50 smooth-transition border-b">
                                <td class="px-4 py-3">
                                    <div class="font-bold text-gray-900">{{ $eq->nombre_equipo }}</div>
                                    <div class="text-xs text-purple-600 font-semibold mt-1">{{ $eq->tipo_equipo }}</div>
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

                                <td class="px-4 py-3 font-medium">S/ {{ number_format($eq->precio_total ?? 0, 2) }}<br><span class="text-xs text-gray-400">Cant: {{ $eq->cantidad }}</span></td>
                                
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $archivos = json_decode($eq->archivo_evidencia, true);
                                        if(!$archivos && !empty($eq->archivo_evidencia)) $archivos = [$eq->archivo_evidencia];
                                        $archivos = $archivos ?? [];
                                    @endphp

                                    @if(count($archivos) > 0)
                                        <div class="flex flex-col gap-1 items-center">
                                            @foreach($archivos as $idx => $ruta)
                                                <a href="{{ asset('storage/'.$ruta) }}" target="_blank" class="text-blue-600 hover:bg-blue-100 px-2 py-1 rounded smooth-transition text-xs font-semibold whitespace-nowrap" title="Ver Documento">
                                                    <i class="fa-solid fa-file-pdf mr-1"></i> Doc {{ $idx + 1 }}
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
                </table>
            </div>
        </div>
    </main>

    <div id="modal-equipo" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh] transform scale-95 transition-transform duration-300" id="modal-content">
            <div class="bg-purple-600 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg" id="titulo-modal"><i class="fa-solid fa-microscope mr-2"></i> Registrar Equipo</h3>
                <button type="button" onclick="cerrarModalEquipo()" class="text-white hover:text-gray-200"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <form id="form-equipo" action="{{ route('equipos.store') }}" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-5" onsubmit="mostrarSpinner(this, 'btn-guardar')">
                @csrf
                <div id="method-put"></div> 
                
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Vincular a IOARR (CUI) *</label>
                        <select name="id_inversion" id="inp_id_inversion" required placeholder="Buscar CUI o Nombre...">
                            <option value="">Buscar CUI o Nombre...</option>
                            @foreach($inversiones as $inv)
                                <option value="{{ $inv->id }}">{{ $inv->cui }} - {{ $inv->nombre_inversion }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">UPSS *</label>
                        <select name="id_upss" id="inp_id_upss" required placeholder="Buscar UPSS...">
                            <option value="">Buscar Área...</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}">{{ $area->nombre_upss }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-5">
                    <div class="col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Equipo *</label>
                        <input type="text" name="nombre_equipo" id="inp_nombre" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 smooth-transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tipo de Equipo *</label>
                        <select name="tipo_equipo" id="inp_tipo" required placeholder="Seleccionar..." class="w-full border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500">
                            <option value="">Seleccionar...</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->nombre_tipo }}">{{ $tipo->nombre_tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Servicio</label>
                        <input type="text" name="servicio" id="inp_servicio" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ambiente</label>
                        <input type="text" name="ambiente" id="inp_ambiente" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">N° Expediente</label>
                        <input type="text" name="expediente" id="inp_expediente" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500">
                    </div>
                    
                    <div class="col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Añadir Evidencias (PDFs)</label>
                        <input type="file" name="archivos_evidencia[]" multiple accept=".pdf" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-purple-500 bg-gray-50 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer">
                        <p class="text-[10px] text-gray-400 mt-1">Puedes seleccionar varios archivos a la vez manteniendo presionada la tecla Ctrl.</p>
                        
                        <div id="contenedor-archivos" class="hidden mt-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <p class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide"><i class="fa-solid fa-folder-open mr-1"></i> Documentos Previamente Subidos</p>
                            <ul id="lista-archivos-existentes" class="space-y-2 grid grid-cols-2 gap-2"></ul>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <h4 class="font-bold text-gray-700 mb-3"><i class="fa-solid fa-coins text-amber-500 mr-2"></i> Datos de Costo</h4>
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Cantidad *</label>
                            <input type="number" name="cantidad" id="inp_cantidad" min="1" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500 text-lg font-bold">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Precio Unitario (S/) *</label>
                            <input type="number" name="precio_unitario" id="inp_precio" step="0.01" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500 text-lg font-bold text-blue-700">
                        </div>
                    </div>
                </div>

                <div class="col-span-3 mt-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Estado Situacional / Acciones</label>
                    <textarea name="estado_situacional" id="inp_estado_situacional" rows="3" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 smooth-transition" placeholder="Escribe aquí las observaciones, acciones tomadas o el estado situacional del equipo..."></textarea>
                </div>

                <div class="pt-2 flex justify-end space-x-3 mt-4">
                    <button type="button" onclick="cerrarModalEquipo()" class="px-5 py-2.5 text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium smooth-transition">Cancelar</button>
                    <button type="submit" id="btn-guardar" class="px-6 py-2.5 text-white bg-purple-600 hover:bg-purple-700 rounded-lg font-bold shadow-md smooth-transition flex items-center">
                        <i class="fa-solid fa-save mr-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-cronograma" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="modal-crono-content">
            <div class="bg-emerald-600 px-6 py-4 flex justify-between items-center text-white">
                <div>
                    <h3 class="font-bold text-lg"><i class="fa-solid fa-calendar-check mr-2"></i> Cronograma SEACE</h3>
                    <p class="text-xs text-emerald-100 mt-1">Equipo: <span id="lbl_nombre_equipo" class="font-bold"></span></p>
                </div>
                <button onclick="cerrarCronograma()" class="text-white hover:text-gray-200 transition-colors"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <form action="{{ route('cronogramas.store') }}" method="POST" onsubmit="mostrarSpinner(this, 'btn-guardar-crono')">
                @csrf
                <input type="hidden" name="id_equipo" id="crono_id_equipo">
                <div class="p-6 overflow-y-auto">
                    <table class="min-w-full text-sm text-left border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-700 uppercase text-xs">
                            <tr><th class="px-4 py-3">Etapa del Proceso</th><th class="px-4 py-3 w-48 text-center">Fecha Inicio</th><th class="px-4 py-3 w-48 text-center">Fecha Fin</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4 font-bold text-gray-800">Convocatoria</td>
                                <td class="px-4 py-3"><input type="date" name="convocatoria_inicio" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-gray-600"></td>
                                <td class="px-4 py-3"><input type="date" name="convocatoria_fin" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-gray-600"></td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4 font-bold text-gray-800">Otorgamiento de la Buena Pro</td>
                                <td class="px-4 py-3"><input type="date" name="buenapro_inicio" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-gray-600"></td>
                                <td class="px-4 py-3"><input type="date" name="buenapro_fin" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all text-gray-600"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t flex justify-end gap-3">
                    <button type="button" onclick="cerrarCronograma()" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors">Cancelar</button>
                    <button type="submit" id="btn-guardar-crono" class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 shadow-md transition-all flex items-center">
                        <i class="fa-solid fa-save mr-2"></i> Guardar Fechas
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: "{{ session('success') }}", showConfirmButton: false, timer: 4000, timerProgressBar: true });
        </script>
    @endif
    @if(session('error'))
        <script>
            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: "{{ session('error') }}", showConfirmButton: false, timer: 5000, timerProgressBar: true });
        </script>
    @endif

    <script>
        let selectInversion, selectUpss;

        document.addEventListener("DOMContentLoaded", function() {
            const table = new simpleDatatables.DataTable("#tabla-equipos", {
                searchable: true, fixedHeight: true,
                labels: { placeholder: "Buscar en tabla...", perPage: "equipos", noRows: "No hay equipos", info: "{start} a {end} de {rows}" }
            });
            selectInversion = new TomSelect("#inp_id_inversion", { create: false, sortField: { field: "text", direction: "asc" } });
            selectUpss = new TomSelect("#inp_id_upss", { create: false });
        });

        function abrirModalEquipo() { 
            document.getElementById('form-equipo').reset();
            document.getElementById('form-equipo').action = "{{ route('equipos.store') }}";
            document.getElementById('method-put').innerHTML = '';
            document.getElementById('titulo-modal').innerHTML = '<i class="fa-solid fa-microscope mr-2"></i> Registrar Equipo';
            
            document.getElementById('contenedor-archivos').classList.add('hidden');
            document.getElementById('lista-archivos-existentes').innerHTML = '';
            document.getElementById('inp_estado_situacional').value = '';

            selectInversion.clear(); selectUpss.clear();

            const modal = document.getElementById('modal-equipo');
            const content = document.getElementById('modal-content');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        }

        function editarEquipo(equipo) {
            document.getElementById('inp_nombre').value = equipo.nombre_equipo;
            document.getElementById('inp_servicio').value = equipo.servicio || '';
            document.getElementById('inp_ambiente').value = equipo.ambiente || '';
            document.getElementById('inp_estado_situacional').value = equipo.estado_situacional || '';
            document.getElementById('inp_expediente').value = equipo.expediente;
            document.getElementById('inp_cantidad').value = equipo.cantidad;
            document.getElementById('inp_precio').value = equipo.precio_unitario;
            document.getElementById('inp_tipo').value = equipo.tipo_equipo; 

            selectInversion.setValue(equipo.id_inversion);
            selectUpss.setValue(equipo.id_upss);

            document.getElementById('form-equipo').action = `/equipos/${equipo.id}`;
            document.getElementById('method-put').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('titulo-modal').innerHTML = '<i class="fa-solid fa-pen mr-2"></i> Editar Equipo';
            
            const contenedorArchivos = document.getElementById('contenedor-archivos');
            const listaArchivos = document.getElementById('lista-archivos-existentes');
            listaArchivos.innerHTML = '';

            let archivos = [];
            try { archivos = JSON.parse(equipo.archivo_evidencia); } catch(e) {}
            if(!archivos && equipo.archivo_evidencia) archivos = [equipo.archivo_evidencia];

            if (archivos && archivos.length > 0) {
                contenedorArchivos.classList.remove('hidden');
                archivos.forEach((ruta, index) => {
                    listaArchivos.innerHTML += `
                        <li class="flex justify-between items-center bg-white border border-gray-200 p-2 rounded-lg text-sm shadow-sm" id="file-row-${index}">
                            <a href="/storage/${ruta}" target="_blank" class="text-blue-600 hover:underline truncate w-40 flex items-center">
                                <i class="fa-solid fa-file-pdf mr-2 text-red-500"></i> Doc ${index + 1}
                            </a>
                            <button type="button" onclick="eliminarArchivoUnico(${equipo.id}, ${index})" class="text-red-500 hover:bg-red-50 p-1.5 rounded transition-colors" title="Borrar este PDF">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </li>
                    `;
                });
            } else {
                contenedorArchivos.classList.add('hidden');
            }

            const modal = document.getElementById('modal-equipo');
            const content = document.getElementById('modal-content');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        }

        function eliminarArchivoUnico(equipoId, index) {
            Swal.fire({
                title: '¿Eliminar este documento?', text: "Esta acción no se puede deshacer.", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/equipos/${equipoId}/archivo/${index}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            document.getElementById(`file-row-${index}`).remove();
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Archivo eliminado', showConfirmButton: false, timer: 2000 });
                            
                            if(document.getElementById('lista-archivos-existentes').children.length === 0) {
                                document.getElementById('contenedor-archivos').classList.add('hidden');
                            }
                        }
                    });
                }
            })
        }

        function cerrarModalEquipo() { 
            const modal = document.getElementById('modal-equipo');
            const content = document.getElementById('modal-content');
            modal.classList.add('opacity-0'); content.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        }

        function confirmarEliminacion(btn) {
            Swal.fire({
                title: '¿Eliminar equipo?', text: "Esta acción lo ocultará del sistema.", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) btn.closest('form').submit();
            });
        }

        function mostrarSpinner(form, btnId) {
            const btn = document.getElementById(btnId);
            btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Procesando...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
        }

        function abrirCronograma(id_equipo, nombre_equipo) { 
            document.getElementById('crono_id_equipo').value = id_equipo;
            document.getElementById('lbl_nombre_equipo').innerText = nombre_equipo;
            
            const modal = document.getElementById('modal-cronograma');
            const content = document.getElementById('modal-crono-content');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        }
        function cerrarCronograma() { 
            const modal = document.getElementById('modal-cronograma');
            const content = document.getElementById('modal-crono-content');
            modal.classList.add('opacity-0'); content.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        }
    </script>
</body>
</html>