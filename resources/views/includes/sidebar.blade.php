@php
    $id_rol = auth()->user()->id_rol ?? 2;
    $nombre_rol = \Illuminate\Support\Facades\DB::table('roles')->where('id', $id_rol)->value('nombre_rol');

    $menu_opciones = \Illuminate\Support\Facades\DB::table('opciones')
        ->join('permisos', 'opciones.id', '=', 'permisos.id_opcion')
        ->where('permisos.id_rol', $id_rol)
        ->orderBy('opciones.orden', 'asc')
        ->select('opciones.*')
        ->get();
@endphp

<aside class="w-64 bg-slate-900 text-white flex flex-col shadow-2xl z-20">
    <div class="p-6 text-center border-b border-slate-800">
        <h1 class="text-xl font-bold tracking-tight text-blue-400">IOARR MANAGER</h1>
    </div>
    
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        @foreach($menu_opciones as $opcion)
            @php
                $es_activa = request()->is($opcion->url . '*');
                $clase = $es_activa 
                    ? 'bg-blue-600 text-white shadow-lg' 
                    : 'text-slate-400 hover:bg-slate-800 hover:text-white';
            @endphp
            
            <a href="{{ url($opcion->url) }}" class="flex items-center px-4 py-3 rounded-xl transition-all {{ $clase }}">
                <i class="fa-solid {{ $opcion->icono }} w-6 text-lg"></i> 
                <span class="ml-2 font-medium">{{ $opcion->nombre_opcion }}</span>
            </a>
        @endforeach
    </nav>

    <div class="p-4 border-t border-slate-800 bg-slate-900/50">
        <div class="flex items-center justify-between p-2 mb-4 bg-slate-800/40 rounded-xl border border-slate-800/60">
            <div class="flex items-center min-w-0">
                <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold shrink-0 shadow-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="ml-3 min-w-0">
                    <p class="text-sm font-semibold truncate text-slate-200">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-slate-500 font-bold uppercase truncate tracking-wider">{{ $nombre_rol }}</p>
                </div>
            </div>
            <a href="{{ route('perfil.index') }}" class="p-1.5 text-slate-500 hover:text-blue-400 hover:bg-slate-800 rounded-lg transition-all" title="Mi Perfil">
                <i class="fa-solid fa-user-gear text-sm"></i>
            </a>
        </div>
        
        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf 
            <button type="submit" class="flex items-center justify-center w-full py-2.5 text-sm text-red-400 border border-red-400/20 rounded-xl hover:bg-red-500 hover:text-white transition-all font-semibold shadow-sm">
                <i class="fa-solid fa-power-off mr-2"></i> Cerrar Sesión
            </button>
        </form>
    </div>
</aside>