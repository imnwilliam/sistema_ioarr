<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PerfilController extends Controller
{
    public function index()
    {
        return view('perfil');
    }

    public function update(Request $request)
    {
        $user = User::find(auth()->user()->id);

        $request->validate([
            'name' => 'required|string|max:255',
            'current_password' => 'required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed'
        ]);

        $user->name = $request->name;

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'La contraseña actual ingresada es incorrecta.');
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            // Cierra la sesión en cualquier otro dispositivo/navegador
            // manteniendo activa solo la sesión actual.
            Auth::logoutOtherDevices($request->new_password);

            return redirect('/perfil')->with('success', 'Perfil actualizado. Se cerró la sesión en tus otros dispositivos por seguridad.');
        }

        $user->save();

        return redirect('/perfil')->with('success', 'Perfil actualizado correctamente.');
    }
}