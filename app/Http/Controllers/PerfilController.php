<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function index()
    {
        return view('perfil');
    }

    public function update(Request $request)
    {
        $user = User::find(auth()->user()->id);

        // Validación de campos
        $request->validate([
            'name' => 'required|string|max:255',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed'
        ]);

        // Guardar cambio de nombre
        $user->name = $request->name;

        // Si envió intención de cambiar clave
        if ($request->filled('new_password')) {
            // Verificar si la clave actual es correcta
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'La contraseña actual ingresada es incorrecta.');
            }
            // Asignar nueva clave encriptada
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return redirect('/perfil')->with('success', 'Perfil actualizado correctamente.');
    }
}