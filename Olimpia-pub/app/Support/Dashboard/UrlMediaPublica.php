<?php

namespace App\Support\Dashboard;

final class UrlMediaPublica
{
    /**
     * Convierte una ruta persistida en una URL usable por el navegador.
     */
    public static function de(?string $ruta): ?string
    {
        if (! filled($ruta)) {
            return null;
        }

        if (str_starts_with($ruta, 'http://') || str_starts_with($ruta, 'https://')) {
            return $ruta;
        }

        $rutaPublica = str_starts_with($ruta, '/') ? $ruta : 'storage/'.$ruta;

        return asset($rutaPublica);
    }
}
