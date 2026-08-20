<?php

namespace App\DTOs\Dashboard;

final readonly class AccionCabeceraDatos
{
    /**
     * Crea una acción del header del dashboard.
     */
    public function __construct(
        public string $clave,
        public string $etiqueta,
        public string $icono,
        public bool $esPerfil = false,
    ) {}
}
