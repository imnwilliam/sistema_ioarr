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

        foreach ($roles as $rol) {
            $rol->permisos = DB::table('permisos')->where('id_rol', $rol->id)->get()->keyBy('id_opcion');
        }

        return view('roles', compact('roles', 'opciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre_rol' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/|unique:roles,nombre_rol',
            'permisos' => 'required|array|min:1',
        ], [
            'nombre_rol.regex' => 'El nombre del rol solo puede contener letras y espacios.',
            'nombre_rol.unique' => 'Ya existe un perfil con ese nombre.',
            'permisos.required' => 'Debes asignar al menos un módulo a este perfil.',
            'permisos.min' => 'Debes asignar al menos un módulo a este perfil.',
        ]);

        $this->validarOpcionesExisten($request->permisos);

        DB::transaction(function () use ($request) {
            $id_rol = DB::table('roles')->insertGetId([
                'nombre_rol' => $request->nombre_rol,
                'descripcion' => $request->descripcion,
                'estado' => 1
            ]);

            $this->sincronizarPermisos($id_rol, $request->permisos);
        });

        return redirect('/roles')->with('success', 'Perfil y permisos creados correctamente.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre_rol' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/|unique:roles,nombre_rol,' . $id,
            'permisos' => 'required|array|min:1',
        ], [
            'nombre_rol.regex' => 'El nombre del rol solo puede contener letras y espacios.',
            'nombre_rol.unique' => 'Ya existe un perfil con ese nombre.',
            'permisos.required' => 'Debes asignar al menos un módulo a este perfil.',
            'permisos.min' => 'Debes asignar al menos un módulo a este perfil.',
        ]);

        $this->validarOpcionesExisten($request->permisos);

        DB::transaction(function () use ($request, $id) {
            DB::table('roles')->where('id', $id)->update([
                'nombre_rol' => $request->nombre_rol,
                'descripcion' => $request->descripcion
            ]);

            // Borramos e insertamos dentro de la misma transacción:
            // si algo falla, se revierte todo y el rol no se queda sin permisos.
            DB::table('permisos')->where('id_rol', $id)->delete();
            $this->sincronizarPermisos($id, $request->permisos);
        });

        return redirect('/roles')->with('success', 'Perfil actualizado correctamente.');
    }

    public function destroy($id)
    {
        if ($id == 1) {
            return redirect('/roles')->with('error', 'No puedes eliminar al Administrador Principal.');
        }

        DB::table('roles')->where('id', $id)->update(['estado' => 0]);
        return redirect('/roles')->with('success', 'Perfil eliminado correctamente.');
    }

    // Valida que cada id_opcion enviado exista realmente en la tabla 'opciones'
    private function validarOpcionesExisten($permisos)
    {
        $idsValidos = DB::table('opciones')->pluck('id')->map(fn($id) => (int)$id)->toArray();

        foreach (array_keys($permisos) as $id_opcion) {
            if (!in_array((int) $id_opcion, $idsValidos)) {
                abort(422, 'Uno de los módulos seleccionados no existe.');
            }
        }
    }

    private function sincronizarPermisos($id_rol, $permisos)
    {
        if (!empty($permisos)) {
            $inserts = [];
            foreach ($permisos as $id_opcion => $valores) {
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