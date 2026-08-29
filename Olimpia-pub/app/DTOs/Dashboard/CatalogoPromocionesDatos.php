<?php

namespace App\DTOs\Dashboard;

final readonly class CatalogoPromocionesDatos
{
    /**
     * @param  list<PromocionTarjetaDatos>  $promociones
     */
    public function __construct(
        public array $promociones,
    ) {}

    /**
     * Indica si el catálogo tiene al menos una tarjeta.
     */
    public function tienePromociones(): bool
    {
        return $this->promociones !== [];
    }

    /**
     * Recorre las promociones en el orden del repositorio.
     *
     * @return list<PromocionTarjetaDatos>
     */
    public function enOrden(): array
    {
        return $this->promociones;
    }
}
