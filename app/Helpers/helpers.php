<?php

use App\Support\Almacenamiento;
use Illuminate\Http\UploadedFile;

if (!function_exists('subir_b2')) {
    function subir_b2(UploadedFile $archivo, ?string $carpeta = null): string
    {
        return Almacenamiento::subir($archivo, $carpeta);
    }
}

if (!function_exists('url_temporal_b2')) {
    function url_temporal_b2(?string $ruta, ?int $minutos = null): ?string
    {
        return Almacenamiento::urlTemporal($ruta, $minutos);
    }
}

if (!function_exists('eliminar_b2')) {
    function eliminar_b2(?string $ruta): bool
    {
        return Almacenamiento::eliminar($ruta);
    }
}