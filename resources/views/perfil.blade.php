<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Configuración de Cuenta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .smooth-transition { transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-gray-50 flex h-screen overflow-hidden">

    @include('includes.sidebar')

    <main class="flex-1 flex flex-col overflow-y-auto relative">
        <header class="h-20 shrink-0 bg-white border-b border-gray-200 flex items-center px-8 sticky top-0 z-30 shadow-sm">
            <h2 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-user-gear text-blue-600 mr-2"></i> Mi Perfil de Cuenta</h2>
        </header>

        <div class="p-8 flex justify-center items-start">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden w-full max-w-2xl">
                
                <div class="p-8 bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white flex items-center gap-5 border-b border-slate-700">
                    <div class="w-16 h-16 rounded-2xl bg-blue-600 flex items-center justify-center text-2xl font-black text-white shadow-lg ring-4 ring-blue-500/20">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-2xl font-extrabold tracking-tight">{{ auth()->user()->name }}</h3>
                        <p class="text-sm text-slate-400 font-semibold mt-0.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-user-shield text-blue-400"></i> Cuenta de Acceso Activa
                        </p>
                    </div>
                </div>

                <form action="{{ route('perfil.update') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1.5">Nombre Completo en Sistema</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-signature text-gray-400 group-focus-within:text-blue-500 smooth-transition"></i>
                                </div>
                                <input type="text" name="name" value="{{ auth()->user()->name }}" required 
                                    class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-medium text-gray-800 bg-gray-50/50 focus:bg-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-400 mb-1.5">Nombre de Usuario (No Editable)</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-user-lock text-gray-400"></i>
                                </div>
                                <input type="text" disabled value="{{ auth()->user()->email }}" 
                                    class="w-full pl-11 pr-4 py-3 border border-gray-200 rounded-xl bg-gray-100 font-medium text-gray-400 cursor-not-allowed">
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6 mt-6">
                        <h4 class="font-bold text-sm text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-key text-amber-500"></i> Cambiar Contraseña (Opcional)
                        </h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Contraseña Actual</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-lock text-gray-400 group-focus-within:text-blue-500 smooth-transition"></i>
                                    </div>
                                    <input type="password" name="current_password" placeholder="Requerida solo si vas a cambiar tu contraseña actual" 
                                        class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm smooth-transition">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Nueva Contraseña</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-lock-open text-gray-400 group-focus-within:text-blue-500 smooth-transition"></i>
                                        </div>
                                        <input type="password" name="new_password" placeholder="Mínimo 6 caracteres" 
                                            class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm smooth-transition">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1.5 uppercase tracking-wide">Confirmar Nueva Contraseña</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-shield text-gray-400 group-focus-within:text-blue-500 smooth-transition"></i>
                                        </div>
                                        <input type="password" name="new_password_confirmation" placeholder="Repite la contraseña" 
                                            class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 text-sm smooth-transition">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-5 border-t border-gray-100 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl shadow-md shadow-blue-600/20 transition-all transform hover:-translate-y-0.5 flex items-center">
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