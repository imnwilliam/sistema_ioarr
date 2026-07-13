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

        .datatable-top, .dataTable-top { padding-bottom: 1rem; }
        .dataTable-input { border-radius: 0.5rem; border: 1.5px solid #d1d5db; padding: 0.5rem 0.75rem; outline: none; transition: all 0.25s ease;}
        .dataTable-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15); }
        .dataTable-selector { border-radius: 0.5rem; border: 1.5px solid #d1d5db; padding: 0.4rem 1.8rem 0.4rem 0.75rem; outline: none; }
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
                            <th>Nombre de Usuario</th>
                            <th class="text-center">Rol Asignado</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Fecha de Creación</th>
                            <th data-sortable="false" class="text-center">Acciones</th>
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
                                <td class="px-4 py-3 font-medium text-gray-700">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($user->id_rol == 1)
                                        <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full"><i class="fa-solid fa-shield-halved mr-1"></i> {{ $user->nombre_rol }}</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full"><i class="fa-solid fa-eye mr-1"></i> {{ $user->nombre_rol ?? 'Sin Rol' }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($user->estado == 1)
                                        <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1 rounded-full"><i class="fa-solid fa-circle-check mr-1"></i> Activo</span>
                                    @else
                                        <span class="bg-gray-200 text-gray-600 text-xs font-bold px-3 py-1 rounded-full"><i class="fa-solid fa-circle-xmark mr-1"></i> Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-gray-500">{{ date('d/m/Y', strtotime($user->created_at)) }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex justify-center gap-1">
                                        <button onclick="editarUsuario({{ json_encode($user) }})" class="text-blue-600 hover:bg-blue-100 p-2 rounded-lg smooth-transition" title="Editar">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button onclick="abrirModalReset({{ $user->id }}, '{{ $user->name }}')" class="text-amber-600 hover:bg-amber-100 p-2 rounded-lg smooth-transition" title="Restablecer contraseña">
                                            <i class="fa-solid fa-key"></i>
                                        </button>
                                        @if($user->id != 1)
                                            @if($user->estado == 1)
                                                <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" class="inline form-toggle-estado">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmarDesactivacion(this)" class="text-red-600 hover:bg-red-100 p-2 rounded-lg smooth-transition" title="Desactivar">
                                                        <i class="fa-solid fa-user-slash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('usuarios.activar', $user->id) }}" method="POST" class="inline">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="text-emerald-600 hover:bg-emerald-100 p-2 rounded-lg smooth-transition" title="Reactivar">
                                                        <i class="fa-solid fa-user-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Crear/Editar Usuario -->
    <div id="modal-usuario" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="modal-content">
            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg" id="titulo-modal-usuario"><i class="fa-solid fa-user-plus mr-2"></i> Crear Nuevo Usuario</h3>
                <button type="button" onclick="cerrarModalUsuario()" class="text-white hover:text-gray-200"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>

            <form id="form-usuario" action="{{ route('usuarios.store') }}" method="POST" class="p-6 space-y-4" onsubmit="mostrarSpinner(this, 'btn-guardar')">
                @csrf
                <div id="method-put-usuario"></div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre Completo *</label>
                    <input type="text" name="name" id="inp_nombre" required pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo se permiten letras y espacios" placeholder="Ej. Juan Pérez" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 smooth-transition">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nombre de Usuario *</label>
                    <input type="text" name="email" id="inp_email" required placeholder="Ej. jperez" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 smooth-transition">
                </div>

                <div id="campos-password" class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Contraseña *</label>
                        <input type="password" name="password" id="inp_password" minlength="6" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 smooth-transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Confirmar Contraseña *</label>
                        <input type="password" name="password_confirmation" id="inp_password_confirmation" minlength="6" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 smooth-transition">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Rol de Acceso *</label>
                        <select name="id_rol" id="inp_rol" required placeholder="Seleccionar Rol...">
                            <option value="">Seleccionar Rol...</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->id }}">{{ $rol->nombre_rol }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="campo-estado" class="hidden">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Estado *</label>
                        <select name="estado" id="inp_estado" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 smooth-transition">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
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

    <!-- Modal Restablecer Contraseña -->
    <div id="modal-reset" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300 opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden flex flex-col transform scale-95 transition-transform duration-300" id="modal-reset-content">
            <div class="bg-amber-500 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg"><i class="fa-solid fa-key mr-2"></i> Restablecer Contraseña</h3>
                <button type="button" onclick="cerrarModalReset()" class="text-white hover:text-amber-100"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>

            <form id="form-reset" method="POST" class="p-6 space-y-4" onsubmit="mostrarSpinner(this, 'btn-reset')">
                @csrf
                @method('PUT')
                <p class="text-sm text-gray-600">Nueva contraseña para <span id="reset-nombre-usuario" class="font-bold"></span>:</p>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Nueva Contraseña *</label>
                    <input type="password" name="password" required minlength="6" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 smooth-transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirmar Nueva Contraseña *</label>
                    <input type="password" name="password_confirmation" required minlength="6" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 smooth-transition">
                </div>

                <div class="pt-4 flex justify-end space-x-3 border-t mt-6">
                    <button type="button" onclick="cerrarModalReset()" class="px-5 py-2.5 text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium smooth-transition">Cancelar</button>
                    <button type="submit" id="btn-reset" class="px-6 py-2.5 text-white bg-amber-500 hover:bg-amber-600 rounded-lg font-bold shadow-md smooth-transition flex items-center">
                        <i class="fa-solid fa-key mr-2"></i> Restablecer
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
    @if($errors->any())
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: "{{ $errors->first() }}", showConfirmButton: false, timer: 5000, timerProgressBar: true });</script>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            new simpleDatatables.DataTable("#tabla-usuarios", {
                searchable: true, fixedHeight: true,
                labels: { placeholder: "Buscar usuario...", perPage: "filas por página", noRows: "No hay usuarios registrados", info: "Mostrando del {start} al {end} de {rows} entradas" }
            });

            new TomSelect("#inp_rol", { create: false });

            const inpNombre = document.getElementById('inp_nombre');
            if(inpNombre) {
                inpNombre.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                });
            }
        });

        function abrirModalUsuario() {
            document.getElementById('form-usuario').reset();
            document.getElementById('form-usuario').action = "{{ route('usuarios.store') }}";
            document.getElementById('method-put-usuario').innerHTML = '';
            document.getElementById('titulo-modal-usuario').innerHTML = '<i class="fa-solid fa-user-plus mr-2"></i> Crear Nuevo Usuario';

            // En creación sí se pide contraseña
            document.getElementById('campos-password').classList.remove('hidden');
            document.getElementById('inp_password').required = true;
            document.getElementById('inp_password_confirmation').required = true;
            document.getElementById('campo-estado').classList.add('hidden');

            const modal = document.getElementById('modal-usuario');
            const content = document.getElementById('modal-content');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        }

        function editarUsuario(usuario) {
            document.getElementById('form-usuario').reset();
            document.getElementById('inp_nombre').value = usuario.name;
            document.getElementById('inp_email').value = usuario.email;
            document.getElementById('inp_rol').tomselect.setValue(usuario.id_rol);
            document.getElementById('inp_estado').value = usuario.estado;

            document.getElementById('form-usuario').action = `/usuarios/${usuario.id}`;
            document.getElementById('method-put-usuario').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('titulo-modal-usuario').innerHTML = '<i class="fa-solid fa-pen-to-square mr-2"></i> Editar Usuario: ' + usuario.name;

            // En edición no se toca la contraseña desde aquí (para eso está "Restablecer")
            document.getElementById('campos-password').classList.add('hidden');
            document.getElementById('inp_password').required = false;
            document.getElementById('inp_password_confirmation').required = false;
            document.getElementById('campo-estado').classList.remove('hidden');

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

        function abrirModalReset(id, nombre) {
            document.getElementById('form-reset').action = `/usuarios/${id}/reset-password`;
            document.getElementById('reset-nombre-usuario').innerText = nombre;

            const modal = document.getElementById('modal-reset');
            const content = document.getElementById('modal-reset-content');
            modal.classList.remove('hidden');
            setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
        }

        function cerrarModalReset() {
            const modal = document.getElementById('modal-reset');
            const content = document.getElementById('modal-reset-content');
            modal.classList.add('opacity-0'); content.classList.add('scale-95');
            setTimeout(() => { modal.classList.add('hidden'); }, 300);
        }

        function confirmarDesactivacion(btn) {
            Swal.fire({
                title: '¿Desactivar este usuario?', text: "El usuario no podrá iniciar sesión hasta que sea reactivado.", icon: 'warning',
                showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa-solid fa-user-slash mr-1"></i> Sí, desactivar'
            }).then((result) => {
                if (result.isConfirmed) btn.closest('form').submit();
            });
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