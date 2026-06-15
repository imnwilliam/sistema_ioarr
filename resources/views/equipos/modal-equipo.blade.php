<div id="modal-equipo" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden flex flex-col max-h-[90vh] transform scale-95 transition-transform duration-300" id="modal-content">
        <div class="bg-purple-600 px-6 py-4 flex justify-between items-center text-white">
            <h3 class="font-bold text-lg" id="titulo-modal"><i class="fa-solid fa-microscope mr-2"></i> Registrar Equipo</h3>
            <button type="button" onclick="cerrarModalEquipo()" class="text-white hover:text-gray-200"><i class="fa-solid fa-xmark text-xl"></i></button>
        </div>
        
        <form id="form-equipo" action="{{ route('equipos.store') }}" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-5" onsubmit="mostrarSpinner(this, 'btn-guardar')">
            @csrf
            <div id="method-put"></div> 
            
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Vincular a IOARR (CUI) *</label>
                    <select name="id_inversion" id="inp_id_inversion" required placeholder="Buscar CUI o Nombre...">
                        <option value="">Buscar CUI o Nombre...</option>
                        @foreach($inversiones as $inv)
                            <option value="{{ $inv->id }}">{{ $inv->cui }} - {{ $inv->nombre_inversion }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">UPSS *</label>
                    <select name="id_upss" id="inp_id_upss" required placeholder="Buscar UPSS...">
                        <option value="">Buscar Área...</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->nombre_upss }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-5">
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nombre del Equipo *</label>
                    <input type="text" name="nombre_equipo" id="inp_nombre" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 smooth-transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Tipo de Equipo *</label>
                    <select name="tipo_equipo" id="inp_tipo" required placeholder="Seleccionar..." class="w-full border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500">
                        <option value="">Seleccionar...</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->nombre_tipo }}">{{ $tipo->nombre_tipo }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Servicio</label>
                    <input type="text" name="servicio" id="inp_servicio" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ambiente</label>
                    <input type="text" name="ambiente" id="inp_ambiente" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">N° Expediente</label>
                    <input type="text" name="expediente" id="inp_expediente" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500">
                </div>
                
                <div class="col-span-3">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Añadir Evidencias (PDFs)</label>
                    <input type="file" name="archivos_evidencia[]" multiple accept=".pdf" class="w-full border border-gray-300 rounded-lg p-2 text-sm outline-none focus:border-purple-500 bg-gray-50 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer">
                    <p class="text-[10px] text-gray-400 mt-1">Puedes seleccionar varios archivos a la vez manteniendo presionada la tecla Ctrl.</p>
                    
                    <div id="contenedor-archivos" class="hidden mt-4 p-4 bg-slate-50 rounded-xl border border-slate-200">
                        <p class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-wide"><i class="fa-solid fa-folder-open mr-1"></i> Documentos Previamente Subidos</p>
                        <ul id="lista-archivos-existentes" class="space-y-2 grid grid-cols-2 gap-2"></ul>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                <h4 class="font-bold text-gray-700 mb-3"><i class="fa-solid fa-coins text-amber-500 mr-2"></i> Datos de Costo</h4>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cantidad *</label>
                        <input type="number" name="cantidad" id="inp_cantidad" min="1" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500 text-lg font-bold">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Precio Unitario (S/) *</label>
                        <input type="number" name="precio_unitario" id="inp_precio" step="0.01" required class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500 text-lg font-bold text-blue-700">
                    </div>
                </div>
            </div>

            <div class="col-span-3 mt-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Estado Situacional / Acciones</label>
                <textarea name="estado_situacional" id="inp_estado_situacional" rows="3" class="w-full border border-gray-300 rounded-lg p-2.5 outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 smooth-transition" placeholder="Escribe aquí las observaciones, acciones tomadas o el estado situacional del equipo..."></textarea>
            </div>

            <div class="pt-2 flex justify-end space-x-3 mt-4">
                <button type="button" onclick="cerrarModalEquipo()" class="px-5 py-2.5 text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium smooth-transition">Cancelar</button>
                <button type="submit" id="btn-guardar" class="px-6 py-2.5 text-white bg-purple-600 hover:bg-purple-700 rounded-lg font-bold shadow-md smooth-transition flex items-center">
                    <i class="fa-solid fa-save mr-2"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>