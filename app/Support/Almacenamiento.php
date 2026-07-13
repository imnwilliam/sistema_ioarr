<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Almacenamiento
{
    protected const DISCO = 'b2';

    /**
     * Sube un archivo al bucket B2 y retorna la ruta relativa
     * (esa ruta es lo único que debes guardar en la base de datos).
     */
    public static function subir(UploadedFile $archivo, ?string $carpeta = null): string
    {
        $carpeta = $carpeta ?? config('almacenamiento.carpeta_evidencias', 'evidencias');

        $extension = strtolower($archivo->getClientOriginalExtension());
        $nombreBase = Str::slug(pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME));
        if ($nombreBase === '') {
            $nombreBase = 'archivo';
        }

        $nombreUnico = time() . '_' . Str::random(10) . '_' . $nombreBase . '.' . $extension;

        return $archivo->storeAs($carpeta, $nombreUnico, self::DISCO);
    }

    /**
     * Genera una URL firmada temporal para un archivo privado del bucket.
     * Retorna null si la ruta está vacía o el archivo ya no existe.
     */
    public static function urlTemporal(?string $ruta, ?int $minutos = null): ?string
    {
        if (empty($ruta)) {
            return null;
        }

        if (!Storage::disk(self::DISCO)->exists($ruta)) {
            return null;
        }

        $minutos = $minutos ?? config('almacenamiento.url_temporal_minutos', 60);

        return Storage::disk(self::DISCO)->temporaryUrl($ruta, now()->addMinutes($minutos));
    }

    /**
     * Elimina un archivo del bucket B2 dada su ruta relativa.
     */
    public static function eliminar(?string $ruta): bool
    {
        if (empty($ruta)) {
            return false;
        }

        if (!Storage::disk(self::DISCO)->exists($ruta)) {
            return false;
        }

        return Storage::disk(self::DISCO)->delete($ruta);
    }
}