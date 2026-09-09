<?php

namespace App\Enums;

enum EstadoEvento: string
{
    case Programado = 'programado';
    case Cancelado = 'cancelado';
    case Finalizado = 'finalizado';

    /**
     * Etiqueta visible en el catálogo y el detalle.
     */
    public function etiqueta(): string
    {
        return match ($this) {
            self::Programado => 'Programado',
            self::Cancelado => 'Cancelado',
            self::Finalizado => 'Finalizado',
        };
    }

    /**
     * Interpreta el valor persistido; si no coincide, queda programado.
     */
    public static function desdeValor(?string $valor): self
    {
        return self::tryFrom((string) $valor) ?? self::Programado;
    }

    /**
     * Valores aceptados en el formulario y la validación.
     *
     * @return list<string>
     */
    public static function valores(): array
    {
        return array_column(self::cases(), 'value');
    }
}
