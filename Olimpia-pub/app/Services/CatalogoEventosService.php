<?php

namespace App\Services;

use App\Contracts\Repositories\EventoRepositoryInterface;
use App\Contracts\Services\CatalogoEventosServiceInterface;
use App\DTOs\Dashboard\CatalogoEventosDatos;
use App\DTOs\Dashboard\EventoDetalleDatos;
use App\DTOs\Dashboard\EventoTarjetaDatos;
use App\DTOs\Dashboard\FiltroRangoFechasDatos;
use App\Exceptions\Evento\EventoNoEncontradoException;
use App\Models\Evento;

class CatalogoEventosService implements CatalogoEventosServiceInterface
{
    /**
     * Inyecta el repositorio de eventos.
     */
    public function __construct(
        private readonly EventoRepositoryInterface $eventoRepository,
    ) {}

    /**
     * Convierte los eventos filtrados en tarjetas del catálogo.
     */
    public function obtenerCatalogo(?FiltroRangoFechasDatos $filtro = null): CatalogoEventosDatos
    {
        $filtro ??= FiltroRangoFechasDatos::predeterminado();

        $eventos = $this->eventoRepository
            ->listar($filtro)
            ->map(fn (Evento $evento): EventoTarjetaDatos => EventoTarjetaDatos::fromModel($evento))
            ->values()
            ->all();

        return new CatalogoEventosDatos($eventos);
    }

    /**
     * Devuelve la ficha de detalle o lanza si el evento no existe.
     */
    public function obtenerDetalle(int $id): EventoDetalleDatos
    {
        $evento = $this->eventoRepository->findById($id);

        if ($evento === null) {
            throw new EventoNoEncontradoException;
        }

        return EventoDetalleDatos::fromModel($evento);
    }
}
