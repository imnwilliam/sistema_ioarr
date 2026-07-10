<div id="modal-cronograma" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 flex items-center justify-center backdrop-blur-sm transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl overflow-hidden flex flex-col transform scale-95 transition-transform duration-300 max-h-[90vh]" id="modal-crono-content">
        <div class="bg-emerald-600 px-6 py-4 flex justify-between items-center text-white shrink-0">
            <div>
                <h3 class="font-bold text-lg"><i class="fa-solid fa-calendar-check mr-2"></i> Cronograma SEACE</h3>
                <p class="text-xs text-emerald-100 mt-1">Equipo: <span id="lbl_nombre_equipo" class="font-bold uppercase tracking-wide"></span></p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="exportarPDFCronograma(this)" title="Exportar PDF"
                    class="btn-exportar bg-white/20 hover:bg-white/30 text-white font-bold text-xs px-3 py-1.5 rounded-lg smooth-transition flex items-center gap-1.5">
                    <i class="fa-solid fa-file-pdf"></i> PDF
                </button>
                <button type="button" onclick="exportarExcelCronograma(this)" title="Exportar Excel"
                    class="btn-exportar bg-white/20 hover:bg-white/30 text-white font-bold text-xs px-3 py-1.5 rounded-lg smooth-transition flex items-center gap-1.5">
                    <i class="fa-solid fa-file-excel"></i> Excel
                </button>
                <button onclick="cerrarCronograma()" class="text-white hover:text-gray-200 transition-colors ml-1"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
        </div>
        
        <form action="{{ route('cronogramas.store') }}" method="POST" class="flex flex-col overflow-hidden" onsubmit="mostrarSpinner(this, 'btn-guardar-crono')">
            @csrf
            <input type="hidden" name="id_equipo" id="crono_id_equipo">
            
            <div class="p-6 overflow-y-auto bg-gray-50 flex-1">
                <table id="tabla-cronograma-modal" class="min-w-full text-sm text-left border border-gray-200 rounded-xl bg-white shadow-sm">
                    <thead class="bg-slate-100 border-b border-gray-200 text-slate-700 uppercase text-[10px] font-extrabold tracking-wider">
                        <tr>
                            <th class="px-4 py-3">Etapa del Proceso</th>
                            <th class="px-4 py-3 w-48 text-center">Fecha y Hora Inicio</th>
                            <th class="px-4 py-3 w-48 text-center">Fecha y Hora Fin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="hover:bg-emerald-50/50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-gray-700 text-xs">
                                Convocatoria
                                <input type="hidden" name="etapas[convocatoria][nombre]" value="Convocatoria">
                            </td>
                            <td class="px-2 py-2"><input type="datetime-local" id="inicio_convocatoria" name="etapas[convocatoria][inicio]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                            <td class="px-2 py-2"><input type="datetime-local" id="fin_convocatoria" name="etapas[convocatoria][fin]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                        </tr>
                        <tr class="hover:bg-emerald-50/50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-gray-700 text-xs">
                                Registro de participantes (Electrónica)
                                <input type="hidden" name="etapas[registro][nombre]" value="Registro de participantes(Electronica)">
                            </td>
                            <td class="px-2 py-2"><input type="datetime-local" id="inicio_registro" name="etapas[registro][inicio]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                            <td class="px-2 py-2"><input type="datetime-local" id="fin_registro" name="etapas[registro][fin]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                        </tr>
                        <tr class="hover:bg-emerald-50/50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-gray-700 text-xs">
                                Formulación de consultas y observaciones (Electrónica)
                                <input type="hidden" name="etapas[formulacion][nombre]" value="Formulación de consultas y observaciones(Electronica)">
                            </td>
                            <td class="px-2 py-2"><input type="datetime-local" id="inicio_formulacion" name="etapas[formulacion][inicio]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                            <td class="px-2 py-2"><input type="datetime-local" id="fin_formulacion" name="etapas[formulacion][fin]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                        </tr>
                        <tr class="hover:bg-emerald-50/50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-gray-700 text-xs">
                                Absolución de consultas y observaciones (Electrónica)
                                <input type="hidden" name="etapas[absolucion][nombre]" value="Absolución de consultas y observaciones(Electronica)">
                            </td>
                            <td class="px-2 py-2"><input type="datetime-local" id="inicio_absolucion" name="etapas[absolucion][inicio]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                            <td class="px-2 py-2"><input type="datetime-local" id="fin_absolucion" name="etapas[absolucion][fin]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                        </tr>
                        <tr class="hover:bg-emerald-50/50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-gray-700 text-xs">
                                Integración de las Bases SEACE
                                <input type="hidden" name="etapas[integracion][nombre]" value="Integración de las Bases SEACE">
                            </td>
                            <td class="px-2 py-2"><input type="datetime-local" id="inicio_integracion" name="etapas[integracion][inicio]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                            <td class="px-2 py-2"><input type="datetime-local" id="fin_integracion" name="etapas[integracion][fin]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                        </tr>
                        <tr class="hover:bg-emerald-50/50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-gray-700 text-xs">
                                Presentación de propuestas (Electrónica)
                                <input type="hidden" name="etapas[presentacion][nombre]" value="Presentación de propuestas(Electronica)">
                            </td>
                            <td class="px-2 py-2"><input type="datetime-local" id="inicio_presentacion" name="etapas[presentacion][inicio]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                            <td class="px-2 py-2"><input type="datetime-local" id="fin_presentacion" name="etapas[presentacion][fin]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                        </tr>
                        <tr class="hover:bg-emerald-50/50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-gray-700 text-xs">
                                Calificación y Evaluación de propuestas SEACE
                                <input type="hidden" name="etapas[calificacion][nombre]" value="Calificación y Evaluación de propuestas SEACE">
                            </td>
                            <td class="px-2 py-2"><input type="datetime-local" id="inicio_calificacion" name="etapas[calificacion][inicio]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                            <td class="px-2 py-2"><input type="datetime-local" id="fin_calificacion" name="etapas[calificacion][fin]" class="w-full border border-gray-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-gray-600"></td>
                        </tr>
                        <tr class="hover:bg-emerald-50/50 transition-colors">
                            <td class="px-4 py-3 font-bold text-gray-900 text-xs bg-emerald-50/50">
                                Otorgamiento de la Buena Pro SEACE
                                <input type="hidden" name="etapas[buenapro][nombre]" value="Otorgamiento de la Buena Pro SEACE">
                            </td>
                            <td class="px-2 py-2 bg-emerald-50/50"><input type="datetime-local" id="inicio_buenapro" name="etapas[buenapro][inicio]" class="w-full border border-emerald-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 transition-all font-bold text-emerald-800"></td>
                            <td class="px-2 py-2 bg-emerald-50/50"><input type="datetime-local" id="fin_buenapro" name="etapas[buenapro][fin]" class="w-full border border-emerald-300 rounded-md p-1.5 text-xs outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 transition-all font-bold text-emerald-800"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-white border-t border-gray-200 flex justify-end gap-3 shrink-0">
                <button type="button" onclick="cerrarCronograma()" class="px-5 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium smooth-transition">Cancelar</button>
                <button type="submit" id="btn-guardar-crono" class="px-6 py-2 text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg font-bold shadow-md smooth-transition flex items-center">
                    <i class="fa-solid fa-save mr-2"></i> Guardar Cronograma
                </button>
            </div>
        </form>
    </div>
</div>