<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolController extends Controller
{
    public function index()
    {
        $opciones = DB::table('opciones')->orderBy('orden')->get();
        $roles = DB::table('roles')->where('estado', 1)->get();

        // Mapeamos los permisos actuales de cada rol para enviarlos por JSON a la vista
        foreach ($roles as $rol) {
            $rol->permisos = DB::table('permisos')->where('id_rol', $rol->id)->get()->keyBy('id_opcion');
        }

        return view('roles', compact('roles', 'opciones'));
    }

    public function store(Request $request)
    {
        // Validación estricta en el backend: Solo letras y espacios.
        $request->validate([
            'nombre_rol' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
        ], [
            'nombre_rol.regex' => 'El nombre del rol solo puede contener letras y espacios.'
        ]);

        $id_rol = DB::table('roles')->insertGetId([
            'nombre_rol' => $request->nombre_rol,
            'descripcion' => $request->descripcion,
            'estado' => 1
        ]);

        $this->sincronizarPermisos($id_rol, $request->permisos);

        return redirect('/roles')->with('success', 'Perfil y permisos creados correctamente.');
    }

    public function update(Request $request, $id)
    {
        // Validación estricta en el backend para la edición.
        $request->validate([
            'nombre_rol' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
        ], [
            'nombre_rol.regex' => 'El nombre del rol solo puede contener letras y espacios.'
        ]);

        DB::table('roles')->where('id', $id)->update([
            'nombre_rol' => $request->nombre_rol,
            'descripcion' => $request->descripcion
        ]);

        // Borramos los permisos antiguos para insertar los nuevos limpiamente
        DB::table('permisos')->where('id_rol', $id)->delete();
        $this->sincronizarPermisos($id, $request->permisos);

        return redirect('/roles')->with('success', 'Perfil actualizado correctamente.');
    }

    public function destroy($id)
    {
        // No borramos al admin principal por seguridad
        if ($id == 1) {
            return redirect('/roles')->with('error', 'No puedes eliminar al Administrador Principal.');
        }

        DB::table('roles')->where('id', $id)->update(['estado' => 0]);
        return redirect('/roles')->with('success', 'Perfil eliminado correctamente.');
    }

    // Función interna para recorrer los checkboxes y guardarlos en BD
    private function sincronizarPermisos($id_rol, $permisos)
    {
        if (!empty($permisos)) {
            $inserts = [];
            foreach ($permisos as $id_opcion => $valores) {
                // Si marcó Lector O Editor, le damos el acceso a ese módulo
                if (isset($valores['lector']) || isset($valores['editor'])) {
                    $inserts[] = [
                        'id_rol' => $id_rol,
                        'id_opcion' => $id_opcion,
                        'lector' => isset($valores['lector']) ? 1 : 0,
                        'editor' => isset($valores['editor']) ? 1 : 0,
                    ];
                }
            }
            if (count($inserts) > 0) {
                DB::table('permisos')->insert($inserts);
            }
        }
    }
}