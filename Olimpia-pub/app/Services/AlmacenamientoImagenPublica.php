<?php

namespace App\Services;

use App\Contracts\Services\AlmacenamientoImagenPublicaInterface;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

abstract class AlmacenamientoImagenPublica implements AlmacenamientoImagenPublicaInterface
{
    /**
     * Inyecta el disco público y la carpeta del módulo.
     */
    public function __construct(
        private readonly Filesystem $disco,
        private readonly string $carpeta,
    ) {}

    /**
     * Almacena el archivo en la carpeta del módulo y devuelve su ruta relativa.
     */
    public function guardar(UploadedFile $archivo): string
    {
        $extension = strtolower((string) ($archivo->guessExtension() ?: 'jpg'));
        $nombre = Str::uuid()->toString().'.'.$extension;
        $ruta = $this->disco->putFileAs($this->carpeta, $archivo, $nombre);

        return $ruta === false ? $this->carpeta.'/'.$nombre : $ruta;
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
