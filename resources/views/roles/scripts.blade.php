@if(session('success'))
    <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: "{{ session('success') }}", showConfirmButton: false, timer: 4000, timerProgressBar: true });</script>
@endif
@if(session('error'))
    <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: "{{ session('error') }}", showConfirmButton: false, timer: 5000, timerProgressBar: true });</script>
@endif
@if($errors->any())
    <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: '{{ $errors->first() }}', showConfirmButton: false, timer: 5000, timerProgressBar: true });</script>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function() {
        new simpleDatatables.DataTable("#tabla-roles", {
            searchable: true, fixedHeight: true,
            labels: { placeholder: "Buscar perfil...", perPage: "filas", noRows: "No hay roles registrados", info: "{start} a {end} de {rows}" }
        });

        // Evento que bloquea números y símbolos en tiempo real
        const inpNombre = document.getElementById('inp_nombre');
        if(inpNombre) {
            inpNombre.addEventListener('input', function(e) {
                // Solo permite letras mayúsculas, minúsculas, vocales con tilde, la 'ñ' y espacios.
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
            });
        }
    });

    // LÓGICA DE NEGOCIO: Si eres editor, por lógica debes ser lector.
    function validarPermisos(checkbox, tipo, id_opcion) {
        const chkLector = document.getElementById('chk_lector_' + id_opcion);
        const chkEditor = document.getElementById('chk_editor_' + id_opcion);

        if (tipo === 'editor' && checkbox.checked) {
            chkLector.checked = true; 
        }
        if (tipo === 'lector' && !checkbox.checked) {
            chkEditor.checked = false; 
        }
    }

    function abrirModalRol() { 
        document.getElementById('form-rol').reset();
        document.getElementById('form-rol').action = "{{ route('roles.store') }}";
        document.getElementById('method-put').innerHTML = '';
        document.getElementById('titulo-modal').innerHTML = '<i class="fa-solid fa-user-shield mr-2"></i> Crear Nuevo Perfil';
        
        // Desmarcar todos los checks al abrir nuevo
        document.querySelectorAll('.chk-lector, .chk-editor').forEach(chk => chk.checked = false);

        const modal = document.getElementById('modal-rol');
        const content = document.getElementById('modal-content');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }

    function editarRol(rol) {
        document.getElementById('inp_nombre').value = rol.nombre_rol;
        document.getElementById('inp_desc').value = rol.descripcion || '';

        document.getElementById('form-rol').action = `/roles/${rol.id}`;
        document.getElementById('method-put').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('titulo-modal').innerHTML = '<i class="fa-solid fa-pen-to-square mr-2"></i> Configurar Permisos: ' + rol.nombre_rol;
        
        // Limpiamos checks
        document.querySelectorAll('.chk-lector, .chk-editor').forEach(chk => chk.checked = false);

        // Activamos los checks correspondientes leyendo el JSON de permisos
        const permisos = rol.permisos;
        for(let id_opcion in permisos) {
            if(permisos[id_opcion].lector == 1) document.getElementById('chk_lector_' + id_opcion).checked = true;
            if(permisos[id_opcion].editor == 1) document.getElementById('chk_editor_' + id_opcion).checked = true;
        }

        const modal = document.getElementById('modal-rol');
        const content = document.getElementById('modal-content');
        modal.classList.remove('hidden');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); }, 10);
    }

    function cerrarModalRol() { 
        const modal = document.getElementById('modal-rol');
        const content = document.getElementById('modal-content');
        modal.classList.add('opacity-0'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }

    function confirmarEliminacion(btn) {
        Swal.fire({
            title: '¿Eliminar este perfil?', text: "Los usuarios con este rol perderán sus accesos.", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Sí, eliminar', hightAuto: false
        }).then((result) => {
            if (result.isConfirmed) btn.closest('form').submit();
        });
    }

    function mostrarSpinner(form, btnId) {
        const btn = document.getElementById(btnId);
        btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Guardando...';
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    }
</script>