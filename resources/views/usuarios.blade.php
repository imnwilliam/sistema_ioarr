<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios - Sistema IOARR Pro</title>
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
        .ts-control.focus { border-color: #2563eb; box-shadow: 0 0 0 1px #2563eb; }
        .dataTable-input { border-radius: 0.5rem; border: 1px solid #d1d5db; padding: 0.5rem; outline: none; }
        .dataTable-input:focus { border-color: #2563eb; }
        .dataTable-selector { border-radius: 0.5rem; border: 1px solid #d1d5db; padding: 0.3rem; }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    @include('includes.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto relative">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-10 shadow-sm">
            <h2 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-users text-blue-600 mr-2"></i> Gestión de Usuarios</h2>
            <div class="flex items-center gap-4">
                <button onclick="abrirModalUsuario()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm smooth-transition">
                    <i class="fa-solid fa-user-plus mr-2"></i> Nuevo Usuario
                </button>
            </div>
        </header>

        <div class="p-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-4">
                <table id="tabla-usuarios" class="min-w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th>Usuario (Nombre)</th>
                            <th>Correo Electrónico</th>
                            <th class="text-center">Rol Asignado</th>
                            <th class="text-center">Fecha de Creación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $user)
                            <tr class="hover:bg-gray-50 smooth-transition border-b">
                                <td class="px-4 py-3 font-bold text-gray-900 flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold mr-3">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    {{ $user->name }}
                                </td>
                                <td class="px-4 py-3">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($user->id_rol == 1)
                                        <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full"><i class="fa-solid fa-shield-halved mr-1"></i> {{ $user->nombre_rol }}</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full"><i class="fa-solid fa-eye mr-1"></i> {{ $user->nombre_rol ?? 'Sin Rol' }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-gray-500">{{ date('d/m/Y', strtotime($user->created_at)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="modal-usuario" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="modal-content">
            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg"><i class="fa-solid fa-user-plus mr-2"></i> Crear Nuevo Usuario</h3>
                <button type="button" onclick="cerrarModalUsuario()" class="text-white hover:text-gray-200"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            
            <form action="{{ route('usuarios.store') }}" method="POST" class="p-6 space-y-4" onsubmit="mostrarSpinner(this, 'btn-guardar')">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre Completo *</label>
                    <input type="text" name="name" required placeholder="Ej. Juan Pérez" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 smooth-transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Correo Electrónico *</label>
                    <input type="email" name="email" required placeholder="ejemplo@ioarr.com" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 smooth-transition">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Contraseña *</label>
                        <input type="password" name="password" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 smooth-transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Rol de Acceso *</label>
                        <select name="id_rol" id="inp_rol" required placeholder="Seleccionar Rol...">
                            <option value="">Seleccionar Rol...</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}">{{ $rol->nombre_rol }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex justify-end space-x-3 border-t mt-6">
                    <button type="button" onclick="cerrarModalUsuario()" class="px-5 py-2.5 text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium smooth-transition">Cancelar</button>
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
    @if($errors->any())
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: "Error al crear el usuario. Revisa los datos.", showConfirmButton: false, timer: 5000, timerProgressBar: true });</script>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Inicializar DataTables
            new simpleDatatables.DataTable("#tabla-usuarios", {
                searchable: true, fixedHeight: true,
                labels: { placeholder: "Buscar usuario...", perPage: "filas por página", noRows: "No hay usuarios registrados", info: "Mostrando {start} a {end} de {rows}" }
            });

            // Inicializar Selector de Rol con buscador
            new TomSelect("#inp_rol", { create: false });
        });

        function abrirModalUsuario() { 
            const modal = document.getElementById('modal-usuario');
            const content = document.getElementById('modal-content');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        }

        function cerrarModalUsuario() { 
            const modal = document.getElementById('modal-usuario');
            const content = document.getElementById('modal-content');
            modal.classList.add('opacity-0'); content.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        }

        function mostrarSpinner(form, btnId) {
            const btn = document.getElementById(btnId);
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Creando...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');
        }
    </script>
</body>
</html>