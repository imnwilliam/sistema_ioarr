<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inversion extends Model
{
    // Le indicamos el nombre exacto de tu tabla
    protected $table = 'inversiones';
    
    // Apagamos los timestamps porque tu tabla original no tiene created_at ni updated_at
    public $timestamps = false;

    // Permitimos que estos campos se puedan guardar/actualizar masivamente desde el formulario
    protected $fillable = [
        'cui',
        'nombre_inversion',
        'estado_pmi',
        'fase'
    ];
}