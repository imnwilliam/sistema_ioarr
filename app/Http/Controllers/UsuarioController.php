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
        // Unimos la tabla 'users' con tu tabla 'roles'
        $usuarios = DB::table('users')
            ->leftJoin('roles', 'users.id_rol', '=', 'roles.id')
            ->select('users.*', 'roles.nombre_rol')
            ->orderBy('users.id', 'desc')
            ->get();

        // Traemos los roles activos para el select del formulario
        $roles = DB::table('roles')->where('estado', 1)->get();

        return view('usuarios', compact('usuarios', 'roles'));
    }

    // 2. Guardar el nuevo usuario encriptado
    public function store(Request $request)
    {
        // Validación básica
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'id_rol' => 'required|integer'
        ]);

        // Creamos el usuario
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // ¡Seguridad!
            'id_rol' => $request->id_rol,
        ]);

        return redirect('/usuarios')->with('success', 'Usuario creado correctamente y contraseña encriptada.');
    }
}