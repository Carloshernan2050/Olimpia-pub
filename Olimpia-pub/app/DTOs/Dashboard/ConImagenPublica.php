<?php

namespace App\DTOs\Dashboard;

use App\Support\Dashboard\UrlMediaPublica;

/**
 * @property-read ?string $urlImagen
 */
trait ConImagenPublica
{
    /**
     * Indica si hay una imagen persistida.
     */
    public function tieneImagen(): bool
    {
        return filled($this->urlImagen);
    }

    /**
     * URL lista para previsualizar la imagen.
     */
    public function urlImagenPublica(): ?string
    {
        return UrlMediaPublica::de($this->urlImagen);
    }
}
