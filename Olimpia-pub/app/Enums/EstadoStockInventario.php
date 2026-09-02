<?php

namespace App\Enums;

use App\Support\Dashboard\UmbralStockInventario;

enum EstadoStockInventario: string
{
    case Disponible = 'disponible';
    case Bajo = 'bajo';
    case Agotado = 'agotado';

    /**
     * Clasifica el stock según el umbral de inventario.
     */
    public static function fromStock(int $stock): self
    {
        if ($stock <= 0) {
            return self::Agotado;
        }

        if ($stock <= UmbralStockInventario::BAJO) {
            return self::Bajo;
        }

        return self::Disponible;
    }

    /**
     * Etiqueta visible en la tabla y el filtro.
     */
    public function etiqueta(): string
    {
        return match ($this) {
            self::Disponible => 'Disponible',
            self::Bajo => 'Stock bajo',
            self::Agotado => 'Agotado',
        };
    }

    /**
     * Valores aceptados en el filtro de estado.
     *
     * @return list<string>
     */
    public static function valores(): array
    {
        return array_column(self::cases(), 'value');
    }
}
