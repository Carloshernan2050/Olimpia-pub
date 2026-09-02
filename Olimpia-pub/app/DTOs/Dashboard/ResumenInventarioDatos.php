<?php

namespace App\DTOs\Dashboard;

final readonly class ResumenInventarioDatos
{
    /**
     * Totales de las tarjetas superiores del inventario.
     */
    public function __construct(
        public int $productos,
        public int $movimientos,
        public int $stockBajo,
        public int $agotados,
    ) {}
}
