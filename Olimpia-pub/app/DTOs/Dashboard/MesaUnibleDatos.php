<?php

namespace App\DTOs\Dashboard;

final readonly class MesaUnibleDatos
{
    /**
     * Mesa de tipo mesa disponible para unir.
     */
    public function __construct(
        public int $id,
        public int $numero,
    ) {}

    public function etiqueta(): string
    {
        return 'Mesa '.$this->numero;
    }
}
