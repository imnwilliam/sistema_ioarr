<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inversion extends Model
{
    // Le indicamos el nombre exacto de tu tabla
    protected $table = 'inversiones';
    
    // Apagamos los timestamps porque tu tabla no tiene created_at ni updated_at
    public $timestamps = false;
}