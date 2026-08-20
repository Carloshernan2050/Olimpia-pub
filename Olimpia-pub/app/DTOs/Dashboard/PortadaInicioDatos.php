<?php

namespace App\DTOs\Dashboard;

final readonly class PortadaInicioDatos
{
    /**
     * @param  list<BloqueInicioDatos>  $bloques
     */
    public function __construct(
        public array $bloques,
    ) {}

    /**
     * Recorre los bloques en el orden de la grilla.
     *
     * @return list<BloqueInicioDatos>
     */
    public function bloquesEnOrden(): array
    {
        return $this->bloques;
    }
}
