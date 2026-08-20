<?php

namespace App\Services;

use App\Contracts\Repositories\ContenidoInicioRepositoryInterface;
use App\Contracts\Services\ContenidoInicioServiceInterface;
use App\DTOs\Dashboard\BloqueInicioDatos;
use App\DTOs\Dashboard\PortadaInicioDatos;
use App\Enums\PosicionInicio;

class ContenidoInicioService implements ContenidoInicioServiceInterface
{
    /**
     * Inyecta el repositorio de bloques de Home.
     */
    public function __construct(
        private readonly ContenidoInicioRepositoryInterface $contenidoInicioRepository,
    ) {}

    /**
     * Arma la portada de Home con un bloque por cada posición de la grilla.
     */
    public function obtenerPortada(): PortadaInicioDatos
    {
        $activos = $this->contenidoInicioRepository->activosPorPosicion();
        $bloques = [];

        foreach (PosicionInicio::enOrdenDeGrilla() as $posicion) {
            $contenido = $activos->get($posicion->value);
            $bloques[] = $contenido === null
                ? BloqueInicioDatos::vacio($posicion)
                : BloqueInicioDatos::fromModel($contenido);
        }

        return new PortadaInicioDatos($bloques);
    }
}
