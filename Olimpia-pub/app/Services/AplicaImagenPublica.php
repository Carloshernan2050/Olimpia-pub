<?php

namespace App\Services;

use App\Contracts\Services\AlmacenamientoImagenPublicaInterface;
use Illuminate\Http\UploadedFile;

trait AplicaImagenPublica
{
    /**
     * Añade la imagen al payload y borra la anterior si se está reemplazando.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function conImagen(
        AlmacenamientoImagenPublicaInterface $imagenes,
        array $payload,
        ?UploadedFile $imagen,
        ?string $rutaAnterior = null,
    ): array {
        if ($imagen === null) {
            return $payload;
        }

        if (filled($rutaAnterior)) {
            $imagenes->eliminar($rutaAnterior);
        }

        $payload['url_imagen'] = $imagenes->guardar($imagen);

        return $payload;
    }
}
