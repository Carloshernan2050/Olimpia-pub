<?php

namespace App\DTOs\Dashboard;

final readonly class PaginacionInventarioDatos
{
    /**
     * Enlaces de página anterior y siguiente del catálogo.
     */
    public function __construct(
        public int $actual,
        public int $ultima,
        public ?string $anterior,
        public ?string $siguiente,
    ) {}

    /**
     * Indica si hace falta el control de páginas.
     */
    public function hayMasDeUnaPagina(): bool
    {
        return $this->ultima > 1;
    }
}
