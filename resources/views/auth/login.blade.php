<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - IOARR MANAGER</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <style> 
        body { font-family: 'Inter', sans-serif; } 
        .smooth-transition { transition: all 0.3s ease; }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    
    <!-- Efectos de luces de fondo (Glow) -->
    <div class="absolute top-[-15%] left-[-10%] w-[500px] h-[500px] bg-blue-600 rounded-full mix-blend-screen filter blur-[120px] opacity-20 animate-pulse"></div>
    <div class="absolute bottom-[-15%] right-[-10%] w-[500px] h-[500px] bg-purple-600 rounded-full mix-blend-screen filter blur-[120px] opacity-20 animate-pulse"></div>

    <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl overflow-hidden z-10 transform transition-all hover:scale-[1.01] duration-500">
        
        <!-- Cabecera del Login -->
        <div class="bg-slate-900 p-8 text-center border-b-4 border-blue-600 relative overflow-hidden">
            <!-- Patrón sutil de fondo en la cabecera -->
            <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 20px 20px;"></div>
            
            <h1 class="text-3xl font-black text-white tracking-tight relative z-10">
                <i class="fa-solid fa-chart-pie text-blue-500 mr-2"></i> IOARR <span class="text-blue-500">MANAGER</span>
            </h1>
            <p class="text-slate-400 text-sm mt-2 font-medium relative z-10">Sistema de Control de Inversiones y Equipos</p>
        </div>

        <!-- Formulario -->
        <div class="p-8">
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Manejo de Errores -->
                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm font-bold border border-red-100 flex items-center">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 mr-3 text-lg"></i>
                        Credenciales incorrectas. Verifica tus datos.
                    </div>
                @endif

                <!-- Campo Correo -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" for="email">Correo Electrónico</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-gray-400 group-focus-within:text-blue-500 smooth-transition"></i>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full pl-11 pr-4 py-3.5 border border-gray-300 rounded-xl outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 smooth-transition text-gray-700 font-medium bg-gray-50 focus:bg-white"
                            placeholder="ejemplo@ioarr.com">
                    </div>
                </div>

                <!-- Campo Contraseña -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2" for="password">Contraseña</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-gray-400 group-focus-within:text-blue-500 smooth-transition"></i>
                        </div>
                        <input id="password" type="password" name="password" required
                            class="w-full pl-11 pr-4 py-3.5 border border-gray-300 rounded-xl outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 smooth-transition text-gray-700 font-medium bg-gray-50 focus:bg-white"
                            placeholder="••••••••">
                    </div>
                </div>

                <!-- Recordarme -->
                <div class="flex items-center justify-between mt-2">
                    <label class="flex items-center cursor-pointer group">
                        <div class="relative flex items-center justify-center w-5 h-5 mr-2">
                            <input id="remember_me" type="checkbox" name="remember" class="peer appearance-none w-5 h-5 border-2 border-gray-300 rounded-md checked:bg-blue-600 checked:border-blue-600 smooth-transition cursor-pointer">
                            <i class="fa-solid fa-check text-white text-xs absolute pointer-events-none opacity-0 peer-checked:opacity-100 smooth-transition"></i>
                        </div>
                        <span class="text-sm text-gray-600 font-medium group-hover:text-gray-900 smooth-transition">Mantener sesión iniciada</span>
                    </label>
                </div>

                <!-- Botón Submit -->
                <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 rounded-xl shadow-lg shadow-blue-600/30 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 hover:shadow-blue-600/40 focus:outline-none focus:ring-4 focus:ring-blue-500/50 transform hover:-translate-y-0.5 smooth-transition">
                    Acceder al Sistema <i class="fa-solid fa-arrow-right-to-bracket ml-2"></i>
                </button>
            </form>
        </div>
        
        <!-- Pie del Modal -->
        <div class="bg-gray-50 p-5 text-center text-xs font-semibold text-gray-400 border-t border-gray-100">
            &copy; {{ date('Y') }} IOARR - Filial.
        </div>
    </div>

</body>
</html>