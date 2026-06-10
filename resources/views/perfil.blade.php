<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Configuración de Cuenta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    @include('includes.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center px-8 sticky top-0 z-10 shadow-sm">
            <h2 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-user-gear text-blue-600 mr-2"></i> Mi Perfil de Cuenta</h2>
        </header>

        <div class="p-8 max-w-2xl">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 bg-slate-900 text-white flex items-center gap-4">
                    <div class="w-16 h-16 rounded-xl bg-blue-600 flex items-center justify-center text-2xl font-bold text-white shadow-md">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold tracking-tight">{{ auth()->user()->name }}</h3>
                        <p class="text-xs text-slate-400 font-medium mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <form action="{{ route('perfil.update') }}" method="POST" class="p-8 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nombre Completo en Sistema</label>
                        <input type="text" name="name" value="{{ auth()->user()->name }}" required class="w-full border border-gray-300 rounded-xl p-3 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-medium text-gray-800">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-400 mb-1">Correo Electrónico (No editable)</label>
                        <input type="email" disabled value="{{ auth()->user()->email }}" class="w-full border border-gray-200 rounded-xl p-3 bg-gray-50 font-medium text-gray-400 cursor-not-allowed">
                    </div>

                    <div class="border-t pt-4 mt-6">
                        <h4 class="font-bold text-sm text-gray-800 mb-4"><i class="fa-solid fa-key text-amber-500 mr-1"></i> Actualizar Seguridad (Opcional)</h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Contraseña Actual</label>
                                <input type="password" name="current_password" placeholder="Requerida solo si vas a cambiar tu contraseña" class="w-full border border-gray-300 rounded-xl p-3 outline-none focus:border-blue-500 text-sm">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Nueva Contraseña</label>
                                    <input type="password" name="new_password" placeholder="Mínimo 6 caracteres" class="w-full border border-gray-300 rounded-xl p-3 outline-none focus:border-blue-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Confirmar Nueva Contraseña</label>
                                    <input type="password" name="new_password_confirmation" placeholder="Repite la contraseña" class="w-full border border-gray-300 rounded-xl p-3 outline-none focus:border-blue-500 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-md shadow-blue-600/10 transition-all transform hover:-translate-y-0.5">
                            <i class="fa-solid fa-floppy-disk mr-2"></i> Actualizar Mis Datos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    @if(session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: "{{ session('success') }}", showConfirmButton: false, timer: 4000, timerProgressBar: true });</script>
    @endif
    @if(session('error'))
        <script>Swal.fire({ icon: 'error', title: 'Error de validación', text: "{{ session('error') }}", confirmButtonColor: '#3b82f6' });</script>
    @endif
    @if($errors->any())
        <script>Swal.fire({ icon: 'warning', title: 'Atención', text: 'Asegúrate de que las contraseñas coincidan y tengan un mínimo de 6 letras.', confirmButtonColor: '#3b82f6' });</script>
    @endif
</body>
</html>