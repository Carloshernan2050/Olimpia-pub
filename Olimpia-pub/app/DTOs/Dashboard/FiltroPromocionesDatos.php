<?php

namespace App\DTOs\Dashboard;

final readonly class FiltroPromocionesDatos
{
    /**
     * Rango de fechas del catálogo (desde / hasta).
     */
    public function __construct(
        public ?string $desde,
        public ?string $hasta,
    ) {}

    /**
     * Filtro inicial: vigentes de hoy, sin rango extra.
     */
    public static function predeterminado(): self
    {
        return new self(null, null);
    }

    /**
     * Interpreta los parámetros de consulta del catálogo.
     */
    public static function fromInput(?string $desde, ?string $hasta): self
    {
        return new self(
            self::fecha($desde),
            self::fecha($hasta),
        );
    }

    /**
     * Indica si el usuario eligió un rango de fechas.
     */
    public function estaActivo(): bool
    {
        return $this->desde !== null || $this->hasta !== null;
    }

    /**
     * Query string para conservar el filtro en otros enlaces.
     *
     * @return array<string, string>
     */
    public function query(): array
    {
        return array_filter([
            'desde' => $this->desde,
            'hasta' => $this->hasta,
        ], fn (?string $valor): bool => $valor !== null && $valor !== '');
    }

    /**
     * Acepta solo fechas ISO; cualquier otro valor se ignora.
     */
    private static function fecha(?string $valor): ?string
    {
        if (! filled($valor) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor) !== 1) {
            return null;
        }

        return $valor;
    }
}
