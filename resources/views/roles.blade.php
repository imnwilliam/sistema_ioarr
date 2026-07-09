<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfiles y Permisos - Sistema IOARR Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    <main class="flex-1 flex flex-col overflow-y-auto relative">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 sticky top-0 z-10 shadow-sm">
            <h2 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-user-shield text-amber-500 mr-2"></i> Gestión de Perfiles y Roles</h2>
            <button onclick="abrirModalRol()" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2 px-4 rounded-lg shadow-sm smooth-transition">
                <i class="fa-solid fa-plus mr-2"></i> Nuevo Perfil
            </button>
        </header>

        <div class="p-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-4">
                <table id="tabla-roles" class="min-w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 uppercase bg-amber-50/50">
                        <tr>
                            <th class="px-4 py-3">Nombre del Rol / Perfil</th>
                            <th class="px-4 py-3">Descripción</th>
                            <th data-sortable="false" class="text-center px-4 py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $rol)
                            <tr class="hover:bg-gray-50 smooth-transition border-b border-gray-100">
                                <td class="px-4 py-4 font-bold text-gray-900 text-base">
                                    <i class="fa-solid fa-id-badge text-amber-500 mr-2 opacity-50"></i> {{ $rol->nombre_rol }}
                                </td>
                                <td class="px-4 py-4 text-gray-500">{{ $rol->descripcion ?? 'Sin descripción asignada' }}</td>
                                <td class="px-4 py-4 text-center flex justify-center gap-2">
                                    <button onclick="editarRol({{ json_encode($rol) }})" class="text-blue-600 hover:bg-blue-100 p-2 rounded-lg smooth-transition" title="Editar Permisos">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    @if($rol->id != 1)
                                    <form action="{{ route('roles.destroy', $rol->id) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmarEliminacion(this)" class="text-red-600 hover:bg-red-100 p-2 rounded-lg smooth-transition" title="Eliminar">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    @include('roles.modal-rol')
    @include('roles.scripts')

</body>
</html>