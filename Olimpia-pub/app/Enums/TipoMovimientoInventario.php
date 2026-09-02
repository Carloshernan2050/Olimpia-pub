<?php

namespace App\Enums;

enum TipoMovimientoInventario: string
{
    case Entrada = 'entrada';
    case Salida = 'salida';

    /**
     * Etiqueta visible en el formulario y el listado.
     */
    public function etiqueta(): string
    {
        return match ($this) {
            self::Entrada => 'Entrada',
            self::Salida => 'Salida',
        };
    }

    /**
     * Valores persistidos en movimiento_inventario.
     *
     * @return list<string>
     */
    public static function valores(): array
    {
        return array_column(self::cases(), 'value');
    }
}
