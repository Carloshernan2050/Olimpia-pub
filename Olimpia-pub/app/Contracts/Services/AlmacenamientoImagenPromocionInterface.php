<?php

namespace App\Contracts\Services;

use Illuminate\Http\UploadedFile;

interface AlmacenamientoImagenPromocionInterface
{
    /**
     * Guarda la imagen y devuelve la ruta relativa en el disco público.
     */
    public function guardar(UploadedFile $archivo): string;

    /**
     * Elimina una imagen persistida, si existe.
     */
    public function eliminar(?string $ruta): void;
}
