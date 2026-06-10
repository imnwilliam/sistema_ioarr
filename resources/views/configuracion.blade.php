<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración - Catálogos Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest"></script>

    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .smooth-transition { transition: all 0.3s ease; }
        .dataTable-input { border-radius: 0.5rem; border: 1px solid #d1d5db; padding: 0.4rem 0.8rem; outline: none; font-size: 0.875rem; }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    @include('includes.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-10 shadow-sm">
            <h2 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-gear text-slate-600 mr-2"></i> Configuración de Catálogos técnico-médicos</h2>
        </header>

        <div class="p-8 grid grid-cols-2 gap-8">
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <div class="flex justify-between items-center mb-4 pb-2 border-b">
                    <h3 class="font-bold text-gray-800"><i class="fa-solid fa-hospital text-blue-500 mr-2"></i> Áreas / UPSS</h3>
                    <button onclick="abrirModalCatalogo('area')" class="text-xs bg-blue-600 text-white font-bold px-3 py-1.5 rounded-lg hover:bg-blue-700 smooth-transition"><i class="fa-solid fa-plus mr-1"></i> Agregar</button>
                </div>
                <table id="tabla-areas" class="min-w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr><th>Nombre de la UPSS</th><th data-sortable="false" class="text-center">Acciones</th></tr>
                    </thead>
                    <tbody>
                        @foreach($areas as $area)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $area->nombre_upss }}</td>
                                <td class="px-4 py-3 text-center flex justify-center gap-1">
                                    <button onclick="editarCatalogo('area', {{ $area->id }}, '{{ addslashes($area->nombre_upss) }}')" class="text-blue-600 hover:bg-blue-50 p-1.5 rounded"><i class="fa-solid fa-pen text-xs"></i></button>
                                    <form action="/configuracion/area/{{ $area->id }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmarBorrado(this)" class="text-red-600 hover:bg-red-50 p-1.5 rounded"><i class="fa-solid fa-trash text-xs"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col">
                <div class="flex justify-between items-center mb-4 pb-2 border-b">
                    <h3 class="font-bold text-gray-800"><i class="fa-solid fa-microscope text-purple-500 mr-2"></i> Tipos de Equipo</h3>
                    <button onclick="abrirModalCatalogo('tipo')" class="text-xs bg-purple-600 text-white font-bold px-3 py-1.5 rounded-lg hover:bg-purple-700 smooth-transition"><i class="fa-solid fa-plus mr-1"></i> Agregar</button>
                </div>
                <table id="tabla-tipos" class="min-w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr><th>Tipo Estructural</th><th data-sortable="false" class="text-center">Acciones</th></tr>
                    </thead>
                    <tbody>
                        @foreach($tipos as $tipo)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $tipo->nombre_tipo }}</td>
                                <td class="px-4 py-3 text-center flex justify-center gap-1">
                                    <button onclick="editarCatalogo('tipo', {{ $tipo->id }}, '{{ addslashes($tipo->nombre_tipo) }}')" class="text-purple-600 hover:bg-purple-50 p-1.5 rounded"><i class="fa-solid fa-pen text-xs"></i></button>
                                    <form action="/configuracion/tipo/{{ $tipo->id }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmarBorrado(this)" class="text-red-600 hover:bg-red-50 p-1.5 rounded"><i class="fa-solid fa-trash text-xs"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </main>

    <div id="modal-cat" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-200 opacity-0">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col transform scale-95 transition-transform duration-200" id="modal-content">
            <div id="modal-header" class="px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-base" id="modal-titulo">Gestionar Registro</h3>
                <button onclick="cerrarModal()" class="text-white opacity-80 hover:opacity-100"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form id="modal-form" method="POST" class="p-6 space-y-4">
                @csrf
                <div id="modal-method"></div>
                <div>
                    <label id="modal-label" class="block text-sm font-bold text-gray-700 mb-1">Nombre *</label>
                    <input type="text" name="" id="modal-input" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:ring-1">
                </div>
                <div class="flex justify-end space-x-2 pt-2 border-t">
                    <button type="button" onclick="cerrarModal()" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm font-medium">Cancelar</button>
                    <button type="submit" class="px-4 py-2 text-white rounded-lg text-sm font-bold shadow-sm" id="modal-btn-submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: "{{ session('success') }}", showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
    @if(session('error'))
        <script>Swal.fire({ icon: 'error', title: 'Operación denegada', text: "{{ session('error') }}", confirmButtonColor: '#3b82f6' });</script>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new simpleDatatables.DataTable("#tabla-areas", { searchable: true, perPageSelect: false, perPage: 5 });
            new simpleDatatables.DataTable("#tabla-tipos", { searchable: true, perPageSelect: false, perPage: 5 });
        });

        function abrirModalCatalogo(tipo) {
            const isArea = tipo === 'area';
            document.getElementById('modal-form').reset();
            document.getElementById('modal-method').innerHTML = '';
            document.getElementById('modal-form').action = isArea ? '/configuracion/area' : '/configuracion/tipo';
            document.getElementById('modal-input').name = isArea ? 'nombre_upss' : 'nombre_tipo';
            document.getElementById('modal-titulo').innerText = isArea ? 'Nueva Área UPSS' : 'Nuevo Tipo de Equipo';
            document.getElementById('modal-label').innerText = isArea ? 'Nombre de la UPSS *' : 'Nombre del Tipo de Equipo *';
            
            const header = document.getElementById('modal-header');
            const btn = document.getElementById('modal-btn-submit');
            const input = document.getElementById('modal-input');
            
            header.className = isArea ? 'px-6 py-4 flex justify-between items-center text-white bg-blue-600' : 'px-6 py-4 flex justify-between items-center text-white bg-purple-600';
            btn.className = isArea ? 'px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-bold' : 'px-4 py-2 text-white bg-purple-600 hover:bg-purple-700 rounded-lg text-sm font-bold';
            input.className = isArea ? 'w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500' : 'w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500';

            mostrarModalAnimation();
        }

        function editarCatalogo(tipo, id, valor) {
            abrirModalCatalogo(tipo);
            const isArea = tipo === 'area';
            document.getElementById('modal-form').action = isArea ? `/configuracion/area/${id}` : `/configuracion/tipo/${id}`;
            document.getElementById('modal-method').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('modal-input').value = valor;
            document.getElementById('modal-titulo').innerText = isArea ? 'Editar Área UPSS' : 'Editar Tipo de Equipo';
        }

        function mostrarModalAnimation() {
            const m = document.getElementById('modal-cat'); const c = document.getElementById('modal-content');
            m.classList.remove('hidden'); setTimeout(() => { m.classList.remove('opacity-0'); c.classList.remove('scale-95'); }, 10);
        }

        function cerrarModal() {
            const m = document.getElementById('modal-cat'); const c = document.getElementById('modal-content');
            m.classList.add('opacity-0'); c.classList.add('scale-95'); setTimeout(() => { m.classList.add('hidden'); }, 200);
        }

        function confirmarBorrado(button) {
            Swal.fire({
                title: '¿Retirar de los catálogos?', text: "Si está en uso, la base de datos protegerá el registro automáticamente.", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280', confirmButtonText: 'Sí, borrar', cancelButtonText: 'Cancelar'
            }).then((res) => { if (res.isConfirmed) { button.closest('form').submit(); } });
        }
    </script>
</body>
</html>