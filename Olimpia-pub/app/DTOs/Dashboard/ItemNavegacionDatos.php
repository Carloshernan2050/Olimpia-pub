<?php

namespace App\DTOs\Dashboard;

final readonly class ItemNavegacionDatos
{
    /**
     * Crea un ítem de la barra secundaria del dashboard.
     */
    public function __construct(
        public string $clave,
        public string $etiqueta,
        public string $icono,
        public ?string $ruta = null,
    ) {}

    /**
     * Indica si el ítem ya tiene pantalla disponible.
     */
    public function estaDisponible(): bool
    {
        return $this->ruta !== null;
    }
}
