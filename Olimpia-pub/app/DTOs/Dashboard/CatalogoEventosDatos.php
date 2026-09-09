<?php

namespace App\DTOs\Dashboard;

final readonly class CatalogoEventosDatos
{
    /**
     * @param  list<EventoTarjetaDatos>  $eventos
     */
    public function __construct(
        public array $eventos,
    ) {}

    /**
     * Indica si el catálogo tiene al menos una tarjeta.
     */
    public function tieneEventos(): bool
    {
        return $this->eventos !== [];
    }

    /**
     * Recorre los eventos en el orden del repositorio.
     *
     * @return list<EventoTarjetaDatos>
     */
    public function enOrden(): array
    {
        return $this->eventos;
    }
}
