<div id="modal-rol" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden flex flex-col max-h-[90vh] transform scale-95 transition-transform duration-300" id="modal-content">
        <div class="bg-amber-500 px-6 py-4 flex justify-between items-center text-white">
            <h3 class="font-bold text-lg" id="titulo-modal"><i class="fa-solid fa-user-shield mr-2"></i> Crear Nuevo Perfil</h3>
            <button type="button" onclick="cerrarModalRol()" class="text-white hover:text-amber-200"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        
        <form id="form-rol" action="{{ route('roles.store') }}" method="POST" class="p-6 overflow-y-auto space-y-6" onsubmit="mostrarSpinner(this, 'btn-guardar')">
            @csrf
            <div id="method-put"></div> 
            
            <div class="grid grid-cols-2 gap-5 bg-gray-50 p-4 rounded-xl border border-gray-100">
                <div class="col-span-1">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Rol *</label>
                    <input type="text" name="nombre_rol" id="inp_nombre" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 smooth-transition" placeholder="Ej: Especialista de Equipos">
                </div>
                <div class="col-span-1">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Descripción</label>
                    <input type="text" name="descripcion" id="inp_desc" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 smooth-transition" placeholder="Ej: Solo puede ver las inversiones...">
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl overflow-hidden">
                <div class="bg-slate-100 px-4 py-3 border-b border-gray-200">
                    <h4 class="font-bold text-slate-700 text-sm tracking-wide uppercase"><i class="fa-solid fa-sliders mr-2"></i> Configuración de Permisos</h4>
                    <p class="text-xs text-slate-500 mt-1">Selecciona qué módulos puede ver y si tiene permisos para modificar (crear/editar/eliminar) información en ellos.</p>
                </div>
                <table class="min-w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-white border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 font-bold">Módulo del Sistema</th>
                            <th class="px-6 py-3 text-center font-bold text-blue-600"><i class="fa-regular fa-eye mr-1"></i> Ver (Lector)</th>
                            <th class="px-6 py-3 text-center font-bold text-emerald-600"><i class="fa-solid fa-pen mr-1"></i> Modificar (Editor)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach($opciones as $op)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-3 font-medium text-gray-700">
                                    <i class="fa-solid {{ $op->icono }} text-slate-400 w-5"></i> {{ $op->nombre_opcion }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <input type="checkbox" name="permisos[{{ $op->id }}][lector]" id="chk_lector_{{ $op->id }}" class="chk-lector w-4 h-4 text-blue-600 rounded focus:ring-blue-500 cursor-pointer" onchange="validarPermisos(this, 'lector', {{ $op->id }})">
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <input type="checkbox" name="permisos[{{ $op->id }}][editor]" id="chk_editor_{{ $op->id }}" class="chk-editor w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500 cursor-pointer" onchange="validarPermisos(this, 'editor', {{ $op->id }})">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-4 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="cerrarModalRol()" class="px-5 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium smooth-transition">Cancelar</button>
                <button type="submit" id="btn-guardar" class="px-6 py-2.5 text-white bg-amber-500 hover:bg-amber-600 rounded-lg font-bold shadow-md smooth-transition flex items-center">
                    <i class="fa-solid fa-save mr-2"></i> Guardar Perfil
                </button>
            </div>
        </form>
    </div>
</div>