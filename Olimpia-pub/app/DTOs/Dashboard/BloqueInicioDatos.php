<?php

namespace App\DTOs\Dashboard;

use App\Enums\PosicionInicio;
use App\Enums\TipoBloqueInicio;
use App\Models\ContenidoInicio;
use App\Support\Dashboard\UrlMediaPublica;

final readonly class BloqueInicioDatos
{
    /**
     * Crea el bloque de Home con su posición, tipo y contenido.
     */
    public function __construct(
        public PosicionInicio $posicion,
        public TipoBloqueInicio $tipo,
        public ?string $titulo,
        public ?string $cuerpo,
        public ?string $urlMedia,
    ) {}

    /**
     * Construye el DTO a partir del modelo persistido.
     */
    public static function fromModel(ContenidoInicio $contenido): self
    {
        return new self(
            $contenido->posicion,
            $contenido->tipo,
            $contenido->titulo,
            $contenido->cuerpo,
            $contenido->url_media,
        );
    }

    /**
     * Bloque vacío para que la grilla no se rompa si falta contenido.
     */
    public static function vacio(PosicionInicio $posicion): self
    {
        return new self(
            $posicion,
            $posicion->tipo(),
            null,
            null,
            null,
        );
    }

    /**
     * Indica si el bloque tiene algo visible para el usuario.
     */
    public function tieneContenido(): bool
    {
        return filled($this->titulo)
            || filled($this->cuerpo)
            || filled($this->urlMedia);
    }

    /**
     * URL lista para el navegador (local o absoluta).
     */
    public function urlMediaPublica(): ?string
    {
        return UrlMediaPublica::de($this->urlMedia);
    }

    /**
     * Pista WebVTT de subtítulos asociada al video local.
     */
    public function urlSubtitulosPublica(): ?string
    {
        return $this->urlPista('.es.vtt');
    }

    /**
     * Pista WebVTT de descripción asociada al video local.
     */
    public function urlDescripcionPublica(): ?string
    {
        return $this->urlPista('.descripcion.es.vtt');
    }

    /**
     * Construye la URL pública de una pista a partir del archivo de video.
     */
    private function urlPista(string $sufijo): ?string
    {
        if (! filled($this->urlMedia) || ! str_ends_with(strtolower($this->urlMedia), '.mp4')) {
            return null;
        }

        return UrlMediaPublica::de(substr($this->urlMedia, 0, -4).$sufijo);
    }
}
