<?php

namespace App\Services;

use App\Contracts\Services\AlmacenamientoImagenPromocionInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class AlmacenamientoImagenPromocion implements AlmacenamientoImagenPromocionInterface
{
    /**
     * Inyecta el disco público donde viven las imágenes de promociones.
     */
    public function __construct(
        private readonly Filesystem $disco,
    ) {}

    /**
     * Almacena el archivo en promociones/ y devuelve su ruta relativa.
     */
    public function guardar(UploadedFile $archivo): string
    {
        $extension = strtolower((string) ($archivo->guessExtension() ?: 'jpg'));
        $nombre = Str::uuid()->toString().'.'.$extension;
        $ruta = $this->disco->putFileAs('promociones', $archivo, $nombre);

        return $ruta === false ? 'promociones/'.$nombre : $ruta;
    }

    /**
     * Borra el archivo del disco si la ruta es local.
     */
    public function eliminar(?string $ruta): void
    {
        if (! filled($ruta) || str_contains($ruta, '://')) {
            return;
        }

        $this->disco->delete($ruta);
    }
}
