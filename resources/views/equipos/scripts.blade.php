@if(session('success'))
    <script>
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: "{{ session('success') }}", showConfirmButton: false, timer: 4000, timerProgressBar: true });
    </script>
@endif
@if(session('error'))
    <script>
        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: "{{ session('error') }}", showConfirmButton: false, timer: 5000, timerProgressBar: true });
    </script>
@endif

<script>
    let selectInversion, selectUpss;
    let filtroInversion, filtroUpss, filtroTipo;
    let dtEvidencias = new DataTransfer(); // Memoria inteligente para acumular archivos a subir

    // Datos completos de equipos YA FILTRADOS por el controlador (respeta filtro_inversion,
    // filtro_upss, filtro_tipo, filtro_expediente). Se usan para exportar PDF sin depender
    // de la paginación del datatable.
    const todosEquiposDatos = @json($equipos);
    const sumaTotalEquipos = {{ $sumaTotal ?? 0 }};

    document.addEventListener("DOMContentLoaded", function() {
        const table = new simpleDatatables.DataTable("#tabla-equipos", {
            searchable: true, fixedHeight: true,
            labels: { placeholder: "Buscar en tabla...", perPage: "equipos", noRows: "No hay equipos", info: "{start} a {end} de {rows}" }
        });
        
        selectInversion = new TomSelect("#inp_id_inversion", { create: false, sortField: { field: "text", direction: "asc" } });
        selectUpss = new TomSelect("#inp_id_upss", { create: false });
        filtroInversion = new TomSelect("#filtro_inversion", { create: false, sortField: { field: "text", direction: "asc" } });
        filtroUpss = new TomSelect("#filtro_upss", { create: false });
        filtroTipo = new TomSelect("#filtro_tipo", { create: false });

        // Filtro estricto para Expediente (solo números)
        const inpFiltroExp = document.getElementById('filtro_expediente_inp');
        if(inpFiltroExp) inpFiltroExp.addEventListener('input', function() { this.value = this.value.replace(/[^0-9]/g, ''); });

        const inpExpediente = document.getElementById('inp_expediente');
        if(inpExpediente) inpExpediente.addEventListener('input', function() { this.value = this.value.replace(/[^0-9]/g, ''); });

        // Lógica de acumulación de archivos
        const fileInput = document.getElementById('file_evidencia');
        if(fileInput) {
            fileInput.addEventListener('change', function(e) {
                for (let i = 0; i < e.target.files.length; i++) {
                    dtEvidencias.items.add(e.target.files[i]);
                }
                this.files = dtEvidencias.files; // Actualizar el input con la cola acumulada
                renderizarListaNuevosArchivos();
            });
        }
    });

    function renderizarListaNuevosArchivos() {
        const contenedor = document.getElementById('contenedor-archivos-nuevos');
        const lista = document.getElementById('lista-archivos-nuevos');
        lista.innerHTML = '';
        
        if (dtEvidencias.files.length > 0) {
            contenedor.classList.remove('hidden');
            Array.from(dtEvidencias.files).forEach((file, index) => {
                let icon = 'fa-file text-gray-500';
                if (file.name.toLowerCase().endsWith('.pdf')) icon = 'fa-file-pdf text-red-500';
                else if (file.name.toLowerCase().match(/\.(xls|xlsx)$/)) icon = 'fa-file-excel text-green-500';
                else if (file.name.toLowerCase().match(/\.(doc|docx)$/)) icon = 'fa-file-word text-blue-500';

                lista.innerHTML += `
                    <li class="flex justify-between items-center bg-white border border-gray-200 p-2 rounded-lg text-sm shadow-sm" id="new-file-${index}">
                        <span class="truncate w-40 text-gray-700 font-medium text-xs flex items-center">
                            <i class="fa-solid ${icon} mr-2"></i> ${file.name}
                        </span>
                        <button type="button" onclick="quitarArchivoNuevo(${index})" class="text-red-500 hover:bg-red-50 p-1.5 rounded transition-colors" title="Quitar archivo">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </li>
                `;
            });
        } else {
            contenedor.classList.add('hidden');
        }
    }

    function quitarArchivoNuevo(index) {
        const newDt = new DataTransfer();
        for (let i = 0; i < dtEvidencias.files.length; i++) {
            if (i !== index) newDt.items.add(dtEvidencias.files[i]);
        }
        dtEvidencias = newDt;
        document.getElementById('file_evidencia').files = dtEvidencias.files;
        renderizarListaNuevosArchivos();
    }

    function abrirModalEquipo() { 
        document.getElementById('form-equipo').reset();
        document.getElementById('form-equipo').action = "{{ route('equipos.store') }}";
        document.getElementById('method-put').innerHTML = '';
        document.getElementById('titulo-modal').innerHTML = '<i class="fa-solid fa-microscope mr-2"></i> Registrar Equipo';
        
        // Limpieza de archivos visuales e internos
        dtEvidencias = new DataTransfer(); 
        renderizarListaNuevosArchivos();
        document.getElementById('contenedor-archivos').classList.add('hidden');
        document.getElementById('lista-archivos-existentes').innerHTML = '';

        selectInversion.clear(); selectUpss.clear();

        const modal = document.getElementById('modal-equipo');
        const content = document.getElementById('modal-content');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }

    function editarEquipo(equipo) {
        document.getElementById('inp_nombre').value = equipo.nombre_equipo;
        document.getElementById('inp_servicio').value = equipo.servicio || '';
        document.getElementById('inp_ambiente').value = equipo.ambiente || '';
        document.getElementById('inp_estado_situacional').value = equipo.estado_situacional || '';
        document.getElementById('inp_expediente').value = equipo.expediente || '';
        document.getElementById('inp_cantidad').value = equipo.cantidad;
        document.getElementById('inp_precio').value = equipo.precio_unitario;
        document.getElementById('inp_tipo').value = equipo.tipo_equipo; 

        selectInversion.setValue(equipo.id_inversion);
        selectUpss.setValue(equipo.id_upss);

        document.getElementById('form-equipo').action = `/equipos/${equipo.id}`;
        document.getElementById('method-put').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('titulo-modal').innerHTML = '<i class="fa-solid fa-pen mr-2"></i> Editar Equipo';
        
        // Limpiamos los archivos nuevos subidos por error previo
        dtEvidencias = new DataTransfer(); 
        document.getElementById('file_evidencia').value = '';
        renderizarListaNuevosArchivos();

        const contenedorArchivos = document.getElementById('contenedor-archivos');
        const listaArchivos = document.getElementById('lista-archivos-existentes');
        listaArchivos.innerHTML = '';

        let archivos = [];
        try { archivos = JSON.parse(equipo.archivo_evidencia); } catch(e) {}
        if(!archivos && equipo.archivo_evidencia) archivos = [equipo.archivo_evidencia];

        if (archivos && archivos.length > 0) {
            contenedorArchivos.classList.remove('hidden');
            archivos.forEach((ruta, index) => {
                let ext = ruta.split('.').pop().toLowerCase();
                let icon = 'fa-file text-gray-500';
                if (ext === 'pdf') icon = 'fa-file-pdf text-red-500';
                else if (['xls','xlsx'].includes(ext)) icon = 'fa-file-excel text-green-500';
                else if (['doc','docx'].includes(ext)) icon = 'fa-file-word text-blue-500';

                listaArchivos.innerHTML += `
                    <li class="flex justify-between items-center bg-white border border-gray-200 p-2 rounded-lg text-sm shadow-sm" id="file-row-${index}">
                        <a href="/equipos/${equipo.id}/evidencia/${index}" target="_blank" class="text-blue-600 hover:underline truncate w-40 flex items-center">
                            <i class="fa-solid ${icon} mr-2"></i> Doc ${index + 1}
                        </a>
                        <button type="button" onclick="eliminarArchivoUnico(${equipo.id}, ${index})" class="text-red-500 hover:bg-red-50 p-1.5 rounded transition-colors" title="Borrar este archivo">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </li>
                `;
            });
        } else {
            contenedorArchivos.classList.add('hidden');
        }

        const modal = document.getElementById('modal-equipo');
        const content = document.getElementById('modal-content');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }

    function eliminarArchivoUnico(equipoId, index) {
        Swal.fire({
            title: '¿Eliminar este documento?', text: "Esta acción no se puede deshacer.", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/equipos/${equipoId}/archivo/${index}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        document.getElementById(`file-row-${index}`).remove();
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Archivo eliminado', showConfirmButton: false, timer: 2000 });
                        if(document.getElementById('lista-archivos-existentes').children.length === 0) {
                            document.getElementById('contenedor-archivos').classList.add('hidden');
                        }
                    }
                });
            }
        })
    }

    function cerrarModalEquipo() { 
        const modal = document.getElementById('modal-equipo');
        const content = document.getElementById('modal-content');
        modal.classList.add('opacity-0'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    function confirmarEliminacion(btn) {
        Swal.fire({
            title: '¿Eliminar equipo?', text: "Esta acción lo ocultará del sistema.", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) btn.closest('form').submit();
        });
    }

    function mostrarSpinner(form, btnId) {
        const btn = document.getElementById(btnId);
        btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Procesando...';
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    }

    // (La lógica del Cronograma se mantiene igual que en tu archivo original...)
    function abrirCronograma(id_equipo, nombre_equipo) { 
        document.getElementById('crono_id_equipo').value = id_equipo;
        document.getElementById('lbl_nombre_equipo').innerText = nombre_equipo;
        
        document.querySelectorAll('#modal-crono-content input[type="datetime-local"]').forEach(inp => inp.value = '');

        fetch(`/cronogramas/equipo/${id_equipo}`)
            .then(response => response.json())
            .then(data => {
                const mapEtapas = {
                    "Convocatoria": "convocatoria",
                    "Registro de participantes(Electronica)": "registro",
                    "Formulación de consultas y observaciones(Electronica)": "formulacion",
                    "Absolución de consultas y observaciones(Electronica)": "absolucion",
                    "Integración de las Bases SEACE": "integracion",
                    "Presentación de propuestas(Electronica)": "presentacion",
                    "Calificación y Evaluación de propuestas SEACE": "calificacion",
                    "Otorgamiento de la Buena Pro SEACE": "buenapro"
                };
                data.forEach(item => {
                    let key = mapEtapas[item.etapa];
                    if (key) {
                        if (item.fecha_inicio) document.getElementById('inicio_' + key).value = item.fecha_inicio.replace(' ', 'T').substring(0, 16);
                        if (item.fecha_fin) document.getElementById('fin_' + key).value = item.fecha_fin.replace(' ', 'T').substring(0, 16);
                    }
                });
            });
        
        const modal = document.getElementById('modal-cronograma');
        const content = document.getElementById('modal-crono-content');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }

    function cerrarCronograma() { 
        const modal = document.getElementById('modal-cronograma');
        const content = document.getElementById('modal-crono-content');
        modal.classList.add('opacity-0'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    // =====================================================================
    // EXPORTAR PDF - TABLA GENERAL DE EQUIPOS
    // Usa "todosEquiposDatos" (ya filtrado por el controlador según los
    // parámetros GET actuales), así que respeta automáticamente cualquier
    // filtro aplicado. No incluye datos de cronograma.
    // =====================================================================

    function fechaArchivo() {
        const d = new Date();
        return `${d.getFullYear()}${String(d.getMonth()+1).padStart(2,'0')}${String(d.getDate()).padStart(2,'0')}_${String(d.getHours()).padStart(2,'0')}${String(d.getMinutes()).padStart(2,'0')}`;
    }

    function ponerBotonCargando(boton, cargando) {
        if (cargando) {
            boton.dataset.original = boton.innerHTML;
            boton.disabled = true;
            boton.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generando...';
        } else {
            boton.disabled = false;
            boton.innerHTML = boton.dataset.original;
        }
    }

    // Arma un texto legible con los filtros actualmente aplicados,
    // leyéndolo directamente de los <select> / <input> del formulario de filtros.
    function obtenerResumenFiltros() {
        const partes = [];

        const selInv = document.getElementById('filtro_inversion');
        if (selInv && selInv.value) partes.push(`Inversión: ${selInv.selectedOptions[0].text}`);

        const selUpss = document.getElementById('filtro_upss');
        if (selUpss && selUpss.value) partes.push(`Área: ${selUpss.selectedOptions[0].text}`);

        const selTipo = document.getElementById('filtro_tipo');
        if (selTipo && selTipo.value) partes.push(`Tipo: ${selTipo.selectedOptions[0].text}`);

        const inpExp = document.getElementById('filtro_expediente_inp');
        if (inpExp && inpExp.value) partes.push(`Expediente: ${inpExp.value}`);

        return partes.length > 0 ? partes.join('  |  ') : 'Sin filtros aplicados (mostrando todos los equipos)';
    }

    function exportarPDFEquipos(boton) {
        if (!todosEquiposDatos || todosEquiposDatos.length === 0) {
            Swal.fire({ toast: true, position: 'top-end', icon: 'info', title: 'No hay equipos para exportar', showConfirmButton: false, timer: 3000 });
            return;
        }

        ponerBotonCargando(boton, true);

        let filasHTML = '';
        todosEquiposDatos.forEach(eq => {
            let archivos = [];
            try { archivos = JSON.parse(eq.archivo_evidencia); } catch(e) {}
            if (!archivos && eq.archivo_evidencia) archivos = [eq.archivo_evidencia];
            archivos = archivos || [];

            const precioTotal = parseFloat(eq.precio_total || 0);

            filasHTML += `
                <tr>
                    <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;">
                        <div style="font-weight:700;color:#1e293b;">${eq.nombre_equipo}</div>
                        <div style="font-size:10px;color:#7c3aed;font-weight:700;margin-top:2px;">${eq.tipo_equipo || '-'}</div>
                        <div style="font-size:10px;color:#6b7280;margin-top:1px;">Exp: ${eq.expediente || 'N/A'}</div>
                    </td>
                    <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;font-weight:700;color:#1d4ed8;">${eq.cui || '-'}</td>
                    <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;">
                        <div style="font-weight:600;color:#374151;">${eq.nombre_upss || 'Sin área'}</div>
                        <div style="font-size:10px;color:#6b7280;margin-top:1px;">
                            ${eq.servicio ? 'Serv: ' + eq.servicio + '<br>' : ''}
                            ${eq.ambiente ? 'Amb: ' + eq.ambiente : ''}
                        </div>
                    </td>
                    <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;font-size:11px;color:#4b5563;max-width:220px;">${eq.estado_situacional || '-'}</td>
                    <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;text-align:center;font-weight:700;color:#1e293b;">${eq.cantidad}</td>
                    <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;text-align:right;font-weight:800;color:#1e293b;">S/ ${precioTotal.toLocaleString('en-US',{minimumFractionDigits:2})}</td>
                    <td style="padding:8px 10px;border-bottom:1px solid #e5e7eb;text-align:center;font-size:11px;color:#6b7280;">${archivos.length > 0 ? archivos.length + ' doc(s)' : 'Sin archivos'}</td>
                </tr>`;
        });

        const fechaGeneracion = new Date().toLocaleString('es-PE');
        const filtrosTexto = obtenerResumenFiltros();

        const contenedorTemp = document.createElement('div');
        contenedorTemp.style.cssText = 'width:1200px; background:#fff; padding:28px; font-family:Inter,sans-serif;';
        contenedorTemp.innerHTML = `
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;border-bottom:3px solid #7c3aed;padding-bottom:12px;">
                <div>
                    <h2 style="font-size:20px;font-weight:900;color:#1e293b;margin:0;">Reporte de Equipos</h2>
                    <p style="font-size:11px;color:#6b7280;margin:4px 0 0;">Generado el ${fechaGeneracion}</p>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:10px;color:#6b7280;font-weight:700;text-transform:uppercase;">Total Registros</div>
                    <div style="font-size:22px;font-weight:900;color:#7c3aed;">${todosEquiposDatos.length}</div>
                </div>
            </div>

            <div style="background:#f5f3ff;border:1px solid #ddd6fe;border-radius:8px;padding:8px 14px;margin-bottom:16px;font-size:11px;color:#5b21b6;font-weight:600;">
                <i style="font-weight:900;">Filtros aplicados:</i> ${filtrosTexto}
            </div>

            <table style="width:100%;border-collapse:collapse;font-size:12px;">
                <thead>
                    <tr style="background:#f1f5f9;">
                        <th style="padding:9px 10px;text-align:left;color:#374151;">Equipo / Tipo / Exp.</th>
                        <th style="padding:9px 10px;text-align:left;color:#374151;">CUI</th>
                        <th style="padding:9px 10px;text-align:left;color:#374151;">UPSS / Serv. / Amb.</th>
                        <th style="padding:9px 10px;text-align:left;color:#374151;">Estado Situacional</th>
                        <th style="padding:9px 10px;text-align:center;color:#374151;">Cant.</th>
                        <th style="padding:9px 10px;text-align:right;color:#374151;">Costo Total</th>
                        <th style="padding:9px 10px;text-align:center;color:#374151;">Evidencias</th>
                    </tr>
                </thead>
                <tbody>${filasHTML}</tbody>
                <tfoot>
                    <tr style="background:#ede9fe;">
                        <td colspan="5" style="padding:12px;text-align:right;font-weight:900;color:#1e293b;">TOTAL GENERAL:</td>
                        <td style="padding:12px;text-align:right;font-weight:900;color:#1e293b;">S/ ${parseFloat(sumaTotalEquipos).toLocaleString('en-US',{minimumFractionDigits:2})}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        `;

        const opciones = {
            margin: 0.4,
            filename: `Equipos_IOARR_${fechaArchivo()}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'in', format: 'a3', orientation: 'landscape' },
            pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
        };

        html2pdf().set(opciones).from(contenedorTemp).save().then(() => {
            ponerBotonCargando(boton, false);
        });
    }

    // =====================================================================
    // EXPORTAR PDF / EXCEL - CRONOGRAMA SEACE (por equipo individual)
    // Lee los valores actuales de los inputs datetime-local del modal
    // (ya cargados por abrirCronograma), así que siempre exporta lo que
    // se está viendo en pantalla en ese momento.
    // =====================================================================

    // Mapa clave interna -> etiqueta legible (mismo orden que la tabla del modal)
    const etapasCronograma = [
        { key: 'convocatoria', label: 'Convocatoria' },
        { key: 'registro', label: 'Registro de participantes (Electrónica)' },
        { key: 'formulacion', label: 'Formulación de consultas y observaciones (Electrónica)' },
        { key: 'absolucion', label: 'Absolución de consultas y observaciones (Electrónica)' },
        { key: 'integracion', label: 'Integración de las Bases SEACE' },
        { key: 'presentacion', label: 'Presentación de propuestas (Electrónica)' },
        { key: 'calificacion', label: 'Calificación y Evaluación de propuestas SEACE' },
        { key: 'buenapro', label: 'Otorgamiento de la Buena Pro SEACE' },
    ];

    function formatearFechaHora(valorDatetimeLocal) {
        if (!valorDatetimeLocal) return '-';
        const d = new Date(valorDatetimeLocal);
        if (isNaN(d.getTime())) return '-';
        return d.toLocaleString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function obtenerDatosCronogramaActual() {
        const nombreEquipo = document.getElementById('lbl_nombre_equipo').innerText || 'Equipo';
        const filas = etapasCronograma.map(et => {
            const inicio = document.getElementById('inicio_' + et.key)?.value || '';
            const fin = document.getElementById('fin_' + et.key)?.value || '';
            return { etapa: et.label, inicio, fin };
        });
        return { nombreEquipo, filas };
    }

    // ----------------- PDF DEL CRONOGRAMA (corregido a landscape) -----------------
    function exportarPDFCronograma(boton) {
        ponerBotonCargando(boton, true);

        const { nombreEquipo, filas } = obtenerDatosCronogramaActual();
        const fechaGeneracion = new Date().toLocaleString('es-PE');

        let filasHTML = '';
        filas.forEach((f, idx) => {
            const destacado = f.etapa.includes('Buena Pro');
            filasHTML += `
                <tr style="${destacado ? 'background:#ecfdf5;' : ''}">
                    <td style="padding:9px 12px;border-bottom:1px solid #e5e7eb;font-weight:${destacado ? '800' : '600'};color:#1e293b;font-size:12px;">${f.etapa}</td>
                    <td style="padding:9px 12px;border-bottom:1px solid #e5e7eb;text-align:center;font-size:11px;color:#374151;">${formatearFechaHora(f.inicio)}</td>
                    <td style="padding:9px 12px;border-bottom:1px solid #e5e7eb;text-align:center;font-size:11px;color:#374151;">${formatearFechaHora(f.fin)}</td>
                </tr>`;
        });

        const contenedorTemp = document.createElement('div');
        contenedorTemp.style.cssText = 'width:950px; background:#fff; padding:28px; font-family:Inter,sans-serif;';
        contenedorTemp.innerHTML = `
            <div style="border-bottom:3px solid #059669;padding-bottom:12px;margin-bottom:18px;">
                <h2 style="font-size:19px;font-weight:900;color:#1e293b;margin:0;">Cronograma SEACE</h2>
                <p style="font-size:12px;color:#059669;font-weight:700;margin:4px 0 0;text-transform:uppercase;">${nombreEquipo}</p>
                <p style="font-size:10px;color:#6b7280;margin:2px 0 0;">Generado el ${fechaGeneracion}</p>
            </div>
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f1f5f9;">
                        <th style="padding:9px 12px;text-align:left;color:#374151;font-size:11px;">Etapa del Proceso</th>
                        <th style="padding:9px 12px;text-align:center;color:#374151;font-size:11px;">Fecha y Hora Inicio</th>
                        <th style="padding:9px 12px;text-align:center;color:#374151;font-size:11px;">Fecha y Hora Fin</th>
                    </tr>
                </thead>
                <tbody>${filasHTML}</tbody>
            </table>
        `;

        const opciones = {
            margin: 0.4,
            filename: `Cronograma_${nombreEquipo.replace(/[^a-zA-Z0-9]/g, '_')}_${fechaArchivo()}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'landscape' },
            pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
        };

        html2pdf().set(opciones).from(contenedorTemp).save().then(() => {
            ponerBotonCargando(boton, false);
        });
    }

    // ----------------- EXCEL DEL CRONOGRAMA (corregido con ExcelJS, con estilos) -----------------
    async function exportarExcelCronograma(boton) {
        ponerBotonCargando(boton, true);

        try {
            const { nombreEquipo, filas } = obtenerDatosCronogramaActual();

            const workbook = new ExcelJS.Workbook();
            workbook.creator = 'Sistema IOARR Pro';
            workbook.created = new Date();

            const hoja = workbook.addWorksheet('Cronograma', {
                views: [{ showGridLines: false }]
            });

            hoja.columns = [{ width: 55 }, { width: 24 }, { width: 24 }];

            // Título
            hoja.mergeCells('A1:C1');
            const tituloCell = hoja.getCell('A1');
            tituloCell.value = `Cronograma SEACE - ${nombreEquipo}`;
            tituloCell.font = { bold: true, size: 14, color: { argb: 'FFFFFFFF' } };
            tituloCell.alignment = { vertical: 'middle', horizontal: 'left' };
            tituloCell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF059669' } };
            hoja.getRow(1).height = 26;

            // Subtítulo
            hoja.mergeCells('A2:C2');
            const subCell = hoja.getCell('A2');
            subCell.value = `Generado el ${new Date().toLocaleString('es-PE')}`;
            subCell.font = { italic: true, size: 10, color: { argb: 'FF6B7280' } };
            hoja.getRow(2).height = 18;

            hoja.addRow([]); // fila vacía

            // Encabezados
            const headerRow = hoja.addRow(['Etapa del Proceso', 'Fecha y Hora Inicio', 'Fecha y Hora Fin']);
            headerRow.eachCell(cell => {
                cell.font = { bold: true, size: 11, color: { argb: 'FFFFFFFF' } };
                cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FF334155' } };
                cell.alignment = { vertical: 'middle', horizontal: 'center' };
                cell.border = {
                    top: { style: 'thin', color: { argb: 'FFCBD5E1' } },
                    bottom: { style: 'thin', color: { argb: 'FFCBD5E1' } },
                    left: { style: 'thin', color: { argb: 'FFCBD5E1' } },
                    right: { style: 'thin', color: { argb: 'FFCBD5E1' } }
                };
            });
            headerRow.height = 22;

            // Filas de datos
            filas.forEach((f, idx) => {
                const esBuenaPro = f.etapa.includes('Buena Pro');
                const row = hoja.addRow([f.etapa, formatearFechaHora(f.inicio), formatearFechaHora(f.fin)]);

                row.eachCell((cell, colNumber) => {
                    cell.border = {
                        top: { style: 'thin', color: { argb: 'FFE5E7EB' } },
                        bottom: { style: 'thin', color: { argb: 'FFE5E7EB' } },
                        left: { style: 'thin', color: { argb: 'FFE5E7EB' } },
                        right: { style: 'thin', color: { argb: 'FFE5E7EB' } }
                    };
                    cell.alignment = colNumber === 1
                        ? { vertical: 'middle', horizontal: 'left' }
                        : { vertical: 'middle', horizontal: 'center' };

                    if (esBuenaPro) {
                        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFECFDF5' } };
                        cell.font = { bold: true, color: { argb: 'FF065F46' } };
                    } else if (idx % 2 === 0) {
                        cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFF9FAFB' } };
                    }
                });
                row.height = 20;
            });

            const buffer = await workbook.xlsx.writeBuffer();
            const blob = new Blob([buffer], { type: 'application/octet-stream' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `Cronograma_${nombreEquipo.replace(/[^a-zA-Z0-9]/g, '_')}_${fechaArchivo()}.xlsx`;
            link.click();
            URL.revokeObjectURL(link.href);
        } finally {
            ponerBotonCargando(boton, false);
        }
    }
</script>