<?php

namespace App\Enums;

enum EstadoPedido: string
{
    case Activo = 'pendiente';
    case Cerrado = 'cerrado';
    case Cancelado = 'cancelado';

    /**
     * Solo un pedido en este estado puede existir por mesa.
     */
    public function esActivo(): bool
    {
        return $this === self::Activo;
    }

    /**
     * Interpreta el valor persistido; si no coincide, queda activo.
     */
    public static function desdeValor(?string $valor): self
    {
        return self::tryFrom((string) $valor) ?? self::Activo;
    }
}
