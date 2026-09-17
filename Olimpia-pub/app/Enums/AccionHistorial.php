<?php

namespace App\Enums;

enum AccionHistorial: string
{
    case TerminarPedido = 'terminar_pedido';

    /**
     * Etiqueta visible de la acción registrada.
     */
    public function etiqueta(): string
    {
        return match ($this) {
            self::TerminarPedido => 'Terminar pedido',
        };
    }
}
