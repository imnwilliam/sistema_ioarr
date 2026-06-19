<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inversiones - Sistema IOARR Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>

    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .smooth-transition { transition: all 0.3s ease; }
        .dataTable-input { border-radius: 0.5rem; border: 1px solid #d1d5db; padding: 0.5rem; outline: none; }
        .dataTable-input:focus { border-color: #3b82f6; }
        .dataTable-selector { border-radius: 0.5rem; border: 1px solid #d1d5db; padding: 0.3rem; }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    @include('includes.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto relative">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-10 shadow-sm">
            <h2 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-folder-tree text-blue-600 mr-2"></i> Gestión de Inversiones</h2>
            @if(auth()->user()->id_rol == 1)
                <button onclick="abrirModalInversion()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm smooth-transition">
                    <i class="fa-solid fa-plus mr-2"></i> Nuevo IOARR
                </button>
            @endif
        </header>

        <div class="p-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-4">
                <table id="tabla-inversiones" class="min-w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th>CUI / IOARR</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-right">PIM (S/)</th>
                            <th class="text-right">Certificado</th>
                            <th class="text-right">Devengado</th>
                            <th class="text-center">Fase</th>
                            <th class="text-center">Estado PMI</th>
                            @if(auth()->user()->id_rol == 1)
                                <th data-sortable="false" class="text-center">Acciones</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inversiones as $inv)
                            <tr class="hover:bg-gray-50 smooth-transition border-b">
                                <td class="px-4 py-3">
                                    <div class="font-black text-blue-700 text-base">{{ $inv->cui }}</div>
                                    <div class="text-xs text-gray-500 font-semibold mt-1 w-64 truncate" title="{{ $inv->nombre_inversion }}">{{ $inv->nombre_inversion }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($inv->tipo_ioarr)
                                        <span class="bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wider whitespace-nowrap">{{ $inv->tipo_ioarr }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-gray-800">{{ number_format($inv->pim ?? 0, 2) }}</td>
                                <td class="px-4 py-3 text-right font-medium text-amber-600">{{ number_format($inv->certificado ?? 0, 2) }}</td>
                                <td class="px-4 py-3 text-right font-medium text-emerald-600">{{ number_format($inv->devengado ?? 0, 2) }}</td>
                                
                                <td class="px-4 py-3 text-center">
                                    <span class="bg-purple-100 text-purple-800 text-xs font-bold px-3 py-1 rounded-full">{{ $inv->fase ?? 'Formulación' }}</span>
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full">{{ $inv->estado_pmi }}</span>
                                </td>
                                @if(auth()->user()->id_rol == 1)
                                    <td class="px-4 py-3 text-center flex justify-center gap-2">
                                        <button onclick="editarInversion({{ json_encode($inv) }})" class="text-blue-600 hover:bg-blue-100 p-2 rounded-lg smooth-transition" title="Editar Valores">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <form action="{{ route('inversiones.destroy', $inv->id) }}" method="POST" class="inline form-eliminar">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmarEliminacion(this)" class="text-red-600 hover:bg-red-100 p-2 rounded-lg smooth-transition" title="Eliminar">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="modal-inversion" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="modal-content">
            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg" id="titulo-modal"><i class="fa-solid fa-folder-plus mr-2"></i> Nuevo IOARR</h3>
                <button type="button" onclick="cerrarModalInversion()" class="text-white hover:text-gray-200"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <form id="form-inversion" action="{{ route('inversiones.store') }}" method="POST" class="p-6 space-y-4" onsubmit="mostrarSpinner(this, 'btn-guardar')">
                @csrf
                <div id="method-put"></div> 
                
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">CUI *</label>
                        <input type="text" name="cui" id="inp_cui" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500 font-bold text-blue-700">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre de la Inversión *</label>
                        <input type="text" name="nombre_inversion" id="inp_nombre" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500">
                    </div>
                    
                    <div class="col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Tipo de IOARR *</label>
                        <select name="tipo_ioarr" id="inp_tipo_ioarr" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                            <option value="">Seleccionar Tipo...</option>
                            <option value="Optimización">Optimización</option>
                            <option value="Reposición">Reposición</option>
                            <option value="Rehabilitación">Rehabilitación</option>
                            <option value="Ampliación Marginal">Ampliación Marginal</option>
                        </select>
                    </div>
                </div>

                <div id="panel-financiero" class="hidden bg-gray-50 p-4 rounded-xl border border-gray-200 mt-4">
                    <h4 class="font-bold text-gray-700 mb-4 border-b pb-2"><i class="fa-solid fa-chart-line mr-2 text-emerald-600"></i> Ejecución Financiera (S/)</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1 uppercase">PIM</label>
                            <input type="number" name="pim" id="inp_pim" step="0.01" class="w-full border border-gray-300 rounded-lg p-2 outline-none focus:border-blue-500 font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-amber-600 mb-1 uppercase">Certificado</label>
                            <input type="number" name="certificado" id="inp_cert" step="0.01" class="w-full border border-amber-300 rounded-lg p-2 outline-none focus:border-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-emerald-600 mb-1 uppercase">Devengado</label>
                            <input type="number" name="devengado" id="inp_dev" step="0.01" class="w-full border border-emerald-300 rounded-lg p-2 outline-none focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-purple-600 mb-1 uppercase">Girado</label>
                            <input type="number" name="girado" id="inp_girado" step="0.01" class="w-full border border-purple-300 rounded-lg p-2 outline-none focus:border-purple-500">
                        </div>
                        
                        <div class="col-span-2 mt-2 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1 uppercase">Estado PMI</label>
                                <select name="estado_pmi" id="inp_estado" class="w-full border border-gray-300 rounded-lg p-2 outline-none focus:border-blue-500">
                                    <option value="Activo">Activo</option>
                                    <option value="Cerrado / Liquidado">Cerrado / Liquidado</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-purple-600 mb-1 uppercase">Fase</label>
                                <select name="fase" id="inp_fase" class="w-full border border-purple-200 rounded-lg p-2 outline-none focus:border-purple-500 bg-purple-50/30">
                                    <option value="Formulación">Formulación</option>
                                    <option value="En ejecución">En ejecución</option>
                                    <option value="En cierre">En cierre</option>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="pt-4 flex justify-end space-x-3 border-t mt-6">
                    <button type="button" onclick="cerrarModalInversion()" class="px-5 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium smooth-transition">Cancelar</button>
                    <button type="submit" id="btn-guardar" class="px-6 py-2.5 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-bold shadow-md smooth-transition flex items-center">
                        <i class="fa-solid fa-save mr-2"></i> Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: "{{ session('success') }}", showConfirmButton: false, timer: 4000, timerProgressBar: true });</script>
    @endif
    @if(session('error'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: "{{ session('error') }}", showConfirmButton: false, timer: 5000, timerProgressBar: true });</script>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new simpleDatatables.DataTable("#tabla-inversiones", {
                searchable: true, fixedHeight: true,
                labels: { placeholder: "Buscar CUI...", perPage: "filas por página", noRows: "No hay inversiones", info: "Mostrando {start} a {end} de {rows}" }
            });
        });

        function abrirModalInversion() { 
            document.getElementById('form-inversion').reset();
            document.getElementById('form-inversion').action = "{{ route('inversiones.store') }}";
            document.getElementById('method-put').innerHTML = '';
            document.getElementById('inp_tipo_ioarr').value = ''; // Limpiamos el nuevo campo
            document.getElementById('panel-financiero').classList.add('hidden');
            document.getElementById('titulo-modal').innerHTML = '<i class="fa-solid fa-folder-plus mr-2"></i> Nuevo IOARR';
            
            const modal = document.getElementById('modal-inversion');
            const content = document.getElementById('modal-content');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        }

        function editarInversion(inv) {
            document.getElementById('inp_cui').value = inv.cui;
            document.getElementById('inp_nombre').value = inv.nombre_inversion;
            document.getElementById('inp_tipo_ioarr').value = inv.tipo_ioarr || ''; // Cargamos el nuevo campo
            document.getElementById('inp_pim').value = inv.pim || 0;
            document.getElementById('inp_cert').value = inv.certificado || 0;
            document.getElementById('inp_dev').value = inv.devengado || 0;
            document.getElementById('inp_girado').value = inv.girado || 0;
            document.getElementById('inp_estado').value = inv.estado_pmi || 'Activo';
            document.getElementById('inp_fase').value = inv.fase || 'Formulación';

            document.getElementById('form-inversion').action = `/inversiones/${inv.id}`;
            document.getElementById('method-put').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('panel-financiero').classList.remove('hidden');
            document.getElementById('titulo-modal').innerHTML = '<i class="fa-solid fa-pen-to-square mr-2"></i> Editar IOARR';
            
            const modal = document.getElementById('modal-inversion');
            const content = document.getElementById('modal-content');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        }

        function cerrarModalInversion() { 
            const modal = document.getElementById('modal-inversion');
            const content = document.getElementById('modal-content');
            modal.classList.add('opacity-0'); content.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        }

        function confirmarEliminacion(btn) {
            Swal.fire({
                title: '¿Eliminar proyecto?', text: "Se ocultará del panel principal.", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Sí, eliminar', cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) { btn.closest('form').submit(); }
            })
        }

        function mostrarSpinner(form, btnId) {
            const btn = document.getElementById(btnId);
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Guardando...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
        }
    </script>
</body>
</html>