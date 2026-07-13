<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'id_rol',
        'estado', // <-- AÑADIDO
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'estado' => 'boolean',
        ];
    }

    // Función para verificar si el usuario es Administrador (Rol 1)
    public function isAdmin()
    {
        return $this->id_rol === 1;
    }

    // Protección del admin principal (id = 1)
    public function esAdminPrincipal()
    {
        return $this->id === 1;
    }
}