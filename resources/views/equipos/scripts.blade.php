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

    document.addEventListener("DOMContentLoaded", function() {
        const table = new simpleDatatables.DataTable("#tabla-equipos", {
            searchable: true, fixedHeight: true,
            labels: { placeholder: "Buscar en tabla...", perPage: "equipos", noRows: "No hay equipos", info: "{start} a {end} de {rows}" }
        });
        
        // Inicializar selects del Modal
        selectInversion = new TomSelect("#inp_id_inversion", { create: false, sortField: { field: "text", direction: "asc" } });
        selectUpss = new TomSelect("#inp_id_upss", { create: false });
        
        // Inicializar selects de los Filtros en la vista principal
        filtroInversion = new TomSelect("#filtro_inversion", { create: false, sortField: { field: "text", direction: "asc" } });
        filtroUpss = new TomSelect("#filtro_upss", { create: false });
        filtroTipo = new TomSelect("#filtro_tipo", { create: false });
    });

    function abrirModalEquipo() { 
        document.getElementById('form-equipo').reset();
        document.getElementById('form-equipo').action = "{{ route('equipos.store') }}";
        document.getElementById('method-put').innerHTML = '';
        document.getElementById('titulo-modal').innerHTML = '<i class="fa-solid fa-microscope mr-2"></i> Registrar Equipo';
        
        document.getElementById('contenedor-archivos').classList.add('hidden');
        document.getElementById('lista-archivos-existentes').innerHTML = '';
        document.getElementById('inp_estado_situacional').value = '';

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
        document.getElementById('inp_expediente').value = equipo.expediente;
        document.getElementById('inp_cantidad').value = equipo.cantidad;
        document.getElementById('inp_precio').value = equipo.precio_unitario;
        document.getElementById('inp_tipo').value = equipo.tipo_equipo; 

        selectInversion.setValue(equipo.id_inversion);
        selectUpss.setValue(equipo.id_upss);

        document.getElementById('form-equipo').action = `/equipos/${equipo.id}`;
        document.getElementById('method-put').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('titulo-modal').innerHTML = '<i class="fa-solid fa-pen mr-2"></i> Editar Equipo';
        
        const contenedorArchivos = document.getElementById('contenedor-archivos');
        const listaArchivos = document.getElementById('lista-archivos-existentes');
        listaArchivos.innerHTML = '';

        let archivos = [];
        try { archivos = JSON.parse(equipo.archivo_evidencia); } catch(e) {}
        if(!archivos && equipo.archivo_evidencia) archivos = [equipo.archivo_evidencia];

        if (archivos && archivos.length > 0) {
            contenedorArchivos.classList.remove('hidden');
            archivos.forEach((ruta, index) => {
                listaArchivos.innerHTML += `
                    <li class="flex justify-between items-center bg-white border border-gray-200 p-2 rounded-lg text-sm shadow-sm" id="file-row-${index}">
                        <a href="/storage/${ruta}" target="_blank" class="text-blue-600 hover:underline truncate w-40 flex items-center">
                            <i class="fa-solid fa-file-pdf mr-2 text-red-500"></i> Doc ${index + 1}
                        </a>
                        <button type="button" onclick="eliminarArchivoUnico(${equipo.id}, ${index})" class="text-red-500 hover:bg-red-50 p-1.5 rounded transition-colors" title="Borrar este PDF">
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
</script>