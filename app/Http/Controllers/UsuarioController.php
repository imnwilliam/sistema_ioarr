<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    // 1. Mostrar la tabla de usuarios y el formulario
    public function index()
    {
        $usuarios = DB::table('users')
            ->leftJoin('roles', 'users.id_rol', '=', 'roles.id')
            ->select('users.*', 'roles.nombre_rol')
            ->orderBy('users.id', 'desc')
            ->get();

        $roles = DB::table('roles')->where('estado', 1)->get();

        return view('usuarios', compact('usuarios', 'roles'));
    }

    // 2. Guardar el nuevo usuario encriptado
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/|max:255',
            'email' => 'required|string|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'id_rol' => 'required|integer|exists:roles,id',
        ], [
            'name.regex' => 'El nombre completo solo puede contener letras y espacios.',
            'email.unique' => 'Ese nombre de usuario ya está en uso.',
            'id_rol.exists' => 'El rol seleccionado no es válido.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'id_rol' => $request->id_rol,
            'estado' => 1,
        ]);

        return redirect('/usuarios')->with('success', 'Usuario creado correctamente y contraseña encriptada.');
    }

    // 3. Actualizar datos de un usuario existente
    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/|max:255',
            'email' => 'required|string|max:255|unique:users,email,' . $id,
            'id_rol' => 'required|integer|exists:roles,id',
            'estado' => 'required|boolean',
        ], [
            'name.regex' => 'El nombre completo solo puede contener letras y espacios.',
            'email.unique' => 'Ese nombre de usuario ya está en uso.',
            'id_rol.exists' => 'El rol seleccionado no es válido.',
        ]);

        // Protección del Administrador Principal
        if ($usuario->esAdminPrincipal()) {
            if ($request->id_rol != 1) {
                return back()->with('error', 'No puedes cambiar el rol del Administrador Principal.');
            }
            if (!$request->estado) {
                return back()->with('error', 'No puedes desactivar al Administrador Principal.');
            }
        }

        $usuario->update([
            'name' => $request->name,
            'email' => $request->email,
            'id_rol' => $request->id_rol,
            'estado' => $request->estado,
        ]);

        return redirect('/usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    // 4. Desactivar usuario (soft-delete vía estado, no delete físico)
    public function destroy($id)
    {
        $usuario = User::findOrFail($id);

        if ($usuario->esAdminPrincipal()) {
            return redirect('/usuarios')->with('error', 'No puedes eliminar al Administrador Principal.');
        }

        $usuario->update(['estado' => 0]);

        return redirect('/usuarios')->with('success', 'Usuario desactivado correctamente.');
    }

    // 5. Reactivar usuario
    public function activar($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->update(['estado' => 1]);

        return redirect('/usuarios')->with('success', 'Usuario reactivado correctamente.');
    }

    // 6. Restablecer contraseña desde el panel de administración
    public function resetPassword(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $usuario->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect('/usuarios')->with('success', 'Contraseña restablecida correctamente para ' . $usuario->name . '.');
    }
}