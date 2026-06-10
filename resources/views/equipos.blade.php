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
        /* Ajustes para integrar librerías con Tailwind */
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
                            <th>UPSS / Ambiente</th>
                            <th>Cant.</th>
                            <th>Costo Total</th>
                            <th data-sortable="false">Acciones</th>
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
                                    <div class="text-xs text-gray-500">{{ $eq->ambiente }}</div>
                                </td>
                                <td class="px-4 py-3 text-center font-bold">{{ $eq->cantidad }}</td>
                                <td class="px-4 py-3 font-medium">S/ {{ number_format($eq->precio_total ?? 0, 2) }}</td>
                                <td class="px-4 py-3 text-center flex justify-center gap-2">
                                    
                                    @if($eq->archivo_evidencia)
                                        <a href="{{ route('equipos.descargar', $eq->id) }}" class="text-blue-600 hover:bg-blue-100 p-2 rounded-lg smooth-transition" title="Descargar Evidencia">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </a>
                                    @else
                                        <span class="text-gray-300 p-2" title="Sin archivo"><i class="fa-solid fa-file-pdf"></i></span>
                                    @endif

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
                        <label class="block text-sm font-bold text-gray-700 mb-1">UPSS (Área) *</label>
                        <select name="id_upss" id="inp_id_upss" required placeholder="Buscar Área...">
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
                        <select name="tipo_equipo" id="inp_tipo" required placeholder="Seleccionar..." class="w-full">
                            <option value="">Seleccionar...</option>
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->nombre_tipo }}">{{ $tipo->nombre_tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Ambiente Físico</label>
                        <input type="text" name="ambiente" id="inp_ambiente" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">N° Expediente</label>
                        <input type="text" name="expediente" id="inp_expediente" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Evidencia (PDF)</label>
                        <input type="file" name="archivo_evidencia" accept=".pdf" class="w-full border border-gray-300 rounded-lg p-1.5 text-sm outline-none focus:border-purple-500 bg-gray-50">
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
                    <button type="submit" id="btn-guardar-crono" class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 shadow-md transition-all transform hover:-translate-y-0.5 flex items-center">
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
        // 1. INICIALIZAR LIBRERÍAS DE LA SUITE VISUAL
        let selectInversion, selectUpss, selectTipo;

        document.addEventListener("DOMContentLoaded", function() {
            // Inicializar DataTables
            const table = new simpleDatatables.DataTable("#tabla-equipos", {
                searchable: true,
                fixedHeight: true,
                labels: { placeholder: "Buscar en tabla...", perPage: "equipos por página", noRows: "No hay equipos registrados", info: "Mostrando {start} a {end} de {rows} equipos" }
            });

            // Inicializar TomSelect (Buscadores en Dropdowns)
            selectInversion = new TomSelect("#inp_id_inversion", { create: false, sortField: { field: "text", direction: "asc" } });
            selectUpss = new TomSelect("#inp_id_upss", { create: false });
            selectTipo = new TomSelect("#inp_tipo", { create: false });
        });

        // 2. MODALES CON TRANSICIONES SUAVES (Fade y Scale)
        function abrirModalEquipo() { 
            document.getElementById('form-equipo').reset();
            document.getElementById('form-equipo').action = "{{ route('equipos.store') }}";
            document.getElementById('method-put').innerHTML = '';
            document.getElementById('titulo-modal').innerHTML = '<i class="fa-solid fa-microscope mr-2"></i> Registrar Equipo';
            
            // Limpiar los selectores inteligentes
            selectInversion.clear(); selectUpss.clear(); selectTipo.clear();

            const modal = document.getElementById('modal-equipo');
            const content = document.getElementById('modal-content');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        }

        function editarEquipo(equipo) {
            document.getElementById('inp_nombre').value = equipo.nombre_equipo;
            document.getElementById('inp_ambiente').value = equipo.ambiente;
            document.getElementById('inp_expediente').value = equipo.expediente;
            document.getElementById('inp_cantidad').value = equipo.cantidad;
            document.getElementById('inp_precio').value = equipo.precio_unitario;

            // Actualizar selectores inteligentes
            selectInversion.setValue(equipo.id_inversion);
            selectUpss.setValue(equipo.id_upss);
            selectTipo.setValue(equipo.tipo_equipo);

            document.getElementById('form-equipo').action = `/equipos/${equipo.id}`;
            document.getElementById('method-put').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('titulo-modal').innerHTML = '<i class="fa-solid fa-pen mr-2"></i> Editar Equipo';
            
            const modal = document.getElementById('modal-equipo');
            const content = document.getElementById('modal-content');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        }

        function cerrarModalEquipo() { 
            const modal = document.getElementById('modal-equipo');
            const content = document.getElementById('modal-content');
            modal.classList.add('opacity-0'); content.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        }

        // 3. CONFIRMACIÓN DE ELIMINACIÓN (SweetAlert2)
        function confirmarEliminacion(btn) {
            Swal.fire({
                title: '¿Eliminar equipo?',
                text: "Esta acción lo ocultará del sistema.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.closest('form').submit(); // Envía el formulario si acepta
                }
            })
        }

        // 4. MICRO-INTERACCIÓN: SPINNER AL GUARDAR
        function mostrarSpinner(form, btnId) {
            const btn = document.getElementById(btnId);
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Procesando...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
        }

        // 5. MODAL CRONOGRAMA
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